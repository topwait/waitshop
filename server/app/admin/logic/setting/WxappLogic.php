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

namespace app\admin\logic\setting;


use app\common\basics\Logic;
use app\common\utils\ConfigUtils;
use Exception;

/**
 * 微信配置-逻辑层
 * Class WxappLogic
 * @package app\admin\logic\set
 */
class WxappLogic extends Logic
{
    /**
     * 获取微信配置信息
     *
     * @author windy
     * @return array
     */
    public static function detail(): array
    {
        return [
            'mnp' => ConfigUtils::get('mnp', [
                'app_id'      => '',
                'app_secret'  => ''
            ]),
            'oa' => ConfigUtils::get('oa', [
                'app_id'      => '',
                'app_secret'  => ''
            ]),
        ];
    }

    /**
     * 设置微信配置信息
     *
     * @author windy
     * @param array $post
     * @return bool
     */
    public static function set(array $post): bool
    {
        try {
            // 微信小程序配置
            ConfigUtils::set('mnp', [
                'app_id'     => $post['mnp_appId'] ?? '',
                'app_secret' => $post['mnp_appSecret'] ?? ''
            ]);

            // 微信公众号配置
            ConfigUtils::set('oa', [
                'app_id'     => $post['oa_appId'] ?? '',
                'app_secret' => $post['oa_appSecret'] ?? ''
            ]);

            return true;
        } catch (Exception $e) {
            static::$error = $e->getMessage();
            return false;
        }
    }
}