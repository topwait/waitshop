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

namespace app\admin\logic\order;


use app\common\basics\Logic;
use app\common\enum\OrderEnum;
use app\common\enum\TeamEnum;
use app\common\model\Express;
use app\common\model\order\Order;
use app\common\model\order\OrderDelivery;
use app\common\model\order\OrderGoods;
use app\common\model\Region;
use app\common\model\store\Store;
use app\common\model\store\StoreVerify;
use app\common\service\OrderService;
use Exception;

/**
 * 订单管理-逻辑层
 * Class OrderLogic
 * @package app\admin\logic\order
 */
class OrderLogic extends Logic
{
    /**
     * 订单列表
     *
     * @author windy
     * @param array $post
     * @return array
     * @throws @\think\db\exception\DbException
     */
    public static function lists(array $post): array
    {
        // 搜索条件
        $where = !empty($post['where']) ? $post['where'] : [];
        self::setSearch([
            'keyword' => [
                'orderSn'  => ['%like%', 'order_sn'],
                'userSn'   => ['=', 'U.sn'],
                'contact'  => ['=', 'address_snap->contact'],
                'mobile'   => ['=', 'address_snap->mobile'],
            ]
        ]);

        // 查询数据
        $model = new Order();
        $lists = $model->alias('O')
            ->field('O.*')
            ->join('user U', 'U.id=O.user_id')
            ->where($where)
            ->where(self::$searchWhere)
            ->with(['user', 'orderGoods', 'delivery'])
            ->order('id', 'desc')
            ->paginate([
                'page'      => $post['page'] ?? 1,
                'list_rows' => $post['limit'] ?? 20,
                'var_page'  => 'page',
            ])->toArray();

        // 处理数据
        foreach ($lists['data'] as &$item) {
            $item['order_status_text']  = OrderEnum::getOrderStatusDesc($item['order_status']);
            $item['pay_status_text']    = OrderEnum::getPayStatusDesc($item['order_status']);
            $item['pay_way_text']       = OrderEnum::getPayWayDesc($item['pay_way']);
            $item['order_type_text']    = OrderEnum::getOrderTypeDesc($item['order_type']);
            $item['delivery_type_text'] = OrderEnum::getDeliverTypeDesc($item['delivery_type']);
            if (!empty($item['address_snap'])) {
                $item['address_snap']['address'] = Region::getAddress([
                    'province' => $item['address_snap']['province'],
                    'city'     => $item['address_snap']['city'],
                    'district' => $item['address_snap']['district']]
                ) . $item['address_snap']['address'];
            }

            if ($item['team_found_status']===1) {
                $item['order_status_text']  = TeamEnum::getFoundStatusDesc($item['team_found_status']);
            }
        }

        // 返回结果
        return ['count'=>$lists['total'], 'list'=>$lists['data']];
    }

    /**
     * 订单统计数据
     *
     * @author windy
     * @return array
     */
    public static function statistics(): array
    {
        $model = new Order();

        // 申请退款
        $detail['apply_refund']   = $model->where(['is_after_sale'=>1])->count();
        // 待发货
        $detail['to_be_shipped']  = $model->where(['order_status'=>OrderEnum::STATUS_WAIT_DELIVERY])->count();
        // 待收货
        $detail['to_be_received'] = $model->where(['order_status'=>OrderEnum::STATUS_WAIT_RECEIVE])->count();
        // 待核销
        $detail['to_be_verify']   = (new Order())->where([
                'pick_up_status' => 0,
                'delivery_type'  => OrderEnum::DELIVER_TYPE_STORE,
                'order_status'   => OrderEnum::STATUS_WAIT_DELIVERY
            ])->count();

        return $detail;
    }

    /**
     * 订单详细
     *
     * @author windy
     * @param int $order_id (订单ID)
     * @return array
     */
    public static function detail(int $order_id): array
    {
        // 查询数据
        $model = new Order();
        $detail = $model->field(true)
            ->where('id', '=', (int)$order_id)
            ->with(['orderGoods', 'delivery'])
            ->findOrEmpty()->toArray();

        // 处理数据
        $detail['order_type_text']    = OrderEnum::getOrderTypeDesc($detail['order_type']);
        $detail['order_status_text']  = OrderEnum::getOrderStatusDesc($detail['order_status']);
        $detail['pay_status_text']    = OrderEnum::getPayStatusDesc($detail['order_status']);
        $detail['pay_way_text']       = OrderEnum::getPayWayDesc($detail['pay_way']);
        $detail['delivery_type_text'] = OrderEnum::getDeliverTypeDesc($detail['delivery_type']);
        if ($detail['delivery_type'] == OrderEnum::DELIVER_TYPE_STORE) {
            $detail['store_name'] = (new Store())->where(['id'=>$detail['pick_up_store']])->value('name');
        }

        if ($detail['team_found_status']===1) {
            $detail['order_status_text']  = TeamEnum::getFoundStatusDesc($detail['team_found_status']);
        }

        // 返回结果
        return $detail;
    }

