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
 * 供应商家参数验证器
 * Class SupplierValidate
 * @package app\admin\validate\product
 */
class SupplierValidate extends Validates
{
    protected $rule = [
        'id'       => 'require|isInteger',
        'name'     => 'require|max:60',
        'nickname' => 'require|max:20',
        'mobile'   => 'require|mobile',
        'address'  => 'require|max:500',
        'remarks'  => 'max:500',
        'sort'     => 'require|number'
    ];

    protected $message = [
        'id.require'       => 'ID不可为空',
        'id.isInteger'     => 'ID不合法',
        'name.require'     => '请填写供应商名称',
        'name.max'         => '供应商名称太长了,超出了限制得60个字符',
        'nickname.require' => '请填写联系人名称',
        'nickname.max'     => '联系人名称太长了,超出了限制得20个字符',
        'mobile.require'   => '请填写联系电话名称',
        'mobile.mobile'    => '填写的联系电话不符合规范',
        'address.require'  => '请填写联系地址',
        'address.max'      => '联系地址太长了,超出了限制得500个字符',
        'remarks.max'      => '备注信息太长了,超出了限制得500个字符',
        'sort.require'     => '请填写排序号',
        'sort.number'     => '排序号需为数字',

    ];

    protected $scene = [
        'id'   => ['id'],
        'add'  => ['name', 'nickname', 'mobile', 'address', 'remarks', 'sort'],
        'edit' => ['id', 'name', 'nickname', 'mobile', 'address', 'remarks', 'sort']
    ];
}