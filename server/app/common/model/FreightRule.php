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

namespace app\common\model;


use app\common\basics\Models;

class FreightRule extends Models
{
    // 设置字段信息
    protected $schema = [
        'id'            => 'int',    //主键ID
        'freight_id'    => 'int',    //运费模板ID
        'first_unit'    => 'int',    //首重/件
        'first_fee'     => 'int',    //首重/件价格
        'continue_unit' => 'int',    //续重/件
        'continue_fee'  => 'int',    //续费(元)
        'region'        => 'text',   //可配送区域(城市id集)
        'address'       => 'text',   //可配送区域(城市名称集)
        'create_time'   => 'int'     //创建时间
    ];
}