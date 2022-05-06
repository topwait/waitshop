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

namespace app\api\service;


use app\common\model\addons\DistributionExtend;
use app\common\model\addons\DistributionGoods;
use app\common\model\addons\DistributionOrder;
use app\common\model\order\OrderGoods;

/**
 * 分销服务类
 * Class DistributionServer
 * @package app\api\service
 */
class DistributionServer
{
    /**
     * 分销佣金结算(目前只分两级)
     *
     * @author windy
     * @param int $orderId (订单ID)
     * @return bool
     */
    public static function commission(int $orderId): bool
    {
        $orderGoods = (new OrderGoods())->alias('OG')
            ->field(['OG.id,O.order_sn,OG.goods_id,OG.sku_id,OG.actual_price,U.id as uid,U.first_leader,U.second_leader'])
            ->join('order O', 'O.id=OG.order_id')
            ->join('user U', 'U.id=O.user_id')
            ->where(['OG.order_id'=>$orderId])
            ->select()
            ->toArray();


        $distributionGoodsModel = new DistributionGoods();
        foreach ($orderGoods as $goods) {
            $ratio = $distributionGoodsModel->where(['goods_id'=>$goods['goods_id'], 'sku_id'=>$goods['sku_id']])->findOrEmpty()->toArray();
            if (!$ratio) {
                return true;
            }
            // 一级分佣
            if ($ratio['first_ratio'] > 0 and $ratio['first_ratio'] <= 100 and $goods['first_leader']) {
                $value = $ratio['first_ratio'] / 100;
                $money = $goods['actual_price'] * $value;
                if ($money < 0.01) {
                    continue;
                }
                DistributionOrder::create([
                    'distribution_sn' => make_order_no(),
                    'order_goods_id'  => $goods['id'],
                    'order_id'        => $orderId,
                    'user_id'         => $goods['first_leader'],
                    'goods_id'        => $goods['goods_id'],
                    'sku_id'          => $goods['sku_id'],
                    'reality_amount'  => $goods['actual_price'],
                    'earnings_money'  => $money,
                    'earnings_ratio'  => $ratio['first_ratio'],
                    'status'          => 1,
                    'create_time'     => time(),
                    'update_time'     => time()
                ]);
                DistributionExtend::update([
                    'distribution_order_num'   => ['inc', 1],
                    'distribution_order_money' => ['inc', $money],
                    'update_time' => time()
                ], ['user_id'=>$goods['first_leader']]);
            }

            // 二级分佣
            if ($ratio['second_ratio'] > 0 and $ratio['second_ratio'] <= 100 and $goods['second_leader']) {
                $value = $ratio['second_ratio'] / 100;
                $money = $goods['actual_price'] * $value;
                if ($money < 0.01) {
                    continue;
                }
                DistributionOrder::create([
                    'distribution_sn' => make_order_no(),
                    'order_goods_id'  => $goods['second_leader'],
                    'order_id'        => $orderId,
                    'user_id'         => $goods['uid'],
                    'goods_id'        => $goods['goods_id'],
                    'sku_id'          => $goods['sku_id'],
                    'reality_amount'  => $goods['actual_price'],
                    'earnings_money'  => $money,
                    'earnings_ratio'  => $ratio['second_ratio'],
                    'status'          => 1,
                    'create_time'     => time(),
                    'update_time'     => time()
                ]);
                DistributionExtend::update([
                    'distribution_order_num'   => ['inc', 1],
                    'distribution_order_money' => ['inc', $money],
                    'update_time' => time()
                ], ['user_id'=>$goods['second_leader']]);
            }
        }

        return true;
    }
}