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

namespace app\admin\logic\addons\distribution;


use app\common\basics\Logic;
use app\common\utils\ConfigUtils;
use Exception;

/**
 * 分销设置-逻辑层
 * Class DistributionSettingLogic
 * @package app\admin\logic\addons\distribution
 */
class DistributionSettingLogic extends Logic
{
    /**
     * 设置分销配置数据
     *
     * @author windy
     * @param array $post
     * @return bool
     */
    public static function setting(array $post): bool
    {
        try {
            ConfigUtils::set('distribution', [
                'is_open'         => $post['is_open'] ?? 0,
                'level_num'       => $post['level_num'] ?? 1,
                'apply_condition' => $post['apply_condition'] ?? 1
            ]);
            return true;
        } catch (Exception $e) {
            static::$error = $e->getMessage();
            return false;
        }
    }
}