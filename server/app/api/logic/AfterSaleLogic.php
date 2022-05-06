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
use app\common\enum\AfterSaleEnum;
use app\common\enum\OrderEnum;
use app\common\model\auth\Admin;
use app\common\model\order\AfterSale;
use app\common\model\order\AfterSaleLog;
use app\common\model\order\Order;
use app\common\model\order\OrderGoods;
use app\common\model\user\User;
use app\common\utils\UrlUtils;
use Exception;

/**
 * 订单售后接口-逻辑层
 * Class AfterSaleLogic
 * @package app\api\logic
 */
class AfterSaleLogic extends Logic
{
    /**
     * 售后列表数据
     *
     * @author windy
     * @param array $get
     * @param int $userId
     * @return array
     * @throws @\think\db\exception\DbException
     */
    public static function lists(array $get, int $userId): array
    {
        $where = [];
        $where[] = ['user_id', '=', $userId];

        if (!empty($get['type']) and $get['type']) {
            switch (intval($get['type'])) {
                case 1: // 处理中
                    $where[] = ['status', 'in', [
                        AfterSaleEnum::STATUS_APPLY_REFUND,
                        AfterSaleEnum::STATUS_WAIT_RETURN_GOODS,
                        AfterSaleEnum::STATUS_WAIT_RECEIVE_GOODS,
                        AfterSaleEnum::STATUS_WAIT_REFUND
                    ]];
                    break;
                case 2: // 已处理
                    $where[] = ['status', 'in', [
                        AfterSaleEnum::STATUS_REFUSE_REFUND,
                        AfterSaleEnum::STATUS_REFUSE_RECEIVE_GOODS,
                        AfterSaleEnum::STATUS_SUCCESS_REFUND,
                        AfterSaleEnum::STATUS_REVOKE_APPLY
                    ]];
                    break;
            }
        }

        $model = new AfterSale();
        $lists = $model->field(true)
            ->where($where)
            ->with(['orderGoods'])
            ->order('id desc')
            ->paginate([
                'page'      => $get['page'] ?? 1,
                'list_rows' => $get['limit'] ?? 20,
                'var_page'  => 'page',
            ])->toArray();

        $data = [];
        foreach ($lists['data'] as $item) {
            $data[] = [
                'id'             => $item['id'],
                'sn'             => $item['sn'],
                'name'           => $item['orderGoods']['name'],
                'image'          => $item['orderGoods']['image'],
                'count'          => $item['orderGoods']['count'],
                'spec_value_str' => $item['orderGoods']['spec_value_str'],
                'refund_price'   => $item['refund_price'],
                'status_text'    => AfterSaleEnum::getStatusDesc($item['status']),
                'status'         => $item['status']
            ];
        }

        $lists['data'] = $data;
        return $lists;
    }

    /**
     * 售后商品信息
     *
     * @author windy
     * @param int $order_goods_id
     * @return array
     */
    public static function goods(int $order_goods_id): array
    {
        $model = new OrderGoods();
        $goods = $model->field('id,name,image,spec_value_str,actual_price,count')
            ->where(['id'=>intval($order_goods_id)])
            ->findOrEmpty()
            ->toArray();

        $reason = AfterSaleEnum::getReasonLists();
        return ['goods'=>$goods, 'reason'=>$reason];
    }

    /**
     * 售后详细
     *
     * @author windy
     * @param int $id 售后ID
     * @return array
     */
    public static function detail(int $id): array
    {
        $detail = (new AfterSale())->field(true)
            ->with(['orderGoods'])
            ->findOrEmpty($id)
            ->toArray();

        unset($detail['orderGoods']['snapshot']);
        $detail['status_text']   = AfterSaleEnum::getStatusDesc($detail['status']);
        $detail['refund_type']   = AfterSaleEnum::getRefundTypeDesc($detail['refund_type']);
        $detail['update_time']   = date('Y年m月d日 H:i', strtotime($detail['update_time']));
        $detail['express_time']  = date('Y-m-d H:i:s', $detail['express_time']);
        $detail['express_image'] = $detail['express_image'] ? UrlUtils::getAbsoluteUrl($detail['express_image']) : '';
        return $detail;
    }

    /**
     * 售后日志
     *
     * @author windy
     * @param int $id 售后ID
     * @param int $userId 用户ID
     * @return array
     * @throws @\think\db\exception\DataNotFoundException
     * @throws @\think\db\exception\DbException
     * @throws @\think\db\exception\ModelNotFoundException
     *
     */
    public static function logs(int $id, int $userId): array
    {
        $lists = (new AfterSaleLog())
            ->field('id,type,operate_id,operate,content,remarks,create_time')
            ->where(['after_sale_id'=>$id])
            ->order('id asc')
            ->select()
            ->toArray();

        $adminModel = new Admin();
        $user = (new User())->field('id,avatar,nickname')->findOrEmpty($userId);
        foreach ($lists as &$item) {
            if (in_array($item['type'], [1, 8])) {
                $item['user'] = $user;
            } else {
                $item['user'] = $adminModel->field('id,avatar,nickname')->findOrEmpty($item['operate_id'])->toArray();
                if (!empty($item['user'])) {
                    $item['user']['nickname'] = '平台商家：' . $item['user']['nickname'];
                } else {
                    $item['user'] = ['avatar'=>'', 'nickname'=>'系统'];
                }
            }
            unset($item['type']);
            unset($item['operate_id']);
        }

        return $lists;
    }

