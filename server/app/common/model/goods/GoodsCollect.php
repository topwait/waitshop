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
use think\model\relation\HasOne;

/**
 * 商品收藏模型
 * Class GoodsCollect
 * @package app\common\model\product
 */
class GoodsCollect extends Models
{
    // 设置字段信息
    protected $schema = [
        'id'          => 'int',  //主键ID
        'user_id'     => 'int',  //用户ID
        'goods_id'    => 'int',  //商品ID
        'count'       => 'int',  //操作次数: 即删除又重新收藏的次数
        'is_delete'   => 'int',  //是否删除: 0=否, 1=是
        'create_time' => 'int',  //创建时间
        'delete_time' => 'int'   //删除时间
    ];

    /**
     * 单关联: 商品模型
     */
    public function goods(): HasOne
    {
        return $this->hasOne('Goods', 'id', 'goods_id')
            ->field('id,name,image,min_price');
    }
}