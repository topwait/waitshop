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
use app\common\enum\LogIntegralEnum;
use app\common\enum\LotteryEnum;
use app\common\model\addons\LotteryPrize;
use app\common\model\addons\LotteryRecord;
use app\common\model\log\LogIntegral;
use app\common\model\user\User;
use app\common\utils\ConfigUtils;
use app\common\utils\TimeUtils;
use app\common\utils\UrlUtils;
use Exception;

/**
 * 幸运抽奖接口-逻辑层
 * Class LotteryLogic
 * @package app\api\logic\addons
 */
class LotteryLogic extends Logic
{
    /**
     * 抽奖的奖品
     *
     * @author windy
     * @param int $userId
     * @return array
     * @throws @\think\db\exception\DataNotFoundException
     * @throws @\think\db\exception\DbException
     * @throws @\think\db\exception\ModelNotFoundException
     */
    public static function prize(int $userId)
    {
        // 抽奖配置
        $config = ConfigUtils::get('lottery', [
            'is_open' => 0,
            'limit'   => 0,
            'rules'   => ''
        ]);

        // 已抽次数
        $drawn = (new LotteryRecord())
            ->where('create_time', '>=', TimeUtils::today()[0])
            ->where('create_time', '<=', TimeUtils::today()[1])
            ->where('user_id', '=', $userId)
            ->count();

        // 剩余次数
        $surplus = $config['limit'] - $drawn;
        $surplus = $surplus <= 0 ? 0 : $surplus;

        // 抽奖记录
        $record = (new LotteryRecord())->alias('LR')
            ->field(['LR.id,LR.prize_type,LR.prize_value,U.nickname'])
            ->join('user U', 'U.id=LR.user_id')
            ->where('user_id', '=', $userId)
            ->where('prize_type', '>', 1)
            ->order('id desc')
            ->limit(20)
            ->select()
            ->toArray();

        foreach ($record as &$item) {
            switch ($item['prize_type']) {
                case LotteryEnum::IN_INTEGRAL:
                    $item['tips'] = "恭喜".hideStar($item['nickname'])."获得".$item['prize_value']."积分";
                    break;
                case LotteryEnum::IN_GROWTH:
                    $item['tips'] = "恭喜".hideStar($item['nickname'])."获得".$item['prize_value']."成长值";
                    break;
                case LotteryEnum::IN_GOODS:
                    $item['tips'] = "恭喜".hideStar($item['nickname'])."获得".$item['prize_value'];
                    break;
            }
            unset($item['prize_type']);
            unset($item['prize_value']);
            unset($item['nickname']);
        }

        // 抽奖奖品
        $prizeData = [];
        $prize = (new LotteryPrize())->order('sort desc')->limit(8)->select()->toArray();
        for ($i=0; $i<8; $i++) {
            if ($i == 4) {
                $prizeData[] = ['id'=>0, 'type'=>0];
            }
            unset($prize[$i]['number']);
            unset($prize[$i]['create_time']);
            unset($prize[$i]['update_time']);
            $prizeData[] = $prize[$i];

        }

        return ['config'=>$config, 'surplus'=>$surplus, 'record'=>$record, 'prize'=>$prizeData];
    }

    /**
     * 抽奖记录
     *
     * @param array $get
     * @param int $userId
     * @return array
     * @throws @\think\db\exception\DbException
     * @author windy
     */
    public static function record(array $get, int $userId): array
    {
        $lists = (new LotteryRecord())
            ->field('id,prize_image,prize_type,prize_value,prize_tips,create_time')
            ->order('id desc')
            ->where('user_id', $userId)
            ->paginate([
                'page'      => $get['page'] ?? 1,
                'list_rows' => $get['limit'] ?? 20,
                'var_page'  => 'page'
            ])->toArray();

        foreach ($lists['data'] as &$item) {
            switch ($item['prize_type']) {
                case LotteryEnum::NOT_PRIZE:
                    $item['tips'] = $item['prize_tips'];
                    break;
                case LotteryEnum::IN_INTEGRAL:
                    $item['tips'] = "获得".$item['prize_value']."积分";
                    break;
                case LotteryEnum::IN_GROWTH:
                    $item['tips'] = "获得".$item['prize_value']."成长值";
                    break;
                case LotteryEnum::IN_GOODS:
                    $item['tips'] = "获得".$item['prize_value'];
                    break;
            }
            $item['prize_image'] = UrlUtils::getAbsoluteUrl($item['prize_image']);
            unset($item['prize_type']);
            unset($item['prize_value']);
            unset($item['prize_tips']);
        }

        return $lists;
    }

