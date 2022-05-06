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
use app\common\model\goods\GoodsCollect;
use Exception;

/**
 * 商品收藏接口-逻辑层
 * Class CollectLogic
 * @package app\api\logic
 */
class CollectLogic extends Logic
{
    /**
     * 收藏列表
     *
     * @author windy
     * @param array $get
     * @param int $userId
     * @return array|false
     * @throws @\think\db\exception\DbException
     */
    public static function lists(array $get, int $userId)
    {
        $model = new GoodsCollect();
        $lists = $model->field(true)
            ->where(['user_id'=>$userId, 'is_delete'=>0])
            ->with(['goods'])
            ->order('id desc')
            ->paginate([
                'page'      => $get['page'] ?? 1,
                'list_rows' => $get['limit'] ?? 20,
                'var_page'  => 'page'
            ])->toArray();

        $data = [];
        foreach ($lists['data'] as $item) {
            $data[] = [
                'id'        => $item['id'],
                'goods_id'  => $item['goods']['id'],
                'name'      => $item['goods']['name'],
                'image'     => $item['goods']['image'],
                'min_price' => $item['goods']['min_price'],
            ];
        }

        $lists['data'] = $data;
        return $lists;
    }

    /**
     * 收藏商品
     *
     * @author windy
     * @param int $status (状态0=取消收藏, 1=收藏商品)
     * @param int $goods_id (商品ID)
     * @param int $userId (用户ID)
     * @return false
     */
    public static function add(int $status, int $goods_id, int $userId): bool
    {
        try {
            if ($status == 0) {
                GoodsCollect::update([
                    'is_delete' => 1,
                    'delete_time' => time()
                ], ['goods_id'=>intval($goods_id), 'user_id'=>$userId]);
                return true;
            } else {
                $collect = (new GoodsCollect())->where([
                    'goods_id'=>intval($goods_id),
                    'user_id' =>intval($userId)
                ])->findOrEmpty()->toArray();
                if (!$collect) {
                    GoodsCollect::create([
                        'goods_id' => $goods_id,
                        'user_id'  => $userId,
                        'count'    => 1
                    ]);
                } else {
                    GoodsCollect::update([
                        'count'       => ['inc', 1],
                        'is_delete'   => 0,
                        'delete_time' => ''
                    ], ['id'=>$collect['id'], 'user_id'=>$userId]);
                }
                return true;
            }
        } catch (Exception $e) {
            static::$error = $e->getMessage();
            return false;
        }
    }

    /**
     * 销毁收藏
     *
     * @author windy
     * @param $id
     * @return bool
     */
    public static function del($id): bool
    {
        try {
            GoodsCollect::update([
                'is_delete' => 1,
                'delete_time' => time()
            ], ['id'=>intval($id)]);

            return true;
        } catch (Exception $e) {
            static::$error = $e->getMessage();
            return false;
        }
    }
}