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

namespace app\admin\validate\goods;


use app\common\basics\Validates;

/**
 * 商品参数验证器
 * Class GoodsValidate
 * @package app\admin\validate\product
 */
class GoodsValidate extends Validates
{
    protected $rule = [
        'id'             => 'require|isInteger',
        'name'           => 'require|max:64',
        'category'       => 'require|array|isCategory',
        'brand_id'       => 'isInteger',
        'supplier_id'    => 'isInteger',
        'image'          => 'require',
        'sort'           => 'require|number',
        'sales_volume'   => 'require|number',
        'is_show'        => 'require|in:0,1'
    ];

    protected $message = [
        'id.require'   => 'ID不可为空',
        'id.isInteger' => 'ID不符合规范',
        'category.require'      => '请选择商品分类',
        'category.isCategory'   => '请选择商品分类',
        'brand_id.isInteger'    => '品牌不符合规范',
        'supplier_id.isInteger' => '供应商不符合规范',
        'image.require'         => '请选择商品主图',
        'sort.require'          => '请填写商品排序',
        'sort.number'           => '商品排序必须为数字',
        'sales_volume.require'  => '请填写预设销量',
        'sales_volume.number'   => '预设销量必须为数字',
        'is_show.require' => '请选择是否上下架',
        'is_show.in'      => '上下架选择异常',
    ];

    protected $scene = [
        'id'  => ['id'],
        'add' => ['name', 'first_category_id', 'second_category_id', 'third_category_id',
            'brand_id', 'supplier_id', 'sort', 'sales_volume', 'is_show'],
        'edit' => ['id', 'name', 'first_category_id', 'second_category_id', 'third_category_id',
            'brand_id', 'supplier_id', 'sort', 'sales_volume', 'is_show'],
    ];

    /**
     * 验证商品分类
     * @param $value(需验证的值)
     * @return bool
     */
    protected function isCategory($value)
    {
        if (is_array($value) && !empty($value) && count($value) > 0) {
            return true;
        }
        return false;
    }
}