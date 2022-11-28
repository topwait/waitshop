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
 * 客户端枚举类
 * Class ClientEnum
 * @package app\common\enum
 */
class ClientEnum
{
    const ANDROID = 1;  // 安卓端
    const IOS     = 2;  // 苹果端
    const PC      = 3;  // 电脑端
    const H5      = 4;  // H5(非微信)
    const OA      = 5;  // 微信公众号
    const MNP     = 6;  // 微信小程序

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
            self::ANDROID => '安卓',
            self::IOS     => '苹果',
            self::PC      => '电脑',
            self::H5      => 'H5',
            self::OA      => '公众号',
            self::MNP     => '微信小程序',
        ];
        if ($status === true) {
            return $desc;
        }
        return $desc[$status] ?? '未知';
    }
}