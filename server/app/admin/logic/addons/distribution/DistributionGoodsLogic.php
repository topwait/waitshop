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

namespace app\admin\logic\addons\distribution;


use app\admin\logic\goods\GoodsLogic;
use app\common\basics\Logic;
use app\common\model\addons\DistributionGoods;
use app\common\model\goods\Goods;
use Exception;
use think\facade\Db;

/**
 * 分销商品-逻辑层
 * Class DistributionGoodsLogic
 * @package app\admin\logic\addons\distribution
 */
class DistributionGoodsLogic extends Logic
{
    /**
     * 分销商品列表
     *
     * @author windy
     * @param array $get
     * @return array
     * @throws @\think\db\exception\DbException
     */
    public static function lists(array $get): array
    {
        self::setSearch([
            '='       => ['category@first_category_id|second_category_id|third_category_id'],
            '%like%'  => ['name']
        ]);

        $lists = (new Goods())
            ->field(['id,name,image,max_price,min_price,sales_volume'])
            ->where(['is_delete'=>0, 'is_distribution'=>1])
            ->where(self::$searchWhere)
            ->paginate([
                'page'      => $get['page'],
                'list_rows' => $get['limit'],
                'var_page'  => 'page',
            ])->toArray();

        foreach ($lists['data'] as &$item) {
            if ($item['min_price'] != $item['max_price']) {
                $item['original_price'] = $item['min_price'] . ' ~ ' . $item['max_price'];
            } else {
                $item['original_price'] = $item['min_price'];
            }

            unset($item['min_price']);
            unset($item['max_price']);
        }

        return ['count'=>$lists['total'], 'list'=>$lists['data']];
    }

    /**
     * 获取秒杀活动详细
     *
     * @author windy
     * @param int $goodsId
     * @return array
     * @throws @\think\db\exception\DataNotFoundException
     * @throws @\think\db\exception\DbException
     * @throws @\think\db\exception\ModelNotFoundException
     */
    public static function detail(int $goodsId): array
    {
        $disGoodsSku = (new DistributionGoods())->field(true)->where(['goods_id'=>$goodsId])->select()->toArray();
        $goodsDetail = GoodsLogic::detail($goodsId);

        foreach ($goodsDetail['sku'] as &$goodsSkuItem) {
            $goodsSkuItem['dis_sku_id']   = 0;
            $goodsSkuItem['first_ratio']  = '';
            $goodsSkuItem['second_ratio'] = '';
            foreach ($disGoodsSku as $skuItem) {
                if ($goodsSkuItem['id'] === $skuItem['sku_id']) {
                    $goodsSkuItem['dis_sku_id']    = $skuItem['id'];
                    $goodsSkuItem['first_ratio']   = $skuItem['first_ratio'];
                    $goodsSkuItem['second_ratio']  = $skuItem['second_ratio'];
                }
            }
        }

        return $goodsDetail;
    }

    /**
     * 新增分销商品
     *
     * @author windy
     * @param array $post
     * @return bool
     */
    public static function add(array $post): bool
    {
        Db::startTrans();
        try {
            $goods = (new DistributionGoods())->where(['goods_id'=>intval($post['goods_id'])])->findOrEmpty();
            if (!$goods->isEmpty()) {
                throw new Exception('分销商品已存在,请勿重复添加');
            }

            $sku = [];
            foreach ($post['sku'] as $item) {
                $sku[] = [
                    'goods_id'     => $post['goods_id'],
                    'sku_id'       => $item['sku_id'],
                    'first_ratio'  => $item['first_ratio'],
                    'second_ratio' => $item['second_ratio'],
                    'create_time'  => time(),
                    'update_time'  => time()
                ];
            }

            (new DistributionGoods())->saveAll($sku);

            Goods::update([
                'is_distribution' => 1,
                'update_time'     => time()
            ], ['id'=>intval($post['goods_id'])]);

            Db::commit();
            return true;
        } catch (Exception $e) {
            Db::rollback();
            static::$error = $e->getMessage();
            return false;
        }
    }

    /**
     * 编辑分销商品
     *
     * @author windy
     * @param array $post
     * @return bool
     */
    public static function edit(array $post): bool
    {
        Db::startTrans();
        try {
            $oldSku  = [];
            $delSku  = [];
            $addSku  = [];
            $editSku = [];
            foreach ($post['sku'] as $item) {
                if (!$item['dis_sku_id']) {
                    $addSku[] = [
                        'goods_id'     => $post['goods_id'],
                        'sku_id'       => $item['sku_id'],
                        'first_ratio'  => $item['first_ratio'],
                        'second_ratio' => $item['second_ratio']
                    ];
                } else {
                    $oldSku[] = intval($item['dis_sku_id']);
                    $editSku[] = [
                        'id'           => $item['dis_sku_id'],
                        'goods_id'     => $post['goods_id'],
                        'sku_id'       => $item['sku_id'],
                        'first_ratio'  => $item['first_ratio'],
                        'second_ratio' => $item['second_ratio']
                    ];
                }
            }

            if ($oldSku) {
                $goodsSkuIds = (new DistributionGoods())->where(['goods_id' => intval($post['id'])])->column('id');
                $delSku = array_diff($goodsSkuIds, $oldSku);
            }

            if (!empty($addSku)) {
                (new DistributionGoods())->saveAll($addSku);
            }

            if (!empty($editSku)) {
                (new DistributionGoods())->saveAll($editSku);
            }

            if (!empty($delSku)) {
                (new DistributionGoods())->whereIn('id', $delSku)->delete();
            }

            Db::commit();
            return true;
        } catch (Exception $e) {
            Db::rollback();
            static::$error = $e->getMessage();
            return false;
        }
    }

    /**
     * 删除分销商品
     *
     * @author windy
     * @param int $id (商品ID)
     * @return bool
     */
    public static function del(int $id): bool
    {
        Db::startTrans();
        try {
            Goods::update([
                'is_distribution' => 0,
                'update_time'     => time()
            ], ['id'=>intval($id)]);

            (new DistributionGoods())
                ->where(['goods_id'=>intval($id)])
                ->delete();

            Db::commit();
            return true;
        } catch (Exception $e) {
            Db::rollback();
            static::$error = $e->getMessage();
            return false;
        }
    }

}