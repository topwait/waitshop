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
use app\common\enum\AfterSaleEnum;
use app\common\enum\OrderEnum;
use app\common\model\order\AfterSale;
use app\common\model\order\AfterSaleLog;
use app\common\model\order\Order;
use app\common\model\order\OrderGoods;
use app\common\service\OrderService;
use Exception;
use think\facade\Db;

/**
 * 订单售后-逻辑层
 * Class AfterSaleLogic
 * @package app\admin\logic\order
 */
class AfterSaleLogic extends Logic
{
    /**
     * 售后列表
     *
     * @author windy
     * @param array $get
     * @return array|bool
     * @throws @\think\db\exception\DbException
     */
    public static function lists(array $get) :array
    {
        self::setSearch([
            '=' => ['refund_type@AS.refund_type', 'type@AS.status'],
            'datetime' => ['datetime@AS.create_time'],
            'keyword'  => [
                'mobile'   => ['=', 'U.mobile'],
                'userSn'   => ['%like%', 'U.sn'],
                'refundSn' => ['%like%', 'AS.sn'],
                'orderSn'  => ['%like%', 'O.order_sn'],
            ]
        ]);

        $model = new AfterSale();
        $lists = $model->field('AS.*')
            ->alias('AS')
            ->where(self::$searchWhere)
            ->order('id', 'desc')
            ->join('user U', 'U.id = AS.user_id')
            ->join('order O', 'O.id = AS.order_id')
            ->with(['user', 'orders', 'orderGoods'])
            ->paginate([
                'page'      => $get['page'] ?? 1,
                'list_rows' => $get['limit'] ?? 20,
                'var_page'  => 'page'
            ])->toArray();

        foreach ($lists['data'] as &$item) {
            $item['orders']['pay_way']      = OrderEnum::getPayStatusDesc($item['orders']['pay_way']);
            $item['orders']['order_status'] = OrderEnum::getOrderStatusDesc($item['orders']['order_status']);
            $item['refund_type']            = AfterSaleEnum::getRefundTypeDesc($item['refund_type']);
            $item['status']                 = AfterSaleEnum::getStatusDesc($item['status']);
        }

        return ['count'=>$lists['total'], 'list'=>$lists['data']];
    }

    /**
     * 售后统计
     *
     * @author windy
     * @return array
     */
    public static function statistics(): array
    {
        $model = new AfterSale();
        $detail['applyRefund']        = $model->where(['status'=>AfterSaleEnum::STATUS_APPLY_REFUND])->count();
        $detail['refuseGoods']        = $model->where(['status'=>AfterSaleEnum::STATUS_REFUSE_REFUND])->count();
        $detail['waitReturnGoods']    = $model->where(['status'=>AfterSaleEnum::STATUS_WAIT_RETURN_GOODS])->count();
        $detail['waitReceiveGoods']   = $model->where(['status'=>AfterSaleEnum::STATUS_WAIT_RECEIVE_GOODS])->count();
        $detail['refuseReceiveGoods'] = $model->where(['status'=>AfterSaleEnum::STATUS_REFUSE_RECEIVE_GOODS])->count();
        $detail['refuseRefund']       = $model->where(['status'=>AfterSaleEnum::STATUS_WAIT_REFUND])->count();
        $detail['successRefund']      = $model->where(['status'=>AfterSaleEnum::STATUS_SUCCESS_REFUND])->count();
        return $detail;
    }

    /**
     * 售后详细
     *
     * @author windy
     * @param int $id (售后ID)
     * @return array
     */
    public static function detail(int $id): array
    {
        $model = new AfterSale();
        $detail = $model->field(true)
            ->with(['user', 'orders.delivery', 'orderGoods'])
            ->findOrEmpty($id)->toArray();

        $detail['orders']['pay_way']            = OrderEnum::getPayWayDesc($detail['orders']['pay_way']);
        $detail['orders']['order_type']         = OrderEnum::getOrderTypeDesc($detail['orders']['order_type']);
        $detail['orders']['order_status']       = OrderEnum::getOrderStatusDesc($detail['orders']['order_status']);
        $detail['orders']['delivery_type_text'] = OrderEnum::getDeliverTypeDesc($detail['orders']['delivery_type']);
        $detail['refund_type_text']             = AfterSaleEnum::getRefundTypeDesc($detail['refund_type']);
        $detail['status_text']                  = AfterSaleEnum::getStatusDesc($detail['status']);

        return $detail;
    }

