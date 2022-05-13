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

namespace app\admin\logic\goods;


use app\common\basics\Logic;
use app\common\model\goods\Goods;
use app\common\utils\ArrayUtils;
use app\common\model\goods\GoodsCategory;
use Exception;

/**
 * 商品分类-逻辑层
 * Class GoodsCategory
 * @package app\admin\model\goods
 */
class CategoryLogic extends Logic
{
    /**
     * 树形分类(HTML)
     *
     * @author windy
     * @return array
     */
    public static function getTreeHtmlCategory(): array
    {
        return ArrayUtils::toTreeHtml(self::all());
    }

    /**
     * 所有分类
     *
     * @author windy
     * @return array
     * @throws @\think\db\exception\DataNotFoundException
     * @throws @\think\db\exception\DbException
     * @throws @\think\db\exception\ModelNotFoundException
     */
    public static function all(): array
    {
        $model = new GoodsCategory;
        return $model->field(true)->where(['is_delete'=>0])
            ->order('sort desc, id desc')
            ->select()->toArray();
    }

    /**
     * 分类详细
     *
     * @author windy
     * @param int $id(分类ID)
     * @return array
     */
    public static function detail(int $id): array
    {
        $model = new GoodsCategory;
        return $model->field(true)->findOrEmpty($id)->toArray();
    }

    /**
     * 新增分类
     *
     * @author windy
     * @param array $post
     * @return bool
     */
    public static function add(array $post): bool
    {
        try {
            // 验证父级
            $parentCategory = null;
            if (intval($post['pid']) > 0) {
                $parentCategory = (new GoodsCategory())
                    ->field('id,name,level,relation')
                    ->where(['id'=>intval($post['pid'])])
                    ->where(['is_delete'=>0])
                    ->findOrEmpty()
                    ->toArray();

                if (!$parentCategory) {
                    throw new Exception('父级不存在!');
                }
            }

            // 新增分类
            $category = GoodsCategory::create([
                'pid'       => $post['pid'],
                'name'      => $post['name'],
                'image'     => $post['image'] ?? '',
                'sort'      => $post['sort'],
                'is_show'   => $post['is_show'],
                'relation'  => '',
                'is_delete' => 0
            ]);

            // 更新关系
            if (intval($post['pid']) === 0) {
                GoodsCategory::update([
                    'level'    => 1,
                    'relation' => "0," . $category['id']
                ], ['id'=>$category['id']]);
            } else {
                GoodsCategory::update([
                    'level'    => $parentCategory + 1,
                    'relation' => $parentCategory['relation'] . ',' . $category['id']
                ], ['id'=>$category['id']]);
            }

            return true;
        } catch (Exception $e) {
            static::$error = $e->getMessage();
            return false;
        }
    }

    /**
     * 编辑分类
     *
     * @author windy
     * @param array $post
     * @return bool
     */
    public static function edit(array $post): bool
    {
        try {
            if ($post['pid'] == $post['id']) {
                throw new Exception('上级不能是自己');
            }

            $model = new GoodsCategory();
            $parentCategory = null;
            if (intval($post['pid']) > 0) {
                $parentCategory = (new GoodsCategory())
                    ->field('id,name,level,relation')
                    ->where(['id'=>intval($post['pid'])])
                    ->where(['is_delete'=>0])
                    ->findOrEmpty()
                    ->toArray();

                if (!$parentCategory) {
                    throw new Exception('父级不存在!');
                }
            }

            $childrenIds = $model->field('id,name')
                ->where("find_in_set(".$post['id'].",relation)")
                ->column('id');

            if (in_array(intval($post['pid']), $childrenIds)) {
                throw new Exception( '选择的上级不能是自己的下级');
            }

            $category = $model
                ->field('id,name,relation')
                ->where(['id'=>intval($post['id'])])
                ->where(['is_delete'=>0])
                ->findOrEmpty()
                ->toArray();

            if (!$category) {
                throw new Exception('分类不存在!');
            }

            // 更新分类
            GoodsCategory::update([
                'pid'       => $post['pid'],
                'name'      => $post['name'],
                'image'     => $post['image'] ?? '',
                'sort'      => $post['sort'],
                'is_show'   => $post['is_show']
            ], ['id'=>(int)$post['id']]);

            // 更新关系
            $replaceLevel = 0;
            $relation = null;
            if (intval($post['pid']) === 0) {
                $replaceLevel = $parentCategory['level'] - 1;
                $relation = "0," . $category['id'];
            } else {
                $replaceLevel = $category['level'] - ($parentCategory['level'] + 1);
                $relation = $parentCategory['relation'] . ',' . strval($category['id']);
            }

            $model->where("find_in_set(".$category['id'].",relation)")
                ->exp('relation', "REPLACE(relation,'". $category['relation'] ."','". $relation ."')")
                ->update([
                    'level' => ['dec', $replaceLevel],
                    'update_time' => time()
                ]);

            return true;
        } catch (Exception $e) {
            static::$error = $e->getMessage();
            return false;
        }
    }

    /**
     * 删除分类
     *
     * @author windy
     * @param int $id (主键ID)
     * @return bool
     */
    public static function del(int $id): bool
    {
        try {
            $model = new GoodsCategory();

            $category = $model->field('id')
                ->where(['id'=>intval($id)])
                ->where(['is_delete'=>0])
                ->findOrEmpty()
                ->toArray();

            if ($category) {
                throw new Exception('分类不存在!');
            }

            $children = $model->field('id')
                ->where(['pid'=>intval($id)])
                ->where(['is_delete'=>0])
                ->findOrEmpty()
                ->toArray();

            if ($children) {
                throw new Exception('当前分类已被子级分类使用,请先移除!');
            }

            $goods = (new Goods())
                ->field('id,name')
                ->where([
                    ['first_category_id|second_category_id|third_category_id', '=', intval($id)],
                    ['is_delete', '=', 0]
                ])->findOrEmpty()
                ->toArray();

            if ($goods) {
                throw new Exception('当前分类被商品使用:'.$goods['name']);
            }

            GoodsCategory::update([
                'is_delete'   => 1,
                'delete_time' => time()
            ], ['id'=>(int)$id]);

            return true;
        } catch (Exception $e) {
            static::$error = $e->getMessage();
            return false;
        }
    }

    /**
     * 切换状态
     *
     * @author windy
     * @param $post
     * @return bool
     */
    public static function change($post): bool
    {
        try {
            GoodsCategory::update([
                $post['field'] => $post['status'],
                'update_time'  => time()
            ], ['id'=>(int)$post['id']]);
            return true;

        } catch (Exception $e) {
            static::$error = $e->getMessage();
            return false;
        }
    }
}