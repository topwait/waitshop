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

namespace app\admin\validate\content;


use app\common\basics\Validates;

/**
 * 文章分类参数验证器
 * Class ArticleCategoryValidate
 * @package app\admin\validate\content
 */
class ArticleCategoryValidate extends Validates
{
    protected $rule = [
        'id'    => 'require|isInteger',
        'name'  => 'require|max:20',
        'sort'  => 'require|number',
        'is_disable' => 'require|in:0,1'
    ];

    protected $message = [
        'id.require'   => '缺少id参数',
        'id.isInteger' => 'id参数必须为正整数',
        'name.require' => '请填写分类名称',
        'name.max'     => '分类名称不能超出20个字符',
        'is_disable.require' => '请选择是否禁用',
        'is_disable.in'      => '是否禁用选择不在合法值内',
    ];

    protected $scene = [
        'id'   => ['id'],
        'add'  => ['name', 'sort', 'is_disable'],
        'edit' => ['id', 'name', 'sort', 'is_disable'],
    ];
}