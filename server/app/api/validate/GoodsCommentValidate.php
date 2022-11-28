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
 * 商品评论-参数验证器
 * Class GoodsCommentValidate
 * @package app\api\validate
 */
class GoodsCommentValidate extends Validates
{
    protected $rule = [
        'order_goods_id'  => 'require|isInteger',
        'goods_comment'   => 'require|isInteger',
        'service_comment' => 'require|isInteger',
        'express_comment' => 'require|isInteger'
    ];

    protected $message = [
        'order_goods_id.require'    => 'order_goods_id参数缺失',
        'order_goods_id.isInteger'  => 'order_goods_id参数必须为正整数',
        'goods_comment.require'     => 'goods_comment参数缺失',
        'goods_comment.isInteger'   => 'goods_comment参数必须为正整数',
        'service_comment.require'   => 'service_comment参数缺失',
        'service_comment.isInteger' => 'service_comment参数必须为正整数',
        'express_comment.require'   => 'express_comment参数缺失',
        'express_comment.isInteger' => 'express_comment必须为正整数',
    ];

    protected $scene = [
        'add' => ['order_goods_id', 'goods_comment', 'service_comment', 'express_comment']
    ];
}