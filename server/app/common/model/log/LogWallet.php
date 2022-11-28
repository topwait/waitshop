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
use app\common\model\user\User;

/**
 * 用户钱包日志模型
 * Class LogWallet
 * @package app\common\model\log
 */
class LogWallet extends Models
{
    // 设置字段信息
    protected $schema = [
        'id'            => 'int',    //主键ID
        'admin_id'      => 'int',    //管理员ID: 大于0=后台操作,否则不是
        'user_id'       => 'int',    //用户ID
        'log_sn'        => 'string', //日志编号
        'source_type'   => 'string', //来源类型
        'source_id'     => 'string', //来源ID
        'source_sn'     => 'string', //来源订单号
        'change_type'   => 'int',    //变动类型 [1=增加， 2=减少]
        'change_amount' => 'int',    //变动金额(增加多少金额 / 减少多少金额)
        'before_amount' => 'int',    //变动“前”金额
        'after_amount'  => 'int',    //变动“后”金额
        'remarks'       => 'int',    //操作备注信息
        'create_time'   => 'int',    //创建时间
    ];

    /**
     * 增加
     *
     * @author windy
     * @param float $changeAmount (要增加的金额)
     * @param int $sourceType (来源类型,可通过枚举获取)
     * @param int $userId (用户ID)
     * @param int $adminId (管理员ID)
     * @param int $sourceId (来源ID)
     * @param string $sourceSn (来源编号)
     * @param string $remarks (备注信息)
     */
    public static function add(int $sourceType, float $changeAmount, int $userId, int $adminId, int $sourceId=0, string $sourceSn='', string $remarks='')
    {
        $money = (new User())->where(['id'=>$userId])->value('money');

        self::create([
            'admin_id'      => $adminId,
            'user_id'       => $userId,
            'log_sn'        => make_order_no(),
            'source_type'   => $sourceType,
            'source_id'     => $sourceId,
            'source_sn'     => $sourceSn,
            'change_type'   => 1,
            'change_amount' => $changeAmount,
            'before_amount' => $money - $changeAmount,
            'after_amount'  => $money,
            'remarks'       => $remarks
        ]);
    }

    /**
     * 减少
     *
     * @param float $changeAmount (要减少的金额)
     * @param int $sourceType (来源类型,可通过枚举获取)
     * @param int $userId (用户ID)
     * @param int $adminId (管理员ID)
     * @param int $sourceId (来源ID)
     * @param string $sourceSn (来源编号)
     * @param string $remarks (备注信息)
     */
    public static function reduce(int $sourceType, float $changeAmount, int $userId, int $adminId, int $sourceId=0, string $sourceSn='', string $remarks='')
    {
        $money = (new User())->where(['id'=>$userId])->value('money');

        self::create([
            'admin_id'      => $adminId,
            'user_id'       => $userId,
            'log_sn'        => make_order_no(),
            'source_type'   => $sourceType,
            'source_id'     => $sourceId,
            'source_sn'     => $sourceSn,
            'change_type'   => 2,
            'change_amount' => $changeAmount,
            'before_amount' => $money + $changeAmount,
            'after_amount'  => $money,
            'remarks'       => $remarks
        ]);
    }
}