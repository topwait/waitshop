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
use app\common\enum\OrderEnum;
use app\common\model\auth\Admin;
use app\common\model\log\LogOrder;
use app\common\model\order\Order;
use app\common\model\Region;
use app\common\model\store\Store;
use app\common\model\store\StoreClerk;
use app\common\model\store\StoreVerify;
use app\common\utils\TimeUtils;
use Exception;

/**
 * 门店接口-逻辑层
 * Class StoreLogic
 * @package app\api\logic
 */
class StoreLogic extends Logic
{
    /**
     * 门店列表
     *
     * @author windy
     * @param array $get
     * @return array
     * @throws @\think\db\exception\DbException
     */
    public static function lists(array $get): array
    {
        list($longitude, $latitude) = wgs84_to_bd09($get['longitude'] ?? '113.32606', $get['latitude']  ?? '23.043445');
        $distance = "CEILING(SQRT(POWER(longitude-{$longitude},2)+POWER(latitude-{$latitude},2))*100000)";

        $model = new Store();
        $lists = $model->field(['id,name,logo,mobile,business_hours,province_id,city_id,district_id,address', $distance=>'distance'])
            ->where(['status'=>1, 'is_delete'=>0])
            ->order(['distance'=>'asc'])
            ->paginate([
                'page'      => $get['page'] ?? 1,
                'list_rows' => $get['limit'] ?? 20,
                'var_page'  => 'page'
            ])->toArray();

        foreach ($lists['data'] as &$item) {
            $item['distance'] = $item['distance'] >= 1000
                ? (round($item['distance'] / 1000, 2)).'km'
                : $item['distance'].'m';

            $item['region'] = Region::getAddress([
                    'province' => $item['province_id'],
                    'city'     => $item['city_id'],
                    'district' => $item['district_id'],
                ]);

            unset($item['province_id']);
            unset($item['city_id']);
            unset($item['district_id']);
        }

        return $lists;
    }

    /**
     * 门店详细数据
     *
     * @author windy
     * @param array $get
     * @return array
     * @throws @\think\db\exception\DataNotFoundException
     * @throws @think\db\exception\DbException
     * @throws @\think\db\exception\ModelNotFoundException
     */
    public static function detail(array $get)
    {
        $shopId = intval($get['id']);
        list($longitude, $latitude) = wgs84_to_bd09($get['longitude'] ?? '113.32606', $get['latitude']  ?? '23.043445');
        $distance  = "CEILING(SQRT(POWER(longitude-{$longitude},2)+POWER(latitude-{$latitude},2))*100000)";

        $model = new Store();
        $detail = $model->field([
                'id,name,logo,mobile,business_hours,province_id',
                'city_id,district_id,address,longitude,latitude,status',
                $distance=>'distance'
            ])
            ->where(['status'=>1, 'is_delete'=>0])
            ->findOrEmpty($shopId)->toArray();

        list($lng, $lat) = db09_to_gcj02($detail['longitude'], $detail['latitude']);
        $detail['longitude'] = $lng;
        $detail['latitude']  = $lat;

        $detail['distance'] = $detail['distance'] >= 1000
                ? (round($detail['distance'] / 1000, 2)).'km'
                : $detail['distance'].'m';

        $detail['region'] = Region::getAddress([
                'province' => $detail['province_id'],
                'city'     => $detail['city_id'],
                'district' => $detail['district_id'],
            ]);

        return $detail;
    }

