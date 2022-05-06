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

namespace app\common\model\addons;


use app\common\basics\Models;
use app\common\model\order\OrderGoods;
use think\model\relation\HasOne;

/**
 * 分销订单模型
 * Class DistributionOrder
 * @package app\common\model\marketing
 */
class DistributionOrder extends Models
{
    // 设置字段信息
    protected $schema = [
        'id'              => 'int',    //主键ID
        'distribution_sn' => 'string', //分销单号
        'user_id'         => 'int',    //用户ID
        'order_id'        => 'int',    //订单ID
        'order_goods_id'  => 'int',    //子订单ID
        'goods_id'        => 'int',    //商品ID
        'sku_id'          => 'int',    //规格ID
        'reality_amount'  => 'float',  //子订单实付金额
        'earnings_money'  => 'float',  //预估收益金额
        'earnings_ratio'  => 'float',  //分佣比例值
        'status'          => 'int',    //结算状态: 1=未结算，2=已结算，3=已失效
        'create_time'     => 'int',    //创建时间
        'update_time'     => 'int'     //更新时间
    ];

    /**
     * 单关联: 订单商品模型
     */
    public function orderGoods(): HasOne
    {
        return $this->hasOne(OrderGoods::class, 'id', 'order_goods_id')
            ->field('id,name,image,spec_value_str,count,actual_price');
    }
}