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

/**
 * 抽奖记录模型
 * Class LotteryRecord
 * @package app\common\model\addons
 */
class LotteryRecord extends Models
{
    // 设置字段信息
    protected $schema = [
        'id'           => 'int',    //主键ID
        'user_id'      => 'int',    //用户ID
        'prize_type'   => 'int',    //奖品类型
        'prize_image'  => 'string', //奖品图片
        'prize_name'   => 'string', //奖品名称
        'prize_value'  => 'string', //奖品内容
        'prize_tips'   => 'string', //奖品提示
        'prize_snap'   => 'string', //奖品快照
        'create_time'  => 'int'     //创建时间
    ];
}