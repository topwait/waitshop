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


use AlibabaCloud\Client\AlibabaCloud;
use AlibabaCloud\Client\Exception\ClientException;
use AlibabaCloud\Client\Exception\ServerException;
use Exception;

class AliyunSms
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
     * 发送阿里云短信
     *
     * @author windy
     * @return mixed|void
     * @throws Exception
     */
    public function send(): bool
    {
        try {
            AlibabaCloud::accessKeyClient($this->config['access_key_id'], $this->config['access_secret'])
                ->regionId('cn-hangzhou')
                ->asDefaultClient();

            $result = AlibabaCloud::rpc()
                ->product('Dysmsapi')
                ->version('2017-05-25')
                ->action('SendSms')
                ->method('POST')
                ->host('dysmsapi.aliyuncs.com')
                ->options([
                    'query' => [
                        'PhoneNumbers'  => $this->phoneNumbers,
                        'SignName'      => $this->config['sign'],
                        'TemplateCode'  => $this->templateCode,
                        'TemplateParam' => $this->templateParam,
                    ],
                ])->request();

            $result = $result->toArray();
            if (isset($result['Code']) && $result['Code'] == 'OK') {
                return true;
            }

            throw new Exception($result['Message'] ?? $result);
        } catch (ClientException | ServerException $e) {
            throw new Exception($e->getErrorMessage());
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
    public function setPhoneNumbers(int $mobile): AliyunSms
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
    public function setTemplateCode(string $code): AliyunSms
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
    public function setTemplateParam(array $param): AliyunSms
    {
        $this->templateParam = json_encode($param, JSON_UNESCAPED_UNICODE);
        return $this;
    }
}