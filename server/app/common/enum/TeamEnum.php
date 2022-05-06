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
 * 拼团活动枚举类
 * Class SharingEnum
 * @package app\common\enum
 */
class TeamEnum
{
    /**
     * 团活动的状态
     */
    const TEAM_NOT_STARTED = 0; //未开始
    const TEAM_CONDUCT_IN  = 1; //进行中
    const TEAM_SYS_END     = 2; //结束了

    /**
     * 拼团状态
     */
    const CONDUCT_STATUS = 1; //拼团中
    const SUCCESS_STATUS = 2; //拼团成功
    const FAIL_STATUS    = 3; //拼团失败

    /**
     * 身份标识
     */
    const IDENTITY_CAPTAIN = 1; //团长
    const IDENTITY_MEMBER  = 2; //成员

    /**
     * 获取团活动状态描述
     *
     * @author windy
     * @param $type
     * @return array|mixed|string
     */
    public static function getTeamStatusDesc($type)
    {
        $desc = [
            self::TEAM_NOT_STARTED => '未开始',
            self::TEAM_CONDUCT_IN  => '进行中',
            self::TEAM_SYS_END     => '系统结束',
            self::TEAM_HAND_END    => '手动结束',
        ];

        if ($type === true){
            return $desc;
        }
        return $desc[$type] ?? '未知';
    }

    /**
     * 获取拼团状态描述
     *
     * @author windy
     * @param $type
     * @return array|mixed|string
     */
    public static function getFoundStatusDesc($type)
    {
        $desc = [
            self::CONDUCT_STATUS  => '拼团中',
            self::SUCCESS_STATUS  => '拼团成功',
            self::FAIL_STATUS     => '拼团失败',
        ];

        if ($type === true){
            return $desc;
        }
        return $desc[$type] ?? '未知';
    }
}