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

namespace app\common\model\order;


use app\common\basics\Models;

/**
 * 订单发货模型
 * Class OrderDeliver
 * @package app\common\model\order
 */
class OrderDelivery extends Models
{
    // 设置字段信息
    protected $schema = [
        'id'             => 'int',      //主键ID
        'user_id'        => 'int',      //用户ID
        'order_id'       => 'int',      //订单ID
        'nickname'       => 'string',   //收货人姓名
        'mobile'         => 'string',   //联系电话
        'province_id'    => 'int',      //省ID
        'city_id'        => 'int',      //市ID
        'district_id'    => 'int',      //区ID
        'address'        => 'string',   //详细地址
        'express_id'     => 'int',      //快递公司ID
        'express'        => 'string',   //快递公司名称
        'waybill_no'     => 'string',   //运单编号
        'delivery_type'  => 'int',      //发货方式
        'create_time'    => 'int',      //创建时间

    ];

    /**
     * 获取器: 格式化发货时间
     *
     * @author windy
     * @param $value
     * @return false|string
     */
    public function getDeliverTimeAttr($value)
    {
        return date('Y-m-d H:i:s', $value);
    }

    /**
     * 获取器: 格式化收货时间
     *
     * @author windy
     * @param $value
     * @return false|string
     */
    public function getCollectTimeAttr($value)
    {
        return date('Y-m-d H:i:s', $value);
    }

    /**
     * 获取器: 格式化商品快照
     *
     * @author windy
     * @param $value
     * @return false|string
     */
    public function getGoodsSnapAttr($value)
    {
        return json_decode($value, true);
    }
}