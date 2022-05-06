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

/**
 * 秒杀商品规格模型
 * Class SharingSku
 * @package app\common\model\marketing
 */
class SeckillSku extends Models
{
    // 设置字段信息
    protected $schema = [
        'id'            => 'int',    //主键ID
        'seckill_id'    => 'int',    //秒杀活动ID
        'goods_id'      => 'int',    //商品ID
        'sku_id'        => 'int',    //商品规格ID
        'seckill_price' => 'float',  //秒杀价格
        'seckill_stock' => 'int',    //秒杀库存
        'sales_volume'  => 'int',    //秒杀销量
    ];
}