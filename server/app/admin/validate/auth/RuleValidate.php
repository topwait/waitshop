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
 * 权限规格参数验证器
 * Class RuleValidate
 * @package app\admin\validate\auth
 */
class RuleValidate extends Validates
{
    protected $rule = [
        'id'       => 'require|isInteger',
        'pid'      => 'require|number',
        'title'    => 'require|max:20',
        'sort'     => 'require|number',
        'is_menu'  => 'require|in:0,1'
    ];

    protected $message = [
        'id.require'      => 'ID不可为空',
        'id.isInteger'    => 'ID参数异常',
        'pid.require'     => '请选择父级菜单',
        'pid.number'      => '父级菜单选择异常',
        'title.require'   => '请填写权限规则名称',
        'title.max'       => '权限规则名称最大20个字符',
        'sort.require'    => '请填写排序号',
        'sort.number'     => '排序号必须为数字',
        'is_menu.require' => '请选择是否为菜单',
        'is_menu.in'      => '选择是否为菜单异常',
    ];

    protected $scene = [
        'id'   => ['id'],
        'add'  => ['pid', 'title', 'sort', 'is_menu'],
        'edit' => ['id', 'pid', 'title', 'sort', 'is_menu'],
    ];
}