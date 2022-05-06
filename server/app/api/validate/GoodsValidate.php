<?php
// +----------------------------------------------------------------------
// | WaitShop开源电商系统 [ 助力中小企业快速落地 ]
// +----------------------------------------------------------------------
// | 欢迎阅读学习程序代码
// | gitee:   https://gitee.com/wafts/WaitShop
// | github:  https://github.com/miniWorlds/waitshop
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
 * 商品参数验证器
 * Class GoodsValidate
 * @package app\api\validate
 */
class GoodsValidate extends Validates
{
    protected $rule = [
        'id'     => 'require|isInteger',
        'cid'    => 'number',
        'filter' => 'in:all,sales,price,news',
        'sort'   => 'in:desc,asc',
        'type'   => 'require|in:hot,best',
    ];

    protected $message = [
        'id.require'    => 'id参数缺失',
        'id.isInteger'  => 'id必须为正整数',
        'cid.isInteger' => 'cid必须为正整数',
        'filter.in'     => 'filter值不在合法范围内',
        'sort.in'       => 'sort值不在合法范围内',
        'type.require'  => 'type参数缺失',
        'type.in'       => 'type参数不是合法值',
    ];

    protected $scene = [
        'id'   => ['id'],
        'list' => ['cid', 'filter', 'sort'],
        'type' => ['type']
    ];
}