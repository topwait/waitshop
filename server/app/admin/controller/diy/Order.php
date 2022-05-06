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


use app\admin\logic\diy\OrderLogic;
use app\common\basics\Backend;
use app\common\utils\JsonUtils;
use think\response\Json;
use think\response\View;

/**
 * 我的订单设计-控制器层
 * Class Order
 * @package app\admin\controller\diy
 */
class Order extends Backend
{
    /**
     * 订单导航列表
     *
     * @author windy
     * @return Json|View
     */
    public function lists()
    {
        if (request()->isAjax()) {
            $lists = OrderLogic::lists($this->getData());
            return JsonUtils::success($lists);
        }

        return view('diy/order/lists');
    }

    /**
     * 订单导航编辑
     *
     * @author windy
     * @return Json|View
     */
    public function edit()
    {
        if (request()->isAjax()) {
            $result = OrderLogic::edit($this->postData());
            if ($result === false) {
                return JsonUtils::error(OrderLogic::getError());
            }
            return JsonUtils::ok('编辑成功');
        }

        return view('diy/order/edit', [
            'detail' => OrderLogic::detail($this->getData('id'))
        ]);
    }
}