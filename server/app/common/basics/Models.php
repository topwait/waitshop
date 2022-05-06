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

namespace app\common\basics;


use app\common\utils\UrlUtils;
use think\Model;

/**
 * 模型基类
 * Class Models
 * @package app\common\basics
 */
class Models extends Model
{
    /**
     * 获取器：处理图片路径
     *
     * @author windy
     * @param $value
     * @return string|array
     */
    public function getImageAttr($value)
    {
        if (!$value) {
            return '';
        } else {
            return UrlUtils::getAbsoluteUrl($value);
        }
    }

    /**
     * 获取器：处理轮播图片
     *
     * @author windy
     * @param $value
     * @return array|string
     */
    public function getBannerAttr($value)
    {
        if (!$value) {
            return [];
        } else {
            $data = explode(',', $value);
            foreach ($data as &$url) {
                $url = UrlUtils::getAbsoluteUrl($url);
            }
            return $data;
        }
    }

    /**
     * 修改器: 处理图片路径
     *
     * @author windy
     * @param $value
     * @return string|array
     */
    public function setImageAttr($value)
    {
        return UrlUtils::getRelativeUrl($value);
    }

    /**
     * 修改器: 处理轮播图
     *
     * @author windy
     * @param $value
     * @return string|array
     */
    public function setBannerAttr($value)
    {
        if (!$value) {
            return '';
        } else {
            $data = [];
            foreach ($value as $uri) {
                $data[] = UrlUtils::getRelativeUrl($uri);
            }
            return implode(',', $data);
        }
    }
}