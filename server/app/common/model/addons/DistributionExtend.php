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

/**
 * 分销推广模型
 * Class DistributionRecord
 * @package app\common\model\marketing
 */
class DistributionExtend extends Models
{
    // 设置字段信息
    protected $schema = [
        'id'                        => 'int',   //主键ID
        'user_id'                   => 'int',   //用户ID
        'fans_num'                  => 'int',   //推广粉丝数
        'distribution_order_num'    => 'int',   //分销订单数量
        'distribution_order_money'  => 'float', //分销订单金额
        'create_time'               => 'int',   //创建时间
        'update_time'               => 'int'    //更新时间
    ];
}