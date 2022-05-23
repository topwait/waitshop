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

namespace app\admin\logic\addons;


use app\admin\logic\goods\GoodsLogic;
use app\common\basics\Logic;
use app\common\enum\OrderEnum;
use app\common\model\addons\Seckill;
use app\common\model\addons\SeckillSku;
use app\common\model\order\Order;
use Exception;
use think\facade\Db;

/**
 * 秒杀活动-逻辑层
 * Class SeckillLogic
 * @package app\admin\logic\addons
 */
class SeckillLogic extends Logic
{

    /**
     * 秒杀活动列表
     *
     * @author windy
     * @param array $get
     * @return array
     * @throws @\think\db\exception\DbException
     */
    public static function lists(array $get): array
    {
        self::setSearch([
            '='        => ['is_end'],
            '%like%'   => ['keyword@name'],
            'datetime' => ['datetime@create_time'],
        ]);

        $model = new Seckill();
        $lists = $model->field(true)
            ->where(['is_delete'=>0])
            ->where(self::$searchWhere)
            ->order(['is_end'=>'asc'])
            ->append(['original_price'])
            ->paginate([
                'page'      => $get['page'],
                'list_rows' => $get['limit'],
                'var_page'  => 'page',
            ])->toArray();

        foreach ($lists['data'] as &$item) {
            if ($item['is_end']) {
                $item['status'] = 0; //已结束
            } else {
                if ($item['end_time'] <= time()) {
                    Seckill::update(['is_end'=>1, 'update_time'=>time()], ['id'=>$item['id']]);
                    $item['is_end'] = 1;
                    $item['status'] = 0;
                }
                if ($item['start_time'] < time() && $item['end_time'] > time()) {
                    $item['status'] = 1; //进行中
                }
            }

            $item['start_time'] = date('Y-m-d H:i:s', $item['start_time']);
            $item['end_time'] = date('Y-m-d H:i:s', $item['end_time']);
        }

        return ['count'=>$lists['total'], 'list'=>$lists['data']];
    }

    /**
     * 秒杀活动详细
     *
     * @author windy
     * @param int $id
     * @return array
     */
    public static function detail(int $id): array
    {
        $model = new Seckill();
        $seckillDetail = $model->field(true)->with(['seckillSku'])->findOrEmpty($id)->toArray();
        $goodsDetail = GoodsLogic::detail($seckillDetail['goods_id']);

        foreach ($goodsDetail['sku'] as &$goodsSkuItem) {
            $goodsSkuItem['seckill_sku_id'] = 0;
            $goodsSkuItem['seckill_price'] = '';
            $goodsSkuItem['seckill_stock'] = '';
            $goodsSkuItem['sales_volume']   = 0;
            foreach ($seckillDetail['seckillSku'] as $seckillSkuItem) {
                if ($goodsSkuItem['id'] === $seckillSkuItem['sku_id']) {
                    $goodsSkuItem['seckill_sku_id']  = $seckillSkuItem['id'];
                    $goodsSkuItem['seckill_price']   = $seckillSkuItem['seckill_price'];
                    $goodsSkuItem['seckill_stock']   = $seckillSkuItem['seckill_stock'];
                    $goodsSkuItem['sales_volume']    = $seckillSkuItem['sales_volume'];
                }
            }
        }

        $seckillDetail['spec']       = $goodsDetail['spec'];
        $seckillDetail['seckillSku'] = $goodsDetail['sku'];
        $seckillDetail['activity_time'] =
            date('Y-m-d H:i:s', $seckillDetail['start_time']) .' - ' .
            date('Y-m-d H:i:s', $seckillDetail['end_time']);

        unset($seckillDetail['start_time']);
        unset($seckillDetail['end_time']);
        return $seckillDetail;
    }

    /**
     * 新增秒杀活动
     *
     * @author windy
     * @param array $post
     * @return bool
     */
    public static function add(array $post): bool
    {
        Db::startTrans();
        try {
            $datetime = explode(' - ', $post['activity_time']);
            if (strtotime($datetime[1])+(60 * 30) <= time()) {
                throw new Exception('结束时间必须比当前时间大30分钟');
            }

            $seckill = Seckill::create([
                'goods_id'        => $post['goods_id'],
                'name'            => $post['name'],
                'intro'           => $post['intro'],
                'image'           => $post['image'],
                'banner'          => $post['banner'],
                'min_buy'         => $post['min_buy'],
                'max_buy'         => $post['max_buy'],
                'min_seckill_price'  => min(array_column($post['sku'], 'seckill_price')),
                'max_seckill_price'  => max(array_column($post['sku'], 'seckill_price')),
                'total_stock'        => array_sum(array_column($post['sku'], 'seckill_stock')),
                'is_end'          => 0,
                'is_delete'       => 0,
                'is_coupon'       => $post['is_coupon'],
                'start_time'      => strtotime($datetime[0]),
                'end_time'        => strtotime($datetime[1]),
            ]);

            $seckillSku = [];
            foreach ($post['sku'] as $item) {
                $seckillSku[] = [
                    'seckill_id'    => $seckill['id'],
                    'goods_id'      => $post['goods_id'],
                    'sku_id'        => $item['sku_id'],
                    'seckill_price' => $item['seckill_price'],
                    'seckill_stock' => $item['seckill_stock'],
                    'sales_volume'  => 0,
                ];
            }

            (new SeckillSku())->saveAll($seckillSku);

            Db::commit();
            return true;
        } catch (Exception $e) {
            Db::rollback();
            static::$error = $e->getMessage();
            return false;
        }
    }

