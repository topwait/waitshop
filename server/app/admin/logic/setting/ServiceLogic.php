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
 * 客服配置-逻辑层
 * Class ServiceLogic
 * @package app\admin\logic\setting
 */
class ServiceLogic extends Logic
{
    /**
     * 获取客服配置
     *
     * @author windy
     * @return array
     */
    public static function detail(): array
    {
        $detail = ConfigUtils::get('service', [
            // 客服QQ
            'qq'    => '',
            // 客服微信
            'wx'    => '',
            // 客服电话
            'phone' => '',
            // 客服图片
            'image' => '',
            // 营业时间
            'business_hours' => ''
        ]);

        $detail['image'] = UrlUtils::getAbsoluteUrl($detail['image']);
        return $detail;
    }

    /**
     * 设置客服配置
     *
     * @author windy
     * @param array $post
     * @return bool
     */
    public static function set(array $post): bool
    {
        try {

             ConfigUtils::set('service', [
                // 客服QQ
                'qq'    => $post['qq'] ?? '',
                // 客服微信
                'wx'    => $post['wx'] ?? '',
                // 客服电话
                'phone' => $post['phone'] ?? '',
                // 客服图片
                'image' => UrlUtils::getRelativeUrl($post['image'] ?? ''),
                // 营业时间
                'business_hours' => $post['business_hours']
            ]);

             return true;
        } catch (Exception $e) {
            static::$error = $e->getMessage();
            return false;
        }
    }
}