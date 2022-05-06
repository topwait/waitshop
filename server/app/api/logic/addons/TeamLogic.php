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


use app\api\logic\AddressLogic;
use app\api\logic\StoreLogic;
use app\api\service\TeamOrderServer;
use app\common\basics\Logic;
use app\common\enum\TeamEnum;
use app\common\model\addons\Team;
use app\common\model\addons\TeamFound;
use app\common\model\addons\TeamJoin;
use app\common\model\addons\TeamSku;
use app\common\model\goods\Goods;
use app\common\model\goods\GoodsCollect;
use app\common\utils\UrlUtils;
use Exception;

/**
 * 拼团接口-逻辑层
 * Class SharingLogic
 * @package app\api\logic\marketing
 */
class TeamLogic extends Logic
{
    /**
     * 拼团活动商品列表
     *
     * @author windy
     * @param array $get
     * @return mixed
     * @throws @\think\db\exception\DbException
     */
    public static function lists(array $get)
    {
        return (new Team())->field('T.id,T.goods_id,T.name,T.image,T.people_num,T.min_team_price,G.max_price')
            ->alias('T')
            ->join('goods G', 'G.id = T.goods_id')
            ->where([
                ['T.status', '=',  TeamEnum::TEAM_CONDUCT_IN],
                ['T.is_delete', '=', 0],
                ['T.start_time', '<=', time()],
                ['T.end_time', '>=', time()]
            ])
            ->paginate([
                'page'      => $get['page'] ?? 1,
                'list_rows' => $get['limit'] ?? 20,
                'var_page'  => 'page'
            ])->append(['team_volume'])->toArray();
    }

