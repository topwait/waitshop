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

namespace app\api\service;


use app\common\enum\ClientEnum;
use app\common\model\user\User;
use app\common\model\user\UserAuth;
use app\common\utils\TokenUtils;

/**
 * 用户服务类
 * Class UserServer
 * @package app\api\service
 */
class UserServer
{
    /**
     * 创建新用户
     * @param $loginRes
     * @param $client
     * @return array
     * @throws \Exception
     */
    public static function createUser($loginRes, $client): array
    {
        try {
            // 获取微信响应信息
            $response = self::getResponse($loginRes, $client);
            $avatarUrl = download_file($response['avatarUrl'],'uploads/user/avatar/', 'png');

            // 创建新用户资料
            $userModel = new User();
            $user = User::create([
                'sn'          => create_random_code($userModel, 'sn', 8),
                'nickname'    => $response['nickName'],
                'avatar'      => $avatarUrl,
                'sex'         => $response['gender'],
                'mobile'      => $response['mobile'],
                'salt'        => TokenUtils::getRandChar(6),
                'login_ip'    => request()->ip(),
                'login_time'  => time(),
                'update_time' => time(),
                'create_time' => time()
            ]);

            // 创建用户客户端
            UserAuth::create([
                'user_id'     => $user['id'],
                'openid'      => $response['openid'],
                'unionid'     => $response['unionid'],
                'client'      => $client,
                'create_time' => time(),
                'update_time' => time()
            ]);

            return ['id'=>$user['id']];
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }

    /**
     * 更新用户信息
     * @param array $loginRes
     * @param int $client
     * @param int $user_id
     * @return array
     * @throws \Exception
     */
    public static function updateUser(array $loginRes, int $client, int $user_id): array
    {
        try {
            $response = self::getResponse($loginRes, $client);
            $userAuth = (new UserAuth())->where(['user_id'=>$user_id, 'client'=>$client])->findOrEmpty()->toArray();
            $userInfo = (new User())->where(['id'=>$user_id])->findOrEmpty()->toArray();

            // 创建客户端信息
            if (!$userAuth) {
                UserAuth::create([
                    'user_id'     => $userInfo['id'],
                    'openid'      => $response['openid'],
                    'unionid'     => $response['unionid'],
                    'client'      => $client,
                    'create_time' => time(),
                    'update_time' => time()
                ]);
            }

            // 更新用户信息
            if (empty($userInfo['avatar']) and $response['avatarUrl']) {
                User::update([
                    'avatar'      => download_file($response['avatarUrl'],'uploads/user/avatar/', 'png'),
                    'nickname'    => $response['nickName'] ?? '',
                    'update_time' => time()
                ], ['id' => $userInfo['id']]);
            }

            // 更新用户关联凭证
            if (empty($userInfo['unionid']) && isset($response['unionid'])) {
                UserAuth::update([
                    'unionid'     => $response['unionid'],
                    'update_time' => time()
                ], ['user_id' => $userInfo['id'], 'client' => $client]);
            }

            return ['id'=>$user_id];
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }

    /**
     * 获取微信响应信息
     * @param $loginRes
     * @param $client
     * @return array
     */
    private static function getResponse($loginRes, $client): array
    {
        $response = [];
        switch ($client) {
            case ClientEnum::MNP:
                $response['openid']    = $loginRes['openid'] ?? '';
                $response['unionid']   = $loginRes['unionid'] ?? '';
                $response['avatarUrl'] = $loginRes['avatarUrl'] ?? '';
                $response['nickName']  = $loginRes['nickName'] ?? '';
                $response['gender']    = $loginRes['gender'] ?? 1;
                $response['mobile']    = $loginRes['phoneNumber'] ?? '';
                break;
        }
        return $response;
    }
}