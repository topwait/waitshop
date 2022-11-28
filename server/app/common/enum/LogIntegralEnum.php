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
 * 积分枚举类
 * Class LogIntegralEnum
 * @package app\common\enum
 */
class LogIntegralEnum
{
    // 日志枚举类型
    const ADMIN_INC_INTEGRAL    = 100; //系统增加积分
    const ADMIN_DEC_INTEGRAL    = 101; //系统扣减积分
    const RECHARGE_INC_INTEGRAL = 102; //充值余额赠送
    const PAY_INC_INTEGRAL      = 103; //下单赠送积分
    const PAY_DEC_INTEGRAL      = 104; //下单扣减积分
    const GOODS_INC_INTEGRAL    = 106; //商品赠送积分
    const REFUND_DEC_INTEGRAL   = 107; //退款退赠积分
    const REGISTER_INC_INTEGRAL = 108; //注册赠送积分
    const SIGN_INC_INTEGRAL     = 109; //签到奖励积分
    const LOTTERY_INC_INTEGRAL  = 110; //抽奖赠送积分
    const REFUND_INC_INTEGRAL   = 111; //退款回退积分
    const INVITE_INC_INTEGRAL   = 112; //邀请赠送积分

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
            self::ADMIN_INC_INTEGRAL     => '系统增加积分',
            self::ADMIN_DEC_INTEGRAL     => '系统扣减积分',
            self::RECHARGE_INC_INTEGRAL  => '充值余额赠送',
            self::PAY_INC_INTEGRAL       => '下单增加积分',
            self::PAY_DEC_INTEGRAL       => '下单扣减积分',
            self::GOODS_INC_INTEGRAL     => '商品赠送积分',
            self::REFUND_DEC_INTEGRAL    => '退款退赠积分',
            self::REGISTER_INC_INTEGRAL  => '注册赠送积分',
            self::SIGN_INC_INTEGRAL      => '签到奖励积分',
            self::LOTTERY_INC_INTEGRAL   => '抽奖奖励积分',
            self::REFUND_INC_INTEGRAL    => '退款回退积分',
            self::INVITE_INC_INTEGRAL    => '邀请赠送积分',
        ];
        if ($status === true) {
            return $desc;
        }
        return $desc[$status] ?? '未知';
    }

}