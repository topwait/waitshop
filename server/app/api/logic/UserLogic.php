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

namespace app\api\logic;


use app\common\basics\Logic;
use app\common\enum\LinkEnum;
use app\common\enum\LogWalletEnum;
use app\common\enum\NoticeEnum;
use app\common\model\diy\DiyMe;
use app\common\model\diy\DiyOrder;
use app\common\model\log\LogIntegral;
use app\common\model\log\LogSms;
use app\common\model\log\LogWallet;
use app\common\model\addons\CouponList;
use app\common\model\addons\DistributionOrder;
use app\common\model\store\StoreClerk;
use app\common\model\user\User;
use app\common\model\user\UserGrade;
use app\common\utils\ConfigUtils;
use app\common\utils\TimeUtils;
use app\common\utils\UrlUtils;
use think\facade\Db;
use Exception;

/**
 * 用户接口-逻辑层
 * Class UserLogic
 * @package app\api\logic
 */
class UserLogic extends Logic
{
    /**
     * 装修设计
     *
     * @author windy
     * @param $userId
     * @return array
     * @throws @\think\db\exception\DataNotFoundException
     * @throws @\think\db\exception\DbException
     * @throws @\think\db\exception\ModelNotFoundException
     */
    public static function design(int $userId=0): array
    {
        // 我的订单装修
        $detail['order'] = (new DiyOrder())
            ->field('id,name,image,path')
            ->order('sort asc, id asc')
            ->limit(5)
            ->select()->toArray();

        // 我的功能装修
        $diyMe = (new DiyMe())
            ->field('id,name,image,link_type,link_url')
            ->order('sort asc, id asc')
            ->where(['is_delete'=>0, 'is_show'=>1])
            ->select()->toArray();

        foreach ($diyMe as &$item) {
            if ($item['link_type'] == 1) {
                $menu = LinkEnum::getLink(intval($item['link_url']));
                if (!empty($menu['link'])) {
                    $item['link_url']  = $menu['link'];
                    if ($menu['name'] == '联系客服') {
                        $item['link_type'] = 3;
                    }
                }
            }

            $patter = '/bundle/pages/writeoff_index/writeoff_index';
            if (strpos($item['link_url'], $patter) === 0) {
                $clerk = (new StoreClerk())
                    ->field('id,nickname')
                    ->where(['user_id'=>$userId])
                    ->where(['is_delete'=>0])
                    ->where(['status'=>1])
                    ->findOrEmpty()
                    ->toArray();
                if ($userId === 0 || !$clerk) {
                    unset($item);
                }
            } else {
                $detail['me'][] = $item;
            }
        }

        // 商城默认头像
        $detail['avatar'] = UrlUtils::getAbsoluteUrl(
            ConfigUtils::get('backstage')['mall_avatar'] ??
            '/static/images/default_avatar.png');

        // 返回配置
        return $detail;
    }

    /**
     * 个人中心
     *
     * @author windy
     * @param int $userId
     * @return array
     * @throws @\think\db\exception\DataNotFoundException
     * @throws @\think\db\exception\DbException
     * @throws @\think\db\exception\ModelNotFoundException
     */
    public static function center(int $userId): array
    {
        // 用户信息
        $userInfo = (new User())->field([
            'id,avatar,nickname,grade_id',
            'money,integral,growth_value'
        ])->withJoin(['grade'=>['id', 'name', 'upgrade']], 'left')
          ->findOrEmpty($userId)->toArray();

        // 优惠券数量
        $userInfo['coupon_num'] = (new CouponList())->where([
            ['user_id', '=', $userInfo['id']],
            ['is_use', '=', 0],
            ['is_expire', '=', 0]
        ])->count();

        // 所有会员等级
        $grades = (new UserGrade())
            ->where(['is_disable'=>0, 'is_delete'=>0])
            ->order('weight asc')
            ->select()
            ->toArray();

        // 当前等级下标
        $gradeIndex = 0;
        foreach ($grades as $key => $grade) {
            if ($grade['id'] == $userInfo['grade_id']) {
                $gradeIndex = $key;
            }
        }

        // 差多少升级, -1=已是最高等级
        if (!empty($grades[$gradeIndex + 1])) {
            $lackGrowthValue = $grades[$gradeIndex + 1]['upgrade']['total_growth_value'] - $userInfo['growth_value'];
            $userInfo['lackGrowthValue'] = $lackGrowthValue <= 0 ? 0 : $lackGrowthValue;
        } else {
            $userInfo['lackGrowthValue'] = -1;
        }

        // 格式化数据
        $userInfo['avatar']    = $userInfo['avatar'] ? $userInfo['avatar'] : UrlUtils::getAbsoluteUrl('static/images/default_avatar.png');
        $userInfo['gradeName'] = $userInfo['grade']['name'];
        unset($userInfo['grade']);

        return $userInfo;
    }

