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

namespace app\admin\logic\goods;


use app\common\basics\Logic;
use app\common\model\goods\Goods;
use app\common\model\goods\GoodsSku;
use app\common\model\goods\GoodsSpec;
use app\common\model\goods\GoodsSpecValue;
use app\common\utils\ArrayUtils;
use app\common\utils\UrlUtils;
use think\facade\Db;
use Exception;

/**
 * 商品管理-逻辑层
 * Class GoodsLogic
 * @package app\admin\logic\goods
 */
class GoodsLogic extends Logic
{
    /**
     * 公共查询条件
     *
     * @author windy
     * @param $type
     * @return array
     */
    public static function queryWhere($type): array
    {
        $where = [];
        switch ($type) {
            case 0: // 所有
                $where = [];
                break;
            case 1: // 出售中
                $where[] = ['is_delete', '=', 0];
                $where[] = ['is_show', '=', 1];
                break;
            case 2: // 仓库中
                $where[] = ['is_delete', '=', 0];
                $where[] = ['is_show', '=', 0];
                break;
            case 3: // 已售罄
                $where[] = ['is_delete', '=', 0];
                $where[] = ['stock', '=', 0];
                break;
            case 4: // 已售罄
                $where[] = ['stock_warn', '>', 0];
                $where[] = ['stock', 'exp', Db::raw('<stock_warn')];
                break;
            case 5: // 回收站
                $where[] = ['is_delete', '=', 1];
                break;
        }
        return $where;
    }

    /**
     * 商品列表
     *
     * @author windy
     * @param array $get
     * @return array|bool
     * @throws @\think\db\exception\DbException
     */
    public static function lists(array $get)
    {
        // 搜索条件
        self::setSearch([
            '='       => ['category@first_category_id|second_category_id|third_category_id'],
            '%like%'  => ['name']
        ]);

        // 执行查询
        $model = new Goods();
        $lists = $model->field(true)
            ->where(self::$searchWhere)
            ->where(self::queryWhere($get['type']))
            ->order('id desc, sort desc')
            ->append(['category'])
            ->paginate([
                'page'      => $get['page'] ?? 1,
                'list_rows' => $get['limit'] ?? 20,
                'var_page'  => 'page'
            ])->toArray();

        return ['count'=>$lists['total'], 'list'=>$lists['data']];
    }

    /**
     * 商品统计数据
     *
     * @author windy
     * @return array
     */
    public static function statistics(): array
    {
        $model = new Goods();
        $onSale    = $model->where(self::queryWhere(1))->count();   //出售中
        $warehouse = $model->where(self::queryWhere(2))->count();   //仓库中
        $sellOut   = $model->where(self::queryWhere(3))->count();   //已售罄
        $alert     = $model->where(self::queryWhere(4))->count();   //警戒库存
        $recovery  = $model->where(self::queryWhere(5))->count();   //回收站

        return [
            'onSale'    => $onSale,
            'warehouse' => $warehouse,
            'sellOut'   => $sellOut,
            'alert'     => $alert,
            'recovery'  => $recovery
        ];
    }

    /**
     * 商品详细
     *
     * @author windy
     * @param int $id
     * @return array|bool
     */
    public static function detail(int $id)
    {
        try {
            $detail['base'] = (new Goods())->field(true)->where(['id'=>$id])->findOrEmpty()->toArray();
            $detail['sku']  = (new GoodsSku())->field(true)->where(['goods_id'=>$id])->select()->toArray();
            $detail['spec'] = (new GoodsSpec())->field(true)->where(['goods_id'=>$id])->select()->toArray();
            $spec_value = (new GoodsSpecValue())->field(true)->where(['goods_id'=>$id])->select()->toArray();
            // 处理规格值
            $data = [];
            foreach ($spec_value as $k => $v) {
                $data[$v['spec_id']][] = $v;
            }
            foreach ($detail['spec'] as $k => $v) {
                $detail['spec'][$k]['values'] = isset($data[$v['id']]) ? $data[$v['id']] : [];
            }
            return $detail;
        } catch (Exception $e) {
            static::$error = $e->getMessage();
            return false;
        }
    }

