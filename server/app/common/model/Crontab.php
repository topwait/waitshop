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

/**
 * 计划任务模型
 * Class Crontab
 * @package app\common\model
 */
class Crontab extends Models
{
    // 设置字段信息
    protected $schema = [
        'id'          => 'int',     //主键ID
        'name'        => 'string',  //任务名称
        'command'     => 'string',  //执行命令
        'params'      => 'string',  //附带参数
        'rules'       => 'string',  //执行规则
        'remarks'     => 'string',  //备注信息
        'status'      => 'int',     //执行状态[1=执行中, 2=暂停, 3=错误]
        'error'       => 'string',  //执行错误信息
        'exe_time'    => 'string',  //执行时间
        'max_time'    => 'string',  //最大执行时间
        'last_time'   => 'int',     //最后执行时间
        'create_time' => 'int',     //创建时间
        'update_time' => 'int',     //更新时间
    ];
}