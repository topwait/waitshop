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

namespace app\common\model\store;


use app\common\basics\Models;
use app\common\model\order\OrderGoods;
use think\model\relation\HasMany;

/**
 * 门店核销记录模型
 * Class ShopVerify
 * @package app\common\model\shop
 */
class StoreVerify extends Models
{
    // 设置字段信息
    protected $schema = [
        'id'          => 'int',  //主键ID
        'operate'     => 'int',  //操作: 1=员工核销，2=后台核销
        'store_id'    => 'int',  //门店ID
        'staff_id'    => 'int',  //核销员ID,和operate字段对应
        'order_id'    => 'int',  //订单ID
        'create_time' => 'int'   //创建时间
    ];

    /**
     * 关联: 订单商品模型
     */
    public function orderGoods(): HasMany
    {
        return $this->hasMany(OrderGoods::class, 'order_id', 'order_id');
    }
}