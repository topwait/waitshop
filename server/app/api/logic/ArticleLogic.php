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

namespace app\api\logic;


use app\common\basics\Logic;
use app\common\model\content\Article;
use app\common\model\content\ArticleCategory;

/**
 * 文章接口-逻辑层
 * Class ArticleLogic
 * @package app\api\logic
 */
class ArticleLogic extends Logic
{
    /**
     * 文章列表
     *
     * @author windy
     * @param array $get
     * @return array
     * @throws @\think\db\exception\DataNotFoundException
     * @throws @\think\db\exception\DbException
     * @throws @\think\db\exception\ModelNotFoundException
     */
    public static function lists(array $get): array
    {
        $where[] = ['is_delete', '=', 0];
        $where[] = ['is_show', '=', 1];

        if (!empty($get['cid']) and $get['cid']) {
            $where[] = ['category_id', '=', intval($get['cid'])];
        }

        $lists = (new Article())->field('id,title,image,intro,views,create_time')
            ->where($where)
            ->order('sort desc, id desc')
            ->paginate([
                'page'      => $get['page'] ?? 1,
                'list_rows' => $get['limit'] ?? 20,
                'var_page'  => 'page'
            ])->toArray();

        $lists['tabs'] = (new ArticleCategory())
            ->field('id,name')
            ->where(['is_disable'=>0, 'is_delete'=>0])
            ->order('sort desc, id desc')
            ->select()->toArray();

        array_unshift($lists['tabs'], ['id'=>0, 'name'=>'全部']);
        return $lists;
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
        Article::update(['views'=>['inc', 1]], ['id'=>$id]);
        return $model->field('id,title,image,intro,content,views,create_time')
            ->findOrEmpty(intval($id))
            ->toArray();
    }
}