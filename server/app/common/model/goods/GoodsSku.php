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

namespace app\common\model\goods;


use app\common\basics\Models;
use app\common\utils\UrlUtils;
use think\model\relation\HasOne;

/**
 * 商品SKU规格模型
 * Class GoodsSku
 * @package app\common\model\store
 */
class GoodsSku extends Models
{
    // 设置字段信息
    protected $schema = [
        'id'              => 'int',    //主键ID
        'goods_id'        => 'int',    //商品ID
        'spec_value_ids' => 'string',  //规格值ID组合
        'spec_value_str' => 'string',  //规格值名组合
        'image'          => 'string',  //规格图片
        'market_price'   => 'float',   //市场价格
        'sell_price'     => 'float',   //售卖价格
        'cost_price'     => 'float',   //成本价格
        'sales_volume'   => 'int',     //销量
        'stock'          => 'int',     //库存
        'volume'         => 'int',     //体积
        'weight'         => 'int',     //重量
        'bar_code'       => 'int',     //条形码
    ];

    /**
     * 关联: 商品模型
     */
    protected function goods(): HasOne
    {
        return $this->hasOne('Goods', 'id', 'goods_id')
            ->field(['id', 'image', 'name', 'freight_id', 'give_integral', 'is_integral', 'is_show', 'is_delete']);
    }

    /**
     * 获取器: 处理规格图片
     *
     * @author windy
     * @param $value
     * @return string
     */
    public function getImageAttr($value)
    {
        if (!$value) {
            return '';
        } else {
            return UrlUtils::getAbsoluteUrl($value);
        }
    }
}