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

namespace app\common\service\payment;


use app\common\enum\ClientEnum;
use app\common\model\ConfigPayment;
use app\common\utils\QrCodeUtils;
use EasyWeChat\Factory;
use Exception;

/**
 * 微信支付服务类
 * Class WeChatPayServer
 * @package app\common\service
 */
class WeChatPayService
{
    /**
     * 微信预下单
     *
     * @author windy
     * @param string $from (支付场景)
     * @param array $order (订单信息)
     * @param string $openid (程序授权openid)
     * @return array|bool
     * @throws @\GuzzleHttp\Exception\GuzzleException
     */
    public static function unifiedOrder(string $from, array $order, string $openid)
    {
        try {
            // 1、获取微信支付参数
            $config = ConfigPayment::getParams('mnp');
            $app = Factory::payment([
                'app_id'     => $config['app_id'],
                'mch_id'     => $config['mch_id'],
                'key'        => $config['key'],
                'cert_path'  => $config['cert_path'],
                'key_path'   => $config['key_path'],
                'notify_url' => $config['notify_url'],
            ]);

            // 2、处理下单的参数
            $attributes = [];
            switch ($from) {
                case 'order':
                    $attributes = [
                        'trade_type'   => 'JSAPI',
                        'openid'       => $openid,
                        'body'         => $order['orderGoods'][0]['goods_snap'][0]['name'] ?? '商品',
                        'total_fee'    => $order['paid_amount'] * 100,
                        'out_trade_no' => $order['order_sn'],
                        'attach'       => 'order',
                        'notify_url'   => request()->domain() . url('Payment/notifyMnp', [], '')
                    ];
                    break;
                case 'recharge':
                    $attributes = [
                        'trade_type'   => 'JSAPI',
                        'openid'       => 'oUpF8uMuAJO_M2pxb1Q9zNjWeS6o',
                        'body'         => '充值'.$order['paid_amount'],
                        'total_fee'    => $order['paid_amount'] * 100,
                        'out_trade_no' => $order['order_sn'],
                        'attach'       => 'recharge',
                        'notify_url'   => request()->domain() . url('Payment/notifyMnp', [], '')
                    ];
            }

            // 2、APP支付类型
            if (in_array($order['order_platform'], [ClientEnum::ANDROID, ClientEnum::IOS])) {
                $attributes['trade_type'] = 'APP';
            }

            // 4、扫码支付类型
            if (in_array($order['order_platform'], [ClientEnum::PC])) {
                unset($attributes['openid']);
                $attributes['trade_type'] = 'NATIVE';
                $attributes['product_id'] = $attributes['out_trade_no'];
            }

            // 5、H5支付类型
            if (in_array($order['order_platform'], [ClientEnum::H5])) {
                $attributes['trade_type'] = 'MWEB';
            }

            // 6、发起预下单请求
            $result = $app->order->unify($attributes);

            // 7、验证预下单结果
            if ($result['return_code'] == 'SUCCESS' and $result['result_code'] == 'SUCCESS') {
                // 小程序/公众号
                if (in_array($order['order_platform'], [ClientEnum::MNP, ClientEnum::OA])) {
                    return $app->jssdk->bridgeConfig($result['prepay_id'], false);
                }

                // APP客户端
                if (in_array($order['order_platform'], [ClientEnum::IOS, ClientEnum::ANDROID])) {
                    return $app->jssdk->appConfig($result['prepay_id']);
                }

                // PC端/扫码
                if (in_array($order['order_platform'], [ClientEnum::PC])) {
                    return QrCodeUtils::getNativeCode($result['code_url']);
                }

                // H5(非微信)
                if (in_array($order['order_platform'], [ClientEnum::H5])) {
                    $redirectUrl = request()->domain().'/mobile/pages/order/list/list';
                    $redirectUrl = urlencode($redirectUrl);
                    return ['url'=>$result['mweb_url'].'&redirect_url='.$redirectUrl];
                }
            } else {
                if (isset($result['return_code']) and $result['return_code'] == 'FAIL') {
                    throw new Exception($result['return_msg']);
                }
                if (isset($result['err_code_des'])) {
                    throw new Exception($result['err_code_des']);
                }
                throw new Exception('未知原因');
            }

            throw new Exception('预下单失败');
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    /**
     * 发起退款
     *
     * @param array $data
     * @return bool
     * @throws Exception
     */
    public static function refund(array $data)
    {
        try {
            // 获取配置信息
            $config = ConfigPayment::getParams('mnp');
            $app = Factory::payment([
                'app_id'     => $config['app_id'],
                'mch_id'     => $config['mch_id'],
                'key'        => $config['key'],
                'cert_path'  => $config['cert_path'],
                'key_path'   => $config['key_path'],
                'notify_url' => $config['notify_url'],
            ]);

            $app->refund->byTransactionId(
                $data['transaction_id'],
                $data['refund_sn'],
                $data['total_fee'],
                $data['refund_fee']
            );

            return true;
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }
}