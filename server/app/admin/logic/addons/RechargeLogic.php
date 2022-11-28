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

namespace app\admin\logic\addons;


use app\common\basics\Logic;
use app\common\enum\ClientEnum;
use app\common\enum\OrderEnum;
use app\common\model\addons\RechargeOrder;
use app\common\model\addons\RechargePackage;
use app\common\utils\ConfigUtils;
use Exception;

/**
 * 充值管理-逻辑层
 * Class RechargeLogic
 * @package app\admin\logic\addons
 */
class RechargeLogic extends Logic
{
    /**
     * 充值配置
     *
     * @author windy
     * @param array $post
     * @return bool
     */
    public static function setting(array $post): bool
    {
        try {
            ConfigUtils::set('recharge', [
                'is_open'       => $post['is_open'] ?? 0,
                'min_recharge'  => $post['min_recharge'] ?? 0,
                'max_recharge'  => $post['max_recharge'] ?? 0,
                'give_integral' => $post['give_integral'] ?? 0,
                'give_growth'   => $post['give_growth'] ?? 0
            ]);
            return true;
        } catch (Exception $e) {
            static::$error = $e->getMessage();
            return false;
        }
    }

    /**
     * 充值订单
     *
     * @author windy
     * @param array $get
     * @return array
     * @throws @\think\db\exception\DbException
     */
    public static function order(array $get): array
    {
        $lists = (new RechargeOrder())->alias('RO')
            ->field(['RO.*,U.avatar,U.nickname,U.money'])
            ->join('user U', 'U.id = RO.user_id')
            ->order('RO.id desc')
            ->paginate([
                'page'      => $get['page'],
                'list_rows' => $get['limit'],
                'var_page'  => 'page',
            ])->toArray();

        foreach ($lists['data'] as &$item) {
            $item['pay_way'] = OrderEnum::getPayWayDesc($item['pay_way']);
            $item['order_platform'] = ClientEnum::getEnumName($item['order_platform']);
            if ($item['pay_time'] > 0) {
                $item['pay_time'] = date('Y-m-d H:i:s', $item['pay_time']);
            }
        }

        return ['count'=>$lists['total'], 'list'=>$lists['data']];
    }

    /**
     * 充值套餐
     *
     * @author windy
     * @param string $op
     * @param array $param
     * @return array|bool
     * @throws @\think\db\exception\DbException
     */
    public static function package(string $op, array $param)
    {
        switch ($op) {
            case 'list':
                $lists = (new RechargePackage())
                    ->field(['id,money,give_money,sort,is_show,create_time'])
                    ->where(['is_delete'=>0])
                    ->order('sort desc, id desc')
                    ->paginate([
                        'page'      => $param['page'],
                        'list_rows' => $param['limit'],
                        'var_page'  => 'page',
                    ])->toArray();
                return ['count'=>$lists['total'], 'list'=>$lists['data']];
                break;
            case 'detail':
                return (new RechargePackage())->findOrEmpty(intval($param['id']));
            case 'add':
                RechargePackage::create([
                    'money'       => $param['money'],
                    'give_money'  => $param['give_money'],
                    'is_show'     => $param['is_show'],
                    'create_time' => time(),
                    'update_time' => time()
                ]);
                return true;
            case 'edit':
                RechargePackage::update([
                    'money'       => $param['money'],
                    'give_money'  => $param['give_money'],
                    'is_show'     => $param['is_show'],
                    'update_time' => time()
                ], ['id'=>intval($param['id'])]);
                return true;
            case 'del':
                RechargePackage::update([
                    'is_delete'    => 1,
                    'delete_time' => time()
                ], ['id'=>intval($param['id'])]);
                return true;
        }

        return null;
    }
}