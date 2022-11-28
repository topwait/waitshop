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

namespace app\common\model\order;


use app\common\basics\Models;
use app\common\model\user\User;
use think\model\relation\HasMany;
use think\model\relation\HasOne;

/**
 * 订单模型
 * Class Order
 * @package app\common\model\order
 */
class Order extends Models
{
    // 设置字段信息
    protected $schema = [
        'id'               => 'int',     //主键ID
        'user_id'          => 'int',     //用户ID
        'order_sn'         => 'string',  //订单编号
        'order_type'       => 'int',     //订单类型[1=普通订单, 2=秒杀订单, 3=拼团订单, 4=砍价订单]
        'order_platform'   => 'int',     //订单平台[1=小程序, 2=H5, 3=ios, 4=安卓]
        'order_status'     => 'int',     //订单状态[0=待付款, 1=待发货, 2=待收货, 3=已完成, 4=已关闭]
        'pay_status'       => 'int',     //支付状态[0=待支付, 1=已支付]
        'refund_status'    => 'int',     //退款状态[0=未退款, 1=全部退款, 2=部分退款]
        'pay_way'          => 'int',     //支付方式[1=余额支付, 2=微信支付, 3=支付宝支付]
        'delivery_type'    => 'int',     //配送方式[1=快递发货, 2=上门自提, 3=同城配送]
        'discount_amount'  => 'float',   //优惠金额
        'integral_amount'  => 'float',   //积分抵扣金额
        'total_amount'     => 'float',   //订单总额 (尚未优惠前的金额)
        'paid_amount'      => 'float',   //实际付款金额 (优惠后的金额)
        'refund_amount'    => 'float',   //退款金额
        'buyers_remarks'   => 'string',  //买家备注
        'transaction_id'   => 'string',  //第三方交易流水号
        'address_snap'     => 'string',  //收货地址快照
        'pick_up_store'    => 'int',     //自提门店ID
        'pick_up_code'     => 'string',  //自提编码
        'pick_up_status'   => 'int',     //自提状态[0=未核销, 1=已核销]
        'seckill_id'       => 'int',     //秒杀活动ID[0=非秒杀订单]
        'team_activity_id' => 'int',     //拼团活动ID[0=非拼团订单]
        'team_found_id'    => 'int',     //拼团开团ID[0=非拼团订单]
        'team_found_status'   => 'int',  //拼团状态[0=非拼团, 1=拼团成功, 2=拼团失败]
        'stock_deduct_method' => 'int',  //库存扣除方式[1=下单扣除, 2=支付扣除]
        'is_after_sale'    => 'int',     //是否正在售后中[0=否, 1=是]
        'is_delete'        => 'int',     //是否删除[0=否, 1=是]
        'pay_time'         => 'int',     //支付时间
        'delivery_time'    => 'int',     //发货时间
        'confirm_time'     => 'int',     //确认收货时间
        'cancel_time'      => 'int',     //订单取消时间
        'create_time'      => 'int',     //创建时间
        'update_time'      => 'int',     //更新时间
        'delete_time'      => 'int',     //删除时间
    ];

    /**
     * 关联: 用户模型
     */
    public function user(): HasOne
    {
        return $this->hasOne(User::class, 'id', 'user_id')
            ->field(['id', 'sn', 'nickname', 'avatar', 'mobile', 'sex', 'money']);
    }

    /**
     * 关联: 订单商品模型
     */
    public function orderGoods(): HasMany
    {
        return $this->hasMany(OrderGoods::class, 'order_id', 'id');
    }

    /**
     * 关联: 订单发货模型
     */
    public function delivery(): HasOne
    {
        return $this->hasOne('OrderDelivery', 'order_id', 'id');
    }

    /**
     * 获取器: 格式化支付时间
     *
     * @author windy
     * @param $value
     * @return false|string
     */
    public function getPayTimeAttr($value)
    {
        if ($value === 0) {
            return 0;
        }
        return date('Y-m-d H:i:s', $value);
    }

    /**
     * 获取器: 格式化确认收货时间
     *
     * @author windy
     * @param $value
     * @return false|string
     */
    public function getConfirmTimeAttr($value)
    {
        if ($value === 0) {
            return 0;
        }
        return date('Y-m-d H:i:s', $value);
    }

    /**
     * 获取器: 格式化取消订单时间
     *
     * @author windy
     * @param $value
     * @return false|string
     */
    public function getCancelTimeAttr($value)
    {
        if ($value === 0) {
            return 0;
        }
        return date('Y-m-d H:i:s', $value);
    }

    /**
     * 获取器: 格式化发货时间
     * @param $value
     * @return false|string
     */
    public function getDeliveryTimeAttr($value)
    {
        if ($value === 0) {
            return 0;
        }
        return date('Y-m-d H:i:s', $value);
    }

    /**
     * 获取器: 格式化收货地址快照
     *
     * @author windy
     * @param $value
     * @return array|mixed
     */
    public function getAddressSnapAttr($value): array
    {
        if ($value) {
            return json_decode($value, true);
        }
        return [];
    }
}