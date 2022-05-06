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


use app\api\service\UserServer;
use app\common\basics\Logic;
use app\common\enum\ClientEnum;
use app\common\enum\LogGrowthEnum;
use app\common\enum\LogIntegralEnum;
use app\common\enum\NoticeEnum;
use app\common\model\addons\DistributionExtend;
use app\common\model\log\LogGrowth;
use app\common\model\log\LogIntegral;
use app\common\model\log\LogSms;
use app\common\model\user\User;
use app\common\model\user\UserAuth;
use app\common\model\user\UserToken;
use app\common\utils\ConfigUtils;
use app\common\utils\TokenUtils;
use app\common\utils\UrlUtils;
use app\common\utils\WeChatUtils;
use EasyWeChat\Factory;
use Exception;

/**
 * 登录接口-逻辑层
 * Class LoginLogic
 * @package app\api\logic
 */
class LoginLogic extends Logic
{
    /**
     * 登录配置
     *
     * @author windy
     * @return array
     */
    public static function config(): array
    {
        $config = ConfigUtils::get('login');
        return [
            // 强制绑定手机
            'force_mobile'   => $config['force_mobile'] ?? 1,
            // 通用登录方式
            'login_way'      => empty($config['login_way']) ? [] : json_decode($config['login_way'], true),
            // 登录Logo图
            'login_logo'     => UrlUtils::getAbsoluteUrl(ConfigUtils::get('backstage')['mall_logo']??'/static/images/mall_logo.png')
        ];
    }

    /**
     * 账号注册
     *
     * @author windy
     * @param array $post
     * @return bool|array
     */
    public static function register(array $post)
    {
        try {
            // 验证码
            $logSms = (new LogSms())->where([
                'code'   => $post['code'],
                'mobile' => $post['mobile'],
                'scene'  => NoticeEnum::SMS_REGISTER_CODE_NOTICE
            ])->findOrEmpty()->toArray();
            if (!$logSms || $logSms['is_verify'] || strtotime($logSms['create_time']) + (60 * 15) < time()) {
                throw new Exception('验证码无效');
            }

            // 查询账号
            $userModel = new User();
            $check = $userModel->where(['mobile'=>$post['mobile'], 'is_delete'=>0])->findOrEmpty()->toArray();
            if ($check) {
                throw new Exception('账号已存在');
            }

            // 生成密码
            $salt = TokenUtils::getRandChar(6);
            $password = encrypt_password($post['password'], $salt);

            // 全员分销
            $isDistribution = ConfigUtils::get('distribution', 'apply_condition');
            $isDistribution = intval($isDistribution) === 1 ? 1 : 0;

            // 创建账号
            $user = User::create([
                'sn'                  => create_random_code($userModel, 'sn', 8),
                'avatar'              => 'static/images/default_avatar.png',
                'nickname'            => trim($post['mobile']),
                'mobile'              => trim($post['mobile']),
                'password'            => $password,
                'salt'                => $salt,
                'login_ip'            => request()->ip(),
                'login_time'          => time(),
                'is_distribution'     => $isDistribution,
                'distribution_code'   => make_invite_code($userModel),
                'create_time'         => time(),
                'update_time'         => time()
            ]);

            // 分销扩展
            self::makeDistributionExtend($user['id']);

            // 注册奖励
            self::registerReward($user['id']);

            // 核销验证码
            LogSms::update(['is_verify'=>1, 'update_time'=>time()], ['id'=>$logSms['id']]);

            // 登录系统
            $token = self::grantToken($user['id'], intval($post['client']));
            return ['token'=>$token];

        } catch (Exception $e) {
            static::$error = $e->getMessage();
            return false;
        }
    }

