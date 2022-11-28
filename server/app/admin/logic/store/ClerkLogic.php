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

namespace app\admin\logic\store;


use app\common\basics\Logic;
use app\common\model\store\StoreClerk;
use Exception;

/**
 * 门店员工-逻辑层
 * Class ClerkLogic
 * @package app\admin\logic\shop
 */
class ClerkLogic extends Logic
{
    /**
     * 店员列表
     *
     * @author windy
     * @param array $get
     * @return array
     * @throws @\think\db\exception\DbException
     */
    public static function lists(array $get): array
    {
        $model = new StoreClerk();
        $lists = $model->withoutField('is_delete,delete_time')
            ->where(['is_delete'=>0])
            ->order('id desc')
            ->with(['user', 'store'])
            ->paginate([
                'page'      => $get['page'],
                'list_rows' => $get['limit'],
                'var_page'  => 'page',
            ])->toArray();

        return ['count'=>$lists['total'], 'list'=>$lists['data']];
    }

    /**
     * 店员详细
     *
     * @author windy
     * @param int $id
     * @return array
     */
    public static function detail(int $id): array
    {
        $model = new StoreClerk();
        return $model->withoutField('is_delete,update_time,delete_time')
            ->with(['user'])
            ->findOrEmpty(intval($id))
            ->toArray();
    }

    /**
     * 新增店员
     *
     * @author windy
     * @param array $post
     * @return bool
     */
    public static function add(array $post): bool
    {
        try {
            StoreClerk::create([
                'user_id'  => $post['user_id'],
                'store_id' => $post['store_id'],
                'nickname' => $post['nickname'],
                'mobile'   => $post['mobile'],
                'status'   => $post['status']
            ]);

            return true;
        } catch (Exception $e) {
            static::$error = $e->getMessage();
            return false;
        }
    }

    /**
     * 编辑店员
     *
     * @author windy
     * @param array $post
     * @return bool
     */
    public static function edit(array $post): bool
    {
        try {
            StoreClerk::update([
                'store_id' => $post['store_id'],
                'nickname' => $post['nickname'],
                'mobile'   => $post['mobile'],
                'status'   => $post['status']
            ], ['id'=>$post['id']]);

            return true;
        } catch (Exception $e) {
            static::$error = $e->getMessage();
            return false;
        }
    }

    /**
     * 删除店员
     *
     * @author windy
     * @param int $id (店员id)
     * @return bool
     */
    public static function del(int $id): bool
    {
        try {
            StoreClerk::update([
                'is_delete'  => 1,
                'update_time' => time()
            ], ['id'=>intval($id)]);

            return true;
        } catch (Exception $e) {
            static::$error = $e->getMessage();
            return false;
        }
    }

    /**
     * 停用店员
     *
     * @author windy
     * @param int $id (店员ID)
     * @return bool
     */
    public static function stop(int $id): bool
    {
        try {
            StoreClerk::update([
                'status'      => 0,
                'update_time' => time()
            ], ['id'=>intval($id)]);

            return true;
        } catch (Exception $e) {
            static::$error = $e->getMessage();
            return false;
        }
    }

    /**
     * 启用店员
     *
     * @author windy
     * @param int $id (店员ID)
     * @return bool
     */
    public static function start(int $id): bool
    {
        try {
            StoreClerk::update([
                'status'      => 1,
                'update_time' => time()
            ], ['id'=>intval($id)]);

            return true;
        } catch (Exception $e) {
            static::$error = $e->getMessage();
            return false;
        }
    }
}