    /**
     * 售后日志
     *
     * @author windy
     * @param int $id (售后ID)
     * @return array
     * @throws @\think\db\exception\DataNotFoundException
     * @throws @\think\db\exception\DbException
     * @throws @\think\db\exception\ModelNotFoundException
     */
    public static function log(int $id): array
    {
        $model = new AfterSaleLog();
        return $model->field(true)
            ->order('id asc')
            ->where(['after_sale_id'=>intval($id)])
            ->select()
            ->toArray();
    }

    /**
     * 同意申请
     *
     * @author windy
     * @param array $post (参数)
     * @param int $adminId (管理员ID)
     * @return bool
     */
    public static function agree(array $post, int $adminId): bool
    {
        try {
            $id = intval($post['id']);
            $afterSale = (new AfterSale())->findOrEmpty($id)->toArray();

            if (!$afterSale) {
                throw new Exception('售后订单不存在');
            }

            if ($afterSale['status'] != AfterSaleEnum::STATUS_APPLY_REFUND) {
                throw new Exception('该售后订单已被处理');
            }

            if ($afterSale['status'] == AfterSaleEnum::STATUS_REVOKE_APPLY) {
                throw new Exception('用户已撤销售后申请');
            }

            switch ($afterSale['refund_type']) {
                // 仅退款
                case AfterSaleEnum::TYPE_ONLY_REFUND:
                    $type = AfterSaleEnum::STATUS_WAIT_REFUND;
                    AfterSale::update([
                        'status'      => $type,
                        'update_time' => time()
                    ], ['id'=>$id]);
                    AfterSaleLog::record($id, $type, $adminId, '卖家已同意了申请', '等待商家确认退款', $post['reason']??'');
                    break;
                // 退货退款
                case AfterSaleEnum::TYPE_REFUND_RETURN:
                    $type = AfterSaleEnum::STATUS_WAIT_RETURN_GOODS;
                    AfterSale::update([
                        'status'      => AfterSaleEnum::STATUS_WAIT_RETURN_GOODS,
                        'update_time' => time()
                    ], ['id'=>$id]);
                    AfterSaleLog::record($id, $type, $adminId, '买家已同意了申请' ,'等待买家退回货物', $post['reason']??'');
                    break;
            }

            return true;
        } catch (Exception $e) {
            static::$error = $e->getMessage();
            return false;
        }
    }

    /**
     * 拒绝退款
     *
     * @author windy
     * @param array $post (参数)
     * @param int $adminId (管理员ID)
     * @return bool
     */
    public static function refuse(array $post, int $adminId): bool
    {
        try {
            $id = intval($post['id']);
            $afterSale = (new AfterSale())->findOrEmpty($id)->toArray();

            if (!$afterSale) {
                throw new Exception('售后订单不存在');
            }

            if ($afterSale['status'] != AfterSaleEnum::STATUS_APPLY_REFUND) {
                throw new Exception('该售后订单已被处理');
            }

            if ($afterSale['status'] == AfterSaleEnum::STATUS_REVOKE_APPLY) {
                throw new Exception('用户已撤销售后申请');
            }

            AfterSale::update([
                'status'      => AfterSaleEnum::STATUS_REFUSE_REFUND,
                'update_time' => time()
            ], ['id'=>$id]);

            OrderGoods::update([
                'refund_status' => OrderEnum::REFUND_STATUS_NORMAL,
                'update_time'   => time()
            ], ['id'=>$afterSale['order_goods_id']]);

            Order::update([
                'is_after_sale' => 0,
                'update_time'   => time()
            ], ['id'=>$afterSale['order_id']]);

            $type = AfterSaleEnum::STATUS_REFUSE_REFUND;
            AfterSaleLog::record($id, $type, $adminId, '商家拒绝退款', '商家驳回售后申请', $post['reason']??'');

            return true;
        } catch (Exception $e) {
            static::$error = $e->getMessage();
            return false;
        }
    }