    /**
     * 手机号码登录
     *
     * @author windy
     * @param array $post
     * @return false|string[]
     */
    public static function mobileLogin(array $post)
    {
        try {
            // 校验验证码是否正确
            $logSms = (new LogSms())->where([
                'code'   => $post['code'],
                'mobile' => $post['mobile'],
                'scene'  => NoticeEnum::SMS_LOGIN_CODE_NOTICE
            ])->findOrEmpty()->toArray();
            if (!$logSms || $logSms['is_verify'] || strtotime($logSms['create_time']) + (60 * 15) < time()) {
                throw new Exception('验证码无效');
            }

            // 根据手机号码查询
            $user = (new User())->where(['mobile'=>$post['mobile']])->findOrEmpty()->toArray();
            if (!$user || $user['is_delete']) {
                throw new Exception('账号不存在');
            }

            if ($user['is_disable']) {
                throw new Exception('账号已被禁用,请联系管理员');
            }

            // 核销验证码
            LogSms::update(['is_verify'=>1, 'update_time'=>time()], ['id'=>$logSms['id']]);

            // 授权令牌给用户
            $token = self::grantToken($user['id'], intval($post['client']));

            return ['token'=>$token];
        } catch (Exception $e) {
            static::$error = $e->getMessage();
            return false;
        }
    }

    /**
     * 账号密码登录
     *
     * @author windy
     * @param array $post
     * @return bool|array
     */
    public static function accountLogin(array $post)
    {
        try {
            $user = (new User())->where(['mobile' => $post['mobile']])->findOrEmpty()->toArray();

            if (!$user || $user['is_delete']) {
                throw new Exception('账号不存在');
            }

            if ($user['is_disable']) {
                throw new Exception('账号已被禁用,请联系管理员');
            }

            $password = encrypt_password($post['password'], $user['salt']);
            if ($user['password'] !== $password) {
                throw new Exception('登录账号或密码错误');
            }

            $token = self::grantToken($user['id'], intval($post['client']));

            return ['token'=>$token];
        } catch (Exception $e) {
            static::$error = $e->getMessage();
            return false;
        }
    }

    /**
     * 小程序静默登录
     *
     * @author windy
     * @param array $post
     * @return array|bool
     */
    public static function silentLogin(array $post)
    {
        try {
            $response = WeChatUtils::getMnpResByCode($post['code']);
            $userInfo = self::getUserByWeChatResponse($response);
            if (empty($userInfo)) {
                throw new Exception('用户尚不存在');
            }

            UserServer::updateUser($response, $post['client'], $userInfo['id']);
            $token = self::grantToken($userInfo['id'], $post['client']);

            return ['token'=>$token];
        } catch (Exception $e) {
            static::$error = $e->getMessage();
            return false;
        }
    }

    /**
     * 微信登录
     *
     * @author windy
     * @param array $post
     * @return array|false
     */
    public static function mnpLogin(array $post)
    {
        try {
            // 获取微信授权信息
            $config = WeChatUtils::getMnpConfig();
            $app = Factory::miniProgram($config);
            $response = $app->auth->session($post['code']);
            if (!isset($response['openid']) || empty($response['openid'])) {
                throw new Exception($response['errmsg'] ?? '获取openID失败');
            }

            // 获取手机号码
            if (!empty($post['encryptedData'])) {
                $result = $app->encryptor->decryptData($response['session_key'], $post['iv'], $post['encryptedData']);
                if (empty($result['phoneNumber'])) {
                    throw new Exception("获取微信手机号失败");
                }
                $response['phoneNumber'] = $result['phoneNumber'];
            }

            $response['avatarUrl']  = $post['avatarUrl'];
            $response['nickName']   = $post['nickName'];
            $response['gender']     = $post['gender'];
            // 获取当前系统用户信息
            $user = self::getUserByWeChatResponse($response);
            // 创建用户或更新用户信息
            if (empty($user)) {
                $userResponse = UserServer::createUser($response, intval($post['client']));
                self::makeDistributionExtend($userResponse['id']);
                self::registerReward($userResponse['id']);
            } else {
                $userResponse = UserServer::updateUser($response, intval($post['client']), $user['id']);
            }
            // 授权令牌给用户
            $token = self::grantToken($userResponse['id'], intval($post['client']));
            return ['token'=>$token];
        } catch (Exception $e) {
            static::$error = $e->getMessage();
            return false;
        }
    }

