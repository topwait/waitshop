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

namespace app\common\model\user;


use app\common\basics\Models;

class UserToken extends Models
{
    // 设置字段信息
    protected $schema = [
        'id'             => 'int',    // 主键ID
        'user_id'        => 'int',    // 用户ID
        'token'          => 'string', // 密钥
        'client'         => 'int',    // 客户端[1=安卓端, 2=苹果端, 3=电脑端, 4=H5(非微信), 5=微信公众号, 6=微信小程序]
        'expire_time'    => 'int',    // 过期时间
        'create_time'    => 'int',    // 创建时间
        'update_time'    => 'int',    // 更新时间
    ];

    /**
     * 根据token获取授权密钥
     *
     * @author windy
     * @param string $token
     * @return array
     */
    public static function getUserIdByToken(string $token): array
    {
        $userToken = (new self)->where(['token'=>$token])->findOrEmpty()->toArray();
        if (!$userToken) {
            return [];
        }

        if ($userToken['expire_time'] <= time()) {
            return [];
        }

        return $userToken;
    }
}