    /**
     * 核销订单主页
     *
     * @author windy
     * @param int $userId
     * @return array
     * @throws @\think\db\exception\DataNotFoundException
     * @throws @\think\db\exception\DbException
     * @throws @\think\db\exception\ModelNotFoundException
     */
    public static function writeOffIndex(int $userId): array
    {
        $storeId = (new StoreClerk())
                ->where(['is_delete'=>0])
                ->where(['user_id'=>$userId])
                ->value('store_id') ?? 0;

        if (!$storeId) {
            throw new Exception('您已不是门店员工');
        }

        $store = (new Store())
            ->field('id,name,logo,mobile,status,address,province_id,province_id,city_id,district_id')
            ->where(['id'=>$storeId])
            ->where(['is_delete'=>0])
            ->findOrEmpty()
            ->toArray();

        if (!$store) {
            throw new Exception('所在门店已关闭');
        }

        if (!$store['status']) {
            throw new Exception('所在门店已停用');
        }

        // 已核销订单数
        $orderModel = new Order();
        $detail['okOrderNum'] = $orderModel->where([
            'delivery_type'  => OrderEnum::DELIVER_TYPE_STORE,
            'pick_up_store'  => $store['id'],
            'pick_up_status' => 1
        ])->count();

        // 未核销订单数
        $orderModel = new Order();
        $detail['noOrderNum'] = $orderModel->where([
            'delivery_type'  => OrderEnum::DELIVER_TYPE_STORE,
            'pick_up_store'  => $store['id'],
            'pick_up_status' => 0
        ])->count();

        // 本月核销订单数
        $orderModel = new Order();
        $detail['monthOrderNum'] = $orderModel
            ->where('delivery_time', '>=', TimeUtils::month()[0])
            ->where('delivery_time', '<=', TimeUtils::month()[1])
            ->where([
                'delivery_type'  => OrderEnum::DELIVER_TYPE_STORE,
                'pick_up_store'  => $store['id'],
                'pick_up_status' => 0
            ])->count();

        // 门店地区
        $store['region'] = Region::getAddress([
            'province' => $store['province_id'],
            'city'     => $store['city_id'],
            'district' => $store['district_id'],
        ]);

        unset($store['province_id']);
        unset($store['city_id']);
        unset($store['district_id']);

        $detail['store'] = $store;
        return $detail;
    }

    /**
     * 待核销订单
     *
     * @author windy
     * @param array $get
     * @param int $userId
     * @return array
     * @throws @\think\db\exception\DbException
     */
    public static function writeOffOrder(array $get, int $userId): array
    {
        $storeId = (new StoreClerk())
            ->where(['is_delete'=>0])
            ->where(['user_id'=>$userId])
            ->value('store_id') ?? 0;

        if (!$storeId) {
            return [];
        }

        return (new Order())
            ->field('id,order_sn,order_type,pick_up_code,total_amount,paid_amount,create_time')
            ->where([
                'delivery_type'  => OrderEnum::DELIVER_TYPE_STORE,
                'order_status'   => OrderEnum::STATUS_WAIT_DELIVERY,
                'pay_status'     => OrderEnum::OK_PAID_STATUS,
                'pick_up_store'  => $storeId,
                'pick_up_status' => 0
            ])
            ->order('id', 'desc')
            ->with(['orderGoods'=>function($query) {
                $query->field('id,order_id,name,image,spec_value_str,sell_price,count');
            }])
            ->paginate([
                'page'      => $get['page'] ?? 1,
                'list_rows' => $get['limit'] ?? 20,
                'var_page'  => 'page',
            ])->toArray();
    }

