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
use app\common\enum\LogWalletEnum;
use app\common\enum\WithdrawalEnum;
use app\common\model\log\LogWallet;
use app\common\model\user\User;
use app\common\model\Withdraw;
use app\common\utils\ConfigUtils;
use Exception;
use think\facade\Db;

/**
 * 佣金提现接口-逻辑层
 * Class WithdrawLogic
 * @package app\api\logic
 */
class WithdrawLogic extends Logic
{
    /**
     * 提现配置
     * @author windy
     * @param int $userId
     * @return array
     */
    public static function config(int $userId): array
    {
        // 基础配置
        $detail = ConfigUtils::get('withdrawal', [
            'withdraw_way'            => json_encode([]),
            'withdraw_min_money'      => 0,
            'withdraw_max_money'      => 0,
            'withdraw_service_charge' => 0
        ]);

        // 提现方式
        $withdrawWay = json_decode($detail['withdraw_way'], true);
        $detail['withdraw_way'] = [];
        foreach ($withdrawWay as $value) {
            $detail['withdraw_way'][] = [
                'name'  => WithdrawalEnum::getTypeDesc($value),
                'value' => intval($value)
            ];
        }

        // 剩余佣金
        $detail['earnings'] = (new User())->where(['id'=>$userId])->value('earnings') ?? 0;

        // 返回结果
        return $detail;
    }

    /**
     * 提现申请
     * @author windy
     * @param array $post
     * @param int $userId
     * @return bool
     */
    public static function apply(array $post, int $userId): bool
    {
        Db::startTrans();
        try {
            // 提现配置
            $config = self::config($userId);

            // 提现验证
            if (empty($post['money'])) {
                throw new Exception('提现金额不能为空');
            }

            if ($config['earnings'] <= 0 || $config['earnings'] < $post['money']) {
                throw new Exception('抱歉当前可提现金额不足');
            }

            if ($post['money'] < $config['withdraw_min_money'] && $config['withdraw_min_money']) {
                throw new Exception('单次最少提现金额为'.$config['withdraw_min_money'].'元');
            }

            if ($post['money'] > $config['withdraw_max_money'] && $config['withdraw_max_money']) {
                throw new Exception('单次最大提现金额为'.$config['withdraw_max_money'].'元');
            }

            // 手续费用
            $service_charge = 0;
            $actual_money   = $post['money'];
            if ($config['withdraw_service_charge']) {
                $charge = $config['withdraw_service_charge'] / 100;
                $service_charge = round($actual_money * $charge, 2);
                $actual_money   = $actual_money - $service_charge;
            }

            // 发起申请
            $withdraw = Withdraw::create([
                'withdraw_sn'          => make_order_no(),
                'user_id'              => $userId,
                'type'                 => $post['type'],
                'real_name'            => $post['real_name'] ?? '',
                'account'              => $post['account'] ?? '',
                'bank'                 => $post['bank'] ?? '',
                'subbank'              => $post['subbank'] ?? '',
                'money_qr_code'        => $post['money_qr_code'] ?? '',
                'apply_money'          => $post['money'],
                'actual_money'         => $actual_money,
                'service_charge'       => $service_charge,
                'service_charge_ratio' => $config['withdraw_service_charge'],
                'remark'               => $post['remark'] ?? '',
                'status'               => 1,
                'create_time'          => time(),
                'update_time'          => time()
            ]);

            // 减少佣金
            User::update(['earnings'=>['dec', $post['money']]], ['id'=>$userId]);

            // 钱包日志
            LogWallet::create([
                'admin_id'      => 0,
                'user_id'       => $userId,
                'log_sn'        => make_order_no(),
                'source_type'   => LogWalletEnum::WITHDRAW_DEC_EARNINGS,
                'source_id'     => $withdraw['id'],
                'source_sn'     => $withdraw['withdraw_sn'],
                'change_type'   => 2,
                'change_amount' => $post['money'],
                'before_amount' => $config['earnings'],
                'after_amount'  => $config['earnings'] - $post['money'],
                'remarks'       => '提现申请'
            ]);

            Db::commit();
            return true;
        } catch (Exception $e) {
            Db::rollback();
            static::$error = $e->getMessage();
            return false;
        }
    }

    /**
     * 提现记录
     *
     * @author windy
     * @param array $get
     * @return array
     * @throws @\think\db\exception\DbException
     */
    public static function record(array $get, $userId): array
    {
        $lists = (new Withdraw())
            ->field(['id,type,status,apply_money,audit_remark,transfer_remark,create_time'])
            ->order('id desc')
            ->where('user_id', $userId)
            ->paginate([
                'page'      => $get['page'] ?? 1,
                'list_rows' => $get['limit'] ?? 20,
                'var_page'  => 'page'
            ])->toArray();

        foreach ($lists['data'] as &$item) {
            $item['state'] = WithdrawalEnum::getStatusDesc($item['status']);
            $item['type']  = '提现至'.WithdrawalEnum::getTypeDesc($item['type']);
            $item['tips']  = $item['audit_remark'] ? $item['audit_remark'] : '';
            $item['tips']  = $item['transfer_remark'] ? $item['transfer_remark'] : $item['tips'];
            unset($item['audit_remark']);
            unset($item['transfer_remark']);
        }

        return $lists;
    }

    /**
     * 提现详情
     *
     * @author windy
     * @param int $id
     * @return array
     */
    public static function detail(int $id): array
    {
        $detail = (new Withdraw())
            ->field(['id,type,status,withdraw_sn,apply_money,apply_money,service_charge,create_time'])
            ->findOrEmpty($id)->toArray();

        $detail['state'] = WithdrawalEnum::getStatusDesc($detail['status']);
        $item['type']  = WithdrawalEnum::getTypeDesc($detail['type']);
        return $detail;
    }
}