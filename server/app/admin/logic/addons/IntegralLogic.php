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

namespace app\admin\logic\addons;


use app\common\basics\Logic;
use app\common\utils\ConfigUtils;
use Exception;

/**
 * 积分设置-逻辑层
 * Class IntegralLogic
 * @package app\admin\logic\addons
 */
class IntegralLogic extends Logic
{
    /**
     * 积分规格详细
     *
     * @author windy
     * @return mixed|null
     */
    public static function detail()
    {
        return ConfigUtils::get('integral', [
            'is_shopping_gift'     => 0,  //是否开启购物送积分[1=开启, 0=关闭]
            'is_shopping_discount' => 0,  //是否开启下单积分抵扣[1=开启, 0=关闭]
            'discount_ratio'       => 0,  //积分抵扣比例(1个积分可抵扣多少元)
            'full_order_price'     => 0,  //订单满多少元允许积分抵扣
            'full_integral_num'    => 0,  //用户积分需要满多少可用,0=不限制
            'max_money_ratio'      => 0   //订单最高可抵扣金额
        ]);
    }

    /**
     * 设置积分规格
     *
     * @author windy
     * @param array $post
     * @return bool
     */
    public static function set(array $post): bool
    {
        try {
            ConfigUtils::set('integral', [
                'is_shopping_gift'     => $post['is_shopping_gift'],      //是否开启购物送积分[1=开启, 0=关闭]
                'is_shopping_discount' => $post['is_shopping_discount'],  //是否开启下单积分抵扣[1=开启, 0=关闭]
                'discount_ratio'       => $post['discount_ratio'],        //积分抵扣比例(1个积分可抵扣多少元)
                'full_order_price'     => $post['full_order_price'],      //订单满多少元允许积分抵扣
                'full_integral_num'    => $post['full_integral_num'],     //用户积分需要满多少可用,0=不限制
                'max_money_ratio'      => $post['max_money_ratio']        //订单最高可抵扣金额
            ]);

            return true;
        } catch (Exception $e) {
            static::$error = $e->getMessage();
            return false;
        }
    }
}