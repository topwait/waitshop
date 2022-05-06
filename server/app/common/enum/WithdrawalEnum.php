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

namespace app\common\enum;


/**
 * 佣金提现枚举类
 * Class WithdrawalEnum
 * @package app\common\enum
 */
class WithdrawalEnum
{
    /**
     * 提现方式
     */
    const TYPE_BALANCE  = 1; //账户余额
    const TYPE_WECHAT   = 2; //微信零钱
    const TYPE_BANK     = 3; //转银行卡
    const TYPE_MNP_CODE = 4; //微信收款码
    const TYPE_ALI_CODE = 5; //支付宝收款码

    /**
     * 提现状态
     */
    const STATUS_WAIT    = 1; //申请中
    const STATUS_ING     = 2; //提现中
    const STATUS_SUCCESS = 3; //提现成功
    const STATUS_FAIL    = 4; //提现失败

    /**
     * 获取提现方式描述
     *
     * @author windy
     * @param $type
     * @return array|mixed|string
     */
    public static function getTypeDesc($type)
    {
        $desc = [
            self::TYPE_BALANCE   => '账户余额',
            self::TYPE_WECHAT    => '微信零钱',
            self::TYPE_BANK      => '银行卡',
            self::TYPE_MNP_CODE  => '微信收款码',
            self::TYPE_ALI_CODE  => '支付宝收款码'
        ];

        if ($type === true){
            return $desc;
        }
        return $desc[$type] ?? '未知';
    }

    /**
     * 获取提现状态描述
     *
     * @author windy
     * @param $type
     * @return array|mixed|string
     */
    public static function getStatusDesc($type)
    {
        $desc = [
            self::STATUS_WAIT    => '申请中',
            self::STATUS_ING     => '待提现',
            self::STATUS_SUCCESS => '提现成功',
            self::STATUS_FAIL    => '提现失败'
        ];

        if ($type === true){
            return $desc;
        }
        return $desc[$type] ?? '未知';
    }
}