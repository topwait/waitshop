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

namespace app\common\enum;

/**
 * 通知模板消息枚举类
 * Class NoticeEnum
 * @package app\common\enum
 */
class NoticeEnum
{
    /**
     * 通知类型
     */
    const SYSTEM_NOTICE     = 1; //系统通知
    const SMS_NOTICE        = 2; //短信通知
    const OA_NOTICE         = 3; //公众号模板通知
    const MNP_NOTICE        = 4; //小程序订阅消息通知

    /**
     * 场景编码
     */
    const ORDER_PAY_NOTICE         = 100; //订单已支付
    const ORDER_DELIVERY_NOTICE    = 101; //订单已发货
    const ORDER_CLOSE_NOTICE       = 102; //订单已关闭
    const ORDER_REFUND_OK_NOTICE   = 103; //订单售后退款成功
    const ORDER_REFUND_FAIL_NOTICE = 104; //订单售后退款失败

    const SMS_LOGIN_CODE_NOTICE    = 110; //验证码登录
    const SMS_REGISTER_CODE_NOTICE = 111; //验证码注册
    const SMS_BIND_MOBILE_NOTICE   = 112; //绑定手机号
    const SMS_CHANGE_MOBILE_NOTICE = 113; //变更手机号
    const SMS_RESET_MOBILE_NOTICE  = 114; //重置登录密码
    const SMS_RESET_PAY_PWD_NOTICE = 115; //重置支付密码


    /**
     * 短信验证码场景
     */
    const SMS_NEED_CODE = [
        self::SMS_LOGIN_CODE_NOTICE,    //验证码登录
        self::SMS_REGISTER_CODE_NOTICE, //验证码注册
        self::SMS_BIND_MOBILE_NOTICE,   //绑定手机号
        self::SMS_CHANGE_MOBILE_NOTICE, //变更手机号
        self::SMS_RESET_MOBILE_NOTICE,  //重置登录密码
        self::SMS_RESET_PAY_PWD_NOTICE, //重置支付密码
    ];

    /**
     * 获取“场景”描述
     *
     * @author windy
     * @param $type
     * @return array|string
     */
    public static function getSceneDesc($type)
    {
        $desc = [
            self::SMS_LOGIN_CODE_NOTICE    => '验证码登录',
            self::SMS_REGISTER_CODE_NOTICE => '验证码注册',
            self::SMS_BIND_MOBILE_NOTICE   => '绑定手机号',
            self::SMS_CHANGE_MOBILE_NOTICE => '变更手机号',
            self::SMS_RESET_MOBILE_NOTICE  => '重置登录密码',
            self::SMS_RESET_PAY_PWD_NOTICE => '重置支付密码',

            self::ORDER_PAY_NOTICE         => '订单已支付',
            self::ORDER_DELIVERY_NOTICE    => '订单已发货',
            self::ORDER_CLOSE_NOTICE       => '订单已关闭',
            self::ORDER_REFUND_OK_NOTICE   => '订单售后退款成功',
            self::ORDER_REFUND_FAIL_NOTICE => '订单售后退款失败',
        ];

        if ($type === true){
            return $desc;
        }
        return $desc[$type] ?? '未知';
    }
}