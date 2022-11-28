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
use think\model\relation\HasOne;

class AfterSale extends Models
{
    // 设置字段信息
    protected $schema = [
        'id'             => 'int',    //主键ID
        'sn'             => 'string', //退款编号
        'user_id'        => 'int',    //用户ID
        'order_id'       => 'int',    //订单ID
        'order_goods_id' => 'int',    //订单商品ID
        'refund_type'    => 'int',    //退款类型[1=仅退款, 2=退货退款]
        'refund_reason'  => 'string', //退款原因
        'refund_remark'  => 'string', //退款备注
        'refund_image'   => 'string', //退款图片
        'status'         => 'int',    //退款状态[0=撤销申请, 1=申请中, 2=已通过, 3=已拒绝]
        'refund_price'   => 'float',  //申请退款价格
        'express_name'   => 'string', //快递公司名称
        'express_no'     => 'string', //快递运单号
        'express_image'  => 'string', //快递单凭证
        'express_time'   => 'int',    //快递退回时间
        'create_time'    => 'int',    //创建时间
        'update_time'    => 'int'     //创建时间
    ];

    /**
     * 关联用户模型
     */
    public function user(): HasOne
    {
        return $this->hasOne(User::class, 'id', 'user_id')
            ->field(['id', 'sn', 'nickname', 'avatar', 'mobile', 'sex', 'money']);
    }

    /**
     * 关联订单模型
     */
    public function orders(): HasOne
    {
        return $this->hasOne(Order::class, 'id', 'order_id');
    }

    /**
     * 关联订单商品模型
     */
    public function orderGoods(): HasOne
    {
        return $this->hasOne(OrderGoods::class, 'id', 'order_goods_id')
            ->field('id,name,image,spec_value_str,actual_price,total_price,count,snapshot');
    }
}