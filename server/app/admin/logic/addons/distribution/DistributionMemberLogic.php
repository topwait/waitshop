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

namespace app\admin\logic\addons\distribution;


use app\common\basics\Logic;
use app\common\model\addons\DistributionExtend;
use app\common\model\addons\DistributionOrder;
use app\common\model\user\User;
use Exception;
use think\facade\Db;

/**
 * 分销会员-逻辑层
 * Class MemberLogic
 * @package app\admin\logic\marketing\distribution
 */
class DistributionMemberLogic extends Logic
{
    /**
     * 分销会员列表
     *
     * @author windy
     * @param array $get
     * @return array|false
     */
    public static function lists(array $get)
    {
        try {
            self::setSearch([
                '='        => ['status@distribution_freeze'],
                'datetime' => ['datetime@create_time'],
                'keyword'  => [
                    'userSn'   => ['%like%', 'sn'],
                    'nickname' => ['%like%', 'nickname'],
                    'mobile'   => ['=', 'mobile'],
                    'code'     => ['=', 'distribution_code'],
                ]
            ]);

            $model = new User();
            $lists = $model->field(['id,grade_id,sn,avatar,nickname,mobile,first_leader,is_distribution,distribution_code,distribution_freeze'])
                  ->where(['is_delete'=>0, 'is_distribution'=>1])
                  ->where(self::$searchWhere)
                  ->with(['grade'])
                  ->order('id desc')
                  ->paginate([
                    'page'      => $get['page'],
                    'list_rows' => $get['limit'],
                    'var_page'  => 'page',
                ])->toArray();

            foreach ($lists['data'] as &$item) {
                if ($item['first_leader']) {
                    $item['upperUser'] = $model->field(['id,grade_id,sn,avatar,nickname,mobile'])
                        ->with(['grade'])
                        ->findOrEmpty($item['first_leader'])
                        ->toArray();
                }
            }

            return ['count'=>$lists['total'], 'list'=>$lists['data']];
        } catch (Exception $e) {
            static::$error = $e->getMessage();
            return false;
        }
    }

    /**
     * 推广的下级会员列表
     *
     * @author windy
     * @param array $get
     * @return array|false
     */
    public static function fans(array $get)
    {
        try {
            self::setSearch([
                '='        => ['status@distribution_freeze'],
                'keyword'  => [
                    'userSn'   => ['%like%', 'sn'],
                    'nickname' => ['%like%', 'nickname'],
                    'mobile'   => ['=', 'mobile'],
                    'code'     => ['=', 'code@distribution_code'],
                ]
            ]);

            $uid = $get['uid'];
            $where[] = ['is_distribution', '=', 1];
            $where[] = ['is_delete', '=', 0];
            $where[] = ($get['type'] ?? 'all') !== 'all' ? [$get['type'], '=', $uid] : ['', 'exp', Db::raw("FIND_IN_SET($uid, ancestor_relation)")];

            $model = new User();
            $lists = $model->field(['id,grade_id,sn,avatar,nickname,mobile,first_leader,is_distribution,distribution_code'])
                ->where($where)
                ->where(self::$searchWhere)
                ->with(['grade'])
                ->order('id desc')
                ->paginate([
                    'page'      => $get['page'],
                    'list_rows' => $get['limit'],
                    'var_page'  => 'page',
                ])->toArray();

            foreach ($lists['data'] as &$item) {
                if ($item['first_leader']) {
                    $item['upperUser'] = $model->field(['id,grade_id,sn,avatar,nickname,mobile'])
                        ->with(['grade'])
                        ->findOrEmpty($item['first_leader'])
                        ->toArray();
                }
            }

            return ['count'=>$lists['total'], 'list'=>$lists['data']];
        } catch (Exception $e) {
            static::$error = $e->getMessage();
            return false;
        }
    }

    /**
     * 分销会员详细
     *
     * @author windy
     * @param int $uid
     * @return array
     */
    public static function detail(int $uid): array
    {
        $model = new User();
        $user = $model->field([
                'id,grade_id,avatar,nickname,mobile,first_leader',
                'money,integral,distribution_code'
            ])->with(['grade'])
            ->where(['id'=>intval($uid)])
            ->findOrEmpty()
            ->toArray();

        $upperUser = $model->field(['id,nickname,mobile,distribution_code'])
            ->findOrEmpty(intval($user['first_leader']))->toArray();

        return ['user'=>$user, 'upperUser'=>$upperUser];
    }

    /**
     * 冻结会员分销资格
     *
     * @author windy
     * @param $uid
     * @return bool
     */
    public static function freeze($uid): bool
    {
        try {
            User::update([
                'distribution_freeze' => 1,
                'update_time' => time()
            ], ['id'=>intval($uid)]);

            return true;
        } catch (Exception $e) {
            static::$error = $e->getMessage();
            return false;
        }
    }

