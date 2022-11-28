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

namespace app\admin\logic\diy;


use app\common\basics\Logic;
use app\common\utils\ConfigUtils;
use Exception;

/**
 * 分类页面装修-逻辑层
 * Class CategoryLogic
 * @package app\admin\logic\diy
 */
class CategoryLogic extends Logic
{
    /**
     * 获取DIY分类参数
     *
     * @author windy
     * @return array
     */
    public static function detail(): array
    {
        return ConfigUtils::get('diy')['categorySkin'] ?? [
            'type' => 30,
            'skin' => 'fantasy'
        ];
    }

    /**
     * 编辑DIY分类页
     *
     * @author windy
     * @param array $post
     * @return bool
     */
    public static function set(array $post): bool
    {
        try {
            $config = ConfigUtils::get('diy') ?? [];
            $config['categorySkin'] = [
                'type' => $post['type'],
                'skin' => $post['skin']
            ];

            ConfigUtils::set('diy', $config);

            return true;
        } catch (Exception $e) {
            static::$error = $e->getMessage();
            return false;
        }
    }
}