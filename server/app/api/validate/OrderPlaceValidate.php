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
 * 订单下单参数验证器
 * Class OrderPlaceValidate
 * @package app\api\validate
 */
class OrderPlaceValidate extends Validates
{
    protected $rule = [
        'remarks'       => 'max:200',
        'client'        => 'require|isInteger',
        'pay_way'       => 'require|isInteger',
        'delivery_type' => 'require|isInteger|checkDeliveryType',
        'address_id'    => 'number|isInteger',
        'store_id'      => 'number|isInteger',
        'products'      => 'checkProducts'
    ];

    protected $singleRule = [
        'goods_id' => 'require|isInteger',  // 商品ID
        'sku_id'   => 'require|isInteger',  // 商品SKU_ID
        'num'      => 'require|isInteger'   // 该商品购买数量
    ];

    protected $message = [
        'client.require'          => '缺少客户端标识字段',
        'client.isInteger'        => '客户端异常',
        'pay_way.require'         => '请选择支付方式',
        'pay_way.isInteger'       => '选择的支付方式异常',
        'delivery_type.require'   => '请选择配送方式',
        'delivery_type.isInteger' => '配送方式选择异常',
        'address_id.number'       => '收货地址异常',
        'address_id.isInteger'    => '收货地址异常',
        'store_id.number'         => '自提门店异常',
        'store_id.isInteger'      => '自提门店异常',
    ];

    protected function checkProducts($values)
    {

        if (!is_array($values)) {
            return '商品参数不正确';
        }

        if (empty($values)) {
            return '商品列表不能为空';
        }

        foreach ($values as $value) {
            $validate = Validates::rule($this->singleRule)->scene('product');
            if (!$validate->check($value)) {
                return '商品列表参数错误';
            }
        }

        return true;
    }

    protected function checkDeliveryType($value, $rule, $data)
    {
        unset($value);
        unset($rule);
        if ($data['delivery_type'] === 1) {
            if (empty($data['address_id']) || $data['address_id'] <= 0) {
                return '请选择收货地址';
            }
        } elseif ($data['delivery_type'] === 2) {
            if (empty($data['store_id']) || $data['store_id'] <= 0) {
                return '请选择自提门店';
            }
            if (empty($data['contact']) || !$data['contact']) {
                return '请填写提货人姓名';
            }
            if (empty($data['mobile']) || !$data['mobile']) {
                return '请填写提货人手机号';
            }
        }

        return true;
    }

    protected $scene = [
        'product' => ['goods_id', 'sku_id', 'num']
    ];
}