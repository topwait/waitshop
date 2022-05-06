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

namespace app\common\command;


use app\common\enum\AfterSaleEnum;
use app\common\enum\DistributionEnum;
use app\common\enum\LogWalletEnum;
use app\common\enum\OrderEnum;
use app\common\model\addons\DistributionOrder;
use app\common\model\log\LogWallet;
use app\common\model\order\AfterSale;
use app\common\model\user\User;
use app\common\utils\ConfigUtils;
use Exception;
use think\console\Command;
use think\console\Input;
use think\console\Output;
use think\facade\Db;

class DistributionSettle extends Command
{
    /**
     * 指令配置
     */
    protected function configure()
    {
        $this->setName('distribution_settle')
            ->setDescription('分销佣金结算');
    }

    /**
     * 结算分销佣金
     *
     * @author windy
     * @param Input $input
     * @param Output $output
     * @return bool
     * @throws @\think\db\exception\DataNotFoundException
     * @throws @\think\db\exception\DbException
     * @throws @\think\db\exception\ModelNotFoundException
     */
    protected function execute(Input $input, Output $output): bool
    {
        // 当前执行时间戳
        $time = time();

        // 订单允许售后时长
        $afterSaleDuration = ConfigUtils::get('trade')['after_sale_duration'] ?? 0;
        $afterSaleDuration = $afterSaleDuration <= 0 ? 0 : intval($afterSaleDuration / 86400);

        // 执行结算逻辑
        Db::startTrans();
        try {
            $orders = (new DistributionOrder())->alias('DO')
                ->field('DO.*')
                ->join('order O', 'O.id = DO.order_id')
                ->where('DO.status', DistributionEnum::STATUS_WAIT)
                ->where('O.order_status', OrderEnum::STATUS_FINISH)
                ->whereRaw("confirm_time+$afterSaleDuration < $time")
                ->select()->toArray();

            $userModel = new User();
            foreach ($orders as $order) {
                $user = $userModel->findOrEmpty($order['id']);
                // 是否可结算
                if (
                    $this->isAfterSale($order) === false
                    || $user['is_distribution'] != 1
                    || $user['freeze_distribution'] == 0
                ) {
                    continue;
                }

                // 增加佣金
                User::update([
                    'earnings'    => ['inc', $order['earnings_money']],
                    'update_time' => $time
                ]);

                // 佣金流水
                LogWallet::add(
                    LogWalletEnum::distribution_inc_earnings,
                    $order['earnings_money'],
                    $order['user_id'], 0,
                    $order['id'],
                    $order['distribution_sn'],
                    '分销结算佣金'
                );

                // 更新状态
                DistributionOrder::update([
                    'status'      => DistributionEnum::STATUS_SUCCESS,
                    'update_time' => time()
                ], ['id'=>$order['id']]);
            }

            Db::commit();
        } catch (Exception $e) {
            Db::rollback();
            throw new Exception($e->getMessage());
        }

        return true;
    }

    /**
     * 是否有售后处理
     *
     * @param $order
     * @return bool
     */
    protected function isAfterSale($order)
    {
        // 查询售后订单
        $afterSale = (new AfterSale())->where([
            ['order_goods_id', '=', $order['order_goods_id']],
            ['status', 'in', [
                AfterSaleEnum::STATUS_APPLY_REFUND,
                AfterSaleEnum::STATUS_WAIT_RETURN_GOODS,
                AfterSaleEnum::STATUS_WAIT_RECEIVE_GOODS,
                AfterSaleEnum::STATUS_WAIT_REFUND,
                AfterSaleEnum::STATUS_SUCCESS_REFUND
            ]]
        ])->findOrEmpty()->toArray();

        // 没有售后订单
        if (!$afterSale) {
            return true;
        }

        // 不可结算状态
        $noSettle = [
            AfterSaleEnum::STATUS_APPLY_REFUND,
            AfterSaleEnum::STATUS_WAIT_RETURN_GOODS,
            AfterSaleEnum::STATUS_WAIT_RECEIVE_GOODS
        ];

        // 结算失败状态
        $failSettle = [
            AfterSaleEnum::STATUS_WAIT_REFUND,
            AfterSaleEnum::STATUS_SUCCESS_REFUND
        ];

        // 不结算: 售后中
        if (in_array($afterSale['status'], $noSettle)) {
            return false;
        }

        // 不结算: 售后成功
        if (in_array($afterSale['status'], $failSettle)) {
            DistributionOrder::update(['status'=>DistributionEnum::STATUS_INVALID, 'update_time'=>time()]);
            return false;
        }

        // 可结算
        return true;
    }
}