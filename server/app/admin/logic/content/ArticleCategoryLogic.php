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

namespace app\admin\logic\content;


use app\common\basics\Logic;
use app\common\model\content\ArticleCategory;
use Exception;

/**
 * 文章分类-逻辑层
 * Class ArticleCategoryLogic
 * @package app\admin\logic\content
 */
class ArticleCategoryLogic extends Logic
{
    /**
     * 所有文章分类
     *
     * @author windy
     * @return array
     * @throws @\think\db\exception\DataNotFoundException
     * @throws @\think\db\exception\DbException
     * @throws @\think\db\exception\ModelNotFoundException
     */
    public static function all(): array
    {
        $model = new ArticleCategory();
        return $model->field(true)
            ->where(['is_delete'=>0, 'is_disable'=>0])
            ->order('sort desc, id desc')
            ->select()->toArray();
    }

    /**
     * 文章分类列表
     *
     * @author windy
     * @param array $get
     * @return array
     * @throws @\think\db\exception\DbException
     */
    public static function lists(array $get): array
    {
        $model = new ArticleCategory();
        $lists = $model->field(true)
            ->where(['is_delete'=>0])
            ->order('sort desc, id desc')
            ->paginate([
                'page'      => $get['page'],
                'list_rows' => $get['limit'],
                'var_page'  => 'page',
            ])->toArray();

        return ['count'=>$lists['total'], 'list'=>$lists['data']];
    }

    /**
     * 文章详细
     *
     * @author windy
     * @param int $id
     * @return array
     */
    public static function detail(int $id): array
    {
        $model = new ArticleCategory();
        return $model->field(true)->findOrEmpty(intval($id))->toArray();
    }

    /**
     * 新增文章分类
     *
     * @author windy
     * @param array $post
     * @return bool
     */
    public static function add(array $post): bool
    {
        try {
            ArticleCategory::create([
                'name' => $post['name'],
                'sort' => $post['sort'],
                'is_disable'  => $post['is_disable'],
                'is_delete'   => 0,
                'create_time' => time(),
                'update_time' => time()
            ]);

            return true;
        } catch (Exception $e) {
            static::$error = $e->getMessage();
            return false;
        }
    }

    /**
     * 编辑文章分类
     *
     * @author windy
     * @param array $post
     * @return bool
     */
    public static function edit(array $post): bool
    {
        try {
            ArticleCategory::update([
                'name' => $post['name'],
                'sort' => $post['sort'],
                'is_disable'  => $post['is_disable'],
                'update_time' => time()
            ], ['id'=>intval($post['id'])]);

            return true;
        } catch (Exception $e) {
            static::$error = $e->getMessage();
            return false;
        }
    }

    /**
     * 删除文章分类
     *
     * @author windy
     * @param int $id
     * @return bool
     */
    public static function del(int $id): bool
    {
        try {
            ArticleCategory::update([
                'is_disable'  => 1,
                'is_delete'   => 1,
                'delete_time' => time()
            ], ['id'=>intval($id)]);

            return true;
        } catch (Exception $e) {
            static::$error = $e->getMessage();
            return false;
        }
    }
}