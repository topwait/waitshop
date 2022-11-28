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

namespace app\admin\validate\setting;


use app\common\basics\Validates;

/**
 * 短信配置参数验证器
 * Class SmsValidate
 * @package app\admin\validate\setting
 */
class SmsValidate extends Validates
{
    protected $rule = [
        'id'        => 'require|isInteger',
        'name'      => 'require',
        'sort'      => 'require|number',
        'is_enable' => 'require|in:0,1',
        'sign'      => 'require',
    ];

    protected $message = [
        'id.require'        => '缺少id参数',
        'id.isInteger'      => 'id参数必须为数字',
        'name.require'      => '短信名称不可为空',
        'sort.require'      => '排序号不可为空',
        'sort.number'       => '排序号需为数字类型',
        'is_enable.require' => '请选择是否用短信',
        'is_enable.in'      => '是否启用短信非法操作',
        'sign.require'      => '短信sing签名不可为空'
    ];
}