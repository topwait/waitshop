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

namespace app\admin\logic\user;


use app\common\basics\Logic;
use app\common\enum\LogIntegralEnum;
use app\common\enum\LogWalletEnum;
use app\common\model\log\LogIntegral;
use app\common\model\log\LogWallet;
use app\common\model\user\User;
use Exception;

/**
 * 用户管理-逻辑层
 * Class UserLogic
 * @package app\admin\logic\user
 */
class UserLogic extends Logic
{
    /**
     * 用户列表
     *
     * @author windy
     * @param array $get
     * @return array|false
     * @throws @\think\db\exception\DbException
     */
    public static function lists(array $get)
    {
        self::setSearch([
            '='        => ['grade@grade_id'],
            'datetime' => ['datetime@create_time'],
            'keyword'  => [
                'userSn'   => ['%like%', 'sn'],
                'nickname' => ['%like%', 'nickname'],
                'mobile'   => ['=', 'mobile']
            ]
        ]);

        $model = new User();
        $lists = $model->field(true)
            ->order('id', 'desc')
            ->with(['grade'])
            ->where(['is_delete'=>0])
            ->where(self::$searchWhere)
            ->paginate([
                'page'      => $get['page'],
                'list_rows' => $get['limit'],
                'var_page'  => 'page',
            ])->toArray();

        foreach ($lists['data'] as &$item) {
            $item['login_time'] = date('Y-m-d H:i:s', $item['login_time']);
        }

        return ['count'=>$lists['total'], 'list'=>$lists['data']];
    }

    /**
     * 用户资料信息
     *
     * @author windy
     * @param int $user_id (用户ID)
     * @return array|false
     */
    public static function info(int $user_id)
    {
        $model = new User();
        return $model->field(true)->with(['grade'])
            ->findOrEmpty((int)$user_id)->toArray();
    }

    /**
     * 充值余额或积分
     *
     * @author windy
     * @param array $post
     * @param int $adminId
     * @return bool
     */
    public static function recharge(array $post, int $adminId): bool
    {
        try {
            if ($post["type"] === "money") {
                // 充值金额
                switch ($post["balance"]) {
                    case "inc": //增加
                        $update = ['money' => ['inc', floatval($post['money']), 'update_time'=>time()]];
                        User::update($update, ['id' => (int)$post['id']]);
                        LogWallet::add(LogWalletEnum::ADMIN_INC_MONEY, floatval($post['money']), intval($post['id']), $adminId, 0, '', $post['remark']);
                        break;
                    case "dec": //减少
                        $update = ['money' => ['dec', floatval($post['money']), 'update_time'=>time()]];
                        User::update($update, ['id' => (int)$post['id']]);
                        LogWallet::reduce(LogWalletEnum::ADMIN_DEC_MONEY, floatval($post['money']), intval($post['id']), $adminId, 0, '', $post['remark']);
                        break;
                    case "final": //最终
                        $money = (new User())->where(['id'=>$post['id']])->value('money');
                        $update = ['money' => floatval($post['money']), 'update_time'=>time()];
                        User::update($update, ['id' => (int)$post['id']]);

                        if (floatval($post['money']) > $money) {
                            $changeMoney = (float)$post['money'] - $money;
                            LogWallet::add(LogWalletEnum::ADMIN_INC_MONEY, $changeMoney, intval($post['id']), $adminId, 0, '', $post['remark']);
                        } else if ($money > (float)$post['money']) {
                            $changeMoney = $money - (float)$post['money'];
                            LogWallet::reduce(LogWalletEnum::ADMIN_DEC_MONEY, $changeMoney, intval($post['id']), $adminId, 0, '', $post['remark']);
                        }
                }

            } elseif ($post["type"] === "integral") {
                // 充值积分
                switch ($post["integral"]) {
                    case "inc": //增加
                        $update = ['integral' => ['inc', (int)$post['points'], 'update_time'=>time()]];
                        User::update($update, ['id' => (int)$post['id']]);
                        LogIntegral::add(LogIntegralEnum::ADMIN_INC_INTEGRAL, floatval($post['points']), intval($post['id']), $adminId, 0, '', $post['remarks']);
                        break;
                    case "dec": //减少
                        $update = ['integral' => ['dec', (int)$post['points'], 'update_time'=>time()]];
                        User::update($update, ['id' => (int)$post['id']]);
                        LogIntegral::add(LogIntegralEnum::ADMIN_DEC_INTEGRAL, floatval($post['points']), intval($post['id']), $adminId, 0, '', $post['remarks']);
                        break;
                    case "final": //最终
                        $integral = (new User())->where(['id'=>$post['id']])->value('integral');
                        $update = ['integral' => intval($post['points']), 'update_time'=>time()];
                        User::update($update, ['id' => (int)$post['id']]);

                        if (intval($post['points']) > $integral) {
                            $changeIntegral = intval($post['points']) - $integral;
                            LogWallet::add(LogIntegralEnum::ADMIN_INC_INTEGRAL, $changeIntegral, intval($post['id']), $adminId, 0, '', $post['remark']);
                        } else if ($integral > intval($post['points'])) {
                            $changeIntegral = $integral - intval($post['points']);
                            LogWallet::reduce(LogIntegralEnum::ADMIN_DEC_INTEGRAL, $changeIntegral, intval($post['id']), $adminId, 0, '', $post['remark']);
                        }
                }

            }

            return true;
        } catch (Exception $e) {
            static::$error = $e->getMessage();
            return false;
        }
    }

    /**
     * 调整用户等级
     *
     * @author windy
     * @param array $post
     * @return bool
     */
    public static function grade(array $post): bool
    {
        try {
            User::update([
                'grade_id'    => $post['grade'] ?? 0,
                'update_time' => time()
            ], ['id'=>(int)$post['id']]);

            return true;
        } catch (Exception $e) {
            static::$error = $e->getMessage();
            return false;
        }
    }
}