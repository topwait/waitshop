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


use app\admin\logic\LoginLogic;
use app\common\model\ConfigPayment;
use Exception;

/**
 * 支付方式配置-逻辑层
 * Class PaymentLogic
 * @package app\admin\logic\set
 */
class PaymentLogic extends LoginLogic
{
    /**
     * 支付方式列表
     *
     * @author windy
     * @param array $get
     * @return array|bool
     * @throws @\think\db\exception\DbException
     */
    public static function lists(array $get)
    {
        $model = new ConfigPayment();
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
     * 支付详细配置
     *
     * @author windy
     * @param string $alias
     * @return array
     */
    public static function detail(string $alias): array
    {
        $model = new ConfigPayment();
        return $model->field(true)
            ->where(['alias'=>$alias])
            ->findOrEmpty()->toArray();
    }

    /**
     * 设置支付参数
     *
     * @author windy
     * @param array $post
     * @return bool
     */
    public static function set(array $post): bool
    {
        try {
            switch (strtolower($post['alias'])) {
                case 'wallet':
                    ConfigPayment::update([
                        'name' => $post['name'],
                        'sort' => $post['sort'],
                        'is_enable' => $post['is_enable'],
                    ], ['id' => (int)$post['id']]);
                    break;
                case 'mnp':
                    create_folder(root_path() . 'extend/pem');
                    $cert = public_path() . $post['cert_path'];
                    $keys = public_path() . $post['key_path'];
                    if (!empty($post['cert_path']) and file_exists($cert)) {
                        copy($cert, root_path().'extend/pem/mnp_apiclient_cert.pem');
                    }
                    if (!empty($post['key_path']) and file_exists($keys))  {
                        copy($keys, root_path().'extend/pem/mnp_apiclient_key.pem');
                    }
                    delete_dir(public_path()."uploads/pem/");

                    ConfigPayment::update([
                        'name'      => $post['name'],
                        'sort'      => $post['sort'],
                        'is_enable' => $post['is_enable'],
                        'params'    => json_encode([
                            'app_id'     => $post['app_id'] ?? '',
                            'mch_id'     => $post['mch_id'] ?? '',
                            'key'        => $post['key'] ?? '',
                            'cert_path'  => $post['cert_path'] ? 'mnp_apiclient_cert.pem' : '',
                            'key_path'   => $post['key_path'] ? 'mnp_apiclient_key.pem' : '',
                            'notify_url' => ''
                        ], JSON_UNESCAPED_UNICODE)
                    ], ['id'=>(int)$post['id']]);
                    break;
                case 'alipay':
                    try {
                        create_folder(root_path() . 'extend/pem');
                        $public  = public_path() . $post['public_key'];
                        $private = public_path() . $post['private_key'];
                        if (!empty($post['public_key']) and file_exists($public))  {
                            copy($public, root_path() . 'extend/pem/alipay_public_key.pem');
                        }
                        if (!empty($post['private_key']) and file_exists($private)) {
                            copy($private, root_path() . 'extend/pem/alipay_private_key.pem');
                        }
                        delete_dir(public_path() . "uploads/pem/");
                    } catch (Exception $e){}

                    ConfigPayment::update([
                        'name'      => $post['name'],
                        'sort'      => $post['sort'],
                        'is_enable' => $post['is_enable'],
                        'params'    => json_encode([
                            'pid'         => $post['pid'] ?? '',
                            'key'         => $post['key'] ?? '',
                            'app_id'      => $post['app_id'] ?? '',
                            'public_key'  => $post['public_key']  ? 'alipay_public_key.pem' : '',
                            'private_key' => $post['private_key'] ? 'alipay_private_key.pem' : '',
                            'notify_url'  => ''
                        ], JSON_UNESCAPED_UNICODE)
                    ], ['id'=>(int)$post['id']]);
                    break;
            }

            return true;
        } catch (Exception $e) {
            static::$error = $e->getMessage();
            return false;
        }
    }
}