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
    const admin_add_growth    = 100; //系统增加成长值
    const admin_reduce_growth = 101; //系统扣减成长值
    const recharge_inc_growth = 102; //充值赠送成长值
    const register_inc_growth = 103; //注册赠送成长值
    const sign_inc_growth     = 104; //签到奖励成长值

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
            self::admin_add_growth     => '系统增加成长值',
            self::admin_reduce_growth  => '系统扣减成长值',
            self::recharge_inc_growth  => '充值赠送成长值',
            self::register_inc_growth  => '注册赠送成长值',
            self::sign_inc_growth      => '签到奖励成长值',
        ];
        if ($status === true) {
            return $desc;
        }
        return $desc[$status] ?? '未知';
    }
}