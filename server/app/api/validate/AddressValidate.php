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

namespace app\api\validate;


use app\common\basics\Validates;

/**
 * 收货地址参数验证器
 * Class AddressValidate
 * @package app\api\validate
 */
class AddressValidate extends Validates
{
    protected $rule = [
        'id'          => 'require|isInteger',
        'nickname'    => 'require|chsAlphaNum',
        'mobile'      => 'require|mobile',
        'province_id' => 'require|number',
        'city_id'     => 'require|number',
        'district_id' => 'require|number',
        'address'     => 'require',
        'is_default'  => 'require|in:0,1'
    ];

    protected $message = [
        'id.require'           => 'id参数缺失',
        'id.isInteger'         => 'id必须为正整数',
        'nickname.require'     => 'nickname参数缺失',
        'nickname.chsAlphaNum' => '收货人名称只允许字母、汉字、数字',
        'mobile.require'       => 'mobile参数缺失',
        'mobile.mobile'        => '请输入正确的手机号码',
        'province_id.require'  => 'province_id参数缺失',
        'province_id.number'   => 'province_id值需为数字',
        'city_id.require'      => 'city_id参数缺失',
        'city_id.number'       => 'city_id值需为数字',
        'district_id.require'  => 'district_id参数缺失',
        'district_id.number'   => 'district_id值需为数字',
        'address.require'      => 'address参数缺失',
        'is_default.require'   => 'is_default参数缺失',
        'is_default.in'        => 'is_default值不在合法范围'
    ];

    protected $scene = [
        'id'     => ['id'],
        'add'    => ['nickname', 'mobile', 'province_id', 'city_id', 'district_id', 'address', 'is_default'],
        'edit'   => ['id', 'nickname', 'mobile', 'province_id', 'city_id', 'district_id', 'address', 'is_default']
    ];
}