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


use app\api\logic\addons\SeckillLogic;
use app\api\logic\PaymentLogic;
use app\api\logic\PayNotifyLogic;
use app\api\service\TeamOrderServer;
use app\api\validate\PaymentValidate;
use app\api\validate\RequestValidate;
use app\common\basics\Api;
use app\common\enum\OrderEnum;
use app\common\model\addons\RechargeOrder;
use app\common\model\order\Order;
use app\common\utils\JsonUtils;
use app\common\utils\WeChatUtils;
use EasyWeChat\Payment\Application;
use Exception;
use think\facade\Log;
use think\response\Json;

/**
 * 支付相关接口-控制器层
 * Class Payment
 * @package app\api\controller
 */
class Payment extends Api
{
    public $notNeedLogin = ['notifyMnp'];

    /**
     * 订单支付
     *
     * @author windy
     * @return Json
     */
    public function prepay(): Json
    {
        // 1、接收参数
        (new RequestValidate())->isPost();
        (new PaymentValidate())->goCheck();
        $from    = $this->postData('from');
        $client  = $this->postData('client');
        $orderId = $this->postData('order_id');
        $payWay  = $this->postData('pay_way');

        // 2、查询订单
        $order = [];
        switch ($from) {
            case 'order':
                $order = (new Order())->field(true)
                    ->where(['id'=>intval($orderId)])
                    ->with(['orderGoods'])
                    ->findOrEmpty()->toArray();
                break;
            case 'recharge':
                break;
            default:
                return JsonUtils::error('支付场景异常');
        }

        // 3、验证订单
        if (!$order || empty($order)) {
            return JsonUtils::error('订单不存在');
        }

        if ($order['order_status'] == OrderEnum::STATUS_CLOSE) {
            return JsonUtils::error('订单已关闭');
        }

        if ($order['pay_status'] == OrderEnum::OK_PAID_STATUS) {
            return JsonUtils::error('订单已支付');
        }

        if ($order['paid_amount'] < 0) {
            return JsonUtils::error('支付金额异常');
        }

        if (!in_array($payWay, [
            OrderEnum::PAY_WAY_BALANCE,
            OrderEnum::PAY_WAY_MNP,
            OrderEnum::PAY_WAY_ALI]))
        {
            return JsonUtils::error('支付方式不存在');
        }

        try {
            if ($order['order_type'] == OrderEnum::TEAM_ORDER) {
                TeamOrderServer::checkTeamStatus($order['user_id'], $order['team_activity_id'], $order['team_found_id']);
            } elseif ($order['order_type'] == OrderEnum::SECKILL_ORDER) {
                SeckillLogic::checkSeckillStatus($order['seckill_id'],  $order['orderGoods'][0]);
            }
        } catch (Exception $e) {
            return JsonUtils::error($e->getMessage());
        }

        // 4、更新支付方式
        if (intval($payWay) != $order['pay_way']) {
            Order::update(['pay_way'=>$payWay], ['id'=>intval($orderId)]);
            $order['pay_way'] = intval($payWay);
        }

        // 5、更新订单客户端
        if (intval($client) != $order['order_platform']) {
            Order::update(['order_platform'=>$client], ['id'=>intval($orderId)]);
            $order['order_platform'] = intval($client);
        }

        // 6、去支付
        $result = PaymentLogic::pay($from, $order);
        if ($result === false) {
            $error = PaymentLogic::getError() ?: '支付失败';
            return JsonUtils::error($error);
        }

        // 7、返回结果
        $data = $result === true ? [] : $result;
        return JsonUtils::ok('OK', $data, PaymentLogic::getReturnCode());
    }

    /**
     * 获取支付方式
     *
     * @author windy
     * @return Json
     */
    public function way(): Json
    {
        (new RequestValidate())->isGet();
        $lists = PaymentLogic::way($this->userId);
        return JsonUtils::ok('获取成功', $lists);
    }

    /**
     * 微信小程序支付回调
     *
     * @author windy
     */
    public function notifyMnp()
    {
        try {
            $config = WeChatUtils::getMnpConfig();
            $app = new Application($config);

            $response = $app->handlePaidNotify(function ($message, $fail) {
                Log::write("微信支付回调开始：" . $message);
                // 回调失败
                if ($message['return_code'] !== 'SUCCESS') {
                    return $fail('通信失败');
                }

                // 回调成功
                if ($message['result_code'] === 'SUCCESS') {
                    $extra['transaction_id'] = $message['transaction_id'];
                    switch ($message['attach']) {
                        case 'order':
                            $order = (new Order())->where(['order_sn'=>$message['out_trade_no']])->findOrEmpty();
                            if (!$order->isEmpty() || $order['pay_status'] >= OrderEnum::OK_PAID_STATUS) {
                                return true;
                            }
                            PayNotifyLogic::handle('order', $message['out_trade_no'], $extra);
                            break;
                        case 'recharge':
                            $order = (new RechargeOrder())->where(['order_sn'=>$message['out_trade_no']])->findOrEmpty();
                            if (!$order->isEmpty() || $order['pay_status'] >= OrderEnum::OK_PAID_STATUS) {
                                return true;
                            }
                            PayNotifyLogic::handle('recharge', $message['out_trade_no'], $extra);
                            break;
                    }
                }

                return true;
            });

            $response->send();
        } catch (Exception $e) {
            Log::write("微信支付回调异常：" . $e->getMessage());
        }
    }
}