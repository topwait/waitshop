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
use app\common\enum\OrderEnum;
use app\common\model\addons\RechargeOrder;
use app\common\model\addons\RechargePackage;
use app\common\model\user\User;
use app\common\utils\ConfigUtils;
use Exception;

class RechargeLogic extends Logic
{
    /**
     * 充值页面信息
     *
     * @author windy
     * @param int $userId
     * @return array
     * @throws @\think\db\exception\DataNotFoundException
     * @throws @\think\db\exception\DbException
     * @throws @\think\db\exception\ModelNotFoundException
     */
    public static function index(int $userId)
    {
        $balance = (new User())->where(['id'=>$userId])->value('money');
        $package = (new RechargePackage())
            ->field(['id,money,give_money'])
            ->where(['is_show'=>1, 'is_delete'=>0])
            ->order('sort desc, id desc')
            ->select()
            ->toArray();

        $config = ConfigUtils::get('recharge');
        $config = [
            'is_open' => $config['is_open'] ?? 0,
            'min_recharge' => $config['min_recharge'] ?? 0,
            'max_recharge' => $config['max_recharge'] ?? 0
        ];

        return [
            'config'  => $config,
            'balance' => $balance,
            'package' => $package
        ];
    }

    /**
     * 执行下单充值
     *
     * @author windy
     * @param array $post
     * @param int $userId
     * @return bool|array
     */
    public static function buy(array $post, int $userId)
    {
        try {
            $config = ConfigUtils::get('recharge');
            $give_amount   = 0;
            $paid_amount   = $post['money'];
            $give_growth   = $config['give_growth'] ?? 0;
            $give_integral = $config['give_integral'] ?? 0;

            if (!empty($post['package_id']) && $post['package_id']) {
                $package = (new RechargePackage())->findOrEmpty(intval($post['package_id']))->toArray();
                if (!$package) {
                    throw new Exception('套餐丢失,请重新选择套餐');
                }
                $give_amount = $package['give_money'];
                $paid_amount = $package['money'];
            } else {
                if ($paid_amount <= 0) {
                    throw new Exception('充值金额不能少于0元');
                }

                $min_recharge = $config['min_recharge'] ?? 0;
                $max_recharge = $config['max_recharge'] ?? 0;
                if ($min_recharge && $paid_amount < $min_recharge) {
                    throw new Exception('充值金额不能少于'.$min_recharge.'元');
                }
                if ($max_recharge && $paid_amount < $max_recharge) {
                    throw new Exception('充值金额不能大于'.$max_recharge.'元');
                }
            }

            $recharge = RechargeOrder::create([
                'user_id'        => $userId,
                'order_sn'       => make_order_no(),
                'order_platform' => $post['client'],
                'pay_way'        => $post['pay_way'] ?? OrderEnum::PAY_WAY_MNP,
                'package_id'     => $post['package_id'] ?? 0,
                'paid_amount'    => $paid_amount,
                'give_amount'    => $give_amount,
                'give_growth'    => $give_growth,
                'give_integral'  => $give_integral,
                'create_time'    => time(),
                'update_time'    => time()
            ]);

            return [
                'order_id' => $recharge['id']
            ];
        } catch (Exception $e) {
            static::$error = $e->getMessage();
            return false;
        }
    }

    /**
     * 充值订单
     *
     * @author windy
     * @param int $orderId
     * @return array
     */
    public static function order(int $orderId): array
    {
        return (new RechargeOrder())
            ->findOrEmpty(intval($orderId))
            ->toArray();
    }

    /**
     * 充值记录
     *
     * @author windy
     * @param array $get
     * @param int $userId
     * @return array
     * @throws @\think\db\exception\DbException
     */
    public static function record(array $get, int $userId): array
    {
        $lists = (new RechargeOrder())
            ->field(['id,paid_amount,give_amount,pay_time'])
            ->where(['pay_status'=>1, 'user_id'=>$userId])
            ->paginate([
                'page'      => $get['page'] ?? 1,
                'list_rows' => $get['limit'] ?? 20,
                'var_page'  => 'page'
            ])->toArray();

        foreach ($lists['data'] as &$item) {
            $item['tips']    = '充值'.$item['paid_amount'];
            $item['arrival'] = '+'.($item['paid_amount'] + $item['give_amount']);
            $item['pay_time'] = date('Y-m-d H:i:s', $item['pay_time']);
            unset($item['paid_amount']);
            unset($item['give_amount']);
        }

        return $lists;
    }
}