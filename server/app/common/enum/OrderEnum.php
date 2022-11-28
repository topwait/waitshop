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

namespace app\common\enum;


/**
 * 订单枚举类
 * Class OrderEnum
 * @package app\common\enum
 */
class OrderEnum
{
    // 订单类型
    const NORMAL_ORDER  = 1;  //普通订单
    const SECKILL_ORDER = 2;  //秒杀订单
    const TEAM_ORDER    = 3;  //拼团订单
    const BARGAIN_ORDER = 4;  //砍价订单

    // 订单状态
    const STATUS_WAIT_PAY      = 0;  //待付款
    const STATUS_WAIT_DELIVERY = 1;  //待发货
    const STATUS_WAIT_RECEIVE  = 2;  //待收货
    const STATUS_FINISH        = 3;  //已完成
    const STATUS_CLOSE         = 4;  //已关闭

    // 支付状态
    const TO_BE_PAID_STATUS   = 0;  //待支付
    const OK_PAID_STATUS      = 1;  //已支付

    // 支付方式
    const PAY_WAY_BALANCE = 1;  //余额支付
    const PAY_WAY_MNP     = 2;  //微信支付
    const PAY_WAY_ALI     = 3;  //支付宝支付

    // 主订单退款状态
    const ORDER_REFUND_NORMAL = 0; // 无退款
    const ORDER_REFUND_PART   = 1; // 部分退款
    const ORDER_REFUND_ALL    = 2; // 全部退款

    // 子订单退款状态
    const REFUND_STATUS_NORMAL = 0; //未退款
    const REFUND_STATUS_APPLY  = 1; //申请中
    const REFUND_STATUS_REFUSE = 2; //拒绝退款
    const REFUND_STATUS_ALL    = 3; //全部退款
    const REFUND_STATUS_PART   = 4; //部分退款

    // 配送方式
    const DELIVER_TYPE_EXPRESS = 1; //快递配送
    const DELIVER_TYPE_STORE   = 2; //门店自提
    const DELIVER_TYPE_CITY    = 3; //同城配送

    /**
     * 获取“订单类型”描述
     *
     * @author windy
     * @param $type
     * @return array|string
     */
    public static function getOrderTypeDesc($type)
    {
        $desc = [
            self::NORMAL_ORDER  => '普通订单',
            self::SECKILL_ORDER => '秒杀订单',
            self::TEAM_ORDER    => '拼团订单',
            self::BARGAIN_ORDER => '砍价订单'
        ];

        if ($type === true){
            return $desc;
        }
        return $desc[$type] ?? '未知';
    }

    /**
     * 获取“订单状态”描述
     *
     * @author windy
     * @param bool $status
     * @return array|string
     */
    public static function getOrderStatusDesc($status = true)
    {
        $desc = [
            self::STATUS_WAIT_PAY      => '待付款',
            self::STATUS_WAIT_DELIVERY => '待发货',
            self::STATUS_WAIT_RECEIVE  => '待收货',
            self::STATUS_FINISH        => '已完成',
            self::STATUS_CLOSE         => '已关闭'
        ];
        if ($status === true) {
            return $desc;
        }
        return $desc[$status] ?? '未知';
    }

    /**
     * 获取“支付状态”描述
     *
     * @author windy
     * @param bool $status
     * @return array|string
     */
    public static function getPayStatusDesc($status = true)
    {
        $desc = [
            self::TO_BE_PAID_STATUS    => '待支付',
            self::OK_PAID_STATUS       => '已支付'
        ];
        if ($status === true) {
            return $desc;
        }
        return $desc[$status] ?? '未知';
    }

    /**
     * 获取“支付方式”描述
     *
     * @author windy
     * @param bool $status
     * @return array|string
     */
    public static function getPayWayDesc($status = true)
    {
        $desc = [
            self::PAY_WAY_BALANCE   => '余额支付',
            self::PAY_WAY_MNP       => '微信支付',
            self::PAY_WAY_ALI       => '支付宝支付',

        ];
        if ($status === true) {
            return $desc;
        }
        return $desc[$status] ?? '未知';
    }

    /**
     * 获取“退款状态”描述
     *
     * @author windy
     * @param bool $status
     * @return array|mixed|string
     */
    public static function getRefundStatusDesc($status= true)
    {
        $desc = [
            self::REFUND_STATUS_NORMAL => '未退款',
            self::REFUND_STATUS_APPLY  => '申请中',
            self::REFUND_STATUS_REFUSE => '拒绝退款',
            self::REFUND_STATUS_PART   => '部分退款',
            self::REFUND_STATUS_ALL    => '全部退款',
        ];
        if ($status === true) {
            return $desc;
        }
        return $desc[$status] ?? '未知';
    }

    /**
     * 获取“配送方式”描述
     *
     * @author windy
     * @param bool $status
     * @return array|mixed|string
     */
    public static function getDeliverTypeDesc($status = true)
    {
        $desc = [
            self::DELIVER_TYPE_EXPRESS => '快递配送',
            self::DELIVER_TYPE_STORE   => '门店自提',
            self::DELIVER_TYPE_CITY    => '同城配送',

        ];
        if ($status === true) {
            return $desc;
        }
        return $desc[$status] ?? '未知';
    }
}