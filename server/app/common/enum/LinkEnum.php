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
 * 小程序菜单链接枚举
 * Class LinkEnum
 * @package app\common\enum
 */
class LinkEnum
{
    const ME_WALLET        = 10; // 我的钱包
    const ME_COLLECT       = 11; // 我的收藏
    const ME_SPREAD        = 12; // 我的推广
    const ME_COUPON        = 13; // 我的券券
    const ME_INTEGRAL      = 14; // 我的积分
    const ME_ADDRESS       = 15; // 收货地址
    const CUSTOMER_SERVICE = 16; // 联系客服
    const SHOP_ARTICLE     = 17; // 新闻咨询
    const GOODS_LISTS      = 18; // 商品列表
    const RECEIVE_CENTER   = 19; // 领券中心
    const GRADE_CENTER     = 20; // 等级中心
    const WITHDRAW_CENTER  = 21; // 提现中心
    const PROFIT_DETAIL    = 22; // 收益明细
    const EVERYDAY_SIGN    = 23; // 每日签到
    const LUCK_DRAW        = 24; // 幸运抽奖
    const WRITE_OFF        = 28; // 核销订单


    /**
     * 获取链接内容
     *
     * @author windy
     * @param bool $status
     * @return array|mixed|string
     */
    public static function getLink($status = true)
    {
        $desc = [
            self::ME_WALLET => [
                'name' => '我的钱包',
                'link' => '/bundle/pages/user_wallet/user_wallet?login=true',
            ],
            self::ME_COLLECT => [
                'name' => '我的收藏',
                'link' => '/bundle/pages/user_collect/user_collect?login=true',
            ],
            self::ME_SPREAD => [
                'name' => '我的推广',
                'link' => '/bundle/pages/distribution_index/distribution_index?login=true',
            ],
            self::ME_COUPON => [
                'name' => '我的券券',
                'link' => '/bundle/pages/coupon_my/coupon_my?login=true',
            ],
            self::ME_INTEGRAL => [
                'name' => '我的积分',
                'link' => '/bundle/pages/user_integral/user_integral?login=true',
            ],
            self::ME_ADDRESS => [
                'name' => '收货地址',
                'link' => '/pages/address/list/list?login=true',
            ],
            self::CUSTOMER_SERVICE => [
                'name' => '联系客服',
                'link' => '/bundle/pages/customer_service/customer_service',
            ],
            self::SHOP_ARTICLE => [
                'name' => '新闻咨询',
                'link' => '/bundle/pages/article_list/article_list',
            ],
            self::GOODS_LISTS => [
                'name' => '商品列表',
                'link' => '/pages/goods/list/list',
            ],
            self::RECEIVE_CENTER => [
                'name'  => '领券中心',
                'link'  => '/bundle/pages/coupon_receive/coupon_receive',
            ],
            self::GRADE_CENTER => [
                'name'  => '等级中心',
                'link'  => '/bundle/pages/user_grade/user_grade?login=true',
            ],
            self::WITHDRAW_CENTER => [
                'name'  => '提现中心',
                'link'  => '/bundle/pages/withdraw_apply/withdraw_apply?login=true',
            ],
            self::PROFIT_DETAIL => [
                'name'  => '收益明细',
                'link'  => '/bundle/pages/distribution_order/distribution_order?login=true',
            ],
            self::EVERYDAY_SIGN => [
                'name'  => '每日签到',
                'link'  => '/bundle/pages/user_sign/user_sign?login=true',
            ],
            self::LUCK_DRAW => [
                'name'  => '幸运抽奖',
                'link'  => '/bundle/pages/luckly_wheel/luckly_wheel?login=true',
            ],
            self::WRITE_OFF => [
                'name'  => '核销订单',
                'link'  => '/bundle/pages/writeoff_index/writeoff_index?login=true',
            ],
        ];

        if ($status === true) {
            return $desc;
        }
        return $desc[$status] ?? '未知';
    }


}