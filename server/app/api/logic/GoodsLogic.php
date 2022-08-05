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

namespace app\api\logic;


use app\common\basics\Logic;
use app\common\model\goods\Goods;
use app\common\model\goods\GoodsCollect;
use app\common\model\HotSearch;

/**
 * 商品接口-逻辑层
 * Class GoodsLogic
 * @package app\api\logic
 */
class GoodsLogic extends Logic
{
    /**
     * 商品列表
     *
     * @author windy
     * @param array $get
     * @return array
     * @throws @\think\db\exception\DbException
     */
    public static function lists(array $get): array
    {
        $where = [
            ['is_show', '=', 1],
            ['is_delete', '=', 0]
        ];

        if (!empty($get['cid']) and $get['cid'] > 0) {
            //$field = 'first_category_id|second_category_id|third_category_id';
            $where[] = ['category_id', '=', (int)$get['cid']];
        }

        if (!empty($get['keyword']) and $get['keyword']) {
            $where[] = ['name', 'like', '%' . trim($get['keyword']) . '%'];
            HotSearch::setHotSearch($get['keyword']);
        }

        $order = ['id', 'click_count'=>'desc'];
        switch ($get['filter'] ?? 'all') {
            case 'all':
                break;
            case 'sales':
                $order = ['sales_volume'=>$get['sort'] ?? 'desc'];
                break;
            case 'price':
                $order = ['min_price'=>$get['sort'] ?? 'desc'];
                break;
            case 'news':
                $order = ['id'=>'desc'];
                break;
        }

        $model = new Goods();
        return $model->field(['id', 'name', 'image', 'min_price', 'market_price', 'stock', 'sales_volume'])
            ->where($where)
            ->order($order)
            ->paginate([
                'page'      => $get['page'] ?? 1,
                'list_rows' => $get['limit'] ?? 20,
                'var_page'  => 'page'
            ])->toArray();
    }

    /**
     * 商品详细
     *
     * @author windy
     * @param int $id (商品ID)
     * @param int $userId (用户ID)
     * @return array|bool
     */
    public static function detail(int $id, int $userId)
    {
        $model = new Goods();

        Goods::update([
            'click_count' => ['inc', 1]
        ], ['id'=>intval($id)]);

        return $model->field([
                'id,name,image,video,banner,spec_type',
                'max_price,min_price,market_price',
                'stock,sales_volume,click_count',
                'intro,content,update_time'
            ])
            ->where(['id'=>$id, 'is_delete'=>0])
            ->with(['sku', 'spec.specValue'])
            ->withAttr(['is_collect'=>function($value, $data) use ($userId){
                unset($value);
                $result = (new GoodsCollect())->where([
                    'user_id'   => $userId,
                    'goods_id'  => $data['id'],
                    'is_delete' => 0
                ])->findOrEmpty()->toArray();
                return $result ? true : false;
            }])->append(['comment', 'is_collect'])
            ->findOrEmpty()->toArray();
    }

    /**
     * 推荐商品
     *
     * @author windy
     * @param array $get
     * @return array
     * @throws @\think\db\exception\DataNotFoundException
     * @throws @\think\db\exception\DbException
     * @throws @\think\db\exception\ModelNotFoundException
     */
    public static function recommend(array $get): array
    {
        // 热销榜单
        if ($get['type'] === 'hot') {
            return (new Goods())->field(['id,name,image,market_price,min_price,sales_volume'])
                ->where([
                    ['is_show', '=', 1],
                    ['is_delete', '=', 0]
                ])->order('sales_volume', 'desc')
                ->order('click_count', 'desc')
                ->limit(4)->select()->toArray();
        }

        // 好物推荐
        if ($get['type'] === 'best') {
            return (new Goods())->field(['id,name,image,market_price,min_price,sales_volume'])
                ->where([
                    ['is_show', '=', 1],
                    ['is_delete', '=', 0],
                    ['is_best', '=', 1]
                ])->order('sales_volume', 'desc')
                ->order('click_count', 'desc')
                ->limit(6)->select()->toArray();
        }

        return [];
    }
}