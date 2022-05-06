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
use app\common\enum\OrderEnum;
use app\common\model\ConfigPayment;
use app\common\model\user\User;
use app\common\model\user\UserAuth;
use app\common\service\payment\AliPayService;
use app\common\service\payment\WeChatPayService;
use Exception;

/**
 * 支付接口-逻辑层
 * Class PaymentLogic
 * @package app\api\logic
 */
class PaymentLogic extends Logic
{
    /**
     * 支付唯一入口
     *
     * @author windy
     * @param string $from (场景)
     * @param array $order (订单数据)
     * @return array|false
     * @throws @\GuzzleHttp\Exception\GuzzleException
     */
    public static function pay(string $from, array $order)    {
        try {
            $result = null;
            switch ($order['pay_way']) {
                case OrderEnum::PAY_WAY_BALANCE:
                    $result = PayNotifyLogic::handle($from, $order['order_sn']);
                    self::$returnCode = 2100; //表示余额支付成功
                    break;
                case OrderEnum::PAY_WAY_MNP:
                    $userId = $order['user_id'];
                    $client = $order['order_platform'];
                    $openid = UserAuth::getUserOpenId($userId, $client);
                    $result = WeChatPayService::unifiedOrder($from, $order, $openid);
                    self::$returnCode = 2200; //表示微信下单成功
                    break;
                case OrderEnum::PAY_WAY_ALI:
                    $result = AliPayService::unifiedOrder($from, $order);
                    self::$returnCode = 2300; //表示支付宝下单成功
                    break;
                default:
                    self::$returnCode = 1;
                    throw new Exception('Abnormal payment Error');
            }

            return $result;
        } catch (Exception $e) {
            static::$error = $e->getMessage();
            return false;
        }
    }

    /**
     * 支付方式列表
     *
     * @author windy
     * @param int $userId
     * @return array
     * @throws @\think\db\exception\DataNotFoundException
     * @throws @\think\db\exception\DbException
     * @throws @\think\db\exception\ModelNotFoundException
     */
    public static function way(int $userId): array
    {
        $model = new ConfigPayment();
        $lists = $model->field('id,name,alias,image,icon')
            ->where(['is_enable'=>1])
            ->order('sort desc, id desc')
            ->select()->toArray();

        foreach ($lists as &$item) {
            switch ($item['alias']) {
                case 'mnp':
                    $item['tips'] = '微信快捷支付';
                    break;
                case 'alipay':
                    $item['tips'] = '支付宝快捷支付';
                    break;
                case 'wallet':
                    $money = (new User())->where(['id'=>$userId])->value('money');
                    $item['tips'] = '可用余额：' . $money;
                    break;
            }
        }

        return $lists;
    }
}