    /**
     * 核销详情
     *
     * @param string $code
     * @param int $userId
     * @return array
     * @throws Exception
     * @author windy
     */
    public static function writeOffDetail(string $code, int $userId): array
    {
        $storeId = (new StoreClerk())
                ->where(['is_delete'=>0])
                ->where(['user_id'=>$userId])
                ->value('store_id') ?? 0;

        if (!$storeId) {
            throw new Exception('你当前已不是门店员工了');
        }

        $order = (new Order())
            ->field('id,order_sn,order_type,order_status,total_num,pick_up_status,
                is_after_sale,total_amount,paid_amount,create_time,address_snap')
            ->where(['pick_up_code'=>$code])
            ->with(['orderGoods'=>function($query) {
                $query->field('id,order_id,goods_id,name,image,spec_value_str,sell_price,count');
            }])
            ->findOrEmpty()
            ->toArray();

        if (!$order) {
            throw new Exception('订单不存在');
        }

        if ($order['pick_up_status']) {
            $verify = (new StoreVerify())
                ->where(['order_id'=>$order['id']])
                ->findOrEmpty()
                ->toArray();

            if ($verify) {
                $order['operate']     = '未知';
                $order['verify_time'] = $verify['create_time'];
                if ($verify['operate'] === 1) {
                    $nickname = (new StoreClerk())->where(['id'=>$verify['staff_id']])->value('nickname') ?? '未知';
                    $order['operate'] = '员工：'.$nickname;
                } else {
                    $nickname = (new Admin())->where(['id'=>$verify['staff_id']])->value('username') ?? '未知';
                    $order['operate'] = '系统：'.$nickname;
                }
            }
        }

        $order['order_type']   = OrderEnum::getOrderTypeDesc($order['order_type']);
        $order['order_status'] = OrderEnum::getOrderStatusDesc($order['order_status']);
        return $order;
    }

    /**
     * 核销记录
     *
     * @author windy
     * @param array $get
     * @param int $userId
     * @return array
     */
    public static function writeOffRecord(array $get, int $userId): array
    {
        $storeId = (new StoreClerk())
                ->where(['is_delete'=>0])
                ->where(['user_id'=>$userId])
                ->value('store_id') ?? 0;

        if (!$storeId) {
            return [];
        }

        $adminModel      = new Admin();
        $storeClerkModel = new StoreClerk();

        return (new StoreVerify())
            ->alias('SV')
            ->field('SV.id,SV.operate,SV.staff_id,SV.order_id,O.order_sn,
                O.pick_up_code,O.order_type,O.total_amount,O.paid_amount,O.create_time')
            ->where([
                'O.delivery_type'  => OrderEnum::DELIVER_TYPE_STORE,
                'O.pick_up_store'  => $storeId,
                'O.pick_up_status' => 1
            ])
            ->order('id', 'desc')
            ->join('order O', 'O.id = SV.order_id')
            ->with(['orderGoods'=>function($query) {
                $query->field('id,order_id,name,image,spec_value_str,sell_price,count');
            }])
            ->withAttr(['operate' => function($value, $data) use($adminModel, $storeClerkModel) {
                if ($value == 1) {
                    $nickname = $storeClerkModel->where(['id'=>$data['staff_id']])->value('nickname') ?? '未知';
                    return '员工：'.$nickname;
                } else {
                    $nickname = $adminModel->where(['id'=>$data['staff_id']])->value('username') ?? '未知';
                    return '系统：'.$nickname;
                }
            }])
            ->paginate([
                'page'      => $get['page'] ?? 1,
                'list_rows' => $get['limit'] ?? 20,
                'var_page'  => 'page',
            ])->toArray();
    }

    /**
     * 订单核销确认
     *
     * @author windy
     * @param int $orderId
     * @param int $userId
     * @return bool
     */
    public static function writeOffSubmit(int $orderId, int $userId): bool
    {
        try {
            $storeId = (new StoreClerk())
                    ->where(['is_delete' => 0])
                    ->where(['user_id' => $userId])
                    ->value('store_id') ?? 0;

            if (!$storeId) {
                throw new Exception('你当前已不是门店员工了');
            }

            $order = (new Order())
                ->field('id,order_status,pick_up_store,pick_up_status,is_after_sale')
                ->where(['id'=>$orderId])
                ->findOrEmpty()
                ->toArray();

            if (!$order) {
                throw new Exception('订单不存在');
            }

            if ($order['pick_up_status']) {
                throw new Exception('订单已核销,无需重复操作');
            }

            if ($order['is_after_sale']) {
                throw new Exception('订单正在售后中,不能核销');
            }

            if ($order['order_status'] != OrderEnum::STATUS_WAIT_DELIVERY) {
                throw new Exception('订单状态异常');
            }

            Order::update([
                'order_status'   => OrderEnum::STATUS_FINISH,
                'pick_up_status' => 1,
                'delivery_time'  => time(),
                'confirm_time'   => time(),
                'update_time'    => time()
            ], ['id'=>$orderId]);

            StoreVerify::create([
                'operate' => 1, // 1=员工, 2=后台
                'store_id' => $order['pick_up_store'],
                'order_id' => $order['id'],
                'staff_id' => $userId,
                'create_time' => time()
            ]);

            return true;
        } catch (Exception $e) {
            static::$error = $e->getMessage();
            return false;
        }
    }
}