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

namespace app\admin\logic\auth;


use app\common\basics\Logic;
use app\common\model\auth\Admin;
use Exception;

/**
 * 管理员-逻辑层
 * Class AdminLogic
 * @package app\admin\logic\auth
 */
class AdminLogic extends Logic
{
    /**
     * 管理员列表
     *
     * @author windy
     * @param array $get
     * @return array
     * @throws @\think\db\exception\DbException
     */
    public static function lists(array $get): array
    {
        $model = new Admin();
        $lists = $model->withoutField(['password', 'salt', 'delete_time', 'is_delete'])
            ->with(['role'])
            ->where(['is_delete'=>0])
            ->order('id asc')
            ->paginate([
                'page'      => $get['page'] ?? 1,
                'list_rows' => $get['limit'] ?? 20,
                'var_page'  => 'page'
            ])->toArray();

        return ['count'=>$lists['total'], 'list'=>$lists['data']];
    }

    /**
     * 管理员详细
     *
     * @author windy
     * @param int $id (主键ID)
     * @return array
     */
    public static function detail(int $id): array
    {
        $model = new Admin();
        return $model->field(true)->findOrEmpty($id)->toArray();
    }

    /**
     * 更新基本信息
     *
     * @author windy
     * @param array $post
     * @param int $id
     * @return bool
     */
    public static function info(array $post, int $id): bool
    {
        try {
            if (!empty($post['password']) and $post['password']) {
                $post['password'] = encrypt_password($post['password']);
            } else {
                $post['password'] = self::detail($id)['password'];
            }

            Admin::update([
                'nickname'   => $post['nickname'],
                'password'   => $post['password'],
                'avatar'     => $post['avatar'] ?? '',
                'email'      => $post['email'],
            ], ['id'=>$id]);

            return true;
        } catch (Exception $e) {
            static::$error = $e->getMessage();
            return false;
        }
    }

    /**
     * 新增管理员
     *
     * @author windy
     * @param array $post
     * @return bool
     */
    public static function add(array $post) :bool
    {
        try {
             $post['password'] = encrypt_password($post['password']);
             Admin::create([
                'username'   => $post['username'],
                'nickname'   => $post['nickname'],
                'password'   => $post['password'],
                'avatar'     => $post['avatar'],
                'email'      => $post['email'],
                'role_id'    => $post['role_id'],
                'is_disable' => $post['is_disable'],
                'is_delete'  => 0
            ]);

             return true;
        } catch (Exception $e) {
            static::$error = $e->getMessage();
            return false;
        }
    }

    /**
     * 编辑管理员
     *
     * @author windy
     * @param array $post
     * @return bool
     */
    public static function edit(array $post) :bool
    {
        try {
            if (!empty($post['password']) and $post['password']) {
                $post['password'] = encrypt_password($post['password']);
            } else {
                $post['password'] = self::detail($post['id'])['password'];
            }

            Admin::update([
                'username'   => $post['username'],
                'nickname'   => $post['nickname'],
                'password'   => $post['password'],
                'avatar'     => $post['avatar'] ?? '',
                'email'      => $post['email'],
                'role_id'    => $post['role_id'],
                'is_disable' => $post['is_disable']
            ], ['id'=>(int)$post['id']]);

            return true;
        } catch (Exception $e) {
            static::$error = $e->getMessage();
            return false;
        }
    }

    /**
     * 删除管理员
     *
     * @author windy
     * @param int $id (主键ID)
     * @return bool
     */
    public static function del(int $id): bool
    {
        try {
            Admin::update([
                'is_delete'   => 1,
                'delete_time' => time()
            ], ['id', '=', (int)$id]);

            return true;
        } catch (Exception $e) {
            static::$error = $e->getMessage();
            return false;
        }
    }

}