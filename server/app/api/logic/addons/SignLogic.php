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

namespace app\api\logic\addons;


use app\common\basics\Logic;
use app\common\enum\LogGrowthEnum;
use app\common\enum\LogIntegralEnum;
use app\common\model\addons\SignRecord;
use app\common\model\addons\SignRule;
use app\common\model\log\LogGrowth;
use app\common\model\log\LogIntegral;
use app\common\model\user\User;
use app\common\utils\ConfigUtils;
use app\common\utils\TimeUtils;
use Exception;

class SignLogic extends Logic
{
    /**
     * 签到日历
     *
     * @author windy
     * @param int $userId
     * @return array
     * @throws @\think\db\exception\DataNotFoundException
     * @throws @\think\db\exception\DbException
     * @throws @\think\db\exception\ModelNotFoundException
     */
    public static function calendar(int $userId): array
    {
        // 实例化模型
        $userModel       = new User();
        $signRuleModel   = new SignRule();
        $signRecordModel = new SignRecord;

        // 连续签到规则
        $signRules = $signRuleModel
            ->field('id,days,give_integral,give_growth')
            ->order('days asc, id desc')
            ->where(['is_delete'=>0])
            ->select()
            ->toArray();

        // 已签到的天数
        $signRecords = $signRecordModel
            ->where([ 'user_id'=>$userId])
            ->order('id asc')
            ->select()
            ->toArray();

        // 今天是否签到
        $todaySign = $signRecordModel
            ->where(['user_id'=>$userId])
            ->whereTime('sign_time', 'today')
            ->findOrEmpty()
            ->toArray();

        // 昨天是否签到
        $yesterdaySign = $signRecordModel
            ->where(['is_end'=>0, 'user_id'=>$userId])
            ->whereTime('sign_time', 'yesterday')
            ->order('id desc')
            ->findOrEmpty()
            ->toArray();

        // 处理签到日历
        $nums = 0;
        $days = [];
        foreach ($signRules as $item) {
            if ($nums < count($signRecords)) {
                $days[] = [
                    'days'     => $item['days'],
                    'growth'   => $signRecords[$nums]['reward_growth'],
                    'integral' => $signRecords[$nums]['reward_integral'],
                    'status'   => 1
                ];
            } else {
                $days[] = [
                    'days'     => $item['days'],
                    'growth'   => $item['give_growth'],
                    'integral' => $item['give_integral'],
                    'status'   => 0
                ];
            }
            $nums++;
        }

        // 连续签到天数
        $evenSignDay = $yesterdaySign['days'] ?? 0;
        if ($todaySign) {
            $evenSignDay += 1;
        }

        // 整合数据
        $config = ConfigUtils::get('sign', ['is_open'=>0, 'explain'=>'']);
        $user   = $userModel->where(['id'=>$userId])->findOrEmpty()->toArray();
        $detail['info'] = [
            'id'            => $user['id'],
            'nickname'      => $user['nickname'],
            'avatar'        => $user['avatar'],
            'integral'      => $user['integral'],
            'growth_value'  => $user['growth_value'],
            'even_sign_day' => $evenSignDay,
            'is_today_sign' => $todaySign ? 1 : 0,
            'is_open'       => intval($config['is_open']),
            'explain'       => $config['explain']
        ];
        $detail['date'] = [
            'week'  => TimeUtils::dayWeek(),
            'month' => date('m'),
            'year'  => TimeUtils::capMonth().date('Y')
        ];
        $detail['calendar'] = $days;
        return $detail;
    }

    /**
     * 发起每日签到
     *
     * @author windy
     * @param int $userId
     * @return bool|array
     */
    public static function sign(int $userId)
    {
        try {
            // 签到配置
            $config = ConfigUtils::get('sign', ['is_open'=>0]);
            if (!$config['is_open']) {
                throw new Exception('签到活动已结束');
            }

            // 今天是否签到
            $todaySign = (new SignRecord())
                ->where(['user_id'=>$userId])
                ->whereTime('sign_time', 'today')
                ->findOrEmpty()
                ->toArray();

            if ($todaySign) {
                throw new Exception('您已签到过了');
            }

            // 昨天签到记录
            $yesterdaySign = (new SignRecord())
                ->field('id,days,reward_integral,reward_growth')
                ->order('id desc')
                ->whereTime('sign_time', 'yesterday')
                ->findOrEmpty()
                ->toArray();


            // 连续签到次数
            $totalSignDay = 1;

            // 是否连续签到
            if ($yesterdaySign) {
                $totalSignDay = $yesterdaySign['days'] + 1;
            }

            // 签到奖励规则
            $singRules = (new SignRule())
                ->where(['is_delete'=>0])
                ->order('days asc')
                ->select()
                ->toArray();

            if (!$singRules) {
                throw new Exception('当前没有签到活动');
            }

            // 创建签到记录
            $record = SignRecord::create([
                'user_id'         => $userId,
                'days'            => $totalSignDay,
                'reward_integral' => $singRules[$totalSignDay-1]['give_integral'] ?? 0,
                'reward_growth'   => $singRules[$totalSignDay-1]['give_growth'] ?? 0,
                'is_end'          => $totalSignDay >= count($singRules) ? 1 : 0,
                'sign_time'       => time(),
                'create_time'     => time(),
                'update_time'     => time()
            ]);

            // 记录奖励日志
            if (!empty($singRules[$totalSignDay-1])) {
                $give = $singRules[$totalSignDay-1];
                if ($give['give_integral'] || $give['give_growth']) {
                    User::update([
                        'integral'     => ['inc', $give['give_integral']],
                        'growth_value' => ['inc', $give['give_growth']],
                        'update_time'  => time()
                    ], ['id' => $userId]);
                }

                if ($give['give_integral']) {
                    LogIntegral::add(
                        LogIntegralEnum::SIGN_INC_INTEGRAL,
                        $give['give_integral'], $userId, 0,
                        $record['id'], '', '签到奖励积分');
                }

                if ($give['give_growth']) {
                    LogGrowth::add(
                        LogGrowthEnum::SIGN_INC_GROWTH,
                        $give['give_growth'], $userId, 0,
                        $record['id'], '', '签到奖励成长值');
                }
            }

            return [
                'days'     => $totalSignDay,
                'integral' => $singRules[$totalSignDay-1]['give_integral'] ?? 0,
                'growth'   => $singRules[$totalSignDay-1]['give_growth'] ?? 0,
            ];
        } catch (Exception $e) {
            static::$error = $e->getMessage();
            return false;
        }
    }
}