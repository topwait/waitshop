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
 * 支付参数验证器
 * Class PaymentValidate
 * @package app\api\validate
 */
class PaymentValidate extends Validates
{
    protected $rule = [
        'from'     => 'require',
        'client'   => 'require|isInteger',
        'pay_way'  => 'require|isInteger',
        'order_id' => 'require|isInteger',
    ];

    protected $message = [
        'from'               => 'from场景参数缺失',
        'client'             => 'client客户端参数缺失',
        'client.isInteger'   => 'client参数异常',
        'pay_way.require'    => 'pay_way参数缺失',
        'pay_way.isInteger'  => 'pay_way参数必须为正整数',
        'order_id.require'   => 'order_id参数缺失',
        'order_id.isInteger' => 'order_id参数必须为正整数',
    ];
}