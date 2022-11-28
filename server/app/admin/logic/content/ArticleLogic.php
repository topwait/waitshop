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
use app\common\model\content\Article;
use Exception;

/**
 * 文章管理-逻辑层
 * Class Article
 * @package app\admin\logic\content
 */
class ArticleLogic extends Logic
{
    /**
     * 文章列表
     *
     * @author windy
     * @param array $get
     * @return array
     * @throws @\think\db\exception\DbException
     */
    public static function lists(array $get): array
    {
        self::setSearch([
            '=' => ['category_id', 'is_notice', 'is_notice', 'is_show'],
            '%like%' => ['title']
        ]);

        $model = new Article();
        $lists = $model->field(true)
            ->with(['category'])
            ->where(['is_delete'=>0])
            ->where(self::$searchWhere)
            ->order('sort desc, id desc')
            ->paginate([
                'page'      => $get['page'],
                'list_rows' => $get['limit'],
                'var_page'  => 'page',
            ])->toArray();

        foreach ($lists['data'] as &$item) {
            $item['category'] = $item['category']['name'] ?? '未知';
        }

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
        $model = new Article();
        return $model->findOrEmpty(intval($id))->toArray();
    }

    /**
     * 新增文章
     *
     * @author windy
     * @param array $post
     * @return bool
     */
    public static function add(array $post): bool
    {
        try {
            Article::create([
                'category_id' => $post['category_id'],
                'title'       => $post['title'],
                'image'       => $post['image'],
                'intro'       => $post['intro'] ?? '',
                'content'     => $post['content'] ?? '',
                'views'       => 0,
                'sort'        => $post['sort'],
                'is_notice'   => $post['is_notice'],
                'is_show'     => $post['is_show'],
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
     * 编辑文章
     *
     * @author windy
     * @param array $post
     * @return bool
     */
    public static function edit(array $post): bool
    {
        try {
            Article::update([
                'category_id' => $post['category_id'],
                'title'       => $post['title'],
                'image'       => $post['image'],
                'intro'       => $post['intro'] ?? '',
                'content'     => $post['content'] ?? '',
                'sort'        => $post['sort'],
                'is_notice'   => $post['is_notice'],
                'is_show'     => $post['is_show'],
                'update_time' => time()
            ], ['id'=>intval($post['id'])]);

            return true;
        } catch (Exception $e) {
            static::$error = $e->getMessage();
            return false;
        }
    }

    /**
     * 删除文章
     *
     * @author windy
     * @param int $id
     * @return bool
     */
    public static function del(int $id): bool
    {
        try {
            Article::update([
                'is_show'     => 0,
                'is_delete'   => 1,
                'delete_time' => time()
            ], ['id'=>$id]);

            return true;
        } catch (Exception $e) {
            static::$error = $e->getMessage();
            return false;
        }
    }
}