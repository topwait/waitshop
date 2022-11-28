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

namespace app\api\service;


use app\common\enum\CouponEnum;
use app\common\model\addons\CouponList;
use think\Exception;

/**
 * 优惠券服务类
 * Class CouponService
 * @package app\api\service
 */
class CouponService
{
    /**
     * 验证优惠券是否可用
     * 用于下单时验证优惠券是否可用
     *
     * @author windy
     * @param array $orderStatus (订单状态数组)
     * @param int $coupon_list_id (领取的优惠券ID)
     * @return array|bool
     * @throws \Exception
     */
    public static function isUsable(array $orderStatus, int $coupon_list_id)
    {
        try {
            $model = new CouponList();
            $coupon = $model->field(true)->alias('cl')
                ->withJoin(['coupon'])
                ->where('coupon_list_id', '=', (int)$coupon_list_id)
                ->findOrEmpty()->toArray();

            if (!$coupon)             throw new \Exception('优惠券不存在');
            if ($coupon['is_use'])    throw new \Exception('优惠券已被使用');
            if ($coupon['is_expire']) throw new \Exception('优惠券已失效');

            $totalPrice = 0;
            switch ($coupon['coupon']['use_goods_type']) {
                case CouponEnum::USE_GOODS_TYPE_ALL:  // 全部商品可用
                    $totalPrice = $orderStatus['totalAmount'];
                    break;
                case CouponEnum::USE_GOODS_TYPE_PART: // 部分商品可用
                    foreach ($orderStatus['pStatusArray'] as $product) {
                        if (in_array($product['id'], $coupon['coupon']['use_goods_ids'])) {
                            $totalPrice += $product['totalPrice'];
                        }
                    }
                    break;
                case CouponEnum::USE_GOODS_TYPE_BAN:  // 部分商品不可用
                    foreach ($orderStatus['pStatusArray'] as $product) {
                        if (!in_array($product['id'], $coupon['coupon']['use_goods_ids'])) {
                            $totalPrice += $product['totalPrice'];
                        }
                    }
                    break;
            }

            if ($coupon['coupon']['min_price'] <= 0 or $totalPrice >= $coupon['coupon']['min_price']) {
                $discountAmount = $coupon['coupon']['reduce_price'];
                if ($discountAmount == 20) { //折扣券
                    $discountAmount = convert_to_price($orderStatus['totalAmount'] * $coupon['coupon']['discount']);
                }
                return [
                    'isUsable'       => true,
                    'totalPrice'     => $totalPrice,
                    'name'           => $coupon['coupon']['name'],
                    'type'           => $coupon['coupon']['type'],
                    'minPrice'       => $coupon['coupon']['min_price'],
                    'discountAmount' => $discountAmount
                ];
            } else {
                return [
                    'isUsable'       => false,
                    'totalPrice'     => $totalPrice,
                    'name'           => $coupon['coupon']['name'],
                    'type'           => $coupon['coupon']['type'],
                    'minPrice'       => $coupon['coupon']['min_price'],
                    'discountAmount' => 0
                ];
            }

        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }

    /**
     * 关闭过期失效的优惠券
     *
     * @author windy
     * return bool
     */
    public static function closeCoupon(): bool
    {
        try {
            $time = time();
            $model = new CouponList();
            $model->field(true)
                ->where([
                    ['is_use', '=', CouponEnum::IS_USE_NOT],
                    ['is_expire', '=', CouponEnum::IS_EXPIRE_NORMAL],
                    ['expire_time', '<=', $time]
                ])->update([
                    'is_expire'   => CouponEnum::IS_EXPIRE_INVALID,
                    'update_time' => $time
                ]);

            return true;
        } catch (\Exception $e) {
            return false;
        }
    }
}