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

namespace app\api\validate;


use app\common\basics\Validates;

/**
 * 登录验证参数验证器
 * Class LoginValidate
 * @package app\api\validate
 */
class LoginValidate extends Validates
{
    protected $rule = [
        'scene'    => 'require',
        'code'     => 'require',
        'client'   => 'require|isInteger',
        'mobile'   => 'require|mobile',
        'password' => 'require|min:6|max:18'
    ];

    protected $message = [
        'scene.require'    => 'scene参数缺失',
        'code.require'     => 'code参数缺失',
        'client.require'   => 'client参数缺失',
        'client.isInteger' => 'client参数不合法',
        'mobile.require'   => 'mobile参数缺失',
        'password.require' => 'password参数缺失',
        'password.min'     => '登录密码错误',
        'password.max'     => '登录密码错误'
    ];

    protected $scene = [
        'scene'    => ['scene'],
        'silent'   => ['scene', 'client', 'code'],
        'mnp'      => ['scene', 'client', 'code'],
        'mobile'   => ['scene', 'client', 'mobile', 'code'],
        'account'  => ['scene', 'client', 'mobile', 'password'],
        'register' => ['client', 'mobile', 'password', 'code']
    ];
}