<?php
// +----------------------------------------------------------------------
// | WaitShop开源电商系统 [ 助力中小企业快速落地 ]
// +----------------------------------------------------------------------
// | 欢迎阅读学习程序代码
// | gitee:   https://gitee.com/wafts/WaitShop
// | github:  https://github.com/miniWorlds/waitshop
// | 官方网站: https://www.waitshop.cn
// +----------------------------------------------------------------------
// | 禁止对本系统程序代码以任何目的、任何形式再次发布或出售
// | WaitShop并不是自由软件,未经许可不能去掉WaitShop相关版权
// | WaitShop系统产品必须购买商业授权后,方可去除前后端官方标识
// | WaitShop团队版权所有并拥有最终解释权
// +----------------------------------------------------------------------
// | Author: WaitShop Team <2474369941@qq.com>
// +----------------------------------------------------------------------

namespace app\admin\controller\content;


use app\admin\logic\content\ArticleCategoryLogic;
use app\admin\logic\content\ArticleLogic;
use app\admin\validate\content\ArticleValidate;
use app\common\basics\Backend;
use app\common\utils\JsonUtils;
use think\response\Json;
use think\response\View;

/**
 * 文章管理-控制器层
 * Class Article
 * @package app\admin\logic\content
 */
class Article extends Backend
{
    /**
     * 文章列表
     *
     * @author windy
     * @return View|Json
     */
    public function lists()
    {
        if (request()->isAjax()) {
            $lists = ArticleLogic::lists($this->getData());
            return JsonUtils::success($lists);
        }

        return view('content/article/lists', [
            'category' => ArticleCategoryLogic::all()
        ]);
    }

    /**
     * 新增文章
     *
     * @author windy
     * @return View|Json
     */
    public function add()
    {
        if (request()->isAjax()) {
            (new ArticleValidate())->goCheck('add');
            $result = ArticleLogic::add($this->postData());
            if ($result === false) {
                return JsonUtils::error(ArticleLogic::getError());
            }
            return JsonUtils::ok('新增成功');
        }

        return view('content/article/add', [
            'categoryList' => ArticleCategoryLogic::all()
        ]);
    }

    /**
     * 编辑文章
     *
     * @author windy
     * @return View|Json
     */
    public function edit()
    {
        if (request()->isAjax()) {
            (new ArticleValidate())->goCheck('edit');
            $result = ArticleLogic::edit($this->postData());
            if ($result === false) {
                return JsonUtils::error(ArticleLogic::getError());
            }
            return JsonUtils::ok('编辑成功');
        }

        return view('content/article/edit', [
            'detail' => ArticleLogic::detail($this->getData('id')),
            'categoryList' => ArticleCategoryLogic::all()
        ]);
    }

    /**
     * 删除文章
     *
     * @author windy
     * @return Json
     */
    public function del(): Json
    {
        if (request()->isAjax()) {
            (new ArticleValidate())->goCheck('id');
            $result = ArticleLogic::del($this->postData('id'));
            if ($result === false) {
                return JsonUtils::error(ArticleLogic::getError());
            }
            return JsonUtils::ok('删除成功');
        }

        return JsonUtils::error('请求异常');
    }
}