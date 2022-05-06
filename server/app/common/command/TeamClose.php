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


use app\common\enum\TeamEnum;
use app\common\model\addons\Team;
use app\common\model\addons\TeamFound;
use app\common\model\addons\TeamJoin;
use app\common\model\order\Order;
use app\common\service\OrderService;
use think\console\Command;
use think\console\Input;
use think\console\Output;

class TeamClose extends Command
{
    /**
     * 指令配置
     */
    protected function configure()
    {
        $this->setName('team_close')
            ->setDescription('系统关闭超时的拼团');
    }

    /**
     * 系统关闭超时的拼团
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
        // 到期拼团活动
        $team = (new Team)->field(true)
            ->where([
                ['end_time', '<=', time()],
                ['status', '=', TeamEnum::TEAM_CONDUCT_IN]
            ])
            ->select()
            ->toArray();

        // 结束拼团活动
        if ($team) {
            Team::update([
                'status'      => TeamEnum::TEAM_SYS_END,
                'update_time' => time()
            ], [['id', 'in', array_column($team, 'id')]]);
        }

        // 正在拼团
        $teamFounds = (new TeamFound())
            ->alias('TF')
            ->field('TF.*,T.is_automatic')
            ->join('team T', 'T.id=TF.team_id')
            ->where('TF.status', '=', TeamEnum::CONDUCT_STATUS)
            ->where('TF.invalid_time', '<=', time())
            ->whereOr('TF.team_id', 'in', array_column($team, 'id'))
            ->select()->toArray();

        // 处理拼团
        $teamJoinModel = new TeamJoin();
        foreach ($teamFounds as $found) {
            // 参团记录
            $teamJoins = $teamJoinModel
                ->alias('TJ')
                ->field('TJ.*,O.paid_amount,O.order_status,O.pay_status')
                ->join('order O', 'O.id=TJ.order_id')
                ->where(['found_id'=>$found['id']])
                ->select()
                ->toArray();

            // 是否成团
            $isTeamSuccess = false;
            $updateStatus  = TeamEnum::FAIL_STATUS;
            if ($found['is_automatic'] || $found['people'] === $found['join']) {
                $isTeamSuccess = true;
                $updateStatus  = TeamEnum::SUCCESS_STATUS;
            }

            // 更新状态
            TeamFound::update([
                'status'      => $updateStatus,
                'update_time' => time()
            ], ['id'=>$found['id']]);

            TeamJoin::update([
                'status'      => $updateStatus,
                'update_time' => time()
            ], [['id', 'in', array_column($teamJoins, 'id')]]);

            Order::update([
                'update_time' => time(),
                'team_found_status' => $isTeamSuccess ? 2 : 3,
            ], [['id', 'in', array_column($teamJoins, 'order_id')]]);

            // 失败退款
            foreach ($teamJoins as $join) {
                OrderService::refund($join['order_id'], $join['paid_amount']);
            }
        }

        return true;
    }
}