    /**
     * 新增商品
     *
     * @author windy
     * @param array $post
     * @return bool
     */
    public static function add(array $post): bool
    {
        Db::startTrans();
        try {
            // 数据处理
            $maxOrMinPrice = self::getMaxOrMinPrice($post);

            // 替换内容中图片地址
            $domain = UrlUtils::getStorageUrl();
            $post['content'] = str_replace($domain, '', $post['content']);

            // 添加商品基础信息
            $goods = Goods::create([
                'name'               => $post['name'],
                'first_category_id'  => $post['category'][0] ?? 0,
                'second_category_id' => $post['category'][1] ?? 0,
                'third_category_id'  => $post['category'][2] ?? 0,
                'brand_id'           => $post['brand_id'] ?? 0,
                'supplier_id'        => $post['supplier_id'] ?? 0,
                'freight_id'         => $post['freight_id'] ?? 0,
                'code'               => $post['code'] ?? '',
                'video'              => $post['video'] ?? '',
                'image'              => $post['image'] ?? '',
                'banner'             => $post['banner'] ?? '',
                'intro'              => $post['intro'] ?? '',
                'content'            => $post['content'] ?? '',
                'spec_type'    => $post['spec_type'],
                'max_price'    => $maxOrMinPrice['max_price'],
                'min_price'    => $maxOrMinPrice['min_price'],
                'market_price' => $maxOrMinPrice['market_price'],
                'stock'        => $maxOrMinPrice['total_stock'],
                'sort'         => $post['sort'] ?? 0,
                'sales_volume' => $post['sales_volume'] ?? 0,
                'stock_warn'   => $post['stock_warn'] ?? 0,
                'click_count'  => $post['click_count'] ?? 0,
                'is_show'      => $post['is_show'] ?? 0,
                'is_integral'  => $post['is_integral'] ?? 0,
                'is_new'       => $post['is_new'] ?? 0,
                'is_best'      => $post['is_best'] ?? 0,
                'is_like'      => $post['is_like'] ?? 0
            ]);

            // 添加商品规格信息
            $post['spec_type'] == 1 ?
                self::addSingleSpec($post, $goods['id']) :
                self::addMultiSpec($post, $goods['id']);

            Db::commit();
            return true;
        } catch (Exception $e) {
            Db::rollback();
            static::$error = $e->getMessage();
            return false;
        }
    }