    /**
     * 用户信息
     *
     * @author windy
     * @param int $userId
     * @return array
     */
    public static function info(int $userId): array
    {
        $model = new User();
        return $model->field('id,sn,avatar,nickname,mobile')->findOrEmpty($userId)->toArray();
    }

    /**
     * 用户钱包信息
     *
     * @author windy
     * @param int $userId
     * @return array
     */
    public static function wallet(int $userId): array
    {
        return (new User())
            ->field(['id,nickname,integral,money,total_order_amount,total_recharge_amount,earnings'])
            ->withAttr(['stayUnlock'=>function() use($userId) {
                return (new DistributionOrder())
                    ->where(['user_id'=>$userId, 'status'=>1])
                    ->sum('earnings_money');
            }])->append(['stayUnlock'])
            ->findOrEmpty($userId)->toArray();
    }

    /**
     * 设置用户信息
     *
     * @author windy
     * @param array $post
     * @param int $userId
     * @return bool
     */
    public static function setUser(array $post, int $userId): bool
    {
        Db::startTrans();
        try {
            if (empty($post['key'])) {
                throw new Exception('更新字段异常');
            }

            if (empty($post['value'])) {
                throw new Exception('更新值异常');
            }

            if ($post['key'] == 'mobile') {
                // 校验手机号
                $m = (new User())
                    ->where('mobile', '=', trim($post['value']))
                    ->where('id', '<>', $userId)
                    ->findOrEmpty();

                if (!$m->isEmpty()) {
                    throw new Exception('该手机号已存在');
                }

                // 校验验证码
                $user = (new User())->field('id,mobile')->findOrEmpty($userId)->toArray();
                $logSms = (new LogSms())->where([
                    'code'   => trim($post['code'] ?? ''),
                    'mobile' => trim($post['value']),
                    'scene'  => $user['mobile'] ? NoticeEnum::SMS_CHANGE_MOBILE_NOTICE : NoticeEnum::SMS_BIND_MOBILE_NOTICE
                ])->findOrEmpty()->toArray();
                if (!$logSms || $logSms['is_verify'] || strtotime($logSms['create_time']) + (60 * 15) < time()) {
                    throw new Exception('验证码无效');
                }

                // 核销验证码
                LogSms::update(['is_verify'=>1, 'update_time'=>time()], ['id'=>$logSms['id']]);
            }

            User::update([
                $post['key']  => trim($post['value']),
                'update_time' => time()
            ], ['id'=>$userId]);

            Db::commit();
            return true;
        } catch (Exception $e) {
            Db::rollback();
            static::$error = $e->getMessage();
            return false;
        }
    }

