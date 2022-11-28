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

namespace app\common\service\payment;


use Alipay\EasySDK\Kernel\Config as AliConfig;
use Alipay\EasySDK\Kernel\Factory;
use Alipay\EasySDK\Payment\Common\Models\AlipayTradeRefundResponse;
use app\common\enum\ClientEnum;
use app\common\model\ConfigPayment;

class AliPayService
{
    /**
     * 初始化参数
     * @author windy
     */
    public static function init()
    {
        $config = ConfigPayment::getParams('alipay');
        $options = new AliConfig();
        $options->protocol           = 'https';
        $options->gatewayHost        = 'openapi.alipay.com';
        $options->signType           = 'RSA2';
        $options->appId              = $config['app_id'] ?? '';
        $options->merchantPrivateKey = $config['private_key'] ?? '';
        $options->alipayPublicKey    = $config['public_key'] ?? '';
        $options->notifyUrl          = url('payment/aliNotify', '', '', true);

        Factory::setOptions($options);
    }

    /**
     * 支付入口
     *
     * @author windy
     * @param $from
     * @param $order
     * @return bool|string
     */
    public static function unifiedOrder($from, $order)
    {
        self::init();
        $result = false;
        switch ($order['order_platform']) {
            case ClientEnum::PC:
                $result = self::pagePay($from, $order);
                break;
            case ClientEnum::ANDROID:
            case ClientEnum::IOS:
                $result = self::appPay($from, $order);
                break;
            case ClientEnum::H5:
                $result = self::wapPay($from, $order);
                break;
        }

        return $result;
    }

    /**
     * 发起退款
     *
     * @author windy
     * @param string $order_sn (订单编号)
     * @param float $refund_amount (退款金额)
     * @return AlipayTradeRefundResponse
     * @throws @\Exception
     */
    public static function refund(string $order_sn, float $refund_amount): AlipayTradeRefundResponse
    {
        return Factory::payment()->common()->refund($order_sn, $refund_amount);
    }

    /**
     * 网页端支付
     *
     * @author windy
     * @param $from
     * @param $order
     * @return string
     */
    private static function pagePay($from, $order): string
    {
        $params = [
            'subject'   => '订单:'.$order['order_sn'],
            'orderSn'   => $order['order_sn'],
            'money'     => $order['order_amount'],
            'returnUrl' => '/pc/user/order'
        ];

        return Factory::payment()->page()
            ->optional('passback_params', $from)
            ->pay(
                $params['subject'], $params['orderSn'],
                $params['money'], $params['returnUrl']
            )->body;
    }

    /**
     * APP端支付
     *
     * @author windy
     * @param $from
     * @param $order
     * @return string
     */
    private static function appPay($from, $order): string
    {
        $params = [
            'subject' => $order['order_sn'],
            'orderSn' => $order['order_sn'],
            'money'   => $order['order_amount']
        ];

        return Factory::payment()->app()
            ->optional('passback_params', $from)
            ->pay(
                $params['subject'], $params['orderSn'],
                $params['money']
            )->body;
    }

    /**
     * 手机网页支付
     *
     * @author windy
     * @param $from
     * @param $order
     * @return string
     */
    private static function wapPay($from, $order): string
    {
        $params = [
            'subject'   => $order['order_sn'],
            'orderSn'   => $order['order_sn'],
            'money'     => '订单:'.$order['order_amount'],
            'quitUrl'   => '/mobile/pages/user_order/user_order',
            'returnUrl' => '/mobile/pages/user_order/user_order'
        ];

        return Factory::payment()->wap()
            ->optional('passback_params', $from)
            ->pay(
                $params['subject'], $params['orderSn'], $params['money'],
                $params['quitUrl'], $params['returnUrl']
            )->body;
    }
}