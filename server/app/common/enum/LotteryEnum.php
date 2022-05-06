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
 * 幸运抽奖枚举类
 * Class LotteryEnum
 * @package app\common\enum
 */
class LotteryEnum
{
    const NOT_PRIZE   = 1;  //谢谢惠顾
    const IN_INTEGRAL = 2;  //赠送积分
    const IN_GROWTH   = 3;  //赠成长值
    const IN_GOODS    = 4;  //赠送实物

    /**
     * 获取枚举对应文本
     *
     * @author windy
     * @param $status
     * @return string|array
     */
    public static function getEnumName($status = true)
    {
        $desc = [
            self::NOT_PRIZE   => '谢谢惠顾',
            self::IN_INTEGRAL => '赠送积分',
            self::IN_GROWTH   => '赠成长值',
            self::IN_GOODS    => '赠送实物'
        ];
        if ($status === true) {
            return $desc;
        }
        return $desc[$status] ?? '未知';
    }
}