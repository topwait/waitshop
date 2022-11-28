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


use app\common\model\user\User;
use app\common\utils\ConfigUtils;

/**
 * 积分服务类
 * Class IntegralService
 * @package app\common\service
 */
class IntegralService
{
    /**
     * 计算是否可用积分抵扣
     *
     * @author windy
     * @param array $orderStatus
     * @param int $userId
     * @return array
     */
    public static function isUsable(array $orderStatus, int $userId): array
    {
        // 1、计算能积分抵扣商品的总金额
        $totalPrice = 0;
        foreach ($orderStatus['pStatusArray'] as $product) {
           if ($product['isIntegral']) {
                $totalPrice += $product['actualPrice'];
           }
        }

        // 2、获取积分相关配置信息
        $integralConfig = ConfigUtils::get('integral', [
            'is_shopping_gift'     => 0,  //是否开启购物送积分[1=开启, 0=关闭]
            'is_shopping_discount' => 0,  //是否开启下单积分抵扣[1=开启, 0=关闭]
            'discount_ratio'       => 0,  //积分抵扣比例(1个积分可抵扣多少元)
            'full_order_price'     => 0,  //订单满多少元允许积分抵扣
            'full_integral_num'    => 0,  //积分使用需超过多少
            'max_money_ratio'      => 0   //订单最高可抵扣金额
        ]);

        // 3、当前是否允许积分抵扣
        if ($integralConfig['is_shopping_discount']
            and ($totalPrice >= $integralConfig['full_order_price'] || $integralConfig['full_order_price'] == 0)
            and ($totalPrice >= $integralConfig['full_integral_num'] || $integralConfig['full_integral_num'] == 0)
        ) {
            // 用户拥有积分
            $userIntegral = (new User())->where(['id'=>$userId])->value('integral') ?? 0;
            // 最大抵扣金额
            $maxAmount    = $totalPrice * ($integralConfig['max_money_ratio'] / 100);
            // 最大允许使用积分
            $maxIntegral  = intval($maxAmount / $integralConfig['discount_ratio']);

            if (!$userIntegral || !$maxAmount || !$maxIntegral) {
                return [
                    'isUsable'       => false,
                    'totalPrice'     => $totalPrice,
                    'useIntegral'    => 0,
                    'discountAmount' => 0
                ];
            }

            $discountAmount = ($userIntegral >= $maxIntegral) ? $maxAmount : $userIntegral * $integralConfig['discount_ratio'];
            $useIntegral = $userIntegral <= $maxIntegral ? $userIntegral : $maxIntegral;
            return [
                'isUsable'       => true,           //是否允许积分抵扣
                'totalPrice'     => $totalPrice,    //订单总价
                'useIntegral'    => $useIntegral,   //抵扣的积分
                'discountAmount' => $discountAmount //抵扣的金额
            ];
        }

        return [
            'isUsable'       => false,
            'totalPrice'     => $totalPrice,
            'useIntegral'    => 0,
            'discountAmount' => 0
        ];
    }
}