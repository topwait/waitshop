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
 * 短信参数验证器
 * Class SmsValidate
 * @package app\api\validate
 */
class SmsValidate extends Validates
{
    protected $rule = [
        'mobile' => 'require|mobile|max:11|min:11',
        'scene'  => 'require|number'
    ];

    public $message = [
        'mobile.require' => 'mobile参数缺失',
        'mobile.mobile'  => '请填写正确的手机号',
        'mobile.min'     => '手机号码不能少于11位数',
        'mobile.max'     => '手机号码不能大于11位数',
        'scene.require'  => 'scene参数缺失',
        'scene.number'   => 'scene场景值必须为数字',
    ];
}