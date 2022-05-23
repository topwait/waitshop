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

namespace app\admin\logic\setting;


use app\common\basics\Logic;
use app\common\utils\ConfigUtils;
use Exception;

/**
 * 交易设置配置-逻辑层
 * Class tradeLogic
 * @package app\admin\logic\set
 */
class TradeLogic extends Logic
{
    /**
     * 获取交易配置
     *
     * @author windy
     * @return array
     */
    public static function detail(): array
    {
        return ConfigUtils::get('trade', [
            // 配送方式: 1=快递发货,2=上门自提,3=无需快递,4=同城配送
            'delivery_method'           => [1],
            // 库存扣除方式: order=下单扣库存,payment=支付扣库存
            'stock_deduct_method'       => 'order',
            // 订单自动取消时长
            'automatic_cancel_duration' => 0,
            // 订单允许取消时长
            'customer_cancel_duration'  => 0,
            // 订单售后退款时长
            'after_sale_duration'       => 0,
            // 订单自动完成时长
            'automatic_finish_duration' => 0,
        ]);
    }

    /**
     * 设置交易配置
     *
     * @author windy
     * @param array $post
     * @return bool
     */
    public static function set(array $post): bool
    {
        try {
            $deliveryMethod = [];
            foreach ($post['delivery_method']??['1'] as $item) {
                $deliveryMethod[] = intval($item);
            }

            ConfigUtils::set('trade', [
                // 配送方式: 1=快递发货,2=上门自提,3=无需快递,4=同城配送
                'delivery_method'           => $deliveryMethod,
                // 库存扣除方式: order=下单扣库存,payment=支付扣库存
                'stock_deduct_method'       => $post['stock_deduct_method'] ?? 'order',
                // 订单自动取消时长
                'automatic_cancel_duration' => $post['automatic_cancel_duration'] ?? 0,
                // 订单允许取消时长
                'customer_cancel_duration'  => $post['customer_cancel_duration'] ?? 0,
                // 订单售后退款时长
                'after_sale_duration'       => $post['after_sale_duration'] ?? 0,
                // 订单自动完成时长
                'automatic_finish_duration' => $post['automatic_finish_duration'] ?? 0,
            ]);

            return true;
        } catch (Exception $e) {
            static::$error = $e->getMessage();
            return false;
        }
    }
}