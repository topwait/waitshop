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
 * 商品收藏-参数验证器
 * Class GoodsCollectValidate
 * @package app\api\validate
 */
class GoodsCollectValidate extends Validates
{
    protected $rule = [
        'id'       => 'require|isInteger',
        'goods_id' => 'require|isInteger',
        'status'   => 'require|in:0,1',
    ];

    protected $message = [
        'id.require'         => 'id参数缺失',
        'id.isInteger'       => 'id参数必须为正整数',
        'goods_id.require'   => 'goods_id参数缺失',
        'goods_id.isInteger' => 'goods_id参数必须为正整数',
        'status.require'     => 'status参数缺失',
        'status.in'          => 'status参数不在合法值内',
    ];

    protected $scene = [
        'id'  => ['id'],
        'add' => ['goods_id', 'status']
    ];
}