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

namespace app\common\utils;


use app\common\model\Config;
use Exception;
use think\facade\Cache;

/**
 * 配置服务类
 * Class ConfigService
 * @package app\common\service
 */
class ConfigUtils
{
    /**
     * 更新或新增配置
     *
     * @author windy
     * @param $key (键)
     * @param $values (值)
     * @param $describe (描述名称)
     * @return mixed
     */
    public static function set($key, $values, $describe='')
    {
        // 查找是否存在
        $model = new Config();
        $config = $model->field(true)->where(['key'=>$key])
            ->findOrEmpty()->toArray();

        // 存在则修改,否则新增
        if ($config) {
            Config::update([
                'values'      => $values,
                'describe'    => $describe ? $describe : $config['describe'],
                'update_time' => time()
            ], ['key'=>$key]);
        } else {
            Config::create([
                'key'      => $key,
                'values'   => $values,
                'describe' => $describe,
                'update_time' => time()
            ]);
        }

        // 删除缓存
        Cache::delete('wait_config');

        // 返回值
        return $values;
    }

    /**
     * 获取配置
     *
     * @author windy
     * @param $key
     * @param null $default
     * @return mixed|null
     */
    public static function get($key, $default=null)
    {
        $data = self::getAll();
        return isset($data[$key]) ? $data[$key]['values'] : $default;
    }

    /**
     * 读取所有配置 (会读缓存)
     *
     * @author windy
     * @return array|mixed
     */
    public static function getAll(): array
    {
        try {
            $model = new Config();
            if (!$data = Cache::get('wait_config')) {
                $setting = $model->field(true)->select()->toArray();
                $data = empty($setting) ? [] : array_column($setting, null, 'key');
                Cache::tag('cache')->set('wait_config', $data);
            }
            return $data;
        } catch (Exception $e) {
            return [];
        }
    }
}