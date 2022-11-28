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

class AfterSaleValidate extends Validates
{
    protected $rule = [
        'id'          => 'require|isInteger',
        'reason'      => 'require|min:1',
        'refund_type' => 'require|in:1,2',

        'express_name'  => 'require',
        'express_no'    => 'require',
        'express_image' => 'require'
    ];

    protected $message = [
        'id.require'            => 'id参数缺失',
        'id.isInteger'          => 'id参数必须为正整数',
        'reason.require'        => 'reason参数缺失',
        'reason.min'            => '请选择售后原因',
        'refund_type.require'   => 'refund_type参数缺失',
        'refund_type.in'        => '售后类型选择异常',
        'express_name.require'  => '快递公司名不能为空',
        'express_no.require'    => '快递单号不能为空',
        'express_image.require' => '快递面单图不能为空',
    ];

    protected $scene = [
        'apply'   => ['id', 'reason', 'refund_type'],
        'express' => ['id', 'express_name', 'express_no', 'express_image'],
    ];
}