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
use app\common\model\ConfigPolicy;
use Exception;

/**
 * 政策协议配置-逻辑层
 * Class PolicyLogic
 * @package app\admin\logic\setting
 */
class PolicyLogic extends Logic
{
    /**
     * 政策协议
     *
     * @author windy
     * @return array
     * @throws @\think\db\exception\DataNotFoundException
     * @throws @\think\db\exception\DbException
     * @throws @\think\db\exception\ModelNotFoundException
     */
    public static function detail(): array
    {
        $lists = (new ConfigPolicy())->field(true)->select()->toArray();

        $data = [];
        foreach ($lists as $item) {
            $data[$item['type']] = $item;
        }

        return $data;
    }

    /**
     * 设置政策协议
     *
     * @author windy
     * @param array $post
     * @return bool
     */
    public static function set(array $post): bool
    {
        try {
            ConfigPolicy::update([
                'content' => $post['service'] ?? '',
                'update_time' => time()
            ], ['id' => 1]);

            ConfigPolicy::update([
                'content' => $post['privacy'] ?? '',
                'update_time' => time()
            ], ['id' => 2]);

            ConfigPolicy::update([
                'content' => $post['ensure'] ?? '',
                'update_time' => time()
            ], ['id' => 3]);

            return true;
        } catch (Exception $e) {
            static::$error = $e->getMessage();
            return false;
        }
    }
}