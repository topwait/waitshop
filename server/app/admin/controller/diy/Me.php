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

namespace app\admin\controller\diy;


use app\admin\logic\diy\MeLogic;
use app\common\basics\Backend;
use app\common\enum\LinkEnum;
use app\common\utils\JsonUtils;
use think\response\Json;
use think\response\View;

/**
 * 我的功能设计-控制器层
 * Class Me
 * @package app\admin\controller\diy
 */
class Me extends Backend
{
    /**
     * 我的功能列表
     *
     * @author windy
     * @return Json|View
     */
    public function lists()
    {
        if (request()->isAjax()) {
            $lists = MeLogic::lists($this->getData());
            return JsonUtils::success($lists);
        }

        return view('diy/me/lists');
    }

    /**
     * 我的功能新增
     *
     * @return View|Json
     */
    public function add()
    {
        if (request()->isAjax()) {
            $result = MeLogic::add($this->postData());
            if ($result === false) {
                return JsonUtils::error(MeLogic::getError());
            }
            return JsonUtils::ok('新增成功');
        }

        return view('diy/me/add', ['menus'=>LinkEnum::getLink()]);
    }

    /**
     * 我的功能编辑
     *
     * @author windy
     * @return Json|View
     */
    public function edit()
    {
        if (request()->isAjax()) {
            $result = MeLogic::edit($this->postData());
            if ($result === false) {
                return JsonUtils::error(MeLogic::getError());
            }
            return JsonUtils::ok('编辑成功');
        }

        return view('diy/me/edit', [
            'detail' => MeLogic::detail($this->getData('id')),
            'menus'  => LinkEnum::getLink()
        ]);
    }

    /**
     * 我的功能删除
     *
     * @author windy
     * @return Json
     */
    public function del(): Json
    {
        if (request()->isAjax()) {
            $result = MeLogic::del($this->postData('id'));
            if ($result === false) {
                return JsonUtils::error(MeLogic::getError());
            }
            return JsonUtils::ok('删除成功');
        }

        return JsonUtils::error('请求异常');
    }

    /**
     * 修改我的功能状态
     *
     * @author windy
     * @return Json
     */
    public function status(): Json
    {
        if (request()->isAjax()) {
            $result = MeLogic::status($this->postData('id'));
            if ($result === false) {
                return JsonUtils::error(MeLogic::getError());
            }
            return JsonUtils::ok('操作成功');
        }

        return JsonUtils::error('请求异常');
    }
}