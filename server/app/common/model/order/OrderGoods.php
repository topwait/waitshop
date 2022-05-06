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
use app\common\utils\UrlUtils;

class OrderGoods extends Models
{
    // 设置字段信息
    protected $schema = [
        'id'               => 'int',    //主键ID
        'order_id'         => 'int',    //订单ID
        'goods_id'         => 'int',    //商品ID
        'sku_id'           => 'int',    //SKU_ID
        'name'             => 'string', //商品名称
        'image'            => 'string', //商品图片
        'spec_value_str'   => 'string', //商品规格
        'count'            => 'int',    //商品数量
        'sell_price'       => 'float',  //商品价格
        'total_price'      => 'float',  //商品总价(未优惠)
        'actual_price'     => 'float',  //实际付款金额(已优惠)
        'discount_price'   => 'float',  //优惠券抵扣金额
        'integral_price'   => 'float',  //积分抵扣金额
        'refund_status'    => 'float',  //退款状态[0=未退款，1=申请中, 2=拒绝退款, 3=部分退款, 4=全部退款]
        'snapshot'         => 'string', //快照信息
        'is_comment'       => 'int',    //是否已品论: 0=否, 1=是
        'create_time'      => 'int',    //创建时间
        'update_time'      => 'int'     //更新时间
    ];

    /**
     * 获取器: 格式化快照
     *
     * @author windy
     * @param $value
     * @return array|mixed
     */
    public function getSnapshotAttr($value): array
    {
        if ($value) {
            $result = json_decode($value, true);
            $result['goods_image'] = $result['goods_image'] ? UrlUtils::getAbsoluteUrl($result['goods_image']) : '';
            $result['spec_image']  = $result['spec_image'] ? UrlUtils::getAbsoluteUrl($result['spec_image']) : '';
            return $result;
        }
        return [];
    }
}