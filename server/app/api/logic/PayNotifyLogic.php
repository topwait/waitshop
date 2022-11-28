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
use app\common\enum\LogGrowthEnum;
use app\common\enum\LogIntegralEnum;
use app\common\enum\LogWalletEnum;
use app\common\enum\NoticeEnum;
use app\common\enum\OrderEnum;
use app\common\model\addons\RechargeOrder;
use app\common\model\goods\Goods;
use app\common\model\goods\GoodsSku;
use app\common\model\log\LogGrowth;
use app\common\model\log\LogIntegral;
use app\common\model\log\LogWallet;
use app\common\model\order\Order;
use app\common\model\user\User;
use app\common\utils\ConfigUtils;
use app\common\utils\TimeUtils;
use think\facade\Db;
use Exception;

/**
 * 支付回调接口-逻辑层
 * Class PayNotifyLogic
 * @package app\api\logic
 */
class PayNotifyLogic extends Logic
{
    /**
     * 处理回调事件
     *
     * @author windy
     * @param $action (调用的方法)
     * @param $order_sn (订单编号)
     * @param array $extra (扩展信息)
     * @return mixed
     * @throws Exception
     */
    public static function handle($action, $order_sn, $extra = [])
    {
        try {
            return self::$action($order_sn, $extra);
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    /**
     * 订单回调处理
     *
     * @author windy
     * @param $orderSn
     * @param $extra
     * @return bool
     * @throws Exception
     */
    public static function order($orderSn, $extra): bool
    {
        Db::startTrans();
        try {
            // 查询订单信息
            $order = (new Order())->field(true)
                ->with(['orderGoods'])
                ->where(['order_sn' => $orderSn])
                ->findOrEmpty()->toArray();

            // 查询用户信息
            $user = (new User())->findOrEmpty($order['user_id'])->toArray();

            // 余额支付(扣余额)
            if ($order['pay_way'] == OrderEnum::PAY_WAY_BALANCE) {
                if ($user['money'] - $order['paid_amount'] < 0) {
                    throw new Exception('余额不足');
                }
                // 扣减用户钱包余额
                User::update([
                    'money'       => ['dec', $order['paid_amount']],
                    'update_time' => self::reqTime()
                ], ['id' => $order['user_id']]);
                // 记录扣减余额日志
                LogWallet::reduce(
                    LogWalletEnum::PAY_DEC_MONEY,
                    $order['paid_amount'],
                    $user['id'], 0, $order['id'],
                    $order['order_sn']);
            }

            // 积分抵扣(扣积分)
            if ($order['use_integral'] > 0) {
                if ($user['integral'] - $order['use_integral']) {
                    throw new Exception('积分不足');
                }
                // 扣减用户积分数量
                User::update([
                    'integral'    => ['dec', $order['use_integral']],
                    'update_time' => self::reqTime()
                ], ['id' => $order['user_id']]);
                // 记录扣减积分日志
                LogWallet::reduce(
                    LogIntegralEnum::PAY_DEC_INTEGRAL,
                    $order['integral_amount'],
                    $user['id'], 0, $order['id'],
                    $order['order_sn']);
            }

            // 更新订单状态
            Order::update([
                'transaction_id' => $extra['transaction_id'] ?? '',
                'pay_status'     => OrderEnum::OK_PAID_STATUS,
                'order_status'   => OrderEnum::STATUS_WAIT_DELIVERY,
                'pay_time'       => self::reqTime()
            ], ['id'=>$order['id']]);

            // 扣除商品库存/增加商品销量
            if ($order['order_type'] == OrderEnum::NORMAL_ORDER and $order['stock_deduct_method'] == 2) {
                foreach ($order['orderGoods'] as $item) {
                    Goods::update([
                        'stock'        => ['dec', $item['count']],
                        'sales_volume' => ['inc', $item['count']]
                    ], ['id' => $item['goods_id']]);

                    GoodsSku::update([
                        'stock'        => ['dec', $item['count']],
                        'sales_volume' => ['inc', $item['count']]

                    ], ['id' => $item['sku_id']]);
                }
            }

            // 初始用户需要更新的字段
            $userData = [
                'update_time' => time(),
                'total_order_amount' => ['inc', $order['paid_amount']]
            ];

            // 下单赠送积分(每天一次)
            $logIntegral = (new LogIntegral())->field('id')
                ->where('create_time', '>=', TimeUtils::today()[0])
                ->where('create_time', '<=', TimeUtils::today()[1])
                ->where('user_id', '=', $user['id'])
                ->where(['source_type'=>LogIntegralEnum::PAY_INC_INTEGRAL])
                ->findOrEmpty();
            if ($logIntegral->isEmpty()) {
                $orderRewardIntegral = ConfigUtils::get('reward')['order_reward_integral'] ?? 0;
                if ($orderRewardIntegral > 0) {
                    // 更新用户积分
                    $userData['integral'] = ['inc', $orderRewardIntegral];
                    // 记录赠送积分
                    LogIntegral::add(
                        LogIntegralEnum::PAY_INC_INTEGRAL,
                        $orderRewardIntegral,
                        $user['id'], 0, $order['id'],
                        $order['order_sn'], '下单赠送积分'
                    );
                }
            }

            // 下单赠送成长值(每天一次)
            $logGrowth = (new LogGrowth())->field('id')
                ->where('create_time', '>=', TimeUtils::today()[0])
                ->where('create_time', '<=', TimeUtils::today()[1])
                ->where('user_id', '=', $user['id'])
                ->where(['source_type'=>LogGrowthEnum::PAY_INC_GROWTH])
                ->findOrEmpty();
            if ($logGrowth->isEmpty()) {
                $orderRewardGrowth   = ConfigUtils::get('reward')['order_reward_growth'] ?? 0;
                if ($orderRewardGrowth > 0) {
                    // 更新用户成长
                    $userData['growth_value'] = ['inc', $orderRewardGrowth];
                    // 记录赠送成长
                    LogGrowth::add(
                        LogGrowthEnum::PAY_INC_GROWTH,
                        $orderRewardGrowth,
                        $user['id'], 0, $order['id'],
                        $order['order_sn'], '下单赠送成长值'
                    );
                }
            }

            // 更新用户表的信息
            User::update($userData, ['id'=>$user['id']]);

            // 消息通知
            $goodsName = $order['orderGoods'][0]['name'] ?? '商品';
            event('Notice', [
                'scene'   => NoticeEnum::ORDER_PAY_NOTICE,
                'mobile'  => $order['address_snap']['mobile'] ?? '',
                'user_id' => $order['user_id'],
                'params'  => [
                    'time'         => date('Y-m-d H:i:s', self::reqTime()),
                    'nickname'     => $user['nickname'],
                    'order_sn'     => $order['order_sn'],
                    'total_num'    => $order['total_num'],
                    'paid_amount'  => $order['paid_amount'],
                    'goods_name'   => strlen($goodsName) > 20 ? mb_substr($goodsName, 0, 16).'...' : $goodsName
                ]
            ]);

            Db::commit();
            return true;
        } catch (Exception $e) {
            Db::rollback();
            throw new Exception($e->getMessage());
        }
    }

    /**
     * 充值回调处理
     *
     * @author windy
     * @param $orderSn (订单编号)
     * @param $extra (额外参数)
     */
    public static function recharge($orderSn, $extra)
    {
        // 充值订单
        $order = (new RechargeOrder())
            ->where(['order_sn'=>$orderSn])
            ->findOrEmpty()
            ->toArray();

        // 更新订单
        RechargeOrder::update([
            'transaction_id' => $extra['transaction_id'] ?? '',
            'pay_status'     => 1,
            'pay_time'       => time(),
            'update_time'    => time()
        ], ['id'=>$order['id']]);

        // 更新钱包
        User::update([
            'money'        => ['inc', $order['paid_amount'] + $order['give_amount']],
            'integral'     => ['inc', $order['give_integral']],
            'growth_value' => ['inc', $order['give_growth']],
        ], ['id'=>$order['user_id']]);

        // 金额变动日志
        $changeAmount = ($order['paid_amount'] + $order['give_amount']);
        LogWallet::add(LogWalletEnum::RECHARGE_MONEY, $changeAmount, $order['user_id'], 0, $order['id'], $order['order_sn'], '用户充值');

        // 积分变动日志
        if ($order['give_integral']) {
            LogIntegral::add(LogIntegralEnum::RECHARGE_INC_INTEGRAL, $order['give_integral'], $order['user_id'], 0, $order['id'], $order['order_sn'], '余额充值赠送');
        }

        // 成长值变动日志
        if ($order['give_growth']) {
            LogGrowth::add(LogGrowthEnum::RECHARGE_INC_GROWTH, $order['give_integral'], $order['user_id'], 0, $order['id'], $order['order_sn'], '余额充值赠送');
        }
    }
}