    /**
     * 执行开始抽奖
     *
     * @author windy
     * @param int $userId
     * @return bool|array
     */
    public static function draw(int $userId) {
        try {
            // 配置参数
            $config = ConfigUtils::get('lottery', ['is_open'=>0, 'limit'=>0]);
            if (!$config['is_open']) {
                throw new Exception("抽奖活动已结束");
            }
            if (!$config['limit']) {
                throw new Exception("抽奖次数已用完");
            }

            // 已抽次数
            $drawn = (new LotteryRecord())
                ->where('create_time', '>=', TimeUtils::today()[0])
                ->where('create_time', '<=', TimeUtils::today()[1])
                ->where('user_id', '=', $userId)
                ->count();

            // 剩余次数
            $surplus = $config['limit'] - $drawn;
            $surplus = $surplus <= 0 ? 0 : $surplus;
            if ($surplus <= 0) {
                throw new Exception("今日次数已用完");
            }

            // 奖品池塘
            $prizes  = (new LotteryPrize())->limit(8)->select()->toArray();
            $inPrize = self::drawPrizePool($prizes);

            // 抽奖记录
            $record = LotteryRecord::create([
                'user_id'      => $userId,
                'prize_image'  => UrlUtils::getRelativeUrl($inPrize['image']),
                'prize_type'   => $inPrize['type'],
                'prize_name'   => $inPrize['name'],
                'prize_value'  => $inPrize['reward'],
                'prize_tips'   => $inPrize['tips'],
                'prize_snap'   => json_encode($inPrize, JSON_UNESCAPED_UNICODE),
                'create_time'  => time()
            ]);

            // 赠送积分
            if ($inPrize['type'] == 2) {
                LogIntegral::add(
                    LogIntegralEnum::LOTTERY_INC_INTEGRAL,
                    $inPrize['reward'], $userId, 0, $record['id'], '', '抽奖赠送积分');

                User::update([
                    'integral' => ['inc', $inPrize['reward']],
                    'update_time' => time()
                ], ['id'=>$userId]);
            }

            // 返回结果
            return [
                'id'   => $inPrize['id'],
                'tips' => $inPrize['tips']
            ];
        } catch (Exception $e) {
            static::$error = $e->getMessage();
            return false;
        }
    }

    /**
     * 从奖品池中抽取结果
     *
     * @author windy
     * @param $prizes (奖品池塘)
     * @return array 中奖的奖品
     */
    private static function drawPrizePool($prizes): array
    {
        $winId  = 0; //中奖编号
        $weight = 0; //累计权重

        // 累计所有权重
        foreach ($prizes as $val) {
            $weight += $val['probability'];
        }

        // 根据权重进行随机抽奖
        shuffle($prizes);
        foreach ($prizes as $key => $value) {
            $randNum = mt_rand(1, $weight);
            if ($randNum <= $value['probability']) {
                $winId = $value['id'];
                break;
            } else {
                $weight -= $value['probability'];
            }
        }

        // 查询出中奖奖品的具体信息
        $inPrize = [];
        foreach ($prizes as $item) {
            if ($item['id'] == $winId) {
                $inPrize = $item;
                break;
            }
        }

        // 中奖但奖品数量不足等同没中奖
        if ($inPrize['type'] != 1 && $inPrize['number'] <= 0) {
            foreach ($prizes as $item) {
                if ($item['type'] == 1) {
                    return $item;
                }
            }
        }

        return $inPrize;
    }

}