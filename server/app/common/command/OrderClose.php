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


use app\common\enum\OrderEnum;
use app\common\model\order\Order;
use app\common\service\OrderService;
use app\common\utils\ConfigUtils;
use Exception;
use think\console\Command;
use think\console\Input;
use think\console\Output;
use think\facade\Db;
use think\facade\Log;

class OrderClose extends Command
{
    /**
     * 指令配置
     */
    protected function configure()
    {
        $this->setName('order_close')
            ->setDescription('系统关闭超时未付款订单');
    }

    /**
     * 执行关闭超时订单逻辑
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

        // 获取订单自动取消时长
        $automaticCancelDuration = ConfigUtils::get('trade')['automatic_cancel_duration'] ?? 0;
        $automaticCancelDuration = $automaticCancelDuration * 60;

        // 如果取消时长为0则不执行
        if ($automaticCancelDuration <= 0) {
            return true;
        }

        // 获取未支付已到结束时间订单
        $orders = (new Order())->field(true)
            ->whereRaw("create_time+$automaticCancelDuration < $time")
            ->where([
                'order_status' => OrderEnum::STATUS_WAIT_PAY,
                'pay_status'   => OrderEnum::TO_BE_PAID_STATUS
            ])->select()->toArray();

        // 判断是否有需要取消的订单
        if (empty($orders)) {
            return true;
        }

        // 循环取消关闭订单
        foreach ($orders as $order) {
            Db::startTrans();
            try {
                // 关闭订单
                OrderService::closeOrder($order['id']);

                // 回退库存
                $stockDeductMethod = ConfigUtils::get('trade')['stock_deduct_method'] ?? '';
                if ($stockDeductMethod == 'order') {
                    OrderService::rollbackStock($order['id']);
                }

                // 退优惠券
                if ($order['coupon_list_id']) {
                    OrderService::rollbackCoupon($order['coupon_list_id']);
                }

                // 提交事务
                Db::commit();
            } catch (Exception $e) {
                Db::rollback();
                Log::write('系统自动关闭异常:'.$e->getMessage());
                throw new Exception($e->getMessage());
            }
        }

        return true;
    }
}