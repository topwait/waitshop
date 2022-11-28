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

namespace app\common\model\log;


use app\common\basics\Models;
use app\common\enum\OrderLogEnum;

/**
 * 订单日志模型
 * Class LogOrder
 * @package app\common\model\log
 */
class LogOrder extends Models
{
    // 设置字段信息
    protected $schema = [
        'id'          => 'int',    //主键ID
        'type'        => 'int',    //类型: 1=系统, 2=卖家, 3=买家
        'channel'     => 'int',    //渠道编号
        'order_id'    => 'int',    //订单ID
        'operator_id' => 'int',    //操作人ID
        'content'     => 'string', //日志内容
        'create_time' => 'int',    //创建时间
    ];

    /**
     * 写入日志记录
     *
     * @author windy
     * @param array $params
     */
    public static function record(array $params)
    {
         self::create([
            'type'          => $params['type'],
            'channel'       => $params['channel'],
            'order_id'      => $params['order_id'],
            'operator_id'   => $params['operator_id'] ?? 0,
            'content'       => $params['content'] ?? OrderLogEnum::getRecordDesc($params['channel']),
            'create_time'   => time(),
            'update_time'   => time(),
        ]);
    }
}