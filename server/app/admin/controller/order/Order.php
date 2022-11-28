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

namespace app\admin\controller\order;


use app\admin\logic\order\OrderLogic;
use app\admin\logic\setting\ExpressLogic;
use app\common\basics\Backend;
use app\common\utils\JsonUtils;
use think\response\Json;
use think\response\View;

/**
 * 订单管理-控制器层
 * Class Order
 * @package app\admin\controller\order
 */
class Order extends Backend
{
    protected $allowAllAction = ['statistics'];

    /**
     * 订单列表
     *
     * @author windy
     * @return View|Json
     */
    public function lists()
    {
        if (request()->isAjax()) {
            $lists = OrderLogic::lists($this->postData());
            return JsonUtils::success($lists);
        }

        return view('order/lists', ['statistics'=>OrderLogic::statistics()]);
    }

    /**
     * 订单统计
     *
     * @author windy
     * @return Json
     */
    public function statistics(): Json
    {
        if (request()->isAjax()) {
            $result = OrderLogic::statistics();
            return JsonUtils::success($result);
        }

        return JsonUtils::error('请求异常');
    }

    /**
     * 订单详情
     *
     * @author windy
     * @return View
     */
    public function detail(): View
    {
        return view('order/detail', [
            'detail' => OrderLogic::detail($this->getData('id'))
        ]);
    }

    /**
     * 取消订单(已支付)
     *
     * @author windy
     * @return Json
     */
    public function cancel(): Json
    {
        if (request()->isAjax()) {
            $result = OrderLogic::cancel($this->postData('order_id'));
            if ($result === false) {
                return JsonUtils::error(OrderLogic::getError());
            }
            return JsonUtils::ok('取消成功');
        }

        return JsonUtils::error('请求异常');
    }

    /**
     * 关闭订单(未支付)
     *
     * @author windy
     * @return Json
     */
    public function close(): Json
    {
        if (request()->isAjax()) {
            $result = OrderLogic::close($this->postData('order_id'));
            if ($result === false) {
                return JsonUtils::error(OrderLogic::getError());
            }
            return JsonUtils::ok('关闭成功');
        }

        return JsonUtils::error('请求异常');
    }

    /**
     * 发货配送
     *
     * @author windy
     * @return View|Json
     */
    public function deliver()
    {
        if (request()->isAjax()) {
            $result = OrderLogic::deliver($this->postData());
            if ($result === false) {
                return JsonUtils::error(OrderLogic::getError());
            }
            return JsonUtils::ok('发货成功');
        }

        return view('order/deliver', [
            'express' => ExpressLogic::all()
        ]);
    }

    /**
     * 自提核销
     *
     * @author windy
     * @return View|Json
     */
    public function pickUp()
    {
        if (request()->isAjax()) {
            $result = OrderLogic::pickUp($this->postData('id'), $this->adminId);
            if ($result === false) {
                return JsonUtils::error(OrderLogic::getError());
            }
            return JsonUtils::ok('核销成功');
        }

        return view('order/pick', [
            'detail' => OrderLogic::detail($this->getData('id'))
        ]);
    }
}