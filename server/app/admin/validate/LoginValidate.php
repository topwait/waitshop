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

namespace app\admin\validate;


use app\common\basics\Validates;

/**
 * 登录参数验证器
 * Class LoginValidate
 * @package app\admin\validate
 */
class LoginValidate extends Validates
{
    protected $rule = [
        'username'       => 'require|min:2|max:8',
        'password'       => 'require|min:6|max:18',
        'captcha|验证码'  => 'require|captcha'
    ];

    protected $message = [
        'username.isNotEmpty' => '用户名不能为空',
        'username.min'        => '用户名或密码有误',
        'username.max'        => '用户名或密码有误',
        'password.isNotEmpty' => '密码不能为空',
        'password.min'        => '用户名或密码有误',
        'password.max'        => '用户名或密码有误',
        'captcha.require'     => '请填写验证码',
        'captcha.captcha'     => '验证码错误',
    ];
}