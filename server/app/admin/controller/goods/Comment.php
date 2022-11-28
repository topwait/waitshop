<?php
// +----------------------------------------------------------------------
// | WaitShop开源电商系统 [ 助力中小企业快速落地 ]
// +----------------------------------------------------------------------
// | 欢迎阅读学习程序代码
// | gitee:   https://gitee.com/wafts/WaitShop
// | github:  https://github.com/topwait/waitshop
// | 官方网站: https://www.waitshop.cn
// +----------------------------------------------------------------------
// | 禁止对本系统程序代码以任何目的、任何形式再次发布或出售
// | WaitShop并不是自由软件,未经许可不能去掉WaitShop相关版权
// | WaitShop系统产品必须购买商业授权后,方可去除前后端官方标识
// | WaitShop团队版权所有并拥有最终解释权
// +----------------------------------------------------------------------
// | Author: WaitShop Team <2474369941@qq.com>
// +----------------------------------------------------------------------

namespace app\admin\controller\goods;


use app\admin\logic\goods\CommentLogic;
use app\admin\validate\goods\CommentValidate;
use app\common\basics\Backend;
use app\common\utils\JsonUtils;
use think\response\Json;
use think\response\View;

/**
 * 商品评论-控制器层
 * Class Comment
 * @package app\admin\controller\goods
 */
class Comment extends Backend
{
    protected $allowAllAction = ['statistics'];

    /**
     * 评论列表
     *
     * @author windy
     * @return View|Json
     */
    public function lists()
    {
        if (request()->isAjax()) {
            $lists = CommentLogic::lists($this->getData());
            return JsonUtils::success($lists);
        }

        return view('goods/comment/lists', [
            'statistics' => CommentLogic::statistics()
        ]);
    }

    /**
     * 数据统计
     *
     * @author windy
     * @return Json
     */
    public function statistics(): Json
    {
        if (request()->isAjax()) {
            $totalCount = CommentLogic::statistics();
            return JsonUtils::success($totalCount);
        }

        return JsonUtils::error('请求异常');
    }

    /**
     * 回复评论
     *
     * @author windy
     * @return View|Json
     */
    public function reply()
    {
        if (request()->isAjax()) {
            (new CommentValidate())->goCheck('reply');

            $result = CommentLogic::reply($this->postData());
            if ($result === false) {
                return JsonUtils::error(CommentLogic::getError());
            }
            return JsonUtils::ok('回复成功');
        }

        return view('goods/comment/reply', [
            'detail' => CommentLogic::detail($this->getData('id'))
        ]);
    }

    /**
     * 隐藏评论
     *
     * @author windy
     * @return Json
     */
    public function hide(): Json
    {
        if (request()->isAjax()) {
            (new CommentValidate())->goCheck('id');

            $result = CommentLogic::hide($this->postData('id'));
            if ($result === false) {
                return JsonUtils::error(CommentLogic::getError());
            }
            return JsonUtils::ok('隐藏成功');
        }

        return JsonUtils::error("请求异常");
    }

    /**
     * 显示评论
     *
     * @author windy
     * @return Json
     */
    public function show(): Json
    {
        if (request()->isAjax()) {
            (new CommentValidate())->goCheck('id');

            $result = CommentLogic::show($this->postData('id'));
            if ($result === false) {
                return JsonUtils::error(CommentLogic::getError());
            }
            return JsonUtils::ok('恢复成功');
        }

        return JsonUtils::error("请求异常");
    }

    /**
     * 删除评论
     *
     * @author windy
     * @return Json
     */
    public function del(): Json
    {
        if (request()->isAjax()) {
            (new CommentValidate())->goCheck('id');

            $result = CommentLogic::del([$this->postData('id')]);
            if ($result === false) {
                return JsonUtils::error(CommentLogic::getError());
            }
            return JsonUtils::ok('删除成功');
        }

        return JsonUtils::error("请求异常");
    }

    /**
     * 恢复评论
     *
     * @author windy
     * @return Json
     */
    public function recovery(): Json
    {
        if (request()->isAjax()) {
            (new CommentValidate())->goCheck('id');

            $result = CommentLogic::recovery($this->postData('id'));
            if ($result === false) {
                return JsonUtils::error(CommentLogic::getError());
            }
            return JsonUtils::ok('恢复成功');
        }

        return JsonUtils::error("请求异常");
    }
}