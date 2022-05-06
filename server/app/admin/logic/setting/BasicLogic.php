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

namespace app\admin\logic\setting;


use app\common\basics\Logic;
use app\common\utils\ConfigUtils;
use app\common\utils\UrlUtils;
use Exception;

/**
 * 基础配置-逻辑层
 * Class BasicLogic
 * @package app\admin\logic\setting
 */
class BasicLogic extends Logic
{

    /**
     * 设置基础参数
     *
     * @author windy
     * @param array $post
     * @return bool
     */
    public static function set(array $post): bool
    {
        try {
            // 基础配置
            ConfigUtils::set('backstage', [
                'backstage_login_title' => $post['backstage_login_title'] ?? '',
                'backstage_web_favicon' => UrlUtils::getRelativeUrl($post['backstage_web_favicon'] ?? '/static/images/backstage_web_favicon.ico'),
                'backstage_side_logo'   => UrlUtils::getRelativeUrl($post['backstage_side_logo']   ?? '/static/images/backstage_side_logo.png'),
                'mall_logo'             => UrlUtils::getRelativeUrl($post['mall_logo']             ?? '/static/images/mall_logo.png'),
                'mall_avatar'           => UrlUtils::getRelativeUrl($post['mall_avatar']           ?? '/static/images/default_avatar.png'),
            ]);

            if (empty($post['backstage_web_favicon'])) {
                $from = public_path() . '/static/images/backstage_web_favicon.ico';
                $to   = public_path() . '/favicon2.ico';
                copy($from, $to);
            } else {
                $from = public_path() . '/uploads/image/' . UrlUtils::getRelativeUrl($post['backstage_side_logo']);
                $to   = public_path() . '/favicon.ico';
                copy($from, $to);
            }

            // 登录配置
            ConfigUtils::set('login', [
                'force_mobile' => $post['force_mobile'] ?? 0,
                'login_way'    => json_encode($post['login_way'] ?? [])
            ]);

            // 联系配置
            ConfigUtils::set('contact', [
                'mall_contact_name'   => $post['mall_contact_name'] ?? '',
                'mall_contact_mobile' => $post['mall_contact_mobile'] ?? ''
            ]);

            // 退货地址
            ConfigUtils::set('return', [
                'return_contact_name'   => $post['return_contact_name'] ?? '',
                'return_contact_mobile' => $post['return_contact_mobile'] ?? '',
                'return_province'       => $post['return_province'] ?? '',
                'return_city'           => $post['return_city'] ?? '',
                'return_district'       => $post['return_district'] ?? '',
                'return_address'        => $post['return_address'] ?? '',
            ]);

            // 搜索配置
            ConfigUtils::set('search', [
                'type'    => $post['search-type'] ?? 1,
                'keyword' => empty($post['search-keyword']) ? ''
                    : trim(trim(str_replace('，', ',', $post['search-keyword']), ','))
            ]);

            return true;
        } catch (Exception $e) {
            static::$error = $e->getMessage();
            return false;
        }
    }

    /**
     * 基础配置信息
     *
     * @author windy
     * @return array
     */
    public static function detail(): array
    {
        // 基础配置
        $detail['backstage'] = ConfigUtils::get('backstage', [
            'backstage_login_title' => '',
            'backstage_web_favicon' => '',
            'backstage_side_logo'   => '',
            'mall_logo'             => '',
            'mall_avatar'           => ''
        ]);

        // 登录配置
        $detail['login'] = ConfigUtils::get('login', [
            'force_mobile' => 0,
            'login_way'    => json_encode([])
        ]);

        // 联系配置
        $detail['contact'] = ConfigUtils::get('contact', [
            'mall_contact_name'   => '',
            'mall_contact_mobile' => ''
        ]);

        // 退货地址
        $detail['return'] = ConfigUtils::get('return', [
            'return_contact_name'   => '',
            'return_contact_mobile' => '',
            'return_province'       => '',
            'return_city'           => '',
            'return_district'       => '',
            'return_address'        => '',
        ]);

        // 搜索配置
        $detail['search'] = ConfigUtils::get('search', [
            'type'    => $post['type'] ?? 1,
            'keyword' => ''
        ]);

        // 处理路径
        $detail['backstage']['backstage_web_favicon'] = UrlUtils::getAbsoluteUrl($detail['backstage']['backstage_web_favicon'], true);
        $detail['backstage']['backstage_side_logo']   = UrlUtils::getAbsoluteUrl($detail['backstage']['backstage_side_logo'], true);
        $detail['backstage']['backstage_side_logo']   = UrlUtils::getAbsoluteUrl($detail['backstage']['backstage_side_logo'], true);
        $detail['backstage']['mall_logo']             = UrlUtils::getAbsoluteUrl($detail['backstage']['mall_logo'], true);
        $detail['backstage']['mall_avatar']           = UrlUtils::getAbsoluteUrl($detail['backstage']['mall_avatar'], true);

        $detail['login']['login_way'] = json_decode($detail['login']['login_way'], true);
        return $detail;
    }

}