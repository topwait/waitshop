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

namespace app\admin\validate\auth;


use app\common\basics\Validates;

/**
 * 管理员角色参数验证器
 * Class RoleValidate
 * @package app\admin\validate\auth
 */
class RoleValidate extends Validates
{
    protected $rule = [
        'id'         => 'require|isInteger',
        'name'       => 'require|max:8',
        'sort'       => 'require|number',
        'describe'   => 'require|max:200',
        'is_disable' => 'require|in:0,1'
    ];

    protected $message = [
        'id.require'         => 'ID不可为空',
        'id.isInteger'       => 'ID参数异常',
        'name.require'       => '请填写角色名称',
        'name.max'           => '角色名称最大8个字符',
        'describe.require'   => '请填写角色描述',
        'describe.max'       => '角色描述超出200个字符了',
        'is_disable.require' => '请选择是否禁用账号',
        'is_disable.in'      => '选择的禁用状态异常',
    ];

    protected $scene = [
        'id'   => ['id'],
        'add'  => ['name', 'sort', 'describe', 'is_disable'],
        'edit' => ['id', 'name', 'sort', 'describe', 'is_disable'],
    ];
}