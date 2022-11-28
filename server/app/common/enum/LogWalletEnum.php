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
 * 钱包流水日志枚举类
 * Class LogWalletEnum
 * @package app\common\enum
 */
class LogWalletEnum
{
    // 日志枚举类型
    const ADMIN_INC_MONEY            = 100; //系统增加余额
    const ADMIN_DEC_MONEY            = 101; //系统扣减余额
    const RECHARGE_MONEY             = 102; //用户充值余额
    const PAY_DEC_MONEY              = 103; //支付扣减余额
    const CANCEL_ORDER_REFUND        = 104; //取消订单退回余额
    const AFTER_SALE_REFUND          = 105; //售后订单退回余额
    const DISTRIBUTION_INC_EARNINGS  = 106; //分销订单结算增加佣金
    const WITHDRAW_DEC_EARNINGS      = 107; //提现扣减佣金(申请中)
    const WITHDRAW_ROLLBACK_EARNINGS = 108; //提现回滚佣金(失败了)
    const WITHDRAW_INCREASE_EARNINGS = 109; //提现增加余额(成功了)
    const INVITE_INC_EARNINGS        = 110; //邀请用户增加佣金

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
            self::ADMIN_INC_MONEY            => '系统增加余额',
            self::ADMIN_DEC_MONEY            => '系统扣减余额',
            self::RECHARGE_MONEY             => '用户充值余额',
            self::PAY_DEC_MONEY              => '下单扣减余额',
            self::CANCEL_ORDER_REFUND        => '取消订单退回',
            self::AFTER_SALE_REFUND          => '售后退回余额',
            self::DISTRIBUTION_INC_EARNINGS  => '订单结算佣金',

            self::WITHDRAW_DEC_EARNINGS      => '提现扣减佣金',
            self::WITHDRAW_ROLLBACK_EARNINGS => '提现回滚佣金',
            self::WITHDRAW_INCREASE_EARNINGS => '提现增加余额',
            self::INVITE_INC_EARNINGS        => '邀请用户增加佣金',

        ];
        if ($status === true) {
            return $desc;
        }
        return $desc[$status] ?? '未知';
    }
}