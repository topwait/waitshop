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

namespace app\common\service;


use app\common\enum\LogGrowthEnum;
use app\common\enum\LogIntegralEnum;
use app\common\enum\LogWalletEnum;
use app\common\enum\OrderEnum;
use app\common\enum\OrderLogEnum;
use app\common\model\goods\Goods;
use app\common\model\goods\GoodsSku;
use app\common\model\log\LogGrowth;
use app\common\model\log\LogIntegral;
use app\common\model\log\LogOrder;
use app\common\model\log\LogWallet;
use app\common\model\addons\CouponList;
use app\common\model\order\Order;
use app\common\model\user\User;
use app\common\service\payment\WeChatPayService;

/**
 * 订单操作服务类
 * Class OrderService
 * @package app\common\service
 */
class OrderService
{
    /**
     * 关闭订单
     *
     * @author windy
     * @param $orderId
     */
    public static function closeOrder(int $orderId)
    {
        Order::update([
            'order_status' => OrderEnum::STATUS_CLOSE,
            'cancel_time'  => time()
        ], ['id' => $orderId]);

        LogOrder::record([
            'type'        => OrderLogEnum::TYPE_SYSTEM,
            'channel'     => OrderLogEnum::SYSTEM_CANCEL_ORDER,
            'order_id'    => $orderId,
            'operator_id' => 0,
        ]);
    }

    /**
     * 回退库存
     *
     * @author windy
     * @param int $orderId
     */
    public static function rollbackStock(int $orderId)
    {
        $order = (new Order())->with(['orderGoods'])->findOrEmpty($orderId)->toArray();
        foreach ($order['orderGoods'] as $item) {
            Goods::update(array('stock' => ['inc', $item['count']]), ['id' => $item['goods_id']]);
            GoodsSku::update(array('stock' => ['inc', $item['count']]), ['id' => $item['sku_id']]);
        }
    }

    /**
     * 退回优惠券
     *
     * @author windy
     * @param int $couponListId
     * @return bool
     */
    public static function rollbackCoupon(int $couponListId): bool
    {
        if ($couponListId <= 0) {
            return false;
        }

        $couponList = (new CouponList())->findOrEmpty($couponListId)->toArray();
        if (!$couponList || $couponList['expiry_time'] <= time()) {
            return false;
        }

        CouponList::create([
            'user_id'      => $couponList['user_id'],
            'coupon_id'    => $couponList['coupon_id'],
            'expiry_time'  => $couponList['expiry_time'],
            'receive_time' => time(),
            'update_time'  => time(),
        ]);

        return true;
    }

    /**
     * 发起退款
     *
     * @author windy
     * @param int $orderId (订单ID)
     * @param float $refundAmount (退款金额)
     * @param int $adminId
     * @throws @\think\Exception
     */
    public static function refund(int $orderId, float $refundAmount, int $adminId=0)
    {
        $order = (new Order())
            ->field('id,user_id,order_sn,pay_way,paid_amount,use_integral,transaction_id')
            ->findOrEmpty($orderId)
            ->toArray();

        switch ($order['pay_way']) {
            case OrderEnum::PAY_WAY_BALANCE: // 余额支付处理
                User::update([
                    'money'       => ['inc', $refundAmount],
                    'update_time' => time()
                ], ['id'=>$order['user_id']]);

                LogWallet::add(LogWalletEnum::CANCEL_ORDER_REFUND, $refundAmount, $order['user_id'], $adminId, $order['id'], $order['order_sn']);
                break;
            case OrderEnum::PAY_WAY_MNP:   // 微信支付处理
                WeChatPayService::refund([
                    'transaction_id' => $order['transaction_id'],
                    'refund_sn'      => make_order_no(),
                    'total_fee'      => $order['paid_amount'],
                    'refund_fee'     => $refundAmount * 100
                ]);
                break;
            case OrderEnum::PAY_WAY_ALI: // 支付宝支付处理
                break;
        }

        // 退回下单赠送的积分
        $integral = (new LogIntegral())
            ->where(['source_id'=>$order['id']])
            ->where(['change_type'=>1])
            ->where(['source_type'=>LogIntegralEnum::PAY_INC_INTEGRAL])
            ->order('id desc')
            ->value('change_amount') ?? 0;
        if ($integral > 0) {
            User::update([
                'integral'    => ['dec', $integral],
                'update_time' => time()
            ], ['id'=>$order['user_id']]);
            LogIntegral::reduce(
                LogIntegralEnum::REFUND_DEC_INTEGRAL,
                $integral,
                $order['user_id'], 0, $order['id'],
                $order['order_sn'], '退款退赠积分'
            );
        }

        // 退回下单赠送的成长
        $growth = (new LogGrowth())
                ->where(['source_id'=>$order['id']])
                ->where(['change_type'=>1])
                ->where(['source_type'=>LogGrowthEnum::PAY_INC_GROWTH])
                ->order('id desc')
                ->value('change_amount') ?? 0;
        if ($growth > 0) {
            User::update([
                'growth_value' => ['dec', $integral],
                'update_time'  => time()
            ], ['id'=>$order['user_id']]);
            LogGrowth::reduce(
                LogGrowthEnum::REFUND_DEC_GROWTH,
                $growth,
                $order['user_id'], 0, $order['id'],
                $order['order_sn'], '退款退赠成长'
            );
        }

        // 退回下单扣除的积分
        if ($order['use_integral'] > 0) {
            User::update([
                'integral'    => ['inc', $order['use_integral']],
                'update_time' => time()
            ], ['id'=>$order['user_id']]);
            LogIntegral::reduce(
                LogIntegralEnum::REFUND_INC_INTEGRAL,
                $order['use_integral'],
                $order['user_id'], 0, $order['id'],
                $order['order_sn'], '退款回退积分'
            );
        }
    }
}