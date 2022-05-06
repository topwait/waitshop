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

namespace app\admin\validate\auth;


use app\common\basics\Validates;

/**
 * 管理员参数验证器
 * Class AdminValidate
 * @package app\admin\validate\auth
 */
class AdminValidate extends Validates
{
    protected $rule = [
        'id'         => 'require|isInteger',
        'username'   => 'require|min:2|max:8|chsAlphaNum|unique:admin',
        'password'   => 'requireWith:password|min:6|max:18',
        'email'      => 'require|email|unique:admin',
        'role_id'    => 'require|number',
        'is_disable' => 'require|in:0,1'
    ];

    protected $message = [
        'id.require'           => 'ID不可为空',
        'id.isInteger'         => 'ID参数异常',
        'username.require'     => '请填写登录账号',
        'username.min'         => '账号最少2个字符',
        'username.max'         => '账号最大8个字符',
        'username.chsAlphaNum' => '账号只允许汉字、字母和数字',
        'username.unique'      => '账号已存在',
        'password.require'     => '请填写登录密码',
        'password.min'         => '登录密码最少6位数',
        'password.max'         => '登录密码最大18位数',
        'email.require'        => '请填写电子邮箱',
        'email.email'          => '填写的电子邮箱不符合规范',
        'email.unique'         => '邮箱已被使用',
        'role_id.require'      => '请选择角色',
        'role_id.number'       => '选择的角色不符合规范',
        'is_disable.require'   => '请选择是否禁用账号',
        'is_disable.in'        => '选择的禁用状态异常',
    ];

    protected $scene = [
        'id'   => ['id'],
        'add'  => ['username', 'password', 'email', 'role_id', 'is_disable'],
        'edit' => ['id', 'username', 'email', 'role_id', 'is_disable']
    ];
}