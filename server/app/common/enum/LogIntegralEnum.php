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
 * 积分枚举类
 * Class LogIntegralEnum
 * @package app\common\enum
 */
class LogIntegralEnum
{
    // 日志枚举类型
    const admin_add_integral    = 100; //系统增加积分
    const admin_reduce_integral = 101; //系统扣减积分
    const recharge_money_give   = 102; //充值余额赠送
    const balance_pay_order     = 103; //下单扣减积分
    const register_inc_integral = 104; //注册赠送积分
    const sign_inc_integral     = 105; //签到奖励积分
    const lottery_inc_integral  = 106; //抽奖赠送

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
            self::admin_add_integral     => '系统增加积分',
            self::admin_reduce_integral  => '系统扣减积分',
            self::recharge_money_give    => '充值余额赠送',
            self::balance_pay_order      => '下单扣减积分',
            self::register_inc_integral  => '注册赠送积分',
            self::sign_inc_integral      => '签到奖励积分',
            self::lottery_inc_integral   => '抽奖奖励积分',
        ];
        if ($status === true) {
            return $desc;
        }
        return $desc[$status] ?? '未知';
    }

}