    /**
     * 申请退款
     *
     * @author windy
     * @param array $post 提交参数
     * @param int $userId 用户ID
     * @return bool
     */
    public static function apply(array $post, int $userId): bool
    {
        try {
            $orderGoods = (new OrderGoods())
                ->field('id,order_id,goods_id,sku_id,name,image,spec_value_str,actual_price,count,refund_status')
                ->where(['id' => intval($post['id'])])
                ->findOrEmpty()
                ->toArray();

            if (!$orderGoods) {
                throw new Exception('订单商品异常');
            }

            if ($orderGoods['refund_status'] === 1) {
                throw new Exception('该商品正在售后中,请勿重复操作！');
            }

            if ($orderGoods['refund_status'] === 3) {
                throw new Exception('该商品已退款成功,请勿重复操作！');
            }

            $afterSale = AfterSale::create([
                'sn'             => make_order_no(),
                'user_id'        => $userId,
                'order_id'       => $orderGoods['order_id'],
                'order_goods_id' => $orderGoods['id'],
                'goods_id'       => $orderGoods['goods_id'],
                'sku_id'         => $orderGoods['sku_id'],
                'refund_num'     => $orderGoods['count'],
                'refund_price'   => $orderGoods['actual_price'],
                'refund_type'    => $post['refund_type'],
                'refund_reason'  => $post['reason'],
                'refund_remark'  => $post['remark'] ?? '',
                'refund_image'   => !empty($post['images']) ? UrlUtils::getRelativeUrl($post['images']) : '',
                'status'         => AfterSaleEnum::STATUS_APPLY_REFUND
            ]);

            $type = OrderEnum::REFUND_STATUS_APPLY;
            OrderGoods::update(['refund_status'=>$type, 'update_time'=>time()], ['id'=>intval($post['id'])]);
            Order::update(['is_after_sale'=>1, 'update_time'=>time()], ['id'=>$orderGoods['order_id']]);

            $title = $post['refund_type'] == 1 ? '买家发起仅退款申请' : '买家发起退货退款申请';
            AfterSaleLog::record($afterSale['id'], $type, $userId, $title, '买家申请售后', $post['remark'] ?? '');

            return true;
        } catch (Exception $e) {
            static::$error = $e->getMessage();
            return false;
        }
    }

    /**
     * 撤销申请
     *
     * @author windy
     * @param int $id 售后ID
     * @param int $userId 用户ID
     * @return bool
     */
    public static function revoke(int $id, int $userId): bool
    {
        try {
            $afterSale = (new AfterSale())->field(true)
                ->where(['id'=>intval($id), 'user_id'=>$userId])
                ->findOrEmpty()
                ->toArray();

            if (!$afterSale) {
                throw new Exception('售后订单不存在');
            }

            $type = AfterSaleEnum::STATUS_REVOKE_APPLY;
            AfterSale::update([
                'status'      => $type,
                'update_time' => time()
            ], ['id'=>intval($id)]);

            OrderGoods::update([
                'refund_status' => OrderEnum::REFUND_STATUS_NORMAL,
                'update_time'   => time()
            ], ['id'=>$afterSale['order_goods_id']]);
            Order::update(['is_after_sale'=>0, 'update_time'=>time()], ['id'=>$afterSale['order_id']]);

            AfterSaleLog::record($id, $type, $userId, '买家撤销售后申请', '撤销申请');

            return true;
        } catch (Exception $e) {
            static::$error = $e->getMessage();
            return false;
        }
    }

    /**
     * 上传快递信息
     *
     * @author windy
     * @param array $post 提交参数
     * @param int $userId 用户ID
     * @return bool
     */
    public static function express(array $post, int $userId): bool
    {
        try {
            $id = intval($post['id']);
            $afterSale = (new AfterSale())->field(true)
                ->where(['id'=>$id, 'user_id'=>$userId])
                ->findOrEmpty()
                ->toArray();

            if (!$afterSale) {
                throw new Exception('售后订单不存在');
            }

            $type = AfterSaleEnum::STATUS_WAIT_RECEIVE_GOODS;
            AfterSale::update([
                'express_name'  => $post['express_name'],
                'express_no'    => $post['express_no'],
                'express_image' => empty($post['express_image'][0]) ? UrlUtils::getRelativeUrl($post['express_image'][0]) : '',
                'status'        => AfterSaleEnum::STATUS_WAIT_RECEIVE_GOODS,
                'express_time'  => time(),
                'update_time'   => time()
            ], ['id'=>$id]);

            AfterSaleLog::record($id, $type, $userId, '买家已退货', '包裹已通过快递方式寄出，等待快递运输');

            return true;
        } catch (Exception $e) {
            static::$error = $e->getMessage();
            return false;
        }
    }
}