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
 * 短信枚举类
 * Class SmsEnum
 * @package app\common\enum
 */
class SmsEnum
{
    /**
     * 短信发送状态
     */
    const SEND_ING     = 1; //发送中
    const SEND_SUCCESS = 2; //发送成功
    const SEND_FAIL    = 3; //发送失败

    /**
     * 获取发送状态描述
     *
     * @author windy
     * @param $type
     * @return array|mixed|string
     */
    public static function getSendStatusDesc($type)
    {
        $desc = [
            self::SEND_ING     => '发送中',
            self::SEND_SUCCESS => '发送成功',
            self::SEND_FAIL    => '发送失败',
        ];

        if ($type === true){
            return $desc;
        }
        return $desc[$type] ?? '未知';
    }
}