    /**
     * 用户积分
     *
     * @author windy
     * @param array $get
     * @param int $userId
     * @return array
     * @throws @\think\db\exception\DbException
     */
    public static function integral(array $get, int $userId): array
    {
        // 当前剩余积分
        $surplusIntegral = (new User())->where(['id'=>$userId])->value('integral');

        // 累计获得积分
        $totalGetIntegral = (new LogIntegral())
            ->where(['user_id'=>$userId, 'change_type'=>1])
            ->sum('change_amount');

        // 累计使用积分
        $totalUseIntegral = (new LogIntegral())
            ->where(['user_id'=>$userId, 'change_type'=>2])
            ->sum('change_amount');

        // 今天获得积分
        $todayIntegral = (new LogIntegral())
            ->where(['user_id'=>$userId, 'change_type'=>1])
            ->where('create_time', '>=', TimeUtils::today()[0])
            ->where('create_time', '<=', TimeUtils::today()[1])
            ->sum('change_amount');

        // 积分日志列表
        $logIntegral = (new LogIntegral())
            ->order('id desc')
            ->where(['user_id'=>$userId])
            ->paginate([
                'page'      => $get['page'] ?? 1,
                'list_rows' => $get['limit'] ?? 20,
                'var_page'  => 'page',
            ])->toArray();

        $logIntegral['surplusIntegral']  = $surplusIntegral;
        $logIntegral['totalGetIntegral'] = $totalGetIntegral;
        $logIntegral['totalUseIntegral'] = $totalUseIntegral;
        $logIntegral['todayIntegral']    = $todayIntegral;
        return $logIntegral;
    }

    /**
     * 账户明细
     *
     * @author windy
     * @param array $get
     * @param int $userId
     * @return array
     * @throws @\think\db\exception\DbException
     */
    public static function account(array $get, int $userId): array
    {
        $where[] = ['user_id', '=', $userId];
        if (!empty($get['type']) && $get['type']) {
            $where[] = ['change_type', '=', intval($get['type'])];
        }

        $lists = (new LogWallet())
            ->field('id,source_type,change_amount,change_type,create_time')
            ->where($where)
            ->order('id desc')
            ->paginate([
                'page'      => $get['page'] ?? 1,
                'list_rows' => $get['limit'] ?? 20,
                'var_page'  => 'page',
            ])->toArray();

        foreach ($lists['data'] as &$item) {
            $item['source_type'] = LogWalletEnum::getLogDesc($item['source_type']);
            $item['change_amount'] = $item['change_type']==1 ? '+'.$item['change_amount'] : '-'.$item['change_amount'];
        }

        return $lists;
    }

    /**
     * 用户等级列表
     *
     * @author windy
     * @param int $userId
     * @return array
     * @throws @\think\db\exception\DataNotFoundException
     * @throws @\think\db\exception\DbException
     * @throws @\think\db\exception\ModelNotFoundException
     */
    public static function grade(int $userId): array
    {
        $lists = (new UserGrade())
            ->field('id,name,weight,icon,background,equity,upgrade')
            ->where(['is_disable'=>0, 'is_delete'=>0])
            ->order('weight asc, id asc')
            ->select()
            ->toArray();

        $weight   = 0;
        $userInfo = (new User())->field('id,grade_id,nickname,avatar,growth_value')->findOrEmpty($userId);
        foreach ($lists as $item) {
            if ($item['id'] == $userInfo['grade_id']) {
                $weight = $item['weight'];
                break;
            }
        }

        foreach ($lists as &$item) {
            $upgrades  = $item['upgrade'];
            $progress = $upgrades['total_growth_value'] > 0 ? round($userInfo['growth_value'] / $upgrades['total_growth_value'], 2) : 0;
            $item['userInfo'] = $userInfo;
            $item['progress'] = ($progress * 100) >= 100 ? 100 : ($progress * 100);
            $item['equity'] = $item['equity'] * 10;
            $item['unlock'] = false;
            if (
                $upgrades['total_growth_value']  <= $userInfo['growth_value'] &&
                $weight >= $item['weight']) {
                $item['unlock']   = true;
                $item['progress'] = 100;
            }
        }

        return $lists;
    }

}