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
use think\model\relation\HasOne;

class CouponList extends Models
{
    // 设置字段信息
    protected $schema = [
        'coupon_list_id' => 'int',    //主键ID
        'user_id'        => 'int',    //用户ID
        'coupon_id'      => 'int',    //优惠券ID
        'order_id'       => 'int',    //使用的订单ID
        'is_use'         => 'int',    //是否使用[0=否, 1=是]
        'is_expire'      => 'int',    //是否失效[0=否, 1=是]
        'use_time'       => 'int',    //使用时间
        'expiry_time'    => 'int',    //失效时间
        'receive_time'   => 'int',    //领取时间
        'update_time'    => 'int'     //更新时间
    ];

    /**
     * 单关联: 优惠券模型
     */
    public function coupon(): HasOne
    {
        return $this->hasOne('Coupon', 'id', 'coupon_id')
            ->withoutField('status,is_delete,create_time,update_time,delete_time,publish_time');
    }
}