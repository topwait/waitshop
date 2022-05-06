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


use app\admin\logic\diy\NavLogic;
use app\common\basics\Backend;
use app\common\utils\JsonUtils;
use think\response\Json;
use think\response\View;

/**
 * 底部导航设计-控制器层
 * Class Nav
 * @package app\admin\controller\diy
 */
class Nav extends Backend
{
    /**
     * 底部导航列表
     *
     * @author windy
     * @return Json|View
     */
    public function lists()
    {
        if (request()->isAjax()) {
            $lists = NavLogic::lists($this->getData());
            return JsonUtils::success($lists);
        }

        return view('diy/nav/lists');
    }

    /**
     * 底部导航编辑
     *
     * @author windy
     * @return Json|View
     */
    public function edit()
    {
        if (request()->isAjax()) {
            $result = NavLogic::edit($this->postData());
            if ($result === false) {
                return JsonUtils::error(NavLogic::getError());
            }
            return JsonUtils::ok('编辑成功');
        }

        return view('diy/nav/edit', [
            'detail' => NavLogic::detail($this->getData('id'))
        ]);
    }
}