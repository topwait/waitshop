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


use app\api\logic\addons\CouponLogic;
use app\api\service\PlaceOrderService;
use app\common\basics\Logic;
use app\common\enum\AfterSaleEnum;
use app\common\enum\LogIntegralEnum;
use app\common\enum\OrderEnum;
use app\common\model\log\LogIntegral;
use app\common\model\order\AfterSale;
use app\common\model\order\Order;
use app\common\model\order\OrderDelivery;
use app\common\model\order\OrderGoods;
use app\common\model\store\Store;
use app\common\model\user\User;
use app\common\service\OrderService;
use app\common\service\logistics\Driver as LogisticsDriver;
use app\common\utils\ConfigUtils;
use app\common\utils\TimeUtils;
use Exception;
use think\facade\Db;

/**
 * 订单接口-逻辑层
 * Class OrderLogic
 * @package app\api\logic
 */
class OrderLogic extends Logic
{
    /**
     * 查询条件
     *
     * @author windy
     * @param int $type
     * @return array
     */
    public static function queryWhere(int $type): array
    {
        $where = [];
        switch ($type) {
            case 0: // 全部
                $where = [];
                break;
            case 1: // 待付款
                $where[] = ['order_status', '=', 0];
                $where[] = ['pay_status', '=', 0];
                break;
            case 2: // 待发货
                $where[] = ['order_status', '=', 1];
                $where[] = ['pay_status', '=', 1];
                break;
            case 3: // 待收货
                $where[] = ['order_status', '=', 2];
                $where[] = ['pay_status', '=', 1];
                break;
            case 4: // 已完成
                $where[] = ['order_status', '=', 3];
                $where[] = ['pay_status', '=', 1];
                break;
        }
        return $where;
    }

