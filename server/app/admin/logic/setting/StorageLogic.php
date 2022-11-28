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
 * 存储配置-逻辑层
 * Class StorageLogic
 * @package app\admin\logic\setting
 */
class StorageLogic extends Logic
{
    /**
     * 配置信息
     *
     * @author windy
     * @return mixed|null
     */
    public static function getStorage()
    {
        return ConfigUtils::get('storage', [
            'default' => 'local',
            'engine'  => array(
                'local'  => [],
                'qiniu'  => [
                    'bucket'     => '',
                    'access_key' => '',
                    'secret_key' => '',
                    'domain'     => ''
                ]
            )
        ]);
    }

    /**
     * 设置配置信息
     *
     * @author windy
     * @param array $post
     * @return bool
     */
    public static function setStorage(array $post): bool
    {
        try {
            ConfigUtils::set('storage', [
                'default' => $post['storage'],
                'engine' => array(
                    'local' => [],
                    'qiniu' => [
                        'bucket'     => $post['qiniu_bucket'],
                        'access_key' => $post['qiniu_ak'],
                        'secret_key' => $post['qiniu_sk'],
                        'domain'     => $post['qiniu_domain']
                    ]
                )
            ]);
            return true;
        } catch (Exception $e) {
            static::$error = $e->getMessage();
            return false;
        }
    }
}