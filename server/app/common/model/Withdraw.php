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
 * 提现模型
 * Class Withdraw
 * @package app\common\model
 */
class Withdraw extends Models
{
    // 设置字段信息
    protected $schema = [
        'id'                    => 'int',     //主键ID
        'user_id'               => 'int',     //用户ID
        'admin_id'              => 'int',     //管理员ID
        'type'                  => 'int',     //提现方式[1=钱包余额, 2=微信零钱, 3=银行卡, 4=微信收款码, 5=支付宝收款码]
        'withdraw_sn'           => 'string',  //提现编号
        'real_name'             => 'string',  //真实姓名
        'account'               => 'string',  //提现账号
        'bank'                  => 'string',  //提现银行
        'subbank'               => 'string',  //银行支行
        'money_qr_code'         => 'string',  //收款二维码
        'apply_money'           => 'float',   //申请提现金额
        'actual_money'          => 'float',   //实际到账金额(扣除手续费后)
        'service_charge'        => 'float',   //扣除的手续费
        'service_charge_ratio'  => 'float',   //主手续费比例(%)
        'audit_remark'          => 'string',  //审核信息
        'transfer_time'         => 'int',     //转账时间
        'transfer_voucher'      => 'string',  //转账凭证
        'transfer_remark'       => 'string',  //转账备注
        'remark'                => 'string',  //备注信息
        'status'                => 'int',     //提现状态[1=申请中, 2=提现中, 3=提现成功, 4=提现失败]
        'create_time'           => 'int',     //创建时间
        'update_time'           => 'int',     //更新时间
    ];
}