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

namespace app\common\utils;


class UrlUtils
{
    /**
     * 获取当前存储域名
     *
     * @author windy
     * @return string
     */
    public static function getStorageUrl()
    {
        $storage = ConfigUtils::get('storage', ['default'=>'local']);
        if ($storage['default'] == 'local') {
            return request()->domain();
        } else {
            $config = $storage['engine'][$storage['default']];
            $domain = isset($config['domain']) ? $config['domain'] : '';
            return $domain;
        }
    }

    /**
     * 转换为绝对路径
     *
     * @author windy
     * @param string $uri (资源)
     * @param bool $isEmpty (如果为空,直接返回空)
     * @return string
     */
    public static function getAbsoluteUrl(string $uri='', bool $isEmpty=false): string
    {
        if ($isEmpty == true && $uri == '') {
            return '';
        }

        if (strpos($uri, 'http://') === 0 || strpos($uri, 'https://') === 0) {
            return $uri;
        }

        if ($uri && $uri !== '/' && substr($uri, 0, 1) !== '/') {
            $uri = '/'.$uri;
        }

        if (strpos($uri, 'static') === 0 || strpos($uri, '/static') === 0) {
            return request()->domain() . $uri;
        }

        $storage = ConfigUtils::get('storage', ['default'=>'local']);
        if ($storage['default'] == 'local') {
            return request()->domain() . $uri;
        } else {
            $config = $storage['engine'][$storage['default']];
            $domain = isset($config['domain']) ? $config['domain'] : 'http://';
            return $domain . $uri;
        }
    }

    /**
     * 转换为相对路径
     *
     * @author windy
     * @param string $uri
     * @return string
     */
    public static function getRelativeUrl(string $uri): string
    {
        if (strstr($uri, 'http://') || strstr($uri, 'https://')) {
            $uri = str_replace('http://', '', $uri);
            $uri = str_replace('https://', '', $uri);
            $uriArr = explode('/', $uri);
            array_shift($uriArr);
            return implode('/', $uriArr);
        }
        return $uri;
    }

    /**
     * 绝对路径与相对路径互转
     *
     * @param string $uri (要转换的路径)
     * @param string $type (类型：get=获取, set=更新)
     * @param string $operate (操作类型: auto=自动, relative=相对, absolute=绝对)
     * @return string
     * @author windy
     */
    public static function swapUrl(string $uri, string $type, string $operate='auto'): string
    {
        switch ($operate) {
            case 'auto':
                if (strpos($uri, '/static') === 0 and $type === 'set') {
                    return $uri;
                }
                if (strpos($uri, 'http://') === 0 || strpos($uri, 'https://') === 0) {
                    return self::getRelativeUrl($uri);
                } else {
                    return self::getAbsoluteUrl($uri);
                }
            case 'relative':
                return self::getRelativeUrl($uri);
            case 'absolute':
                return self::getAbsoluteUrl($uri);
            default:
                return '';
        }
    }
}