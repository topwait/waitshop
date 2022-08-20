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


use app\common\basics\Logic;
use app\common\enum\DistributionEnum;
use app\common\enum\LogIntegralEnum;
use app\common\enum\LogWalletEnum;
use app\common\model\addons\DistributionApply;
use app\common\model\addons\DistributionExtend;
use app\common\model\addons\DistributionOrder;
use app\common\model\log\LogIntegral;
use app\common\model\log\LogWallet;
use app\common\model\user\User;
use app\common\utils\ConfigUtils;
use app\common\utils\UrlUtils;
use Exception;
use think\facade\Db;

/**
 * 分销-逻辑层
 * Class DistributionLogic
 * @package app\api\logic
 */
class DistributionLogic extends Logic
{
    /**
     * 绑定用户上下级关系
     *
     * @author windy
     * @param string $code
     * @param int $userId
     * @return bool
     */
    public static function code(string $code, int $userId): bool
    {
        Db::startTrans();
        try {
            $model = new User();
            $myLeader = $model->field(['id', 'first_leader', 'second_leader', 'third_leader', 'ancestor_relation'])
                ->where('distribution_code', '=', $code)
                ->findOrEmpty()->toArray();

            // 更新我的第一上级、第二上级、第三上级、关系链
            $ancestorRelation = trim("{$myLeader['id']},{$myLeader['ancestor_relation']}", ',');
            User::update([
                'first_leader'      => $myLeader['id'],
                'second_leader'     => $myLeader['first_leader'],
                'third_leader'      => $myLeader['second_leader'],
                'ancestor_relation' => $ancestorRelation,
            ], ['id'=>$userId]);

            // 更新我向下一级的第二上级、第三上级
            User::update([
                'second_leader' => $myLeader['id'],
                'third_leader'  => $myLeader['first_leader']
            ], ['first_leader'=>$userId]);

            // 更新我向下二级的第三级
            User::update([
                'third_leader' => $myLeader['id'],
            ], ['second_leader'=>$userId]);

            // 更新与我相关的所有关系链
            $model->where("find_in_set({$userId},ancestor_relation)")
                ->exp('ancestor_relation', "replace(ancestor_relation,'{$userId}','" . trim("{$userId},{$ancestorRelation}", ',') . "')")
                ->update();

            // 记录我的推广粉丝数量
            $fansNum = $model->where('first_leader', '=', $userId)
                ->where('is_delete', '=', 0)
                ->count();

            DistributionExtend::update([
                'fans_num'    => $fansNum,
                'update_time' => time()
            ], ['id'=>$userId]);

            // 邀请赠送积分/佣金
            $reward   = ConfigUtils::get('reward');
            $integral = intval($reward['invited_reward_integral'] ?? 0);
            $earnings = intval($reward['invited_reward_earnings'] ?? 0);
            if ($integral || $earnings ) {
                User::update([
                    'integral' => ['inc', $integral],
                    'money'    => ['inc', $earnings],
                ], ['id' => $myLeader['id']]);

                LogIntegral::add(LogIntegralEnum::INVITE_INC_INTEGRAL,
                    $integral, $myLeader['id'], 0, 0, '',
                    '邀请用户赠送积分:'.$userId);

                LogWallet::add(LogWalletEnum::INVITE_INC_EARNINGS,
                    $earnings, $myLeader['id'], 0, 0, '',
                    '邀请用户赠送佣金:'.$userId);
            }

            Db::commit();
            return true;
        } catch (Exception $e) {
            Db::rollback();
            static::$error = $e->getMessage();
            return false;
        }
    }

    /**
     * 申请成为分销员
     *
     * @author windy
     * @param array $post 请求参数
     * @param int $userId 用户ID
     * @return bool
     */
    public static function apply(array $post, int $userId): bool
    {
        try {
            $apply = (new DistributionApply())
                ->where(['user_id'=>$userId, 'status'=>0])
                ->findOrEmpty()
                ->toArray();

            if ($apply) {
                throw new Exception("已收到您的申请,请耐心等待审核");
            }

            if ($post['invite_code']) {
                $code = (new User())->where(['distribution_code'=>$post['distribution_code']])->findOrEmpty()->toArray();
                if (!$code) {
                    throw new Exception('请填写正确的邀请码或不填');
                }
            }

            DistributionApply::create([
                'user_id'      => $userId,
                'nickname'     => $post['nickname'],
                'telephone'    => $post['telephone'],
                'apply_reason' => $post['apply_reason'] ?? '',
                'invite_code'  => $post['invite_code'] ?? '',
                'province_id'  => $post['province_id'],
                'city_id'      => $post['city_id'],
                'district_id'  => $post['district_id'],
                'status'       => 0
            ]);

            return true;
        } catch (Exception $e) {
            static::$error = $e->getMessage();
            return false;
        }
    }