    /**
     * 编辑秒杀活动
     *
     * @author windy
     * @param array $post
     * @return bool
     */
    public static function edit(array $post): bool
    {
        try {
            $datetime = explode(' - ', $post['activity_time']);
            if (strtotime($datetime[1])+(60 * 30) <= time()) {
                throw new Exception('结束时间必须比当前时间大30分钟');
            }

            Seckill::update([
                'goods_id'        => $post['goods_id'],
                'name'            => $post['name'],
                'intro'           => $post['intro'],
                'image'           => $post['image'],
                'banner'          => $post['banner'],
                'min_buy'         => $post['min_buy'],
                'max_buy'         => $post['max_buy'],
                'min_seckill_price'  => min(array_column($post['sku'], 'seckill_price')),
                'max_seckill_price'  => max(array_column($post['sku'], 'seckill_price')),
                'total_stock'        => array_sum($post['seckill_stock']),
                'is_end'          => 0,
                'is_delete'       => 0,
                'is_coupon'       => $post['is_coupon'],
                'start_time'      => strtotime($datetime[0]),
                'end_time'        => strtotime($datetime[1]),
            ], ['id'=>$post['id']]);

            $oldSeckillSku  = [];
            $delSeckillSku  = [];
            $addSeckillSku  = [];
            $editSeckillSku = [];
            foreach ($post['sku'] as $item) {
                if (!$item['seckill_sku_id']) {
                    $addSeckillSku[] = [
                        'seckill_id'    => $post['id'],
                        'goods_id'      => $post['goods_id'],
                        'sku_id'        => $item['sku_id'],
                        'seckill_price' => $item['seckill_price'],
                        'seckill_stock' => $item['seckill_stock']
                    ];
                } else {
                    $oldSeckillSku[] = intval($item['seckill_sku_id']);
                    $editSeckillSku[] = [
                        'id'            => $item['seckill_sku_id'],
                        'seckill_id'    => $post['id'],
                        'goods_id'      => $post['goods_id'],
                        'sku_id'        => $item['sku_id'],
                        'seckill_price' => $item['seckill_price'],
                        'seckill_stock' => $item['seckill_stock']
                    ];
                }
            }

            if ($oldSeckillSku) {
                $seckillSkuIds = (new SeckillSku())->where(['seckill_id' => $post['id']])->column('id');
                $delSeckillSku = array_diff($seckillSkuIds, $oldSeckillSku);
            }

            if (!empty($addSeckillSku)) {
                (new SeckillSku())->saveAll($addSeckillSku);
            }

            if (!empty($editSeckillSku)) {
                (new SeckillSku())->saveAll($editSeckillSku);
            }

            if (!empty($delSeckillSku)) {
                (new SeckillSku())->whereIn('id', $delSeckillSku)->delete();
            }

            return true;
        } catch (Exception $e) {
            static::$error = $e->getMessage();
            return false;
        }
    }

    /**
     * 删除秒杀活动
     *
     * @author windy
     * @param int $id
     * @return bool
     */
    public static function del(int $id): bool
    {
        try {
            Seckill::update([
                'is_end'      => 1,
                'is_delete'   => 1,
                'delete_time' => time()
            ], ['id'=>intval($id)]);

            (new Order())::update([
                'order_type' => OrderEnum::STATUS_CLOSE,
                'update_time' => time()
            ], ['seckill_id'=>intval($id)]);

            return true;
        } catch (Exception $e) {
            static::$error = $e->getMessage();
            return false;
        }
    }

    /**
     * 结束秒杀活动
     *
     * @author windy
     * @param int $id
     * @return bool
     */
    public static function end(int $id): bool
    {
        try {
            Seckill::update([
                'is_end'      => 1,
                'update_time' => time()
            ], ['id'=>intval($id)]);

            (new Order())::update([
                'order_type' => OrderEnum::STATUS_CLOSE,
                'update_time' => time()
            ], ['seckill_id'=>intval($id)]);

            return true;
        } catch (Exception $e) {
            static::$error = $e->getMessage();
            return false;
        }
    }
}