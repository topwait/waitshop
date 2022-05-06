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

namespace app\admin\logic\diy;


use app\common\basics\Logic;
use app\common\model\diy\DiyPage;
use Exception;

/**
 * 页面设计-逻辑层
 * Class PageLogic
 * @package app\admin\logic\diy
 */
class PageLogic extends Logic
{
    /**
     * DIY页面列表
     *
     * @author windy
     * @param array $get
     * @return array|bool
     * @throws @\think\db\exception\DbException
     */
    public static function lists(array $get)
    {
        $where = [
            ['is_delete', '=', 0]
        ];

        $model = new DiyPage();
        $lists = $model->withoutField('page_data')->where($where)
            ->order('page_type', 'desc')
            ->paginate([
                'page'      => $get['page'],
                'list_rows' => $get['limit'],
                'var_page'  => 'pa@ge',
            ])->toArray();

        return ['count'=>$lists['total'], 'list'=>$lists['data']];
    }

    /**
     * DIY页面详细
     *
     * @author windy
     * @param int $page_id
     * @return array
     */
    public static function detail(int $page_id): array
    {
        $model = new DiyPage();
        return $model->field(true)->findOrEmpty((int)$page_id)->toArray();
    }

    /**
     * 新增DIY页面
     *
     * @author windy
     * @param array $post
     * @return bool
     */
    public static function add(array $post): bool
    {
        try {
             DiyPage::create([
                'page_type' => 20,
                'page_name' => $post['page']['config']['name'],
                'page_data' => $post
             ]);

             return true;
        } catch (Exception $e) {
            static::$error = $e->getMessage();
            return false;
        }
    }

    /**
     * 编辑DIY页面
     *
     * @author windy
     * @param array $post
     * @param int $page_id
     * @return bool
     */
    public static function edit(array $post, int $page_id): bool
    {
        try {
            DiyPage::update([
                'id'          => (int)$page_id,
                'page_name'   => $post['page']['config']['name'],
                'page_data'   => $post,
                'update_time' => time()
            ]);

            return true;
        } catch (Exception $e) {
            static::$error = $e->getMessage();
            return false;
        }
    }

    /**
     * 删除DIY页面
     *
     * @author windy
     * @param int $page_id
     * @return bool
     */
    public static function del(int $page_id): bool
    {
        try {
            DiyPage::update([
                'is_delete'   => 1,
                'delete_time' => time()
            ], ['id'=>(int)$page_id]);

            return true;
        } catch (Exception $e) {
            static::$error = $e->getMessage();
            return false;
        }
    }

    /**
     * 设置为首页
     *
     * @author windy
     * @param int $page_id
     * @return bool
     */
    public static function home(int $page_id): bool
    {
        try {
            DiyPage::update(['page_type' => 20, 'update_time'=>time()], ['is_delete'=>0]);
            DiyPage::update(['page_type' => 10, 'update_time'=>time()], ['id'=>(int)$page_id]);

            return true;
        } catch (Exception $e) {
            static::$error = $e->getMessage();
            return false;
        }
    }
}