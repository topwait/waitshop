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
 * 成长值枚举类
 * Class LogGrowthEnum
 * @package app\common\enum
 */
class LogGrowthEnum
{
    // 日志枚举类型
    const ADMIN_INC_GROWTH    = 100; //系统增加成长值
    const ADMIN_DEC_GROWTH    = 101; //系统扣减成长值
    const RECHARGE_INC_GROWTH = 102; //充值赠送成长值
    const REGISTER_INC_GROWTH = 103; //注册赠送成长值
    const SIGN_INC_GROWTH     = 104; //签到奖励成长值
    const PAY_INC_GROWTH      = 105; //下单奖励成长值

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
            self::ADMIN_INC_GROWTH     => '系统增加成长值',
            self::ADMIN_DEC_GROWTH     => '系统扣减成长值',
            self::RECHARGE_INC_GROWTH  => '充值赠送成长值',
            self::REGISTER_INC_GROWTH  => '注册赠送成长值',
            self::SIGN_INC_GROWTH      => '签到奖励成长值',
            self::PAY_INC_GROWTH       => '下单奖励成长值',
        ];
        if ($status === true) {
            return $desc;
        }
        return $desc[$status] ?? '未知';
    }
}