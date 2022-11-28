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

namespace app\admin\validate\goods;


use app\common\basics\Validates;

/**
 * 品牌参数验证器
 * Class BrandValidate
 * @package app\admin\validate\product
 */
class BrandValidate extends Validates
{
    protected $rule = [
        'id'      => 'require|isInteger',
        'name'    => 'require|max:60',
        'letter'  => 'require',
        'sort'    => 'require|number',
        'is_show' => 'require|in:0,1'
    ];

    protected $message = [
        'id.require'     => 'ID不可为空',
        'id.isInteger'   => 'ID不合法',
        'name.require'   => '请填写品牌名称',
        'name.max'       => '品牌名称太长了,超出了限制得60个字符',
        'letter.require' => '请选择品牌首字母',
        'sort'           => '请填写排序号',
        'is_show'        => '显示隐藏传入规则错误'
    ];

    protected $scene = [
        'id'   => ['id'],
        'add'  => ['name', 'letter', 'sort', 'is_show'],
        'edit' => ['id', 'name', 'letter', 'sort', 'is_show']
    ];
}