    /**
     * 分销主页信息
     *
     * @author windy
     * @param int $userId
     * @return array
     */
    public static function index(int $userId): array
    {
        $userModel = new User();
        $distributionOrderModel = new DistributionOrder();

        // 获取用户信息
        $detail['user'] = $userModel->field('id,sn,nickname,avatar,first_leader,is_distribution,distribution_level')
            ->where(['id'=>$userId])
            ->withAttr([
                'first_leader'       => function($value) { return (new User())->where(['id'=>$value])->value('nickname') ?? ''; },
                'distribution_level' => function() { return '分销会员'; }
            ])->findOrEmpty()->toArray();

        // 今日预估收益
        $detail['todayEarnings'] = $distributionOrderModel
            ->where(['user_id'=>$userId, 'status'=>1])
            ->whereTime('create_time', 'today')
            ->sum('earnings_money');

        // 本月预估收益
        $detail['monthEarnings'] = $distributionOrderModel
            ->where(['user_id'=>$userId, 'status'=>1])
            ->whereTime('create_time', 'month')
            ->sum('earnings_money');

        // 累计获得收益
        $detail['totalEarnings'] = $distributionOrderModel
            ->where(['user_id'=>$userId, 'status'=>1])
            ->sum('earnings_money');

        // 待解锁收益
        $detail['stayUnlock'] = $distributionOrderModel
            ->where(['user_id'=>$userId, 'status'=>1])
            ->sum('earnings_money');

        // 可提现收益
        $detail['canExtract'] = $userModel->where(['id'=>$userId])->value('earnings');

        // 申请结果
        if (!$detail['user']['is_distribution']) {
            $detail['apply'] = (new DistributionApply())
                ->withoutField('create_time,update_time')
                ->where(['user_id' => $userId])
                ->order('id desc')
                ->findOrEmpty()
                ->toArray();
        }

        // 返回结果
        return $detail;
    }

    /**
     * 推广的粉丝列表
     *
     * @author windy
     * @param array $get 请求参数
     * @param int $userId 用户Id
     * @return array
     * @throws @\think\db\exception\DbException
     */
    public static function fans(array $get, int $userId): array
    {
        $where = [];
        if (!empty($get['keyword']) && $get['keyword']) {
            $where[] = ['U.nickname|U.mobile', 'like', '%'.$get['keyword'].'%'];
        }

        switch ($get['type'] ?? 'all') {
            case 'first':
                $where[] = ['U.first_leader', '=', $userId];
                break;
            case 'second':
                $where[] = ['U.second_leader', '=', $userId];
                break;
            default:
                $where[] = ['U.first_leader|U.second_leader', '=', $userId];
        }

        $order = ['U.id'=>'desc'];
        switch ($get['filter'] ?? 'all') {
            case 'all':
                break;
            case 'fans':
                $order = ['fans_num'=>$get['sort'] ?? 'desc', 'U.id'=>'desc'];
                break;
            case 'money':
                $order = ['deal_order_num'=>$get['sort'] ?? 'desc', 'U.id'=>'desc'];
                break;
            case 'order':
                $order = ['deal_order_money'=>$get['sort'] ?? 'desc', 'U.id'=>'desc'];
                break;
        }

        $lists = (new User())->alias('U')
            ->field('U.id,U.avatar,U.nickname,U.mobile,DE.fans_num,DE.distribution_order_num,DE.distribution_order_money')
            ->leftJoin('distribution_extend DE', 'DE.user_id=U.id')
            ->where($where)
            ->order($order)
            ->paginate([
                'page'      => $get['page'] ?? 1,
                'list_rows' => $get['limit'] ?? 20,
                'var_page'  => 'page'
            ])->toArray();

        foreach ($lists['data'] as &$item) {
            $item['avatar']           = UrlUtils::getAbsoluteUrl($item['avatar']);
            $item['mobile']           = hidePhone($item['mobile']);
            $item['fans_num']         = $item['fans_num'] ?? 0;
            $item['deal_order_num']   = $item['deal_order_num'] ?? 0;
            $item['deal_order_money'] = $item['deal_order_money'] ?? 0;
        }

        return $lists;
    }

    /**
     * 分销订单列表
     *
     * @author windy
     * @param array $get 请求参数
     * @param int $userId 用户ID
     * @return array
     * @throws @\think\db\exception\DbException
     */
    public static function order(array $get, int $userId): array
    {
        $where[] = ['DO.user_id', '=', $userId];
        if (!empty($get['status']) && $get['status']) {
            $where[] = ['DO.status', '=', intval($get['status'])];
        }

        $model = new DistributionOrder();
        $lists = $model->alias('DO')
            ->field('DO.id,O.order_sn,OG.name,OG.image,OG.count,DO.reality_amount,DO.earnings_money,DO.create_time,DO.status')
            ->join('order O', 'O.id = DO.order_id')
            ->join('order_goods OG', 'OG.id  = DO.order_goods_id')
            ->where($where)
            ->paginate([
                'page'      => $get['page'] ?? 1,
                'list_rows' => $get['limit'] ?? 20,
                'var_page'  => 'page'
            ])->toArray();

        foreach ($lists['data'] as &$item) {
            $item['str_status'] = DistributionEnum::getSettleStatus($item['status']);
        }

        return $lists;
    }

    /**
     * 佣金明细记录
     *
     * @author windy
     * @param array $get
     * @param int $userId
     * @return array
     * @throws @\think\db\exception\DbException
     */
    public static function record(array $get, int $userId): array
    {
        $lists = (new LogWallet())
            ->field(['id,source_type,change_type,change_amount,create_time'])
            ->where([
                'user_id'     => $userId,
                'source_type' => LogWalletEnum::DISTRIBUTION_INC_EARNINGS
            ])
            ->paginate([
                'page'      => $get['page'] ?? 1,
                'list_rows' => $get['limit'] ?? 20,
                'var_page'  => 'page'
            ])->toArray();

        foreach ($lists['data'] as &$item) {
            $symbol = $item['change_type'] == 1 ? '+' : '-';
            $item['change_amount'] = $symbol.$item['change_amount'];
            $item['source_type'] = LogWalletEnum::getLogDesc($item['source_type']);
        }

        return $lists;
    }
}