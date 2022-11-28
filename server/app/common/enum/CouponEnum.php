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
 * 优惠券枚举类
 * Class CouponEnum
 * @package app\common\enum
 */
class CouponEnum
{
    const FULL_REDUCE_TYPE = 10;  // 满减券
    const DISCOUNT_TYPE    = 20;  // 折扣券

    const USE_GOODS_TYPE_ALL  = 0; // 全部商品可用
    const USE_GOODS_TYPE_PART = 1; // 部分商品可用
    const USE_GOODS_TYPE_BAN  = 2; // 限制不可用商品

    const GET_METHOD_DIRECT   = 1; // 直接领取
    const GET_METHOD_APPOINT  = 2; // 指定发放

    const NOT_PUBLISH_STATUS   = 0; // 未发布状态
    const OK_PUBLISH_STATUS    = 1; // 已发布状态
    const CLOSE_PUBLISH_STATUS = 2; // 已关闭发布状态

    const IS_EXPIRE_NORMAL   = 0; // 正常
    const IS_EXPIRE_INVALID  = 1; // 已失效

    const IS_USE_NOT = 0; // 未使用
    const IS_USE_OK  = 1; // 已使用

    /**
     * 获取优惠券类型
     *
     * @author windy
     * @param $status
     * @return string|array
     */
    public static function getTypeName($status = true)
    {
        $desc = [
            self::FULL_REDUCE_TYPE => '满减券',
            self::DISCOUNT_TYPE    => '折扣券'
        ];
        if ($status === true) {
            return $desc;
        }
        return $desc[$status] ?? '未知';
    }

    /**
     * 获取是否限制商品类型
     *
     * @author windy
     * @param bool $status
     * @return string|string[]
     */
    public static function getUseGoodsTypeDesc($status = true)
    {
        $desc = [
            self::USE_GOODS_TYPE_ALL   => '全场通用',
            self::USE_GOODS_TYPE_PART  => '指定商品可用',
            self::USE_GOODS_TYPE_BAN   => '指定商品不可用'
        ];
        if ($status === true) {
            return $desc;
        }
        return $desc[$status] ?? '未知';
    }

    /**
     * 获取方式
     *
     * @author windy
     * @param bool $status
     * @return string|string[]
     */
    public static function getMethodName($status = true)
    {
        $desc = [
            self::GET_METHOD_DIRECT   => '直接领取',
            self::GET_METHOD_APPOINT  => '指定发放',
        ];
        if ($status === true) {
            return $desc;
        }
        return $desc[$status] ?? '未知';
    }
}