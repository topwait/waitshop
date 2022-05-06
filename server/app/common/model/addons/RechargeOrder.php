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
 * 充值订单模型
 * Class RechargeOrder
 * @package app\common\model\addons
 */
class RechargeOrder extends Models
{
    // 设置字段信息
    protected $schema = [
        'id'             => 'int',    //主键ID
        'user_id'        => 'int',    //用户ID
        'order_sn'       => 'string', //订单编号
        'order_platform' => 'int',    //订单平台: [1=安卓, 2=苹果, 3=H5, 4=小程序]
        'pay_way'        => 'int',    //支付方式: [2=微信支付, 3=支付宝支付]
        'pay_status'     => 'int',    //支付状态: [0=待支付, 1=已支付]
        'pay_time'       => 'int',    //支付时间
        'package_id'     => 'int',    //套餐ID
        'transaction_id' => 'string', //支付流水号
        'paid_amount'    => 'float',  //充值金额
        'give_amount'    => 'float',  //赠送金额
        'give_integral'  => 'int',    //赠送积分
        'give_growth'    => 'int',    //赠送成长值
        'create_time'    => 'int',    //创建时间
        'update_time'    => 'int'     //更新时间
    ];
}