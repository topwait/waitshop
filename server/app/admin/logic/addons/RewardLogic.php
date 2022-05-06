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

namespace app\admin\logic\addons;


use app\common\basics\Logic;
use app\common\utils\ConfigUtils;
use Exception;

/**
 * 赠送奖励配置-逻辑层
 * Class RewardLogic
 * @package app\admin\logic\addons
 */
class RewardLogic extends Logic
{
    /**
     * 设置赠送奖励
     *
     * @author windy
     * @param array $post
     * @return bool
     */
    public static function set(array $post): bool
    {
        try {
            ConfigUtils::set('reward', [
                // 注册奖励
                'register_reward_integral' => $post['register_reward_integral'] ?? 0,
                'register_reward_growth'   => $post['register_reward_growth'] ?? 0,
                // 下单奖励
                'order_reward_integral'    => $post['order_reward_integral'] ?? 0,
                'order_reward_growth'      => $post['order_reward_growth'] ?? 0,
                // 邀请奖励
                'invited_reward_integral'  => $post['invited_reward_integral'] ?? 0,
                'invited_reward_earnings'  => $post['invited_reward_earnings'] ?? 0,
            ], '赠送奖励配置');

            return true;
        } catch (Exception $e) {
            static::$error = $e->getMessage();
            return false;
        }
    }

    /**
     * 赠送奖励详情
     *
     * @author windy
     * @return array
     */
    public static function detail(): array
    {
        return ConfigUtils::get('reward', [
            // 注册奖励
            'register_reward_integral' => 0,
            'register_reward_growth'   => 0,
            // 下单奖励
            'order_reward_integral'    => 0,
            'order_reward_growth'      => 0,
            // 邀请奖励
            'invited_reward_integral'  => 0,
            'invited_reward_earnings'  => 0,
        ]);
    }
}