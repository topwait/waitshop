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

namespace app\api\logic;


use app\common\basics\Logic;
use app\common\model\Cart;
use Exception;

/**
 * 购物车接口-逻辑层
 * Class CartLogic
 * @package app\api\logic
 */
class CartLogic extends Logic
{
    /**
     * 购物车列表
     *
     * @author windy
     * @param int $userId
     * @return array
     * @throws @\think\db\exception\DataNotFoundException
     * @throws @\think\db\exception\DbException
     * @throws @\think\db\exception\ModelNotFoundException
     */
    public static function lists(int $userId): array
    {
        $model = new Cart();
        $lists = $model
            ->withoutField(['create_time', 'update_time'])
            ->where('user_id', '=', $userId)
            ->withJoin([
                'sku'   => ['id', 'spec_value_str', 'image', 'stock', 'sell_price'],
                'goods' => ['id', 'name', 'image', 'is_show', 'is_delete']
            ])->select()->toArray();

        $normal  = [];    // 正常商品
        $invalid = [];    // 失效的商品
        $reset   = false; // 是否重置库存
        foreach ($lists as $key => &$item) {
            $item['name']           = $item['goods']['name'];
            $item['stock']          = $item['sku']['stock'];
            $item['selected']       = $item['selected'] ? true : false;
            $item['sell_price']     = $item['sku']['sell_price'];
            $item['spec_value_str'] = $item['sku']['spec_value_str'];
            $item['image']          = $item['sku']['image'] ? $item['sku']['image'] : $item['goods']['image'];
            $item['is_show']        = $item['goods']['is_show'];
            $item['is_delete']      = $item['goods']['is_delete'];
            unset($item['goods']);
            unset($item['sku']);
            if (!$item['is_show'] || $item['is_delete'] || $item['stock'] <= 0) {
                array_push($invalid, $item);
                unset($lists[$key]);
            } else {
                if ($item['num'] > $item['stock']) {
                    $reset = true;
                    Cart::update([
                        'num'         => $item['stock'],
                        'update_time' => time()
                    ], ['id'=>$item['id'], 'user_id'=>$userId]);
                }

                $item['num'] = $item['num'] > $item['stock'] ? $item['stock'] : $item['num'];
                array_push($normal, $item);
            }
        }

        return ['normal'=>$normal, 'invalid'=>$invalid, 'reset'=>$reset];
    }

    /**
     * 添加到购物车
     *
     * @author windy
     * @param array $post
     * @param int $userId
     * @return bool
     */
    public static function add(array $post, int $userId): bool
    {
        try {
            $where = [
                ['user_id', '=', $userId],
                ['goods_id', '=', (int)$post['goods_id']],
                ['sku_id', '=', (int)$post['sku_id']]
            ];

            $model = new Cart();
            $cart  = $model->field(true)->where($where)->findOrEmpty()->toArray();
            $count = $model->field(true)->where(['user_id'=>$userId])->count();

            if ($count >= 30) {
                throw new \think\Exception('购物车已满,请先去付款');
            }

            if ($cart) {
                Cart::update([
                    'num'         => ['inc', (int)$post['num']],
                    'update_time' => time()
                ], $where);
            } else {
                Cart::create([
                    'user_id'  => $userId,
                    'goods_id' => (int)$post['goods_id'],
                    'sku_id'   => (int)$post['sku_id'],
                    'num'      => (int)$post['num'],
                    'selected' => 1
                ]);
            }

            return true;
        } catch (Exception $e) {
            static::$error = $e->getMessage();
            return false;
        }
    }

    /**
     * 销毁购物车商品
     *
     * @author windy
     * @param array $post
     * @param int $user_id
     * @return bool
     */
    public static function destroy(array $post, int $user_id): bool
    {
        try {
            $model = new Cart();
            $model->where('id', 'in', $post['ids'])
                ->where('user_id', '=', $user_id)
                ->delete();

            return true;
        } catch (Exception $e) {
            static::$error = $e->getMessage();
            return false;
        }
    }

    /**
     * 更新产品购买数量
     *
     * @author windy
     * @param int $id (购物车ID)
     * @param int $num (数量)
     * @param int $userId (用户ID)
     * @return bool
     */
    public static function change(int $id, int $num, int $userId): bool
    {
        try {
            Cart::update([
                'num'         => $num < 0 ? 1 : $num,
                'update_time' => time()
            ], ['id'=>$id, 'user_id'=>$userId]);

            return true;
        } catch (Exception $e) {
            static::$error = $e->getMessage();
            return false;
        }
    }

    /**
     * 更新选择或取消选择
     *
     * @author windy
     * @param array $post
     * @param int $user_id
     * @return bool
     */
    public static function choice(array $post, int $user_id): bool
    {
        try {
            $where = ['user_id'=>(int)$user_id];
            $where = (int)$post['all'] ? $where : ['id'=>(int)$post['id'], 'user_id'=>$user_id];

            Cart::update([
                'selected'    => (int)$post['selected'],
                'update_time' => time()
            ], $where);

            return true;
        } catch (Exception $e) {
            static::$error = $e->getMessage();
            return false;
        }
    }

    /**
     * 获取购物车数量
     *
     * @author windy
     * @param int $user_id
     * @return array
     */
    public static function num(int $user_id): array
    {
        $model = new Cart();
        $count = $model->where(['user_id'=>(int)$user_id])->sum('num') ?? 0;
        return ['count'=>$count];
    }
}