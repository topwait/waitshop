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
 * 购物车参数验证器
 * Class CartValidate
 * @package app\api\validate
 */
class CartValidate extends Validates
{
    protected $rule = [
        'id'       => 'require|number',
        'ids'      => 'require|array',
        'goods_id' => 'require|isInteger',
        'sku_id'   => 'require|isInteger',
        'num'      => 'require|isInteger',
        'selected' => 'require|number'
    ];

    protected $message = [
        'id.require'          => 'id参数缺失',
        'id.isInteger'        => 'id必须为正整数',
        'ids.require'         => 'ids参数缺失',
        'ids.array'           => 'ids必须为数组',
        'goods_id.require'    => 'goods_id参数缺失',
        'goods_id.isInteger'  => 'goods_id必须为正整数',
        'sku_id.require'      => 'sku_id参数缺失',
        'sku_id.isInteger'    => 'sku_id必须为正整数',
        'num.require'         => 'num参数缺失',
        'selected.isInteger'  => 'selected参数缺失',
        'selected.number'     => 'selected参数必须为数字',
    ];

    protected $scene = [
        'add'     => ['goods_id', 'sku_id', 'num'],
        'change'  => ['id', 'num'],
        'choice'  => ['id', 'all', 'selected'],
        'destroy' => ['ids'],
    ];
}