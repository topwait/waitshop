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

namespace app\admin\logic\finance;


use app\common\basics\Logic;
use app\common\enum\LogWalletEnum;
use app\common\enum\WithdrawalEnum;
use app\common\model\log\LogWallet;
use app\common\model\user\User;
use app\common\model\Withdraw;
use app\common\utils\UrlUtils;
use Exception;

/**
 * 佣金提现记录-逻辑层
 * Class WithdrawLogic
 * @package app\admin\logic\finance
 */
class WithdrawLogic extends Logic
{
    /**
     * 提现申请记录
     *
     * @author windy
     * @param array $get
     * @return array
     * @throws @\think\db\exception\DbException
     */
    public static function lists(array $get): array
    {
        self::setSearch([
            '='      => ['way@W.type', 'type@W.status'],
            '%like%' => ['user@U.sn|U.nickname|U.mobile', 'sn@W.withdraw_sn']
        ]);

        $lists = (new Withdraw())->alias('W')
            ->field(['W.*,U.sn,U.nickname,U.avatar,U.mobile'])
            ->join('user U', 'U.id = W.user_id')
            ->order(['id' => 'desc'])
            ->where(self::$searchWhere)
            ->paginate([
                'page'      => $get['page'] ?? 1,
                'list_rows' => $get['limit'] ?? 20,
                'var_page'  => 'page'
            ])->toArray();

        foreach ($lists['data'] as &$item) {
            $item['avatar'] = UrlUtils::getAbsoluteUrl($item['avatar']);
            $item['type'] = WithdrawalEnum::getTypeDesc($item['type']);
        }

        return ['count'=>$lists['total'], 'list'=>$lists['data']];
    }

    /**
     * 提现详细
     *
     * @author windy
     * @param int $id
     * @return mixed
     */
    public static function detail(int $id)
    {
        $detail = (new Withdraw())
            ->alias('W')
            ->field(['W.*,U.sn,U.nickname,U.mobile,A.username'])
            ->join('user U', 'U.id = W.user_id')
            ->leftJoin('admin A', 'A.id = W.admin_id')
            ->findOrEmpty($id)->toArray();

        $detail['type']   = WithdrawalEnum::getTypeDesc($detail['type']);
        $detail['status'] = WithdrawalEnum::getStatusDesc($detail['status']);
        $detail['transfer_time']    = date('Y-m-d H:i:s',  $detail['transfer_time']);
        $detail['transfer_voucher'] = $detail['transfer_voucher'] ? UrlUtils::getAbsoluteUrl($detail['transfer_voucher']) : '';

        return $detail;
    }

    /**
     * 提现统计数据
     *
     * @author windy
     * @return array
     */
    public static function statistics(): array
    {
        $model = new Withdraw();
        $detail['wait'] = $model->where(['status'=>WithdrawalEnum::STATUS_WAIT])->count();
        $detail['ing'] = $model->where(['status'=>WithdrawalEnum::STATUS_ING])->count();
        $detail['success'] = $model->where(['status'=>WithdrawalEnum::STATUS_SUCCESS])->count();
        $detail['fail'] = $model->where(['status'=>WithdrawalEnum::STATUS_FAIL])->count();
        return $detail;
    }

    /**
     * 审核提现审核
     *
     * @author windy
     * @param array $post
     * @param int $adminId
     * @return bool
     */
    public static function audit(array $post, int $adminId): bool
    {
        try {
            // 提现记录
            $withdraw = (new Withdraw())->findOrEmpty(intval($post['id']))->toArray();
            if (!$withdraw) {
                throw new Exception('申请记录不存在');
            }
            if ($withdraw['status'] != WithdrawalEnum::STATUS_WAIT) {
                throw new Exception('申请已被处理了');
            }

            // 更新状态
            switch ($withdraw['type']) {
                case WithdrawalEnum::TYPE_BANK:
                case WithdrawalEnum::TYPE_MNP_CODE:
                case WithdrawalEnum::TYPE_ALI_CODE:
                    Withdraw::update([
                        'status'       => $post['status'] == 1 ? WithdrawalEnum::STATUS_ING : WithdrawalEnum::STATUS_FAIL,
                        'admin_id'     => $adminId,
                        'audit_remark' => $post['remark'] ?? '',
                        'update_time'  => time()
                    ], ['id'=>$withdraw['id']]);
                    break;
                case WithdrawalEnum::TYPE_BALANCE:
                    Withdraw::update([
                        'status'       => $post['status'] == 1 ? WithdrawalEnum::STATUS_SUCCESS : WithdrawalEnum::STATUS_FAIL,
                        'admin_id'     => $adminId,
                        'audit_remark' => $post['remark'] ?? '',
                        'update_time'  => time()
                    ], ['id'=>$withdraw['id']]);

                    if ($post['status'] == 1) {
                        User::update(['money' => ['inc', $withdraw['actual_money']], 'update_time' => time()], ['id' => $withdraw['user_id']]);
                        LogWallet::add(
                            WithdrawalEnum::TYPE_BALANCE,
                            $withdraw['actual_money'], $withdraw['user_id'],
                            $adminId, $withdraw['id'], $withdraw['withdraw_sn'],
                            '提现到账'
                        );
                    }
                    break;
                case WithdrawalEnum::TYPE_WECHAT:
                    // todo 微信零钱
                    break;
            }

            // 提现回滚
            if ($post['status'] != 1) {
                $user = (new User())->field('id,earnings')->where(['id'=>$withdraw['user_id']])->findOrEmpty()->toArray();
                // 退回佣金
                User::update(['earnings'=>['inc', $post['apply_money']]], ['id'=>$withdraw['user_id']]);
                // 钱包日志
                LogWallet::create([
                    'admin_id'      => 0,
                    'user_id'       => $withdraw['user_id'],
                    'log_sn'        => make_order_no(),
                    'source_type'   => LogWalletEnum::WITHDRAW_ROLLBACK_EARNINGS,
                    'source_id'     => $withdraw['id'],
                    'source_sn'     => $withdraw['withdraw_sn'],
                    'change_type'   => 1,
                    'change_amount' => $withdraw['apply_money'],
                    'before_amount' => $user['earnings'],
                    'after_amount'  => $user['earnings'] + $withdraw['apply_money'],
                    'remarks'       => '提现回滚佣金'
                ]);
            }

            return true;
        } catch (Exception $e) {
            static::$error = $e->getMessage();
            return false;
        }
    }

    /**
     * 转账审核
     *
     * @author windy
     * @param array $post
     * @param int $adminId
     * @return false
     */
    public static function transfer(array $post, int $adminId): bool
    {
        try {
            // 提现记录
            $withdraw = (new Withdraw())->findOrEmpty(intval($post['id']))->toArray();
            if (!$withdraw) {
                throw new Exception('申请记录不存在');
            }
            if ($withdraw['status'] != WithdrawalEnum::STATUS_ING) {
                throw new Exception('提现申请已被处理了');
            }

            // 更新状态
            Withdraw::update([
                'status'           => $post['status'] == 1 ? WithdrawalEnum::STATUS_SUCCESS : WithdrawalEnum::STATUS_FAIL,
                'admin_id'         => $adminId,
                'transfer_remark'  => $post['remark'] ?? '',
                'transfer_voucher' => $post['transfer_voucher'] ?? '',
                'transfer_time'    => time()
            ], ['id'=>$withdraw['id']]);

            return true;
        } catch (Exception $e) {
            static::$error = $e->getMessage();
            return false;
        }
    }
}