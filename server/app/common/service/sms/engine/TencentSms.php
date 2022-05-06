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

namespace app\common\service\sms\engine;


use Exception;
use TencentCloud\Common\Credential;
use TencentCloud\Common\Profile\ClientProfile;
use TencentCloud\Common\Profile\HttpProfile;
use TencentCloud\Sms\V20190711\Models\SendSmsRequest;
use TencentCloud\Sms\V20190711\SmsClient;

class TencentSms
{
    /**
     * 配置参数
     */
    private $config;

    /**
     * 要发送的手机号
     */
    private $phoneNumbers;

    /**
     * 模板编码
     */
    private $templateCode;

    /**
     * 短信参数
     */
    private $templateParam;

    /**
     * 初始化配置
     *
     * AliyunSms constructor.
     * @param array $config
     */
    public function __construct(array $config)
    {
        $this->config = $config;

    }

    /**
     * 发送腾讯云短信
     *
     * @author windy
     * @throws Exception
     */
    public function send(): bool
    {
        try {
            $cred = new Credential($this->config['secret_id'], $this->config['secret_key']);
            $httpProfile = new HttpProfile();
            $httpProfile->setEndpoint('sms.tencentcloudapi.com');

            $clientProfile = new ClientProfile();
            $clientProfile->setHttpProfile($httpProfile);

            $client = new SmsClient($cred, "", $clientProfile);
            $req = new SendSmsRequest();

            $params = [
                'Sign'             => $this->config['sign'],
                'SmsSdkAppid'      => $this->config['app_id'],
                'TemplateID'       => $this->templateCode,
                'PhoneNumberSet'   => ['+86' . $this->phoneNumbers],
                'TemplateParamSet' => $this->templateParam,
            ];

            $req->fromJsonString(json_encode($params));
            $resp = json_decode($client->SendSms($req)->toJsonString(), true);

            if (isset($resp['SendStatusSet']) && $resp['SendStatusSet'][0]['Code'] == 'Ok') {
                return true;
            } else {
                $message = $res['SendStatusSet'][0]['Message'] ?? json_encode($resp);
                throw new Exception($message);
            }
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    /**
     * 设置发送手机号码
     *
     * @author windy
     * @param int $mobile
     * @return $this
     */
    public function setPhoneNumbers(int $mobile): TencentSms
    {
        $this->phoneNumbers = trim($mobile);
        return $this;
    }

    /**
     * 设置短信模板编码
     *
     * @author windy
     * @param string $code
     * @return $this
     */
    public function setTemplateCode(string $code): TencentSms
    {
        $this->templateCode = trim($code);
        return $this;
    }

    /**
     * 设置短信发送参数
     *
     * @author windy
     * @param array $param
     * @return $this
     */
    public function setTemplateParam(array $param): TencentSms
    {
        $this->templateParam = json_encode($param, JSON_UNESCAPED_UNICODE);
        return $this;
    }
}