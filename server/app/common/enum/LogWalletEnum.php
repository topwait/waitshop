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

namespace app\common\enum;


/**
 * 钱包流水日志枚举类
 * Class LogWalletEnum
 * @package app\common\enum
 */
class LogWalletEnum
{
    // 日志枚举类型
    const admin_add_money            = 100; //系统增加余额
    const admin_reduce_money         = 101; //系统扣减余额
    const recharge_money             = 102; //用户充值余额
    const balance_pay_order          = 103; //下单扣减余额
    const cancel_order_refund        = 104; //取消订单退回
    const after_sale_refund          = 105; //售后退回余额
    const distribution_inc_earnings  = 106; //分销订单结算增加佣金
    const withdraw_reduce_earnings   = 107; //提现扣减佣金(申请中)
    const withdraw_rollback_earnings = 108; //提现回滚佣金(失败了)
    const withdraw_increase_money    = 109; //提现增加余额(成功了)

    /**
     * 获取日志描述
     *
     * @author windy
     * @param bool $status
     * @return array|mixed|string
     */
    public static function getLogDesc($status = true)
    {
        $desc = [
            self::admin_add_money            => '系统增加余额',
            self::admin_reduce_money         => '系统扣减余额',
            self::recharge_money             => '用户充值余额',
            self::balance_pay_order          => '下单扣减余额',
            self::cancel_order_refund        => '取消订单退回',
            self::after_sale_refund          => '售后退回余额',
            self::distribution_inc_earnings  => '订单结算获得佣金',

            self::withdraw_reduce_earnings   => '提现扣减佣金',
            self::withdraw_rollback_earnings => '提现回滚佣金',
            self::withdraw_increase_money    => '提现增加余额',

        ];
        if ($status === true) {
            return $desc;
        }
        return $desc[$status] ?? '未知';
    }
}