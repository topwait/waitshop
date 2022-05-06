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
 * 分销枚举类
 * Class DistributionEnum
 * @package app\common\enum
 */
class DistributionEnum
{
    /**
     * 分销订单结算状态
     */
    const STATUS_WAIT    = 1; //待结算
    const STATUS_SUCCESS = 2; //已结算
    const STATUS_INVALID = 3; //已失效

    /**
     * 分销订单结算状态文本
     *
     * @author windy
     * @param bool $status
     * @return string|string[]
     */
    public static function getSettleStatus($status = true)
    {
        $desc = [
            self::STATUS_WAIT    => '待结算',
            self::STATUS_SUCCESS => '已结算',
            self::STATUS_INVALID => '已失效',
        ];
        if ($status === true) {
            return $desc;
        }
        return $desc[$status] ?? '未知';
    }
}