    /**
     * 我的订单
     *
     * @author windy
     * @param array $get
     * @param int $userId
     * @return array
     * @throws @\think\db\exception\DbException
     */
    public static function lists(array $get, int $userId): array
    {
        // 查询条件
        $where = self::queryWhere($get['type'] ?? 0);
        $where[] = ['user_id', '=', $userId];
        $where[] = ['is_delete', '=', 0];

        // 执行查询列表
        $model = new Order();
        $lists = $model->field('id,order_sn,order_type,delivery_type,
            order_status,total_amount,paid_amount,team_found_status,create_time')
            ->where($where)
            ->order('id', 'desc')
            ->with(['orderGoods'=>function($query) { $query->field('id,order_id,name,image,spec_value_str,sell_price,count'); }])
            ->paginate([
                'page'      => $get['page'] ?? 1,
                'list_rows' => $get['limit'] ?? 20,
                'var_page'  => 'page',
            ])->toArray();

        // 允许取消时间(分)
        $customer_cancel_duration = ConfigUtils::get('trade')['customer_cancel_duration'] ?? 0;
        $customer_cancel_duration = $customer_cancel_duration * 60;

        // 获取自动关闭时长(分)
        $automatic_cancel_duration = ConfigUtils::get('trade')['automatic_cancel_duration'] ?? 0;
        $surplus_close_time = $automatic_cancel_duration * 60;

        // 循环处理数据
        foreach ($lists['data'] as &$item) {
            $allowCancelTime  = (strtotime($item['create_time']) + $customer_cancel_duration);
            $surplusCloseTime = (strtotime($item['create_time']) + $surplus_close_time) - time();

            $item['extend'] = [
                'order_type'         => OrderEnum::getOrderTypeDesc($item['order_type']),
                'order_status'       => OrderEnum::getOrderStatusDesc($item['order_status']),
                'is_allow_cancel'    => $allowCancelTime > time() && $item['order_status'] <= 1,
                'surplus_close_time' => $item['order_status'] === OrderEnum::STATUS_WAIT_PAY ?  $surplusCloseTime : -1
            ];

            if ($item['order_status'] == OrderEnum::STATUS_WAIT_DELIVERY &&
                $item['delivery_type'] == OrderEnum::DELIVER_TYPE_STORE) {
                $item['extend']['order_status'] = '待提货';
            }

            if ($item['order_type'] == OrderEnum::TEAM_ORDER) {
                if ($item['team_found_status'] === 1) {
                    $item['extend']['order_status'] = '正在拼团中';
                }
                if ($item['team_found_status'] === 2) {
                    $item['extend']['order_status'] = '拼团成功,'.$item['extend']['order_status'];
                }
            }
        }

        // 选项卡
        $condition = ['user_id'=>$userId, 'is_delete'=>0];
        $lists['tabs'] = [
            ['name'=>'全部',   'count'=> 0],
            ['name'=>'待付款', 'count'=> $model->where(self::queryWhere(1))->where($condition)->count()],
            ['name'=>'待发货', 'count'=> $model->where(self::queryWhere(2))->where($condition)->count()],
            ['name'=>'待收货', 'count'=> $model->where(self::queryWhere(3))->where($condition)->count()],
            ['name'=>'已完成', 'count'=> 0]
        ];

        return $lists;
    }

    /**
     * 创建订单
     *
     * @author windy
     * @param array $post (订单基本参数)
     * @param array $oProducts (订单商品数据)
     * @param int $userId (当前用户ID)
     * @return array|bool
     */
    public static function placeOrder(array $post, array $oProducts, int $userId)
    {
        try {
            $orderStatus = PlaceOrderService::check($post, $oProducts, $userId);
            if (!$orderStatus['pass'] || !$orderStatus['status']) {
                $orderStatus['order_id'] = -1;
                return $orderStatus;
            }

            return PlaceOrderService::createOrder($orderStatus);
        } catch (Exception $e) {
            static::$error = $e->getMessage();
            return false;
        }
    }

    /**
     * 结算详细
     *
     * @author windy
     * @param array $post
     * @param int $userId
     * @return array|bool
     */
    public static function settle(array $post, int $userId)
    {
        try {
            // 是否使用最佳优惠券
            $orderCoupon = CouponLogic::orderCoupon($post, $userId);
            if (!empty($post['coupon_list_id']) and $post['coupon_list_id'] == -1) {
                if (!empty($orderCoupon['usable'])) {
                    $post['coupon_list_id'] = $orderCoupon['usable'][0]['coupon_list_id'];
                    $orderCoupon['usable'][0]['selected'] = true;
                } else {
                    $post['coupon_list_id'] = 0;
                }
            }

            // 收货地址数据
            $userAddress = [];
            if (empty($post['address_id'])) {
                $userAddress = AddressLogic::default($userId);
            } elseif ($post['address_id'] > 0) {
                $userAddress = AddressLogic::getAddressById($post['address_id'], $userId);
            }

            // 自提门店数据
            $userStore = [];
            if (!empty($post['store_id']) && $post['store_id']) {
                $userStore = StoreLogic::detail([
                    'id'        => $post['store_id'],
                    'longitude' => $post['longitude'] ?? 0,
                    'latitude'  => $post['latitude'] ?? 0
                ]);
            }

            // 返回订单信息
            $orderStatus = PlaceOrderService::check($post, $post['products'], $userId);
            $orderStatus['coupon']  = $orderCoupon;
            $orderStatus['address'] = $userAddress;
            $orderStatus['store']   = $userStore;
            $orderStatus['deliveryMethod'] = ConfigUtils::get('trade')['delivery_method'] ?? ['1'];

            return $orderStatus;
        } catch (Exception $e) {
            static::$error = $e->getMessage();
            return false;
        }
    }

    /**
     * 订单详情
     *
     * @author windy
     * @param int $id
     * @param int $userId
     * @return array
     */
    public static function detail(int $id, int $userId): array
    {
        $model = new Order();
        $detail = $model->withoutField('user_id,order_platform,coupon_list_id,transaction_id,is_delete,update_time,delete_time')
            ->with(['orderGoods'=>function($query) { $query->field('id,order_id,goods_id,name,image,spec_value_str,sell_price,count,refund_status'); }])
            ->where([
                ['id', '=', intval($id)],
                ['user_id', '=', intval($userId)]
            ])->findOrEmpty()->toArray();

        // 自提订单获取门店
        if ($detail['delivery_type'] == OrderEnum::DELIVER_TYPE_STORE) {
            $store = (new Store())->where(['id'=>$detail['address_snap']['id']])->findOrEmpty()->toArray();
            $detail['address_snap']['store_name'] = $store['name'];
            $detail['address_snap']['business_hours'] = $store['business_hours'];
        }

        // 获取自动关闭时长(分)
        $automatic_cancel_duration = ConfigUtils::get('trade')['automatic_cancel_duration'] ?? 0;
        $surplus_close_time = (strtotime($detail['create_time']) + ($automatic_cancel_duration * 60)) - time();

        // 获取允许售后时长(天)
        $after_sale_duration = ConfigUtils::get('trade')['after_sale_duration'] ?? 0;
        $after_sale_duration = $after_sale_duration * 86400;

        // 获取允许取消时长(分)
        $customer_cancel_duration = ConfigUtils::get('trade')['customer_cancel_duration'] ?? 0;
        $customer_cancel_duration = $customer_cancel_duration * 60;

        // 获取自动收货时长(天)
        $order_finish_time = '';
        $automatic_finish_duration = ConfigUtils::get('trade')['automatic_finish_duration'] ?? 0;
        $automatic_finish_duration = $automatic_finish_duration * 86400;
        if ($automatic_finish_duration > 0 && $detail['order_status'] == OrderEnum::STATUS_WAIT_RECEIVE) {
            $deliveryTime = strtotime($detail['delivery_time']);
            if ($deliveryTime + $automatic_finish_duration > time()) {
                $surplusTime = $deliveryTime + $automatic_finish_duration - time();
                $date = TimeUtils::formatTime($surplusTime);
                $order_finish_time = '还剩'.$date.'自动确认';
            }
        }

        // 售后产品处理
        foreach ($detail['orderGoods'] as &$item) {
            if ($item['refund_status'] > 0) {
                $item['after_sale_id'] = (new AfterSale())->where(['order_goods_id'=>$item['id']])->value('id');
            }
        }

        // 额外扩展返回参数
        $createTime  = strtotime($detail['create_time'])   ?: 0;
        $deliverTime = strtotime($detail['delivery_time']) ?: 0;
        $detail['extend'] = [
            'pay_way'            => OrderEnum::getPayWayDesc($detail['pay_way']),
            'order_type'         => OrderEnum::getOrderTypeDesc($detail['order_type']),
            'order_status'       => OrderEnum::getOrderStatusDesc($detail['order_status']),
            'order_close_type'   => $automatic_cancel_duration <= 0 ? 0 : 1,                       // 不自动关闭<=0
            'order_close_time'   => $surplus_close_time <= 0 ? -1 : $surplus_close_time,           // 剩余自动关闭时间
            'order_finish_time'  => $order_finish_time,                                            // 自动确认收货文本
            'is_allow_afterSale' => ($deliverTime + $after_sale_duration)     > time() || $detail['order_status'] == 1, // 是否还允许申请售后
            'is_allow_cancel'    => ($createTime + $customer_cancel_duration) > time() && $detail['order_status'] <= 1  // 是否允许整单取消
        ];

        // 超时关闭(防止定任务未执行)
        if ($detail['order_status'] == 0 and $surplus_close_time <= 0) {
            self::end($id, $userId);
        }

        return $detail;
    }

    /**
     * 删除订单
     *
     * @author windy
     * @param $id (订单ID)
     * @param $userId (用户ID)
     * @return false
     */
    public static function del(int $id, int $userId): bool
    {
        try {
            Order::update([
                'is_delete'   => 1,
                'update_time' => time(),
                'delete_time' => time()
            ], ['id'=>$id, 'user_id'=>$userId]);

            return true;
        } catch (Exception $e) {
            static::$error = $e->getMessage();
            return false;
        }
    }

    /**
     * 取消订单
     *
     * @author windy
     * @param int $id (订单ID)
     * @param int $userId (用户ID)
     * @return bool
     */
    public static function cancel(int $id, int $userId): bool
    {
        Db::startTrans();
        try {
            $order = (new Order())->field(true)
                ->where(['id'=>$id, 'user_id'=>$userId])
                ->findOrEmpty()->toArray();

            if (!$order) {
                throw new Exception('订单不存在');
            }

            // 未支付订单: 标记为已关闭
            Order::update([
                'order_status' => OrderEnum::STATUS_CLOSE,
                'cancel_time'   => time(),
                'update_time'   => time()
            ], ['id'=>$id]);

            // 已支付订单: 取消、退款
            if ($order['pay_status'] == OrderEnum::OK_PAID_STATUS) {
                // 更新订单状态为已退款
                Order::update([
                    'refund_status' => OrderEnum::REFUND_STATUS_ALL,
                    'cancel_time'   => time(),
                    'update_time'   => time()
                ], ['id'=>$id]);

                // 订单商品标记为退款成功
                OrderGoods::update([
                    'refund_status' => OrderEnum::REFUND_STATUS_ALL,
                    'update_time'   => time()
                ], ['order_id'=>$id]);

                // 调用退款接口退款
                OrderService::refund($id, $order['paid_amount']);
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
     * 确认收货
     *
     * @author windy
     * @param int $id
     * @param int $userId
     * @return bool
     */
    public static function confirm(int $id, int $userId): bool
    {
        try {
            $order = (new Order())->field(true)
                ->where(['id'=>$id, 'user_id'=>$userId])
                ->findOrEmpty()->toArray();

            if (!$order) {
                throw new Exception('订单不存在');
            }

            if ($order['order_status'] != OrderEnum::STATUS_WAIT_RECEIVE) {
                throw new Exception('订单状态异常');
            }

            if ($order['order_status'] == OrderEnum::STATUS_FINISH) {
                throw new Exception('订单已完成了亲!');
            }

            // 赠送商品积分
            $orderGoods = (new OrderGoods())
                ->alias('OG')
                ->field('OG.id,OG.order_id,OG.goods_id,O.user_id,O.order_sn,OG.snapshot')
                ->join('order O', 'O.id=OG.order_id')
                ->where(['OG.order_id'=>$id])
                ->select()
                ->toArray();

            $totalGiveIntegral = 0;
            foreach ($orderGoods as $item) {
                $integral = intval($item['snapshot']['give_integral'] ?? 0);
                if ($integral) {
                    $totalGiveIntegral += $integral;
                }
            }

            if ($totalGiveIntegral) {
                User::update([
                    'integral'    => ['inc', $totalGiveIntegral],
                    'update_time' => time()
                ], ['id'=>$orderGoods[0]['user_id']]);
                LogIntegral::add(
                    LogIntegralEnum::GOODS_INC_INTEGRAL,
                    $totalGiveIntegral,
                    $orderGoods[0]['user_id'], 0,
                    $orderGoods[0]['order_id'],
                    $orderGoods[0]['order_sn'], '商品赠送积分'
                );
            }

            // 更新订单状态
            Order::update([
                'order_status' => OrderEnum::STATUS_FINISH,
                'confirm_time' => time(),
                'update_time'  => time()
            ], ['id'=>$id]);

            return true;
        } catch (Exception $e) {
            static::$error = $e->getMessage();
            return false;
        }
    }

    /**
     * 关闭超时未支付订单
     *
     * @author windy
     * @param int $id
     * @param int $userId
     * @return bool
     */
    public static function end(int $id, int $userId): bool
    {
        try {
            // 当前执行时间戳
            $time = time();

            // 获取订单自动取消时长
            $automaticCancelDuration = ConfigUtils::get('trade')['automatic_cancel_duration'] ?? 0;
            $automaticCancelDuration = $automaticCancelDuration * 60;

            if ($automaticCancelDuration <= 0) {
                return true;
            }

            // 获取未支付已到结束时间订单
            $order = (new Order())->field(true)
                ->whereRaw("create_time+$automaticCancelDuration < $time")
                ->where([
                    'id'           => $id,
                    'user_id'      => $userId,
                    'order_status' => OrderEnum::STATUS_WAIT_PAY,
                    'pay_status'   => OrderEnum::TO_BE_PAID_STATUS
                ])->findOrEmpty()->toArray();

            if (!$order) {
                return true;
            }

            // 关闭订单
            OrderService::closeOrder($order['id']);

            // 回退库存
            OrderService::rollbackStock($order['id']);

            // 退优惠券
            OrderService::rollbackCoupon($order['coupon_list_id']);

            return true;
        } catch (Exception $e) {
            static::$error = $e->getMessage();
            return false;
        }
    }

    /**
     * 查询物流轨迹
     *
     * @author windy
     * @param int $id (订单ID)
     * @param int $userId (用户ID)
     * @return bool|mixed
     */
    public static function traces(int $id, int $userId)
    {
        try {
            // 获取订单信息
            $order = (new Order())
                ->alias('O')
                ->field(['O.id,O.order_status,O.pay_time,O.confirm_time,O.delivery_time,O.create_time,O.total_num,O.address_snap,OG.image'])
                ->join('orderGoods OG', 'OG.order_id=O.id')
                ->where(['O.id'=>intval($id), 'O.user_id'=>$userId])
                ->findOrEmpty()
                ->toArray();

            // 获取发货信息
            $orderDelivery = (new OrderDelivery())
                ->alias('OD')
                ->field([
                    'OD.id,OD.mobile,OD.express_name,OD.waybill_no',
                    'E.code100,E.codebird'
                ])
                ->join('express E', 'E.id = OD.express_id')
                ->where(['order_id' => intval($id)])
                ->where(['user_id' => $userId])
                ->findOrEmpty()
                ->toArray();

            // 查询物流信息
            if ($order['delivery_time']) {
                $config = ConfigUtils::get('logistics') ?? [];
                $logisticsDriver = new LogisticsDriver($config);
                $codeNo = ($config['way'] ?? '') == 'kd100' ? $orderDelivery['code100'] : $orderDelivery['codebird'];
                $traces = $logisticsDriver->query($codeNo, $orderDelivery['waybill_no'], ['CustomerName' => substr($orderDelivery['mobile'], -4)]);
            }

            $trace = [];
            foreach ($traces ?? [] as $k => $v) {
                $trace[$k] = array_values($v);
            }

            // 物流状态
            $tips = '已下单';
            $finish = [];
            if ($order['order_status'] == OrderEnum::STATUS_WAIT_RECEIVE) {
                $tips = '商品已出库';
                $finish = [
                    'title' => '待签收',
                    'tips'  => '包裹正在运输途中',
                    'time'  => $order['delivery_time']
                ];
            }
            if ($order['order_status'] == OrderEnum::STATUS_FINISH) {
                $tips = '交易完成';
                $finish = [
                    'title' => '交易完成',
                    'tips'  => '订单交易完成',
                    'time'  => $order['confirm_time']
                ];
            }

            return [
                'addr'     => $order['address_snap'],
                'order'    => [
                    'tips'          => $tips,
                    'image'         => $order['image'],
                    'count'         => $order['total_num'],
                    'waybill_no'    => $orderDelivery['waybill_no'],
                    'express_name'  => $orderDelivery['express_name'],
                ],
                'take'     => $order['address_snap'],
                'finish'   => $finish,
                'traces'   => $trace,
                'shipment' => [
                    'title' => '已发货',
                    'tips'  => '包裹正在等待揽件',
                    'time'  => $order['delivery_time']
                ],
                'pay' => [
                    'title' => '已支付',
                    'tips'  => '订单支付成功，等待商家发货',
                    'time'  => $order['pay_time']
                ],
                'buy' => [
                    'title' => '已下单',
                    'tips'  => '订单提交成功',
                    'time'  => $order['create_time']
                ]
            ];

        } catch (Exception $e) {
            static::$error = $e->getMessage();
            return false;
        }
    }
}