    /**
     * 发货配送
     *
     * @author windy
     * @param array $post
     * @return bool
     */
    public static function deliver(array $post): bool
    {
        try {
            $time = time();
            $order = (new Order())->findOrEmpty((int)$post['id'])->toArray();
            $express = (new Express())->findOrEmpty((int)$post['express_id'])->toArray();

            Order::update([
                'order_status'  => OrderEnum::STATUS_WAIT_RECEIVE,
                'delivery_type' => $post['delivery_type'],
                'delivery_time' => $time
            ], ['id'=>$order['id']]);

            if ($post['delivery_type'] == 1) {
                OrderDelivery::create([
                    'user_id'       => $order['user_id'],
                    'order_id'      => $order['id'],
                    'order_sn'      => $order['order_sn'],
                    'contact'       => $order['address_snap']['contact'],
                    'mobile'        => $order['address_snap']['mobile'],
                    'province_id'   => $order['address_snap']['province'],
                    'city_id'       => $order['address_snap']['city'],
                    'district_id'   => $order['address_snap']['district'],
                    'address'       => $order['address_snap']['address'],
                    'express_id'    => $express['id'],
                    'express_name'  => $express['name'],
                    'waybill_no'    => $post['waybill_no'],
                    'delivery_type' => $post['delivery_type'],
                ]);
            }

            return true;
        } catch (Exception $e) {
            static::$error = $e->getMessage();
            return false;
        }
    }

    /**
     * 确定核销订单
     *
     * @author windy
     * @param int $id (订单ID)
     * @param int $adminId (管理员ID)
     * @return bool
     */
    public static function pickUp(int $id, int $adminId): bool
    {
        try {
            $order = (new Order())->findOrEmpty(intval($id))->toArray();

            if (!$order) {
                throw new Exception('核销订单异常,请刷新后再试');
            }

            if ($order['pay_status'] == 0) {
                throw new Exception('订单尚未付款,无法进行核销');
            }

            if ($order['pick_up_status'] != 0) {
                throw new Exception('订单已被核销,请务重复操作');
            }

            Order::update([
                'pick_up_status' => 1,
                'update_time'    => time()
            ]);

            StoreVerify::create([
                'operate'  => 2,
                'store_id' => $order['pick_up_store'],
                'staff_id' => $adminId,
                'order_id' => $id
            ]);

            return true;
        } catch (Exception $e) {
            static::$error = $e->getMessage();
            return false;
        }
    }

    /**
     * 取消订单(已支付订单)
     *
     * @author windy
     * @param int $id (订单ID)
     * @return bool
     */
    public static function cancel(int $id): bool
    {
        try {
            $order = (new Order())->findOrEmpty(intval($id))->toArray();

            if (!$order) {
                throw new Exception('订单不存在');
            }

            if ($order['is_after_sale']) {
                throw new Exception('订单正在售后不允许取消');
            }

            if ($order['refund_status'] !== 0) {
                throw new Exception('参与过售后的订单不允许取消');
            }

            // 更新订单状态
            Order::update([
                'order_status'  => OrderEnum::STATUS_CLOSE,
                'refund_status' => OrderEnum::REFUND_STATUS_ALL,
                'cancel_time'   => self::reqTime(),
                'update_time'   => self::reqTime()
            ], ['id'=>intval($id)]);

            // 更新订单商品状态
            OrderGoods::update([
                'refund_status' => OrderEnum::REFUND_STATUS_ALL,
                'update_time'   => self::reqTime()
            ], ['order_id'=>intval($id)]);

            // 发起退款请求
            OrderService::refund(intval($id), $order['paid_amount']);

            return true;
        } catch (Exception $e) {
            static::$error = $e->getMessage();
            return false;
        }
    }

    /**
     * 关闭订单(未支付订单)
     *
     * @author windy
     * @param int $id (订单ID)
     * @return bool
     */
    public static function close(int $id): bool
    {
        try {
            // 更新订单状态
            Order::update([
                'order_status'  => OrderEnum::STATUS_CLOSE,
                'cancel_time'   => self::reqTime(),
                'update_time'   => self::reqTime()
            ], ['id'=>intval($id)]);

            return true;
        } catch (Exception $e) {
            static::$error = $e->getMessage();
            return false;
        }
    }
}