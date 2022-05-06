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

namespace app\api\service;


use app\api\logic\AddressLogic;
use app\api\logic\StoreLogic;
use app\common\enum\CouponEnum;
use app\common\enum\OrderEnum;
use app\common\enum\TeamEnum;
use app\common\model\addons\CouponList;
use app\common\model\addons\Team;
use app\common\model\addons\TeamFound;
use app\common\model\addons\TeamJoin;
use app\common\model\addons\TeamSku;
use app\common\model\order\Order;
use app\common\model\order\OrderGoods;
use app\common\model\Region;
use app\common\utils\UrlUtils;
use think\Exception;
use think\facade\Db;

/**
 * 拼团下单服务类
 * 注意：当前只支持单个商品的下单,不支持多个商品
 * Class TeamOrderServer
 * @package app\api\service
 */
class TeamOrderServer
{
    protected static $post;         // 订单基础参数
    protected static $oProducts;    // 客户端传递过来的商品数组
    protected static $productsSku;  // 数据库查出来的SKU商品数据
    protected static $user_id;      // 当前用户ID

    /**
     * 验证下单的商品状态
     * @author windy
     * @param array $post
     * @param array $oProducts
     * @param int $userId
     * @return array
     * @throws \Exception
     */
    public static function check(array $post, array $oProducts, int $userId): array
    {
        try {
            // 1、接收参数
            self::$post        = $post;
            self::$user_id     = $userId;
            self::$oProducts   = $oProducts;
            self::$productsSku = self::getProductsSkuByOrder($oProducts);

            // 2、验证拼团
            self::checkTeamStatus($userId, $post['team_id'] ?? 0, $post['found_id'] ?? 0);

            // 3、返回数据
            return self::getOrderStatus();
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }

    /**
     * 获取收货地址信息
     * @author windy
     * @return array
     * @throws \Exception
     */
    public static function getAddress(): array
    {
        $data = [];
        switch (self::$post['delivery_type']) {
            case OrderEnum::DELIVER_TYPE_EXPRESS:
                $address = AddressLogic::getAddressById(self::$post['address_id'], self::$user_id);
                if (!$address) {
                    throw new Exception('请选择收货地址');
                }
                $data = [
                    'id'       => $address['id'],
                    'contact'  => $address['nickname'],
                    'mobile'   => $address['mobile'],
                    'province' => $address['province_id'],
                    'city'     => $address['city_id'],
                    'district' => $address['district_id'],
                    'region'   => Region::getAddress([
                        'province' => $address['province_id'],
                        'city'     => $address['city_id'],
                        'district' => $address['district_id'],
                    ]),
                    'address'  => $address['address']
                ];
                break;
            case OrderEnum::DELIVER_TYPE_STORE:
                $store = StoreLogic::detail([
                    'id'        => self::$post['store_id'],
                    'longitude' => self::$post['longitude'] ?? 0,
                    'latitude'  => self::$post['longitude'] ?? 0,
                ]);
                if (!$store) { throw new Exception('请选择收货门店'); }
                if ($store['status'] == 0) { throw new Exception('当前门店已暂停营业');}
                $data = [
                    'id'       => $store['id'],
                    'contact'  => self::$post['contact'],
                    'mobile'   => self::$post['mobile'],
                    'province' => $store['province_id'],
                    'city'     => $store['city_id'],
                    'district' => $store['district_id'],
                    'region'   => Region::getAddress([
                        'province' => $store['province_id'],
                        'city'     => $store['city_id'],
                        'district' => $store['district_id'],
                    ]),
                    'address'  => $store['address']
                ];
                break;
        }

        return $data;
    }

    /**
     * 查询真实拼团商品数据
     * @author windy
     * @param array $oProducts
     * @return array
     */
    private static function getProductsSkuByOrder(array $oProducts): array
    {
        $where = [];
        foreach ($oProducts as $item) {
            $where[] = array(
                ['TS.team_id', '=',  intval(self::$post['team_id'])],
                ['TS.sku_id',   '=', intval($item['sku_id'])],
                ['TS.goods_id' ,'=', intval($item['goods_id'])]
            );
        }

        return (new TeamSku())->alias('TS')
            ->field([
                'GS.id,TS.team_id,GS.goods_id,GS.spec_value_ids,GS.spec_value_str,GS.image',
                'TS.team_price as sell_price,GS.sell_price as cost_price,TS.sales_volume',
                'GS.market_price,TS.team_stock as stock,GS.volume,GS.weight,GS.bar_code'
            ])
            ->join('goods_sku GS', 'GS.id=TS.sku_id')
            ->withAttr('goods', function($value, $data) {
                unset($value);
                return (new Team())
                    ->field(['id,goods_id,name,image,people_num,min_buy,max_buy,effective_time,status,is_coupon,is_delete'])
                    ->findOrEmpty($data['team_id'])
                    ->toArray();
            })
            ->where($where)
            ->append(['goods'])
            ->select()
            ->toArray();
    }

    /**
     * 循环检查每个商品的状态是否达标
     * @author windy
     * @throws \Exception
     */
    private static function getOrderStatus(): array
    {
        $status = [
            'isCoupon'       => 0,    // 是否允许使用优惠券
            'couponListId'   => 0,    // 使用优惠券的ID
            'totalCount'     => 0,    // 购买商品总数量
            'totalAmount'    => 0,    // 整个订单的总价格(未优惠)
            'orderAmount'    => 0,    // 订单实际应付金额(优惠后)
            'freightAmount'  => 0,    // 订单运费金额
            'discountAmount' => 0,    // 优惠券的金额
            'integralAmount' => 0,    // 积分抵扣金额
            'useIntegral'    => 0,    // 积分抵扣数量
            'isUseIntegral'  => 0,    // 积分是否抵扣开关
            'integralArray'  => [],   // 积分是否可用信息
            'pStatusArray'   => []    // 检测的每个商品状态
        ];

        // 计算订单金额
        foreach (self::$oProducts as $oProduct) {
            $pStatus = self::getProductStatus($oProduct);
            if ($pStatus['is_coupon']) {
                $status['isCoupon'] = 1;
            }

            $status['totalAmount'] += $pStatus['totalPrice'];
            $status['orderAmount'] += $pStatus['totalPrice'];
            $status['totalCount'] += $pStatus['count'];
            array_push($status['pStatusArray'], $pStatus);
        }

        // 使用优惠券抵扣
        if (!empty(self::$post['coupon_list_id']) and self::$post['coupon_list_id'] > 0) {
            $coupon = CouponService::isUsable($status, self::$post['coupon_list_id']);
            if ($coupon['isUsable']) {
                // 计算每个商品的优惠金额
                $loop = 1;
                $totalProductDiscount = 0;
                foreach ($status['pStatusArray'] as &$product) {
                    $discount = $product['totalPrice'] / $coupon['totalPrice'] * $coupon['discountAmount'];
                    $discount = round($discount, 2);
                    if ($discount > $product['actualPrice']) {
                        $discount = $product['actualPrice'];
                    }

                    // 最后一个商品剩余的钱都给他
                    if ($loop == $status['totalCount']) {
                        $discount = $coupon['discountAmount'] - $totalProductDiscount;
                        if ($discount > $product['sellPrice']) {
                            $discount = $product['sellPrice'];
                        }
                    }
                    // 记录商品优惠金额
                    $product['discountPrice'] = $discount;
                    $product['actualPrice'] -= $discount;
                    $product['actualPrice'] = convert_to_price($product['actualPrice']);
                    $totalProductDiscount += $discount;
                }

                $status['couponListId'] = self::$post['coupon_list_id'];
                $status['discountAmount'] += $totalProductDiscount;
                $status['orderAmount'] = $status['orderAmount'] - $totalProductDiscount;
            }
        }

        // 使用积分抵扣
        $status['integralArray'] = IntegralService::isUsable($status, self::$user_id);
        if (!empty(self::$post['is_use_integral']) and self::$post['is_use_integral'] > 0) {
            $status['isUseIntegral'] = intval(self::$post['is_use_integral']);
            $integral = IntegralService::isUsable($status, self::$user_id);
            if ($integral['isUsable']) {
                // 计算每个商品的优惠金额
                $totalProductDiscount = 0;
                foreach ($status['pStatusArray'] as &$product) {
                    if ($product['isIntegral'] !== 1) continue;
                    $discount = $product['actualPrice'] / $integral['totalPrice'] * $integral['discountAmount'];
                    $discount = round($discount, 2);

                    $product['integralPrice'] = $discount;
                    $totalProductDiscount += $discount;
                }

                $status['useIntegral'] = $integral['useIntegral'];
                $status['integralAmount'] = $totalProductDiscount;
                $status['orderAmount'] = $status['orderAmount'] - $totalProductDiscount;
            }
        }

        // 计算订单运费
        $freightAmount = FreightService::computeFreight($status, self::$user_id, self::$post['address_id'] ?? 0);
        $status['totalAmount'] += $freightAmount;
        $status['orderAmount'] += $freightAmount;
        $status['freightAmount'] += $freightAmount;

        // 处理返回结果
        $status['totalAmount'] = convert_to_price($status['totalAmount']);
        $status['orderAmount'] = convert_to_price($status['orderAmount']);
        $status['discountAmount'] = convert_to_price($status['discountAmount']);
        $status['integralAmount'] = convert_to_price($status['integralAmount']);
        $status['freightAmount'] = convert_to_price($status['freightAmount']);
        return $status;
    }

    /**
     * 检验单个商品状态
     * @author windy
     * @param $oProduct
     * @return array
     * @throws \Exception
     */
    private static function getProductStatus($oProduct): array
    {
        $pIndex  = -1;
        $pStatus = [
            'id'             => null,    // 商品ID
            'sku_id'         => null,    // 商品SKU
            'isIntegral'     => 0,       // 是否可用积分抵扣[0=不允许, 1=允许]
            'sellPrice'      => 0,       // 商品当个售价
            'totalPrice'     => 0,       // 商品总价格(未优惠)
            'actualPrice'    => 0,       // 实际应付金额(已优惠)
            'discountPrice'  => 0,       // 优惠券抵扣金额
            'integralPrice'  => 0,       // 积分抵扣金额
            'count'          => 0,       // 该商品购买数量
            'name'           => '',      // 商品名称
            'image'          => '',      // 商品图片
            'spec_value_str' => '',      // 商品规格名称
            'volume'         => 0,       // 商品体积
            'weight'         => 0,       // 商品重量
            'freight_id'     => 0,       // 运费模板ID[0=全国包邮],
            'people_num'     => 0,       // 成团所需人数
            'min_buy'        => 0,       // 最少下单数量
            'max_buy'        => 0,       // 最大下单数量
            'effective_time' => 0,       // 成团限制时间
            'is_coupon'      => 0,       // 是否允许使用优惠券
        ];

        foreach (self::$productsSku as $sku) {
            $oCount   = (int)$oProduct['num'];
            $oSkuID   = (int)$oProduct['sku_id'];
            $oGoodsID = (int)$oProduct['goods_id'];
            if ($oSkuID == $sku['id'] and $oGoodsID == $sku['goods_id']) {
                $pIndex = $oSkuID;
                if ($sku['goods']['is_delete']) {
                    throw new \Exception("当前拼团活动已结束");
                }
                if ($sku['goods']['status'] > TeamEnum::TEAM_CONDUCT_IN) {
                    throw new \Exception("当前拼团活动已结束");
                }
                if ($sku['goods']['status'] == TeamEnum::TEAM_NOT_STARTED) {
                    throw new \Exception("当前拼团活动尚未开始");
                }
                if ($oCount < $sku['goods']['min_buy']) {
                    throw new \Exception('单笔最少'.$sku['goods']['min_buy'].'件起购');
                }
                if ($oCount > $sku['goods']['max_buy']) {
                    throw new \Exception('单笔最多可购'.$sku['goods']['min_buy'].'件');
                }

                $pStatus['id']             = $sku['goods_id'];
                $pStatus['name']           = $sku['goods']['name'];
                $pStatus['isIntegral']     = $sku['goods']['is_integral'] ?? 0;
                $pStatus['totalPrice']     = convert_to_price(($sku['sell_price'] * $oCount));
                $pStatus['actualPrice']    = convert_to_price(($sku['sell_price'] * $oCount));
                $pStatus['sellPrice']      = convert_to_price($sku['sell_price']);
                $pStatus['count']          = $oCount;
                $pStatus['sku_id']         = $sku['id'];
                $pStatus['spec_value_str'] = $sku['spec_value_str'];
                $pStatus['volume']         = $sku['volume'];
                $pStatus['weight']         = $sku['weight'];
                $pStatus['freight_id']     = $sku['goods']['freight_id'] ?? 0;
                $pStatus['image']          = $sku['image'] ? $sku['image'] : $sku['goods']['image'];
                $pStatus['image']          = UrlUtils::getAbsoluteUrl($pStatus['image']);
                $pStatus['people_num']     = $sku['goods']['people_num'];
                $pStatus['min_buy']        = $sku['goods']['min_buy'];
                $pStatus['max_buy']        = $sku['goods']['min_buy'];
                $pStatus['effective_time'] = $sku['goods']['effective_time'];
                $pStatus['is_coupon']      = $sku['goods']['is_coupon'];

                if (intval($sku['stock']) - $oCount <= 0) {
                    throw new \Exception('商品库存不足');
                }
            }
        }

        if ($pIndex == -1) {
            throw new \Exception('商品找不到了,请重新下单');
        }

        return $pStatus;
    }

    /**
     * 获取构建商品快照信息
     * @author windy
     * @param array $product
     * @return array[]
     * @throws \Exception
     */
    private static function getProductSnap(array $product): array
    {
        foreach (self::$productsSku as $sku) {
            if ($product['sku_id'] == $sku['id'] and $product['id'] == $sku['goods_id']) {
                return [
                    'sku_id'         => $sku['id'],
                    'team_id'        => self::$post['team_id'],
                    'goods_id'       => $sku['goods']['id'],
                    'goods_name'     => $sku['goods']['name'],
                    'spec_value_str' => $sku['spec_value_str'],
                    'spec_value_ids' => $sku['spec_value_ids'],
                    'goods_image'    => UrlUtils::getRelativeUrl($sku['goods']['image']),
                    'spec_image'     => UrlUtils::getRelativeUrl($sku['image']),
                    "market_price"   => $sku['market_price'],
                    "sell_price"     => $sku['sell_price'],
                    "cost_price"     => $sku['cost_price'],
                    "volume"         => $sku['volume'],
                    "weight"         => $sku['weight'],
                    "bar_code"       => $sku['bar_code'],
                ];
            }
        }
        throw new Exception('商品丢失了,下单失败');
    }

    /**
     * 创建订单
     * @author windy
     * @param array $orderStatus
     * @return array
     * @throws \Exception
     */
    public static function createOrder(array $orderStatus): array
    {
        Db::startTrans();
        try {
            // 1、获取收货地址
            $address = self::getAddress();

            // 2、自提订单处理
            if (self::$post['delivery_type']==OrderEnum::DELIVER_TYPE_STORE) {
                $pickUp = [
                    'store'  => $address['id'],
                    'code'   => create_random_code((new Order), 'pick_up_code', 8),
                    'status' => 0
                ];
            }

            // 3、创建订单
            $time = time();
            $orderNo = make_order_no();
            $order = Order::create([
                'user_id'         => self::$user_id,
                'order_sn'        => $orderNo,
                'order_platform'  => self::$post['client'],
                'pay_way'         => self::$post['pay_way'],
                'delivery_type'   => self::$post['delivery_type'],
                'buyers_remarks'  => self::$post['buyers_remarks'] ?? '',
                'total_amount'    => $orderStatus['totalAmount'],
                'paid_amount'     => $orderStatus['orderAmount'],
                'freight_amount'  => $orderStatus['freightAmount'],
                'discount_amount' => $orderStatus['discountAmount'],
                'integral_amount' => $orderStatus['integralAmount'],
                'use_integral'    => $orderStatus['useIntegral'],
                'coupon_list_id'  => $orderStatus['couponListId'],
                'order_type'      => OrderEnum::TEAM_ORDER,
                'order_status'    => OrderEnum::STATUS_WAIT_PAY,
                'pay_status'      => OrderEnum::TO_BE_PAID_STATUS,
                'address_snap'    => json_encode($address, JSON_UNESCAPED_UNICODE),
                'pick_up_store'   => $pickUp['store'] ?? 0,
                'pick_up_code'    => $pickUp['code'] ?? 0,
                'pick_up_status'  => $pickUp['status'] ?? 0,
                'team_activity_id' => self::$post['team_id'] ?? 0,
                'team_found_id'    => self::$post['found_id'] ?? 0 ,
                'create_time'      => $time,
                'update_time'      => $time
            ]);

            // 4、创建订单商品
            foreach ($orderStatus['pStatusArray'] as $item) {
                OrderGoods::create([
                    'order_id'          => $order['id'],
                    'goods_id'          => $item['id'],
                    'sku_id'            => $item['sku_id'],
                    'name'              => $item['name'],
                    'image'             => UrlUtils::getRelativeUrl($item['image']),
                    'spec_value_str'    => $item['spec_value_str'],
                    'count'             => $item['count'],
                    'sell_price'        => $item['sellPrice'],
                    'total_price'       => $item['totalPrice'],
                    'actual_price'      => $item['actualPrice'],
                    'discount_price'    => $item['discountPrice'],
                    'integral_price'    => $item['integralPrice'],
                    'snapshot'          => json_encode(self::getProductSnap($item), JSON_UNESCAPED_UNICODE),
                    'create_time'       => $time,
                    'update_time'       => $time
                ]);
            }

            // 5、更新优惠券为使用
            if ($orderStatus['couponListId']) {
                CouponList::update([
                    'is_use' => CouponEnum::IS_USE_OK,
                    'is_expire' => CouponEnum::IS_EXPIRE_NORMAL,
                    'order_id' => $order['id'],
                    'use_time' => $time
                ], [
                    'coupon_list_id' => $orderStatus['couponListId'],
                    'user_id' => self::$user_id
                ]);
            }

            Db::commit();
            return [
                'pass'        => true,
                'status'      => true,
                'order_no'    => $orderNo,
                'order_id'    => intval($order['id'])
            ];
        } catch (\Exception $e) {
            Db::rollback();
            throw new \Exception($e->getMessage());
        }
    }

    /**
     * 创建团信息
     * 注: 支付回调中调用
     * @author windy
     * @param array $order
     * @throws @\think\db\exception\DataNotFoundException
     * @throws @\think\db\exception\DbException
     * @throws @\think\db\exception\ModelNotFoundException
     */
    public static function createTeam(array $order)
    {
        // 时间
        $time = time();

        // 新团
        $foundId = 0;

        // 获取团活动
        $teamActivity = (new Team())->findOrEmpty($order['team_activity_id'])->toArray();

        // 转相对路径
        $banner = [];
        foreach ($teamActivity['banner'] as $img) {
            $banner[] = UrlUtils::getRelativeUrl($img);
        }

        // 开团
        if (!$order['team_found_id']) {
            $teamFound = TeamFound::create([
                'user_id'      => $order['user_id'],
                'team_id'      => $teamActivity['id'],
                'people'       => $teamActivity['people_num'],
                'join'         => 0,
                'status'       => TeamEnum::CONDUCT_STATUS,
                'kaituan_time' => $time,
                'invalid_time' => $teamActivity['effective_time'] * 60 + $time,
                'snapshot'     => json_encode([
                    'team_id'  => $teamActivity['id'],
                    'goods_id' => $teamActivity['goods_id'],
                    'name'     => $teamActivity['name'],
                    'image'    => UrlUtils::getRelativeUrl($teamActivity['image']),
                    'banner'   => json_encode($banner),
                    'intro'    => $teamActivity['intro'],
                    'min_buy'  => $teamActivity['min_buy'],
                    'max_buy'  => $teamActivity['max_buy'],
                    'is_coupon'       => $teamActivity['is_coupon'],
                    'is_automatic'    => $teamActivity['is_automatic'],
                    'is_distribution' => $teamActivity['is_distribution'],
                    'effective_time'  => $teamActivity['effective_time']
                ], JSON_UNESCAPED_UNICODE),
            ]);
            $foundId = $teamFound['id'];
            Team::update(['total_found_number'=>['inc', 1]], ['id'=>$order['team_activity_id']]);
        }

        // 参团
        TeamJoin::create([
            'team_id'     => $order['team_activity_id'],
            'found_id'    => $foundId ? $foundId : $order['team_found_id'],
            'user_id'     => $order['user_id'],
            'order_id'    => $order['id'],
            'identity'    => $foundId ? TeamEnum::IDENTITY_CAPTAIN : TeamEnum::IDENTITY_MEMBER,
            'status'      => TeamEnum::CONDUCT_STATUS,
            'create_time' => $time,
            'update_time' => $time,
            'snapshot'    => json_encode([
                'team_id'  => $teamActivity['id'],
                'goods_id' => $teamActivity['goods_id'],
                'name'     => $teamActivity['name'],
                'image'    => UrlUtils::getRelativeUrl($teamActivity['image']),
                'banner'   => json_encode($banner),
                'intro'    => $teamActivity['intro'],
                'min_buy'  => $teamActivity['min_buy'],
                'max_buy'  => $teamActivity['max_buy'],
                'is_coupon'       => $teamActivity['is_coupon'],
                'is_automatic'    => $teamActivity['is_automatic'],
                'is_distribution' => $teamActivity['is_distribution'],
                'effective_time'  => $teamActivity['effective_time']
            ])
        ]);

        // 同步
        Team::update(['total_join_number'=>['inc', 1]], ['id'=>$order['team_activity_id']]);
        TeamFound::update(['join'=>['inc', 1]], ['id'=>$foundId ? $foundId : $order['team_found_id']]);
        Order::update([
            'team_found_id'     => $foundId?$foundId:$order['team_found_id'],
            'team_found_status' => TeamEnum::CONDUCT_STATUS
        ], ['id'=>$order['id']]);

        // 验证拼团订单是否达到成团条件
        if (!$foundId) {
            $teamFound = (new TeamFound())->findOrEmpty($order['team_found_id'])->toArray();
            if ($teamFound && $teamFound['join'] >= $teamFound['people']) {
                $teamOrders = (new Order())
                    ->where([
                        ['order_type', '=', OrderEnum::TEAM_ORDER],
                        ['pay_status', '=', OrderEnum::OK_PAID_STATUS],
                        ['team_found_id', '=', $order['team_found_id']]
                    ])->select()->toArray();
                if (count($teamOrders) >= $teamFound['people']) {
                    Team::update(['total_success_number' => ['inc', 1]], ['id' => $teamFound['team_id']]);
                    TeamFound::update([
                        'status' => TeamEnum::SUCCESS_STATUS,
                        'update_time' => time()
                    ], ['id' => $teamFound['id']]);
                    TeamJoin::update([
                        'status' => TeamEnum::SUCCESS_STATUS,
                        'update_time' => time()
                    ], [['order_id', 'in', array_column($teamOrders, 'id')]]);
                    Order::update([
                        'team_found_status' => TeamEnum::SUCCESS_STATUS,
                        'update_time' => $time
                    ], [['id', 'in', array_column($teamOrders, 'id')]]);
                }
            }
        }
    }

    /**
     * 开团/参团数据验证
     * @author windy
     * @param int $userId 用户ID
     * @param int $teamId 团活动ID
     * @param int $foundId 开团ID
     * @throws \Exception
     */
    public static function checkTeamStatus(int $userId, int $teamId=0, int $foundId=0)
    {
        // 验证团活动是否结束
        $teamActivity = (new Team)->where([
            ['id', '=', $teamId],
            ['status', '=', TeamEnum::CONDUCT_STATUS],
            ['start_time', '<', time()],
            ['end_time', '>', time()],
            ['is_delete', '=', 0]
        ])->findOrEmpty()->toArray();

        if (!$teamActivity) {
            throw new \Exception('当前拼团活动已结束,下次再来吧！');
        }

        // 验证参的团是否结束
        if (!empty($userId) && $foundId) {
            $teamFound = (new TeamFound())->findOrEmpty($foundId)->toArray();
            $teamJoin  = (new TeamJoin())->where(['found_id'=>$foundId, 'user_id'=>$userId])->findOrEmpty()->toArray();
            if (!$teamFound) {
                throw new \Exception('当前团不存在,换一个吧！');
            }
            if ($teamFound['status'] > TeamEnum::CONDUCT_STATUS) {
                throw new \Exception('当前团已完结,换一个吧！');
            }
            if ($teamFound['people'] == $teamFound['join']) {
                throw new \Exception('当前团已满员,换一个吧！');
            }
            if ($teamFound['invalid_time'] <= time()) {
                throw new \Exception('当前团已超时,换一个吧！');
            }
            if ($teamFound['user_id'] == $userId) {
                throw new \Exception('不允许参加自己开的团');
            }
            if ($teamJoin) {
                throw new \Exception('您已是该团成员,请勿重复操作');
            }
        }
    }

}