    /**
     * 商家确认收货
     *
     * @author windy
     * @param array $post
     * @param int $adminId (管理员ID)
     * @return bool
     */
    public static function takeGoods(array $post, int $adminId): bool
    {
        try {
            $id = intval($post['id']);
            $afterSale = (new AfterSale())->findOrEmpty($id)->toArray();

            if (!$afterSale) {
                throw new Exception('售后订单不存在');
            }

            if ($afterSale['status'] != AfterSaleEnum::STATUS_WAIT_RECEIVE_GOODS) {
                throw new Exception('售后状态不是待收货状态');
            }

            if ($afterSale['status'] == AfterSaleEnum::STATUS_REVOKE_APPLY) {
                throw new Exception('用户已撤销售后申请');
            }

            $type = AfterSaleEnum::STATUS_WAIT_REFUND;
            AfterSale::update([
                'status'      => $type,
                'update_time' => time()
            ], ['id' => $id]);

            AfterSaleLog::record($id, $type, $adminId, '商家确认签收', '已确认收到退货，等待商家退款', trim($post['reason'] ?? ''));

            return true;
        } catch (Exception $e) {
            static::$error = $e->getMessage();
            return false;
        }
    }

    /**
     * 商家拒绝收货
     *
     * @author windy
     * @param array $post
     * @param int $adminId (管理员ID)
     * @return bool
     */
    public static function refuseGoods(array $post, int $adminId): bool
    {
        try {
            $id = intval($post['id']);
            $afterSale = (new AfterSale())->findOrEmpty($id)->toArray();

            if (!$afterSale) {
                throw new Exception('售后订单不存在');
            }

            if ($afterSale['status'] != AfterSaleEnum::STATUS_WAIT_RECEIVE_GOODS) {
                throw new Exception('售后状态不是待收货状态');
            }

            if ($afterSale['status'] == AfterSaleEnum::STATUS_REVOKE_APPLY) {
                throw new Exception('用户已撤销售后申请');
            }

            $type = AfterSaleEnum::STATUS_REFUSE_RECEIVE_GOODS;
            AfterSale::update([
                'status'      => $type,
                'update_time' => time()
            ], ['id' => $id]);

            OrderGoods::update([
                'refund_status' => OrderEnum::REFUND_STATUS_NORMAL,
                'update_time'   => time()
            ], ['id'=>$afterSale['order_goods_id']]);

            Order::update([
                'is_after_sale' => 0,
                'update_time'   => time()
            ], ['id'=>$afterSale['order_id']]);

            AfterSaleLog::record($id, $type, $adminId, '商家拒绝签', '商家拒签了退回货物，如有疑问请联系客服', trim($post['reason']??''));

            return true;
        } catch (Exception $e) {
            static::$error = $e->getMessage();
            return false;
        }
    }

    /**
     * 确认发起退款
     *
     * @author windy
     * @param array $post
     * @param int $adminId
     * @return bool
     * @throws Exception
     */
    public static function confirmRefund(array $post, int $adminId): bool
    {
        Db::startTrans();
        try {
            $id = intval($post['id']);
            $afterSale = (new AfterSale())->findOrEmpty($id)->toArray();

            if (!$afterSale) {
                throw new Exception('售后订单不存在');
            }

            if ($afterSale['status'] != AfterSaleEnum::STATUS_WAIT_REFUND) {
                throw new Exception('售后状态不是待确认退款状态');
            }

            if ($afterSale['status'] == AfterSaleEnum::STATUS_REVOKE_APPLY) {
                throw new Exception('用户已撤销售后申请');
            }

            $type = AfterSaleEnum::STATUS_SUCCESS_REFUND;
            AfterSale::update([
                'status'      => $type,
                'update_time' => time()
            ], ['id' => $id]);

            // 是否全部退款
            $orderNum = (new Order())->findOrEmpty($afterSale['order_id'])->value('total_num') ?? 0;
            $afterNum = (new AfterSale())->where(['order_id'=>$id, 'status'=>AfterSaleEnum::STATUS_SUCCESS_REFUND])->count();
            $isAllRefund = $orderNum == $afterNum ? 1 : 2; // 1=全部退款, 2=部分退款

            $orderUpdate = [
                'refund_status' => $isAllRefund,
                'refund_amount' => ['inc', $afterSale['refund_price']],
                'is_after_sale' => 0,
                'update_time'   => time()
            ];
            if ($isAllRefund===1) { $orderUpdate['order_status'] = OrderEnum::STATUS_CLOSE; }
            Order::update($orderUpdate, ['id'=>$afterSale['order_id']]);

            AfterSaleLog::record($id, $type, $adminId, '商家确认退款', '退款成功', trim($post['reason'] ??''));
            OrderService::refund($afterSale['order_id'], $afterSale['refund_price']);

            Db::commit();
            return true;
        } catch (Exception $e) {
            Db::rollback();
            static::$error = $e->getMessage();
            return false;
        }
    }
}