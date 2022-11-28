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
 * 订单日志枚举类
 * Class OrderLogEnum
 * @package app\common\enum
 */
class OrderLogEnum
{
    // 类型
    const TYPE_SYSTEM = 1; //系统
    const TYPE_SHOP   = 2; //商家
    const TYPE_USER   = 3; //会员

    //订单动作
    const USER_ADD_ORDER        = 101; //提交订单
    const USER_CANCEL_ORDER     = 102; //取消订单
    const USER_CONFIRM_ORDER    = 103; //确认收货
    const USER_PAID_ORDER       = 104 ;//支付订单
    const USER_VERIFICATION     = 105; //会员核销订单

    const SHOP_CANCEL_ORDER     = 201; //商家取消订单
    const SHOP_DELIVERY_ORDER   = 202; //商家发货
    const SHOP_CONFIRM_ORDER    = 203; //商家确认收货
    const SHOP_ADDRESS_EDIT     = 204; //商家修改地址
    const SHOP_ORDER_REMARKS    = 205; //商家备注
    const SHOP_CHANGE_PRICE     = 206; //商家修改价格
    const SHOP_EXPRESS_PRICE    = 207; //商家修改运费
    const SHOP_VERIFICATION     = 208; //商家提货核销

    const SYSTEM_CANCEL_ORDER   = 301;//系统取消订单
    const SYSTEM_CONFIRM_ORDER  = 302;//系统确认订单

    /**
     * 订单日志枚举描述
     *
     * @author windy
     * @param $type
     * @return array|string
     */
    public static function getRecordDesc($type)
    {
        $desc = [
            //系统
            self::SYSTEM_CANCEL_ORDER   => '系统取消订单',
            self::SYSTEM_CONFIRM_ORDER  => '系统确认收货',

            //商家
            self::SHOP_CANCEL_ORDER     => '商家取消订单',
            self::SHOP_DELIVERY_ORDER   => '商家发货',
            self::SHOP_CONFIRM_ORDER    => '商家确认收货',
            self::SHOP_ADDRESS_EDIT     => '商家修改地址',
            self::SHOP_ORDER_REMARKS    => '商家备注',
            self::SHOP_CHANGE_PRICE     => '商家修改价格',
            self::SHOP_EXPRESS_PRICE    => '商家修改运费',
            self::SHOP_VERIFICATION     => '商家提货核销',

            //会员
            self::USER_ADD_ORDER        => '会员提交订单',
            self::USER_CANCEL_ORDER     => '会员取消订单',
            self::USER_CONFIRM_ORDER    => '会员确认收货',
            self::USER_PAID_ORDER       => '会员支付订单',
            self::USER_VERIFICATION     => '会员核销订单',
        ];

        if ($type === true){
            return $desc;
        }
        return $desc[$type] ?? '未知';
    }
}