    /**
     * 解冻会员分销资格
     *
     * @author windy
     * @param $uid
     * @return bool
     */
    public static function thaw($uid): bool
    {
        try {
            User::update([
                'distribution_freeze' => 0,
                'update_time' => time()
            ], ['id'=>intval($uid)]);

            return true;
        } catch (Exception $e) {
            static::$error = $e->getMessage();
            return false;
        }
    }

    /**
     * 佣金明细
     *
     * @param array $get
     * @return array
     * @throws @\think\db\exception\DbException
     */
    public static function commission(array $get): array
    {

        self::setSearch([
            '='        => ['status@DO.status'],
            'keyword'  => [
                'distribution_sn' => ['%like%', 'distribution_sn@DO.distribution_sn'],
                'order_sn'        => ['%like%', 'order_sn@O.order_sn'],
            ]
        ]);


        $lists = (new DistributionOrder())
            ->alias('DO')
            ->field('DO.id,order_goods_id,distribution_sn,O.order_sn,
                reality_amount,earnings_money,earnings_ratio,DO.status,DO.create_time')
            ->where(['DO.user_id'=>intval($get['uid'])])
            ->where(self::$searchWhere)
            ->with(['orderGoods'])
            ->join('order O', 'O.id=DO.order_id')
            ->order('DO.id desc')
            ->paginate([
                'page'      => $get['page'],
                'list_rows' => $get['limit'],
                'var_page'  => 'page',
            ])->toArray();

        foreach ($lists['data'] as &$item) {
            $item['reality_amount'] = '￥'.$item['reality_amount'];
            $item['earnings_money'] = '￥'.$item['earnings_money'];
            $item['earnings_ratio'] = '%'.$item['earnings_ratio'];
        }

        return ['count'=>$lists['total'], 'list'=>$lists['data']];
    }

    /**
     * 新增分销会员
     *
     * @author windy
     * @param string $sn
     * @return bool
     */
    public static function addMember(string $sn): bool
    {
        try {
            $model = new User();
            $user = $model->field('id,is_distribution')
                ->where(['sn'=>trim($sn)])
                ->findOrEmpty()->toArray();
            if (!$user) throw new Exception('用户不存在');
            if ($user['is_distribution']) throw new Exception('已是分销会员,无需重复添加');

            User::update([
                'is_distribution' => 1,
                'update_time' => time()
            ], ['id'=>$user['id']]);

            return true;
        } catch (Exception $e) {
            static::$error = $e->getMessage();
            return false;
        }
    }

    /**
     * 调整分销会员上级
     *
     * @author windy
     * @param $userId (自己用户ID)
     * @param $leaderSn (新上级编码)
     * @return bool
     */
    public static function updateLeader($userId, $leaderSn): bool
    {
        Db::startTrans();
        try {
            // 清空上级/更改新上级
            $data = [
                'first_leader'      => 0,
                'second_leader'     => 0,
                'third_leader'      => 0,
                'ancestor_relation' => '',
            ];
            $myLeaderId         = 0;
            $myFirstLeaderId    = 0;
            $myAncestorRelation = '';

            if ($leaderSn > 0) {
                $myLeader = (new User())->where(['sn'=>$leaderSn])->findOrEmpty()->toArray();
                $myLeaderId         = $myLeader['id'];
                $myFirstLeaderId    = $myLeader['first_leader'];
                $myThirdLeaderId    = $myLeader['second_leader'];
                $myAncestorRelation = trim("{$myLeader['id']},{$myLeader['ancestor_relation']}", ',');
                $data = [
                    'first_leader'      => $myLeaderId,
                    'second_leader'     => $myFirstLeaderId,
                    'third_leader'      => $myThirdLeaderId,
                    'ancestor_relation' => $myAncestorRelation,
                ];
            }

            // 更新我的第一上级、第二上级、第三上级、关系链
            User::update($data, ['id'=>$userId]);

            // 更新我向下一级的第二上级、第三上级
            User::update([
                'second_leader' => $myLeaderId,
                'third_leader'  => $myFirstLeaderId
            ], ['first_leader'=>$userId]);

            // 更新我向下二级的第三级
            User::update([
                'third_leader' => $myLeaderId,
            ], ['second_leader'=>$userId]);

            // 更新与我相关的所有关系链
            (new User())->where("find_in_set({$userId},ancestor_relation)")
                ->exp('ancestor_relation', "replace(ancestor_relation,'{$userId}','" . trim("{$userId},{$myAncestorRelation}", ',') . "')")
                ->update();

            // 记录我的推广粉丝数量
            $fansNum = (new User())
                ->where('first_leader', '=', $userId)
                ->where('is_delete', '=', 0)
                ->count();

            DistributionExtend::update([
                'fans_num'    => $fansNum,
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
}