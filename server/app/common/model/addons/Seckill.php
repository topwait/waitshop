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
use app\common\model\goods\Goods;
use app\common\model\goods\GoodsSpec;
use think\model\relation\HasMany;

/**
 * 秒杀活动模型
 * Class Seckill
 * @package app\common\model\addons
 */
class Seckill extends Models
{
    // 设置字段信息
    protected $schema = [
        'id'                => 'int',    //主键ID
        'goods_id'          => 'int',    //商品ID
        'name'              => 'string', //活动名称
        'intro'             => 'string', //活动简介
        'image'             => 'string', //商品主图
        'banner'            => 'string', //商品轮播
        'min_seckill_price' => 'float',  //最低秒杀价
        'max_seckill_price' => 'float',  //最高秒杀价
        'min_buy'           => 'int',    //每单最低购买数, 0=不限制
        'max_buy'           => 'int',    //每单最高购买数, 0=不限制
        'total_stock'       => 'int',    //总库存
        'sales_volume'      => 'int',    //总销量
        'is_end'            => 'int',    //活动是否结束[0=否, 1=是]
        'is_coupon'         => 'int',    //用优惠券[0=否, 1=是]
        'is_delete'         => 'int',    //是否删除[0=否, 1=是]
        'start_time'        => 'int',    //活动开始时间
        'end_time'          => 'int',    //活动结束时间
        'create_time'       => 'int',    //创建时间
        'update_time'       => 'int',    //更新时间
        'delete_time'       => 'int',    //删除时间
    ];

    /**
     * 多关联: 秒杀规格模型
     */
    public function seckillSku(): HasMany
    {
        return $this->hasMany(SeckillSku::class, 'seckill_id', 'id');
    }

    /**
     * 关联: 商品规格模型
     */
    public function spec(): HasMany
    {
        return $this->hasMany( GoodsSpec::class, 'goods_id', 'goods_id');
    }

    /**
     * 获取器: 获取商品原售价
     *
     * @author windy
     * @param $value
     * @param $data
     * @return mixed|string
     */
    public function getOriginalPriceAttr($value, $data): string
    {
        unset($value);
        $result = (new Goods())->field(['min_price', 'max_price'])->findOrEmpty($data['goods_id']);

        if (!$result->isEmpty()) {
            if ($result['min_price'] == $result['max_price']) {
                return $result['min_price'];
            } else {
                return $result['min_price'] .' ~ ' . $result['max_price'];
            }
        }

        return '未知';
    }

}