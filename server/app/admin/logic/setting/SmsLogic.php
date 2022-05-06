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
use app\common\model\ConfigSms;
use Exception;

/**
 * 短信配置-逻辑层
 * Class SmsLogic
 * @package app\admin\logic\setting
 */
class SmsLogic extends Logic
{
    /**
     * 短信渠道列表
     *
     * @author windy
     * @param array $get
     * @return array|bool
     * @throws @\think\db\exception\DbException
     */
    public static function lists(array $get)
    {
        $model = new ConfigSms();
        $lists = $model->field(true)
            ->order('id', 'asc')
            ->order('sort', 'desc')
            ->paginate([
                'page'      => $get['page'] ?? 1,
                'list_rows' => $get['limit'] ?? 20,
                'var_page'  => 'page'
            ])->toArray();

        return ['count'=>$lists['total'], 'list'=>$lists['data']];
    }

    /**
     * 短信详细配置
     *
     * @author windy
     * @param string $alias
     * @return array
     */
    public static function detail(string $alias): array
    {
        $model = new ConfigSms();
        return $model->field(true)
            ->where(['alias'=>$alias])
            ->findOrEmpty()->toArray();
    }

    /**
     * 设置短信参数
     *
     * @author windy
     * @param array $post
     * @return bool
     */
    public static function set(array $post): bool
    {
        try {
            if ($post['is_enable']) {
                ConfigSms::update(['is_enable'=>0], [['id', '>=', 1]]);
            }

            switch (strtolower($post['alias'])) {
                case 'aliyun':
                    ConfigSms::update([
                        'name' => $post['name'],
                        'sort' => $post['sort'],
                        'is_enable' => $post['is_enable'],
                        'params' => json_encode([
                            'sign'          => trim($post['sign'] ?? ''),
                            'access_key_id' => trim($post['access_key_id'] ?? ''),
                            'access_secret' => trim($post['access_secret'] ?? ''),
                        ], JSON_UNESCAPED_UNICODE)
                    ], ['id' => (int)$post['id']]);
                    break;
                case 'tencent':
                    ConfigSms::update([
                        'sort' => $post['sort'],
                        'name' => $post['name'],
                        'is_enable' => $post['is_enable'],
                        'params' => json_encode([
                            'sign'       => trim($post['sign'] ?? ''),
                            'app_id'     => trim($post['app_id'] ?? ''),
                            'secret_id'  => trim($post['secret_id'] ?? ''),
                            'secret_key' => trim($post['secret_key'] ?? ''),
                        ], JSON_UNESCAPED_UNICODE)
                    ], ['id' => (int)$post['id']]);
                    break;
            }

            return true;
        } catch (Exception $e) {
            static::$error = $e->getMessage();
            return false;
        }
    }
}