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

namespace app\common\model\addons;


use app\common\basics\Models;

class Coupon extends Models
{
    // 设置字段信息
    protected $schema = [
        'id'               => 'int',      //主键ID
        'type'             => 'int',      //优惠券类型[10=满减券，20=折扣券]
        'name'             => 'string',   //优惠券名称
        'min_price'        => 'int',      //最低消费金额
        'reduce_price'     => 'int',      //满减券：减免金额
        'discount'         => 'int',      //折扣券：折扣率(0-100)
        'total_num'        => 'int',      //发放总数量(-1为不限制)
        'get_method'       => 'int',      //领取方式[1=直接领取，2=指定发放]
        'get_num_type'     => 'int',      //领取限制[0=不限制，1=限制次数，2=每天限制次数]
        'get_num'          => 'int',      //可领取次数
        'use_goods_type'   => 'int',      //使用商品[0-全部商品可用, 1=部分商品可用，2=部分商品不可用]
        'use_goods_ids'    => 'string',   //限制使用商品的ID, 逗号隔开
        'use_expire_time'  => 'int',      //领取后多久过期(0为不过期)，单位: 天
        'receive_count'    => 'int',      //累计领取数量
        'explain'          => 'string',   //使用说明
        'status'           => 'int',      //状态[0=未发布, 1=已发布, 2=已关闭，3=已过期]
        'is_delete'        => 'int',      //是否删除[0=否, 1=是]
        'publish_time'     => 'int',      //发布时间
        'create_time'      => 'int',      //创建时间
        'update_time'      => 'int',      //更新时间
        'delete_time'      => 'int',      //删除时间
    ];

    /**
     * 修改器: 限制商品ID
     *
     * @author windy
     * @param $value
     * @return string
     */
    public function setUseGoodsIdsAttr($value)
    {
        if ($value) {
            $arr = array_unique($value);
            return implode(',', $arr);
        }
        return '';
    }

    /**
     * 获取器: 限制商品ID
     *
     * @author windy
     * @param $value
     * @return array|false|string[]
     */
    public function getUseGoodsIdsAttr($value)
    {
        return $value ? explode(',', $value) : [];
    }
}