    /**
     * 编辑商品
     *
     * @author windy
     * @param array $post (表单参数)
     * @return bool
     */
    public static function edit(array $post): bool
    {
        Db::startTrans();
        try {
            // 数据处理
            $maxOrMinPrice = self::getMaxOrMinPrice($post);
            $goods = (new Goods())->where(['id'=>(int)$post['id']])->findOrEmpty();

            // 替换内容中图片地址
            $domain = UrlUtils::getStorageUrl();
            $post['content'] = str_replace($domain, '', $post['content']);

            // 编辑商品基础信息
            Goods::update([
                'name'               => $post['name'],
                'first_category_id'  => empty($post['category'][0]) ? 0 : $post['category'][0],
                'second_category_id' => empty($post['category'][1]) ? 0 : $post['category'][1],
                'third_category_id'  => empty($post['category'][2]) ? 0 : $post['category'][2],
                'brand_id'           => $post['brand_id'] ?? 0,
                'supplier_id'        => $post['supplier_id'] ?? 0,
                'freight_id'         => $post['freight_id'] ?? 0,
                'code'               => $post['code'] ?? '',
                'video'              => $post['video'] ?? '',
                'image'              => $post['image'] ?? '',
                'banner'             => $post['banner'] ?? '',
                'intro'              => $post['intro'] ?? '',
                'content'            => trim($post['content']) ?? '',
                'spec_type'    => $post['spec_type'],
                'max_price'    => $maxOrMinPrice['max_price'],
                'min_price'    => $maxOrMinPrice['min_price'],
                'market_price' => $maxOrMinPrice['market_price'],
                'stock'        => $maxOrMinPrice['total_stock'],
                'sort'         => $post['sort'],
                'sales_volume' => $post['sales_volume'] ?? 0,
                'stock_warn'   => $post['stock_warn'] ?? 0,
                'click_count'  => $post['click_count'] ?? 0,
                'is_show'      => $post['is_show'] ?? 0,
                'is_integral'  => $post['is_integral'] ?? 0,
                'is_new'       => $post['is_new'] ?? 0,
                'is_best'      => $post['is_best'] ?? 0,
                'is_like'      => $post['is_like'] ?? 0
            ], ['id'=>(int)$post['id']]);

            // 编辑商品规格信息
            if ($post['spec_type'] == 1) { // 单规格处理
                if ($goods['spec_type'] == 2) {
                    self::deleteGoodsSpec((int)$post['id']);
                    self::addSingleSpec($post, (int)$post['id']);
                } else {
                    self::editSingleSpec($post, (int)$post['id']);
                }
            } else { // 多规格处理
                if ($goods['spec_type'] == 1) {
                    self::deleteGoodsSpec((int)$post['id']);
                    self::addMultiSpec($post, (int)$post['id']);
                } else {
                    self::editMultiSpec($post, (int)$post['id']);
                }
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
     * 删除商品
     *
     * @author windy
     * @param $id (主键ID)
     * @return bool
     */
    public static function del(int $id): bool
    {
        try {
            Goods::update([
                'is_show'     => 0,
                'is_delete'   => 1,
                'delete_time' => time()
            ], ['id'=>(int)$id]);

            return true;
        } catch (Exception $e) {
            static::$error = $e->getMessage();
            return false;
        }
    }

    /**
     * 销毁商品
     *
     * @author windy
     * @param $id (主键ID)
     * @return bool
     */
    public static function destroy(int $id): bool
    {
        try {
            Goods::update([
                'is_show'     => 0,
                'is_delete'   => 2,
                'delete_time' => time()
            ], ['id'=>(int)$id]);

            return true;
        } catch (Exception $e) {
            static::$error = $e->getMessage();
            return false;
        }
    }

    /**
     * 商品移入到仓库中
     *
     * @author windy
     * @param $id (主键ID)
     * @return bool
     */
    public static function addWarehouse($id): bool
    {
        try {
            Goods::update([
                'is_show'     => 0,
                'is_delete'   => 0,
                'delete_time' => 0,
                'update_time' => time()
            ], ['id'=>(int)$id]);

            return true;
        } catch (Exception $e) {
            static::$error = $e->getMessage();
            return false;
        }
    }

    /**
     * 切换状态
     *
     * @author windy
     * @param array $post
     * @return bool
     */
    public static function change(array $post): bool
    {
        try {
            Goods::update([
                $post['field'] => $post['status'],
                'update_time'  => time()
            ], ['id'=>(int)$post['id']]);

            return true;
        } catch (Exception $e) {
            static::$error = $e->getMessage();
            return false;
        }
    }

    /**
     * 上架或下架(支持批量)
     *
     * @author windy
     * @param $id
     * @param $status
     * @return bool
     */
    public static function upperOrLower($id, $status): bool
    {
        try {
            $where[] = is_numeric($id) ? ['id', '=', (int)$id] : ['id', 'in', $id];
            Goods::update([
                'is_show'     => $status,
                'update_time' => time()
            ], $where);

            return true;
        } catch (Exception $e) {
            static::$error = $e->getMessage();
            return false;
        }
    }

    /**
     * 算出最大最小价格
     *
     * @author windy
     * @param array $post (表单数据)
     * @return mixed
     */
    private static function getMaxOrMinPrice(array $post): array
    {
        if ($post['spec_type'] == 1) {
            $data['max_price']    = $post['one_sell_price'];
            $data['min_price']    = $post['one_sell_price'];
            $data['market_price'] = $post['one_market_price'];
            $data['total_stock']  = $post['one_stock'];
        } else {
            $data['max_price']    = max($post['sell_price']);
            $data['min_price']    = min($post['sell_price']);
            $data['market_price'] = max($post['market_price']);
            $data['total_stock']  = array_sum($post['stock']);
        }
        return $data;
    }

    /**
     * 单规格: 添加
     *
     * @author windy
     * @param array $post (表单数据)
     * @param int $goods_id (商品ID)
     */
    private static function addSingleSpec(array $post, int $goods_id)
    {
        $goodsSpec = GoodsSpec::create(['goods_id'=>$goods_id, 'name'=>'默认']);
        $goodsSpecValue = GoodsSpecValue::create(['spec_id'=>$goodsSpec['id'], 'goods_id'=>$goods_id, 'value'=>'默认']);
        GoodsSku::create([
            'image'             => $post['one_spec_image'] ?? '',
            'goods_id'          => $goods_id,
            'spec_value_ids'    => $goodsSpecValue['id'],
            'spec_value_str'    => '默认',
            'market_price'      => $post['one_market_price'],
            'sell_price'        => $post['one_sell_price'],
            'cost_price'        => $post['one_cost_price'],
            'stock'             => $post['one_stock'],
            'volume'            => $post['one_volume'],
            'weight'            => $post['one_weight'],
            'bar_code'          => $post['one_bar_code'] ?? '',
        ]);
    }

    /**
     * 多规格: 添加
     *
     * @author windy
     * @param array $post (表单数据)
     * @param int $goods_id (商品ID)
     * @return bool
     * @throws Exception
     */
    private static function addMultiSpec(array $post, int $goods_id): bool
    {
        try {
            $goodsSkuModel = new GoodsSku();
            $goodsSpecModel = new GoodsSpec();
            $goodsSpecValueModel = new GoodsSpecValue();

            // 1、添加规格项
            $specs_project_data = [];
            foreach ($post['spec_name'] as $key => $value) {
                $specs_project_data[] = [
                    'goods_id' => $goods_id,
                    'name' => $value
                ];
            }
            $goodsSpecModel->saveAll($specs_project_data);

            // 2、查询规格项
            $goods_spec_name_key_id = $goodsSpecModel->field(true)
                ->where('goods_id', '=', $goods_id)
                ->where('name', 'in', array_values($post['spec_name']))
                ->column('id', 'name');

            // 3、重置规格项下标
            $spec_name_array = [];
            foreach ($post['spec_name'] as $key=>$value) {
                array_push($spec_name_array, $value);
            }
            $post['spec_name'] = $spec_name_array;

            // 4、遍历组合规格值
            $spec_value_data = [];
            foreach ($post['spec_value_names'] as $k => $v) {
                $row = explode(',', $v);
                foreach ($row as $k2 => $v2) {
                    $spec_value_data[] = [
                        'goods_id' => $goods_id,
                        'spec_id'  => $goods_spec_name_key_id[$post['spec_name'][$k]],
                        'value'    => $v2,
                    ];
                }
            }
            $goodsSpecValueModel->saveAll($spec_value_data);

            // 5、查询规格值
            $goods_spec_name_value_id = $goodsSpecValueModel->field(true)
                ->where('goods_id', '=', $goods_id)
                ->column('id', 'value');

            // 6、添加商品SKU
            $spec_lists = ArrayUtils::formToLinear(self::filterSkuParams($post));
            foreach ($spec_lists as $k => $v) {
                $spec_lists[$k]['spec_value_ids'] = '';
                $temp = explode(',', $v['spec_value_str']);

                foreach ($temp as $k2 => $v2) {
                    $spec_lists[$k]['spec_value_ids'] .= $goods_spec_name_value_id[$v2] . ',';
                }
                $spec_lists[$k]['spec_value_ids'] = trim($spec_lists[$k]['spec_value_ids'], ',');
                $spec_lists[$k]['image'] = $spec_lists[$k]['spec_image'] ?? '';
                $spec_lists[$k]['goods_id'] = $goods_id;
            }
            $goodsSkuModel->saveAll($spec_lists);
            return true;
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    /**
     * 删除商品所有规格信息
     *
     * @author windy
     * @param int $goods_id (商品ID)
     */
    private static function deleteGoodsSpec(int $goods_id)
    {
        (new GoodsSpec())->where(['goods_id'=>$goods_id])->delete();
        (new GoodsSpecValue())->where(['goods_id'=>$goods_id])->delete();
        (new GoodsSku())->where(['goods_id'=>$goods_id])->delete();
    }

    /**
     * 更新：单规格
     *
     * @author windy
     * @param array $post
     * @param int $goods_id
     */
    private static function editSingleSpec(array $post, int $goods_id)
    {
        GoodsSku::update([
            'image'             => $post['one_spec_image'] ?? '',
            'market_price'      => $post['one_market_price'],
            'sell_price'        => $post['one_sell_price'],
            'cost_price'        => $post['one_cost_price'],
            'stock'             => $post['one_stock'],
            'volume'            => $post['one_volume'],
            'weight'            => $post['one_weight'],
            'bar_code'          => $post['one_bar_code'] ?? '',
        ], ['id'=>intval($post['one_sku_id']), 'goods_id'=>$goods_id]);
    }

    /**
     * 更新: 多规格
     *
     * @author windy
     * @param array $post
     * @param int $goods_id
     * @throws Exception
     */
    private static function editMultiSpec(array $post, int $goods_id)
    {
        try {
            $goodsSkuModel = new GoodsSku();
            $goodsSpecModel = new GoodsSpec();
            $goodsSpecValueModel = new GoodsSpecValue();

            // ==================== 1、规格 ======================
            // 1、规格项目: 处理规格数据
            $create_spec_project = [];
            $update_spec_project = [];
            foreach ($post['spec_name'] as $key => $value) {
                if ($post['spec_id'][$key]) {
                    $update_spec_project[] = [
                        'id' => intval($post['spec_id'][$key]),
                        'goods_id' => $goods_id,
                        'name' => $value
                    ];
                } else {
                    $create_spec_project[] = [
                        'goods_id' => $goods_id,
                        'name' => $value
                    ];
                }
            }
            $mysqli_spec_ids = $goodsSpecModel->where(['goods_id' => $goods_id])->column('id');
            $delete_spec_ids = array_diff($mysqli_spec_ids, array_column($update_spec_project, 'id'));

            // 2、规格项目: 曾删改操作
            if (!empty($delete_spec_ids)) {
                $goodsSpecModel->whereIn('id', $delete_spec_ids)->delete();
            }
            if (!empty($update_spec_project)) {
                $goodsSpecModel->saveAll($update_spec_project);
            }
            if (!empty($create_spec_project)) {
                $goodsSpecModel->insertAll($create_spec_project);
            }


            // ==================== 2、值 ======================
            // 1、查询规格项目
            $spec_name_key_id = $goodsSpecModel->field(true)
                ->where('goods_id', '=', $goods_id)
                ->where('name', 'in', $post['spec_name'])
                ->column('id', 'name');

            // 2、规格值: 处理数据
            $create_spec_value = [];
            $update_spec_value = [];
            foreach ($post['spec_value_names'] as $key => $value) {
                $spec_value_ids = explode(',', $post['spec_value_ids'][$key]);
                $spec_value_names = explode(',', $value);
                $spec_project_arr = array_values($post['spec_name']);

                foreach ($spec_value_ids as $k => $v) {
                    if ($v) {
                        $update_spec_value[] = [
                            'id' => intval($v),
                            'goods_id' => $goods_id,
                            'spec_id' => $spec_name_key_id[$spec_project_arr[$key]],
                            'value' => $spec_value_names[$k]
                        ];
                    } else {
                        $create_spec_value[] = [
                            'goods_id' => $goods_id,
                            'spec_id' => $spec_name_key_id[$spec_project_arr[$key]],
                            'value' => $spec_value_names[$k]
                        ];
                    }
                }
            }
            $mysqli_spec_value_ids = $goodsSpecValueModel->where(['goods_id' => $goods_id])->column('id');
            $delete_spec_value_ids = array_diff($mysqli_spec_value_ids, array_column($update_spec_value, 'id'));

            // 3、规格值: 曾删改操作
            if (!empty($delete_spec_value_ids)) {
                $goodsSpecValueModel->whereIn('id', $delete_spec_value_ids)->delete();
            }
            if (!empty($update_spec_value)) {
                $goodsSpecValueModel->saveAll($update_spec_value);
            }
            if (!empty($create_spec_value)) {
                $goodsSpecValueModel->insertAll($create_spec_value);
            }


            // ==================== 3、SKU处理 ======================
            // 1、查询处理规格值数据
            $create_sku = [];
            $update_sku = [];
            $spec_lists = ArrayUtils::formToLinear(self::filterSkuParams($post));
            $spec_name_value_id = $goodsSpecValueModel->where(['goods_id' => $goods_id])->column('id', 'value');
            // 2、SKU: 数据处理
            foreach ($spec_lists as $k => $v) {
                $spec_lists[$k]['spec_value_ids'] = '';
                $spec_value_str = explode(',', $v['spec_value_str']);
                foreach ($spec_value_str as $k2 => $v2) {
                    $spec_lists[$k]['spec_value_ids'] .= $spec_name_value_id[$v2] . ',';
                }
                $spec_lists[$k]['spec_value_ids'] = trim($spec_lists[$k]['spec_value_ids'], ',');
                $spec_lists[$k]['goods_id'] = $goods_id;
                $sku_id = $spec_lists[$k]['sku_id'];
                unset($spec_lists[$k]['sku_id']);

                if ($sku_id) {
                    $spec_lists[$k]['id'] = intval($sku_id);
                    $update_sku[] = $spec_lists[$k];
                } else {
                    $create_sku[] = $spec_lists[$k];
                }
            }
            $mysqli_sku_ids = $goodsSkuModel->where(['goods_id' => $goods_id])->column('id');
            $delete_sku_ids = array_diff($mysqli_sku_ids, array_column($update_sku, 'id'));

            // 3、SKU: 曾删改操作
            if (!empty($delete_sku_ids)) {
                $goodsSkuModel->whereIn('id', $delete_sku_ids)->delete();
            }
            if (!empty($update_sku)) {
                $goodsSkuModel->saveAll($update_sku);
            }
            if (!empty($create_sku)) {
                $goodsSkuModel->insertAll($create_sku);
            }

        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    /**
     * 过滤只剩SKU所需参数
     *
     * @author windy
     * @param array $post (表单数据)
     * @return mixed
     */
    private static function filterSkuParams(array $post): array
    {
        $data['sku_id']         = $post['sku_id'];
        $data['spec_value_str'] = $post['spec_value_str'] ?? '';
        $data['image']          = $post['spec_image'] ?? '';
        $data['market_price']   = $post['market_price'] ?? 0;
        $data['sell_price']     = $post['sell_price'] ?? 0;
        $data['cost_price']     = $post['cost_price'] ?? 0;
        $data['stock']          = $post['stock'] ?? 0;
        $data['volume']         = $post['volume'] ?? 0;
        $data['weight']         = $post['weight'] ?? 0;
        $data['bar_code']       = $post['bar_code'] ?? '';
        return $data;
    }
}