    /**
     * 拼团商品详细
     *
     * @author windy
     * @param int $id 团活动主键
     * @param int $userId
     * @return array
     * @throws @\think\db\exception\DataNotFoundException
     * @throws @\think\db\exception\DbException
     * @throws @\think\db\exception\ModelNotFoundException
     */
    public static function detail(int $id, int $userId): array
    {
        // 获取拼团商品信息
        $detail = (new Team())->alias('T')
            ->field(['T.id,T.goods_id,T.name,T.image,T.banner,G.content,T.people_num,
                T.min_buy,T.max_buy,T.min_team_price,T.end_time,T.click_count,T.total_join_number'])
            ->with(['spec.specValue'])
            ->where(['T.id'=>$id])
            ->join('goods G', 'G.id=T.goods_id')
            ->withAttr(['is_collect'=>function($value, $data) use ($userId){
                unset($value);
                $result = (new GoodsCollect())->where([
                    'user_id'   => $userId,
                    'goods_id'  => $data['goods_id'],
                    'is_delete' => 0
                ])->findOrEmpty()->toArray();
                return $result ? true : false;
            }])->append(['is_collect'])
            ->findOrEmpty()
            ->toArray();

        // 更新浏览量
        Team::update([
            'click_count' => ['inc', 1]
        ], ['id'=>intval($id)]);
        
        // 剩余结算时间
        $detail['surplus_end_time'] = $detail['end_time'] > time() ? $detail['end_time'] - time() : 0;

        // 是否单独购买
        $g = (new Goods())->where(['id'=>$detail['goods_id'], 'is_delete'=>0, 'is_show'=>1])->findOrEmpty()->toArray();
        $detail['is_alone_buy'] = $g ? true : false;

        // 获取拼团商品SKU
        $sku = (new TeamSku())->alias('TS')
            ->field([
                'GS.id,GS.goods_id,GS.spec_value_ids,GS.spec_value_str',
                'GS.image,GS.sell_price,TS.team_price,TS.team_stock,TS.sales_volume'
            ])
            ->join('goods_sku GS', 'GS.id=TS.sku_id')
            ->where(['team_id'=>$id])
            ->select()
            ->toArray();

        // 获取要显示的规格
        $specValueArr = [];
        $specValueIds = array_column($sku, 'spec_value_ids');
        foreach ($specValueIds as $item) {
            foreach (explode(",", $item) as $i) {
                $specValueArr[] = intval($i);
            }
            $specValueArr = array_unique($specValueArr);
        }

        // 过滤不显示的规格
        foreach ($detail['spec'] as &$spec) {
            $specValue = [];
            foreach ($spec['specValue'] as $item) {
                if (in_array($item['id'], $specValueArr)) {
                    $specValue[] = $item;
                }
            }
            if (empty($specValue)) {
                unset($spec);
            } else {
                $spec['specValue'] = $specValue;
            }
        }

        // 正在开团
        $teamFound = (new TeamFound)->withoutField('status,kaituan_time')
            ->where([
                ['team_id', '=', $id],
                ['status', '=', TeamEnum::CONDUCT_STATUS],
                ['invalid_time', '>', time()]
            ])
            ->with(['user'])
            ->order('id asc')
            ->select()->toArray();

        foreach ($teamFound as &$item) {
            $item['lack_people']  = $item['people'] - $item['join'];
            $item['surplus_time'] = $item['invalid_time'] - time();
            unset($item['people']);
            unset($item['join']);
            unset($item['invalid_time']);
            unset($item['user']['sn']);
            unset($item['user']['mobile']);
        }

        // 处理内容图片
        $domain  = UrlUtils::getStorageUrl() . '/';
        $pattern = '/(<img .*?src=")(.*?)(.*?>)/is';
        $detail['content'] = preg_replace($pattern, "\${1}$domain\${2}\${3}", $detail['content']);

        // 返回结果
        $detail['sku'] = $sku;
        $detail['found'] = $teamFound;
        return $detail;
    }

    /**
     * 拼团记录列表
     *
     * @author windy
     * @param array $get
     * @param int $userId
     * @return array
     * @throws @\think\db\exception\DbException
     */
    public static function record(array $get, int $userId): array
    {
        self::setSearch([
            '=' => ['status@TJ.status']
        ]);

        return (new TeamJoin())
            ->alias('TJ')
            ->field('TJ.id,TJ.order_id,TJ.status,TJ.create_time,TF.people,TF.join,
                OG.name,OG.image,OG.count,OG.spec_value_str,OG.total_price,OG.actual_price')
            ->where('TJ.user_id', '=', $userId)
            ->where(self::$searchWhere)
            ->join('team_found TF', 'TF.id=TJ.found_id')
            ->join('order_goods OG', 'OG.order_id=TJ.order_id')
            ->paginate([
                'page'      => $get['page'] ?? 1,
                'list_rows' => $get['limit'] ?? 20,
                'var_page'  => 'page'
            ])->toArray();
    }

    /**
     * 拼团订单信息
     *
     * @author windy
     * @param array $post
     * @param int $userId
     * @return array|bool
     * @throws @\think\Exception
     */
    public static function settle(array $post, int $userId)
    {
        try {
            // 收货地址数据
            $userAddress = [];
            if (empty($post['address_id'])) {
                $userAddress = AddressLogic::default($userId);
            } elseif ($post['address_id'] > 0) {
                $userAddress = AddressLogic::getAddressById($post['address_id'], $userId);
            }

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

            // 自提门店数据
            $userStore = [];
            if (!empty($post['store_id']) && $post['store_id']) {
                $userStore = StoreLogic::detail([
                    'id' => $post['store_id'],
                    'longitude' => $post['longitude'] ?? 0,
                    'latitude' => $post['latitude'] ?? 0
                ]);
            }

            // 返回信息
            $orderStatus = TeamOrderServer::check($post, $post['products'], $userId);
            $orderStatus['coupon'] = $orderCoupon;
            $orderStatus['address'] = $userAddress;
            $orderStatus['store'] = $userStore;
            return $orderStatus;
        } catch (Exception $e) {
            static::$error = $e->getMessage();
            return false;
        }
    }

    /**
     * 执行开团/参团/下单
     *
     * @author windy
     * @param array $post
     * @param array $oProducts
     * @param int $userId
     * @return array|false
     */
    public static function buy(array $post, array $oProducts, int $userId)
    {
        try {
            $orderStatus = TeamOrderServer::check($post, $oProducts, $userId);
            return TeamOrderServer::createOrder($orderStatus);
        } catch (Exception $e) {
            static::$error = $e->getMessage();
            return false;
        }
    }
}