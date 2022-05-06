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

use app\api\service\PlaceOrderService;
use app\common\basics\Logic;
use app\common\enum\CouponEnum;
use app\common\model\addons\Coupon;
use app\common\model\addons\CouponList;
use Exception;

/**
 * 优惠券接口-逻辑层
 * Class CouponLogic
 * @package app\api\logic\marketing
 */
class CouponLogic extends Logic
{
    /**
     * 查询条件
     *
     * @author windy
     * @param int $type
     * @return array
     */
    public static function queryWhere(int $type=0): array
    {
        $where = [];
        switch (intval($type)) {
            case 0: // 未使用
                $where[] = ['is_expire', '=', CouponEnum::IS_EXPIRE_NORMAL];
                $where[] = ['is_use', '=', CouponEnum::IS_USE_NOT];
                $where[] = ['expiry_time', '>=', time()];
                break;
            case 1: // 已使用
                $where[] = ['is_use', '=', CouponEnum::IS_USE_OK];
                break;
            case 2: // 已失效
                $where[] = ['is_expire', '=', CouponEnum::IS_EXPIRE_INVALID];
        }
        return $where;
    }

    /**
     * 优惠券列表
     *
     * @author windy
     * @param array $get
     * @param int $userId
     * @return array
     * @throws @\think\db\exception\DbException
     */
    public static function lists(array $get, int $userId): array
    {
        $model = new Coupon();
        $lists = $model->field('id,type,name,min_price,reduce_price,discount,explain,use_expire_time,use_goods_type')
            ->order('min_price', 'asc')
            ->where([
                ['get_method', '=', CouponEnum::GET_METHOD_DIRECT],
                ['status', '=', CouponEnum::OK_PUBLISH_STATUS],
                ['is_delete', '=', 0]
            ])->paginate([
                'page'      => $get['page'] ?? 1,
                'list_rows' => $get['limit'] ?? 20,
                'var_page'  => 'page'
            ])->toArray();

        foreach ($lists['data'] as &$item) {
            $item['reduce_price']    = intval($item['reduce_price']);
            $item['use_condition']   = $item['min_price'] ? '满'.intval($item['min_price']).'可用' : '无门槛';
            $item['use_expire_time'] = $item['use_expire_time'] ? '领取'.$item['use_expire_time'].'天内使用' : '无限制';
            $item['use_goods_type']  = CouponEnum::getUseGoodsTypeDesc($item['use_goods_type']);

            $couponList =(new CouponList())->where(['coupon_id'=>$item['id'], 'user_id'=>$userId])->findOrEmpty();
            $item['is_receive'] = $couponList->isEmpty() ? 0 : 1;
        }

        return $lists;
    }

    /**
     * 领取优惠券
     *
     * @author windy
     * @param $id (优惠券ID)
     * @param $user_id (用户ID)
     * @return bool
     */
    public static function receive($id, $user_id): bool
    {
        try {
            $model = new CouponList();
            $coupon = (new Coupon)->field(true)
                ->where([
                    ['id', '=', intval($id)],
                    ['get_method', '=', CouponEnum::GET_METHOD_DIRECT],
                    ['status', '=', CouponEnum::OK_PUBLISH_STATUS],
                    ['is_delete', '=', 0]
                ])->findOrEmpty()->toArray();

            if (!$coupon) {
                static::$error = '优惠券不存在';
                return false;
            }

            if ($coupon['get_num_type'] === 1) {
                $receiveCount = $model->where([
                    ['coupon_id', '=', (int)$id],
                    ['user_id', '=', $user_id]
                ])->count();
                if ($receiveCount >= $coupon['get_num']) {
                    static::$error = '超出可领取数量';
                    return false;
                }
            } elseif ($coupon['get_num_type'] === 2) {
                $todayStart = strtotime(date('Y-m-d').'00:00:00');
                $todayEnd   = strtotime(date('Y-m-d').'23:59:59');
                $receiveCount = $model->field(true)->where([
                    ['coupon_id', '=', (int)$id],
                    ['user_id', '=', $user_id],
                    ['create_time', '>=', $todayStart],
                    ['create_time', '<=', $todayEnd],
                ])->count();
                if ($receiveCount >= $coupon['use_goods_type']) {
                    static::$error = '超出今天可领取数量';
                    return false;
                }
            }

            $time = time();
            $expiry_time = $time + ($coupon['use_expire_time'] * (24 * 60 * 60));
            CouponList::create([
                'user_id'     => $user_id,
                'coupon_id'   => $id,
                'is_use'      => 0,
                'is_expire'   => 0,
                'use_time'    => 0,
                'receive_time' => $time,
                'update_time'  => $time,
                'expiry_time'  => $coupon['use_expire_time'] ? intval($expiry_time) : 0
            ]);

            return true;
        } catch (Exception $e) {
            static::$error = $e->getMessage();
            return false;
        }
    }

