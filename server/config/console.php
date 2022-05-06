<?php
// +----------------------------------------------------------------------
// | 控制台配置
// +----------------------------------------------------------------------

return [
    // 指令定义
    'commands' => [
        // 任务调度器
        'crontab'              => 'app\common\command\Crontab',
        // 关闭超时订单
        'order_close'          => 'app\common\command\OrderClose',
        // 关闭超时拼团
        'team_close'           => 'app\common\command\TeamClose',
        // 结算分销佣金
        'distribution_settle'  => 'app\common\command\DistributionSettle',
    ],
];