    /**
     * 微信端手机号码
     *
     * @author windy
     * @param array $post
     * @return array|false
     */
    public static function getWxPhone(array $post)
    {
        try {
            $config = WeChatUtils::getMnpConfig();
            $app = Factory::miniProgram($config);
            $response = $app->auth->session($post['code']);
            if (!isset($response['session_key'])) {
                throw new Exception($response['errmsg'] ?? '系统异常');
            }

            $result = $app->encryptor->decryptData($response['session_key'], $post['iv'], $post['encryptedData']);
            if (empty($result['phoneNumber'])) {
                throw new Exception("获取微信手机号失败");
            }

            return ['countryCode'=>$result['countryCode'], 'phoneNumber'=>$result['phoneNumber']];
        } catch (Exception $e) {
            static::$error = $e->getMessage();
            return false;
        }
    }

    /**
     * 根据微信返回信息查询当前用户id
     *
     * @author windy
     * @param array $response
     * @return mixed
     */
    private static function getUserByWeChatResponse(array $response)
    {
        return (new UserAuth())->alias('AU')
            ->join('user U', 'AU.user_id = U.id')
            ->where(['U.is_delete' => 0])
            ->where(function ($query) use ($response) {
                $query->whereOr(['AU.openid' => $response['openid']]);
                if(isset($response['unionid']) && !empty($response['unionid'])){
                    $query->whereOr(['AU.unionid' => $response['unionid']]);
                }
            })->findOrEmpty()->toArray();
    }

    /**
     * 授予登录令牌
     *
     * @author windy
     * @param int $userId
     * @param int $client
     * @return string
     */
    private static function grantToken(int $userId, int $client): string
    {
        $token = TokenUtils::generateToken($userId);
        $userToken = (new UserToken())
            ->where(['user_id'=>$userId, 'client'=>$client])
            ->findOrEmpty()
            ->toArray();

        if ($userToken) {
            if ($userToken['expire_time'] > time()) {
                $token = $userToken['token'];
                UserToken::update([
                    'expire_time' => self::reqTime() + (3600 * 7),
                    'update_time' => self::reqTime()
                ], ['id'=>$userToken['id'], 'client'=>$client]);
            } else {
                UserToken::update([
                    'token'       => $token,
                    'expire_time' => self::reqTime() + (3600 * 7),
                    'update_time' => self::reqTime()
                ], ['id'=>$userToken['id'], 'client'=>$client]);
            }
        } else {
            UserToken::create([
                'user_id'     => $userId,
                'token'       => $token,
                'client'      => $client,
                'expire_time' => self::reqTime() + (3600 * 7),
                'create_time' => self::reqTime(),
                'update_time' => self::reqTime()
            ]);
        }

        User::update([
            'login_ip'   => request()->ip(),
            'login_time' => time()
        ], ['id'=>$userId]);

        return $token;
    }

    /**
     * 机制注册奖励
     *
     * @author windy
     * @param int $userId
     */
    private static function registerReward(int $userId)
    {
        // 赠送奖励
        $rewardConfig = ConfigUtils::get('reward');
        $rewardIntegral = $rewardConfig['register_reward_integral'] ?? 0;
        $rewardGrowth   = $rewardConfig['register_reward_growth'] ?? 0;

        // 发放奖励
        User::update([
            'integral'            => ['inc', $rewardIntegral],
            'growth_value'        => ['inc', $rewardGrowth],
        ], ['id'=>$userId]);

        // 记录赠送积分
        if ($rewardIntegral) {
            LogIntegral::add(
                LogIntegralEnum::register_inc_integral,
                $rewardIntegral, $userId,
                0, 0, '',
                '注册赠送积分'
            );
        }

        // 记录增成长值
        if ($rewardGrowth) {
            LogGrowth::add(
                LogGrowthEnum::register_inc_growth,
                $rewardGrowth, $userId,
                0, 0, '',
                '注册赠成长值');
        }
    }

    /**
     * 生成分销推荐扩展
     *
     * @author windy
     * @param int $userId
     * @return bool
     */
    private static function makeDistributionExtend(int $userId): bool
    {
        $check = (new DistributionExtend())->where(['user_id'=>$userId])->findOrEmpty();
        if (!$check->isEmpty()) {
            return true;
        }

        DistributionExtend::create([
            'user_id'     => $userId,
            'create_time' => time(),
            'update_time' => time()
        ]);

        return true;
    }
}