    /**
     * 我的可用优惠券列表
     *
     * @author windy
     * @param array $get
     * @param int $user_id
     * @return array|false
     * @throws @\think\db\exception\DbException
     */
    public static function myCoupon(array $get, int $user_id)
    {
        $where = self::queryWhere($get['type'] ?? 0);
        $where[] = ['user_id', '=', $user_id];

        $model = new CouponList();
        $lists = $model->field(true)
            ->order('expiry_time', 'asc')
            ->where($where)
            ->with(['coupon'])
            ->paginate([
                'page'      => $get['page'] ?? 1,
                'list_rows' => $get['limit'] ?? 20,
                'var_page'  => 'page'
            ])->toArray();

        $notUse = $model->where(self::queryWhere(0))->where(['user_id'=>$user_id])->count();
        $okUse  = $model->where(self::queryWhere(1))->where(['user_id'=>$user_id])->count();
        $invUse = $model->where(self::queryWhere(2))->where(['user_id'=>$user_id])->count();
        $lists['tabs'] = [
            ['name'=>'未使用('.$notUse.')', 'count'=>0],
            ['name'=>'已使用('.$okUse.')',  'count'=>0],
            ['name'=>'已过期('.$invUse.')', 'count'=>0],
        ];

        $data = [];
        foreach ($lists['data'] as $item) {
            $coupon = $item['coupon'];
            $data[] = [
                'id'              => $coupon['id'],
                'cl_id'           => $item['coupon_list_id'],
                'type'            => $coupon['type'],
                'name'            => $coupon['name'],
                'discount'        => $coupon['discount'],
                'reduce_price'    => intval($coupon['reduce_price']),
                'use_condition'   => $coupon['min_price'] ? '满'.intval($coupon['min_price']).'可用' : '无门槛',
                'use_goods_type'  => CouponEnum::getUseGoodsTypeDesc($coupon['use_goods_type']),
                'expiry_time'     => date('Y-m-d H:i:s', $item['expiry_time']),
                'explain'         => $coupon['explain'],
            ];
        }

        $lists['data'] = $data;
        return $lists;
    }

    /**
     * 我的失效未使用优惠券
     *
     * @author windy
     * @param array $get
     * @param int $user_id
     * @return array|false
     */
    public static function invalidCoupon(array $get, int $user_id)
    {
        try {
            $model = new CouponList();
            return $model->field(true)
                ->order('expiry_time', 'desc')
                ->where([
                    ['user_id', '=', $user_id],
                    ['is_expire', '=', CouponEnum::IS_EXPIRE_INVALID],
                    ['is_use', '=', CouponEnum::IS_USE_NOT],
                ])->paginate([
                    'page'      => $get['page'] ?? 1,
                    'list_rows' => $get['limit'] ?? 20,
                    'var_page'  => 'page'
                ])->toArray();

        } catch (Exception $e) {
            static::$error = $e->getMessage();
            return false;
        }
    }

    /**
     * 下单可用优惠券列表
     *
     * @author windy
     * @param $post
     * @param int $user_id
     * @return array|false
     * @throws @\think\Exception
     * @throws @\think\db\exception\DataNotFoundException
     * @throws @\think\db\exception\DbException
     * @throws @\think\db\exception\ModelNotFoundException
     */
    public static function orderCoupon($post, int $user_id)
    {
        $time = time();
        $model = new CouponList();
        $couponList = $model->field(true)
            ->withJoin(['coupon'])
            ->order('coupon.reduce_price', 'desc')
            ->where([
                ['is_use', '=', CouponEnum::IS_USE_NOT],
                ['is_expire', '=', CouponEnum::IS_EXPIRE_NORMAL],
                ['user_id', '=', $user_id]
            ])->select()->toArray();

        $lists = [
            'usable'   => [], // 可用的优惠券
            'unusable' => []  // 不可用的优惠券
        ];

        $orderStatus = PlaceOrderService::check($post, $post['products'], $user_id);
        foreach ($couponList as $coupon) {
            // 1、计算满足优惠券要求商品的累计金额
            $totalPrice = 0;
            switch ($coupon['coupon']['use_goods_type']) {
                case CouponEnum::USE_GOODS_TYPE_ALL:  // 全部商品可用
                    $totalPrice = $orderStatus['totalAmount'];
                    break;
                case CouponEnum::USE_GOODS_TYPE_PART: // 部分商品可用
                    foreach ($orderStatus['pStatusArray'] as $product) {
                        if (in_array($product['id'], $coupon['coupon']['use_goods_ids'])) {
                            $totalPrice += $product['totalAmount'];
                        }
                    }
                    break;
                case CouponEnum::USE_GOODS_TYPE_BAN:  // 部分商品不可用
                    foreach ($orderStatus['pStatusArray'] as $product) {
                        if (!in_array($product['id'], $coupon['coupon']['use_goods_ids'])) {
                            $totalPrice += $product['totalAmount'];
                        }
                    }
                    break;
            }

            $expiryTime = ($coupon['expiry_time'] - $time);
            $expiryTips = '今天到期';
            if ($expiryTime > 86400) {
                $expiryTips = intval(floor($expiryTime / 86400)).'天后到期';
            } else {
                if ($expiryTime <= 21600) $expiryTips = intval(floor($expiryTime / 3600)).'小时后到期';
                if ($expiryTime <= 3600) $expiryTips = '1小时内到期';
                if ($expiryTime <= 0) $expiryTips = '已到期';
            }

            $tplCoupon = [
                'id'             => $coupon['coupon_id'],
                'coupon_list_id' => $coupon['coupon_list_id'],
                'type'           => $coupon['coupon']['type'],
                'name'           => $coupon['coupon']['name'],
                'reduce_price'   => $coupon['coupon']['reduce_price'],
                'discount'       => $coupon['coupon']['discount'],
                'expiry_tips'    => $expiryTips,
                'expiry_time'    => date('Y-m-d H:i', $coupon['expiry_time']),
                'use_goods_type' => CouponEnum::getUseGoodsTypeDesc($coupon['coupon']['use_goods_type']),
                'use_condition'  => $coupon['coupon']['min_price'] ? '满'.intval($coupon['coupon']['min_price']).'可用' : '无门槛',
            ];

            // 2、判断是否满足最低消费条件(是则可用)
            if ($coupon['coupon']['min_price'] <= 0 or $totalPrice >= $coupon['coupon']['min_price']) {
                $lists['usable'][] = $tplCoupon;
            } else {
                $lists['unusable'][] = $tplCoupon;
            }
        }

        return $lists;
    }
}