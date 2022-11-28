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
 * 广告枚举类
 * Class AdEnum
 * @package app\common\enum
 */
class AdEnum
{
    const POS_CATEGORY  = 10;  // 分类页
    const POS_USER      = 11;  // 用户页
    const POST_CART     = 12;  // 购物车

    const LINK_TYPE_PAGE = 10; // 普通跳转页面
    const LINK_TYPE_WEB  = 20; // webView跳转
    const LINK_TYPE_BTN  = 30; // 按钮跳转
    const LINK_TYPE_MNP  = 40; // 小程序跳转

    /**
     * 获取广告位置描述
     *
     * @author windy
     * @param bool $status
     * @return string|string[]
     */
    public static function getPositionDesc($status = true)
    {
        $desc = [
            self::POS_CATEGORY  => '分类页',
            self::POS_USER      => '用户页',
            self::POST_CART     => '购物车',
        ];
        if ($status === true) {
            return $desc;
        }
        return $desc[$status] ?? '未知';
    }

    /**
     * 获取跳转类型
     *
     * @param bool $status
     * @return string|string[]
     */
    public static function getLinkTypeDesc($status = true)
    {
        $desc = [
            self::LINK_TYPE_PAGE => '页面跳转',
            self::LINK_TYPE_WEB  => 'webView跳转',
            self::LINK_TYPE_BTN  => '按钮跳转',
            self::LINK_TYPE_MNP  => '小程序跳转',
        ];
        if ($status === true) {
            return $desc;
        }
        return $desc[$status] ?? '未知';
    }
}