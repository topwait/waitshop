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
 * 运费模板枚举类
 * Class FreightEnum
 * @package app\common\enum
 */
class FreightEnum
{
    const PIECE   = 10;  // 按件数
    const WEIGHT  = 20;  // 按重量
    const VOLUME  = 30;  // 按体积

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
            self::PIECE   => '按件数',
            self::WEIGHT  => '按重量',
            self::VOLUME  => '按体积'
        ];
        if ($status === true) {
            return $desc;
        }
        return $desc[$status] ?? '未知';
    }
}