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

namespace app\api\controller;


use app\api\logic\OrderLogic;
use app\api\validate\IDMustBePositiveInt;
use app\api\validate\OrderPlaceValidate;
use app\api\validate\PageValidate;
use app\api\validate\RequestValidate;
use app\common\basics\Api;
use app\common\utils\JsonUtils;
use think\response\Json;

/**
 * 订单接口-控制器层
 * Class Order
 * @package app\api\controller
 */
class Order extends Api
{
    /**
     * 我的订单
     *
     * @author windy
     * @return Json
     */
    public function lists(): Json
    {
        (new RequestValidate())->isGet();
        (new PageValidate())->goCheck();

        $lists = OrderLogic::lists($this->getData(), $this->userId);
        return JsonUtils::success($lists);
    }

    /**
     * 下单 (生成订单)
     *
     * @author windy
     * @return Json
     */
    public function buy(): Json
    {
        (new RequestValidate())->isPost();
        (new OrderPlaceValidate())->goCheck();

        $post = $this->request->post();
        $oProducts = $post['products'];
        unset($post['products']);

        $order = OrderLogic::placeOrder($post, $oProducts, $this->userId);
        if ($order === false || !$order['pass'] || !$order['status']) {
            if (!$order['pass']) {
                foreach ($order['pStatusArray'] as $item) {
                    if (!$item['haveStock']) {
                        return JsonUtils::error($item['errorTips']);
                    }
                }
            }
            if (!$order['status']) {
                foreach ($order['pStatusArray'] as $item) {
                    if ($item['haveStatus'] != 0) {
                        return JsonUtils::error($item['errorTips']);
                    }
                }
            }
            return JsonUtils::error(OrderLogic::getError() ?? '下单异常');
        } else if($order['pass'] and $order['status']) {
            return JsonUtils::ok('OK', $order);
        }

        return JsonUtils::error(OrderLogic::getError(), $order);
    }

    /**
     * 订单结算
     *
     * @author windy
     * @return Json
     */
    public function settle(): Json
    {
        (new RequestValidate())->isPost();

        $detail = OrderLogic::settle($this->postData(), $this->userId);
        if ($detail === false) {
            return JsonUtils::error(OrderLogic::getError());
        }
        return JsonUtils::ok('OK', $detail);
    }

    /**
     * 订单详细
     *
     * @author windy
     * @return Json
     */
    public function detail(): Json
    {
        (new RequestValidate())->isGet();
        (new IDMustBePositiveInt())->goCheck();

        $detail = OrderLogic::detail($this->getData('id'), $this->userId);
        return JsonUtils::success($detail);
    }

    /**
     * 删除订单
     *
     * @author windy
     * @return Json
     */
    public function del(): Json
    {
        (new RequestValidate())->isPost();
        (new IDMustBePositiveInt())->goCheck();

        $result = OrderLogic::del($this->postData('id'), $this->userId);
        if ($result === false) {
            return JsonUtils::error(OrderLogic::getError());
        }
        return JsonUtils::ok('删除成功');
    }

    /**
     * 取消订单
     *
     * @author windy
     * @return Json
     */
    public function cancel(): Json
    {
        (new RequestValidate())->isPost();
        (new IDMustBePositiveInt())->goCheck();

        $result = OrderLogic::cancel($this->postData('id'), $this->userId);
        if ($result === false) {
            return JsonUtils::error(OrderLogic::getError());
        }
        return JsonUtils::ok('取消成功');
    }

    /**
     * 确认收货
     *
     * @author windy
     * @return Json
     */
    public function confirm(): Json
    {
        (new RequestValidate())->isPost();
        (new IDMustBePositiveInt())->goCheck();

        $result = OrderLogic::confirm($this->postData('id'), $this->userId);
        if ($result === false) {
            return JsonUtils::error(OrderLogic::getError());
        }
        return JsonUtils::ok('收货成功');
    }

    /**
     * 订单到时间自动取消
     *
     * @author windy
     * @return Json
     */
    public function end(): Json
    {
        (new RequestValidate())->isPost();
        (new IDMustBePositiveInt())->goCheck();

        $result = OrderLogic::end($this->postData('id'), $this->userId);
        if ($result === false) {
            return JsonUtils::error(OrderLogic::getError());
        }
        return JsonUtils::ok('取消成功');
    }

    /**
     * 查物流
     *
     * @author windy
     * @return Json
     */
    public function traces(): Json
    {
        (new RequestValidate())->isGet();
        (new IDMustBePositiveInt())->goCheck();

        $result = OrderLogic::traces($this->getData('id'), $this->userId);
        if ($result === false) {
            return JsonUtils::error(OrderLogic::getError());
        }

        return JsonUtils::ok('查询成功', $result);
    }
}