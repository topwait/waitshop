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

namespace app\api\logic\addons;


use app\common\basics\Logic;
use app\common\model\addons\Seckill;
use app\common\model\addons\SeckillSku;
use app\common\model\goods\GoodsCollect;
use app\common\utils\UrlUtils;
use Exception;

class SeckillLogic extends Logic
{
    /**
     * 秒杀商品列表
     *
     * @author windy
     * @param array $get (请求参数)
     * @return mixed
     */
    public static function lists(array $get): array
    {
        $lists = (new Seckill())->field([
                'S.id,S.goods_id,S.name,S.image,S.min_seckill_price',
                'G.max_price as cost_price,S.total_stock,S.sales_volume',
                'S.start_time,S.end_time'
            ])
            ->alias('S')
            ->join('goods G', 'G.id = S.goods_id')
            ->where([
                ['S.is_end', '=', 0],
                ['S.is_delete', '=', 0],
                ['S.end_time', '>=', time()]
            ])
            ->paginate([
                'page'      => $get['page'] ?? 1,
                'list_rows' => $get['limit'] ?? 20,
                'var_page'  => 'page'
            ])->toArray();

        foreach ($lists['data'] as &$item) {
            $item['is_start'] = $item['start_time'] <= time() ? true : false;
            $item['progress'] = intval($item['sales_volume'] ? $item['sales_volume'] / $item['total_stock'] * 100 : 0);
        }

        return $lists;
    }

    /**
     * 秒杀商品详细
     *
     * @author windy
     * @param int $id (主键)
     * @param int $userId (用户ID)
     * @return array|bool
     */
    public static function detail(int $id, int $userId)
    {
        // 获取秒杀商品信息
        $detail = (new Seckill())->alias('S')
            ->field(['S.id,S.goods_id,S.name,S.image,S.banner,G.content,S.min_buy,
                S.max_buy,S.min_seckill_price,S.total_stock,S.is_end,S.start_time,S.end_time'])
            ->with(['spec.specValue'])
            ->where(['S.id'=>$id])
            ->join('goods G', 'G.id=S.goods_id')
            ->withAttr(['is_collect'=>function($value, $data) use ($userId){
                unset($value);
                return (new GoodsCollect())->where([
                    'user_id'  => $userId,
                    'goods_id' => $data['id']
                ])->findOrEmpty()->toArray() ? true : false;
            }])->append(['is_collect'])
            ->findOrEmpty()
            ->toArray();

        if (!$detail) {
            static::$error = "秒杀商品不存在";
            return false;
        }

        // 获取秒杀商品SKU
        $sku = (new SeckillSku())->alias('SS')
            ->field([
                'GS.id,GS.goods_id,GS.spec_value_ids,GS.spec_value_str',
                'GS.image,GS.sell_price as cost_price,SS.seckill_price as sell_price',
                'SS.seckill_stock as stock,SS.sales_volume'
            ])
            ->join('goods_sku GS', 'GS.id=SS.sku_id')
            ->where(['seckill_id'=>$id])
            ->select()
            ->toArray();

        // 获取要显示的规格
        $specValueArr = [];
        $specValueIds = array_column($sku, 'spec_value_ids');
        foreach ($specValueIds as $item) {
            foreach (explode(",", $item) as $i) {
                $specValueArr[] = intval($i);
            }
            $specValueArr = array_unique($specValueArr);
        }

        // 过滤不显示的规格
        foreach ($detail['spec'] as &$spec) {
            $specValue = [];
            foreach ($spec['specValue'] as $item) {
                if (in_array($item['id'], $specValueArr)) {
                    $specValue[] = $item;
                }
            }
            if (empty($specValue)) {
                unset($spec);
            } else {
                $spec['specValue'] = $specValue;
            }
        }

        // 处理秒杀时间
        $detail['surplus_end_time']   = $detail['end_time']   > time() ? $detail['end_time'] - time() : 0;
        $detail['surplus_start_time'] = $detail['start_time'] > time() ? $detail['start_time'] - time() : 0;

        // 处理内容图片
        $domain  = UrlUtils::getStorageUrl() . '/';
        $pattern = '/(<img .*?src=")(.*?)(.*?>)/is';
        $detail['content'] = preg_replace($pattern, "\${1}$domain\${2}\${3}", $detail['content']);

        // 返回结果
        $detail['sku'] = $sku;
        return $detail;
    }

    /**
     * 验证秒杀商品
     *
     * @author windy
     * @param int $seckillId
     * @param array $orderGoods
     * @throws Exception
     */
    public static function checkSeckillStatus(int $seckillId, array $orderGoods)
    {
        $seckill = (new SeckillSku())->alias('SK')
            ->field('SK.*,S.name,S.start_time,S.end_time,S.is_end,S.is_delete')
            ->join('seckill S', 'S.id=SK.seckill_id')
            ->where([
                ['SK.sku_id', '=', $orderGoods['sku_id']],
                ['SK.goods_id', '=', $orderGoods['goods_id']],
                ['SK.seckill_id', '=', $seckillId]
            ])->findOrEmpty()->toArray();

        if (!$seckill || $seckill['is_delete'] || $seckill['is_end'] || $seckill['end_time'] <= time()) {
            throw new Exception('秒杀活动结束,下次再来吧');
        }

        if ($seckill['start_time'] > time()) {
            throw new Exception('秒杀活动尚未开始,敬请期待');
        }

        if ($seckill['seckill_stock'] - $orderGoods['count'] < 0) {
            throw new Exception('很抱歉,库存不足');
        }
    }
}