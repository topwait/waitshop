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

namespace app\common\utils;


use EasyWeChat\Factory;
use EasyWeChat\Kernel\Exceptions\InvalidConfigException;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Exception;

class WeChatUtils
{
    /**
     * 微信小程序配置
     *
     * @author windy
     * @return array
     */
    public static function getMnpConfig(): array
    {
        $config = ConfigUtils::get('mnp', []);
        return [
            'app_id'        => $config['app_id'],
            'secret'        => $config['app_secret'],
            'response_type' => 'array',
            'log'           => [
                'level' => 'debug',
                'file'  => '../runtime/log/weChat.log'
            ],
        ];
    }

    /**
     * 微信公众号配置
     *
     * @author windy
     * @return array
     */
    public static function getOaConfig(): array
    {
        $config = ConfigUtils::get('oa', []);
        return array(
            'app_id' => $config['app_id'],
            'secret' => $config['app_secret'],
            'token' => $config['token'] ?? '',
            'aes_key' => $config['aes_key'] ?? '',
            'response_type' => 'array',
            'log' => [
                'level' => 'debug',
                'file' => '../runtime/log/weChat.log'
            ]
        );
    }

    /**
     * 微信开放平台配置
     *
     * @author windy
     * @return array
     */
    public static function getOpConfig(): array
    {
        $config = ConfigUtils::get('op', []);
        return array(
            'app_id'        => $config['app_id'],
            'secret'        => $config['app_secret'],
            'response_type' => 'array',
            'log'           => [
                'level' => 'debug',
                'file'  => '../runtime/log/weChat.log'
            ]
        );
    }

    /**
     * 根据code获取微信信息
     *
     * @author windy
     * @param string $code
     * @return array
     * @throws Exception
     * @throws InvalidConfigException
     */
    public static function getMnpResByCode(string $code): array
    {
        $app = Factory::miniProgram(self::getMnpConfig());
        $response = $app->auth->session($code);
        if (!isset($response['openid']) || empty($response['openid'])) {
            throw new Exception($response['errmsg'] ?? '获取openID失败');
        }
        return $response;
    }

    /**
     * 解密微信数据
     *
     * @author windy
     * @param $session_key
     * @param $iv
     * @param $encryptedData
     * @return array
     * @throws @\EasyWeChat\Kernel\Exceptions\DecryptException
     */
    public static function decryptMnpData($session_key, $iv, $encryptedData): array
    {
        $app = Factory::miniProgram(self::getMnpConfig());
        return $app->encryptor->decryptData($session_key, $iv, $encryptedData);
    }

    /**
     * 公众号跳转url
     *
     * @author windy
     * @param string $url
     * @return RedirectResponse
     */
    public static function getCodeUrl(string $url): RedirectResponse
    {
        $app = Factory::officialAccount(self::getOaConfig());
        return $app
            ->oauth
            ->scopes(['snsapi_userinfo'])
            ->redirect($url);
    }
}