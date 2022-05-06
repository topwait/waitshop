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


use app\common\utils\TimeUtils;
use Exception;
use think\console\Command;
use think\console\Input;
use think\console\Output;
use think\facade\Console;
use app\common\model\Crontab as CrontabModel;

class Crontab extends Command
{
    /**
     * 指令配置
     */
    protected function configure()
    {
        $this->setName('crontab')
            ->setDescription('定时任务');
    }

    /**
     * 定时任务调度器
     *
     * @param Input $input
     * @param Output $output
     * @return bool
     * @throws @\think\db\exception\DataNotFoundException
     * @throws @\think\db\exception\DbException
     * @throws @\think\db\exception\ModelNotFoundException
     * @author windy
     */
    protected function execute(Input $input, Output $output)
    {
        $crontab = (new CrontabModel())->where(['status'=>1])->select()->toArray();

        if (empty($crontab)) {
            return false;
        }

        $startTime = time();
        foreach ($crontab as $cron) {
            try {
                $startTime = TimeUtils::millisecond();
                $parameter = explode(' ', $cron['params']);
                if (is_array($parameter) && !empty($cron['params'])) {
                    Console::call($cron['command'], $parameter);
                } else {
                    Console::call($cron['command']);
                }
                CrontabModel::update(['error'=>''], ['id'=>$cron['id']]);
            } catch (Exception $e) {
                CrontabModel::update(['error'=>$e->getMessage(), 'status'=>3], ['id'=>$cron['id']]);
            } finally {
                $endTime = TimeUtils::millisecond() - $startTime;
                $maxTime = $cron['max_time'] > $endTime ? $cron['max_time'] : $endTime;
                CrontabModel::update(['last_time'=>time(), 'exe_time'=>$endTime, 'max_time'=>$maxTime], ['id'=>$cron['id']]);
            }
        }

        return true;
    }
}