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
use app\common\model\goods\GoodsBrand;
use Exception;

/**
 * 品牌管理-逻辑层
 * Class BrandLogic
 * @package app\admin\logic\goods
 */
class BrandLogic extends Logic
{
    /**
     * 所有品牌
     *
     * @author windy
     * @return array
     * @throws @\think\db\exception\DataNotFoundException
     * @throws @\think\db\exception\DbException
     * @throws @\think\db\exception\ModelNotFoundException
     */
    public static function all(): array
    {
        $model = new GoodsBrand;
        return $model->field(true)
            ->where(['is_delete'=>0])
            ->order(['sort', 'id'=>'desc'])
            ->select()->toArray();
    }

    /**
     * 品牌列表
     *
     * @author windy
     * @param array $get
     * @return array
     * @throws @\think\db\exception\DbException
     */
    public static function lists(array $get): array
    {
        $model = new GoodsBrand;
        $lists = $model->field(true)->where(['is_delete'=>0])
            ->order(['sort', 'id'=>'desc'])
            ->paginate([
                'page'      => $get['page'] ?? 1,
                'list_rows' => $get['limit'] ?? 20,
                'var_page'  => 'page'
            ])->toArray();

        return ['count'=>$lists['total'], 'list'=>$lists['data']];
    }


    /**
     * 品牌详细
     *
     * @author windy
     * @param int $id (主键ID)
     * @return array
     */
    public static function detail(int $id): array
    {
        $model = new GoodsBrand;
        return $model->field(true)->findOrEmpty((int)$id)->toArray();
    }

    /**
     * 新增品牌
     *
     * @author windy
     * @param array $post
     * @return bool
     */
    public static function add(array $post): bool
    {
        try {
            GoodsBrand::create([
                'name'      => $post['name'],
                'letter'    => $post['letter'],
                'image'     => $post['image'] ?? '',
                'sort'      => $post['sort'] ?? 0,
                'is_show'   => $post['is_show'],
                'is_delete' => 0
            ]);

            return true;
        } catch (Exception $e) {
            static::$error = $e->getMessage();
            return false;
        }
    }

    /**
     * 编辑品牌
     *
     * @author windy
     * @param array $post
     * @return bool
     */
    public static function edit(array $post): bool
    {
        try {
            GoodsBrand::update([
                'name'      => $post['name'],
                'letter'    => $post['letter'],
                'image'     => $post['image'] ?? '',
                'sort'      => $post['sort'] ?? 0,
                'is_show'   => $post['is_show'],
            ], ['id'=>(int)$post['id']]);

            return true;
        } catch (Exception $e) {
            static::$error = $e->getMessage();
            return false;
        }
    }

    /**
     * 删除品牌
     *
     * @author windy
     * @param int $id (主键ID)
     * @return bool
     */
    public static function del(int $id): bool
    {
        try {
            $goods = (new Goods())
                ->field('id')
                ->where('brand_id', '=', intval($id))
                ->where('is_delete', '=', 0)
                ->findOrEmpty()
                ->toArray();

            if ($goods) {
                throw new Exception('当前品牌已被使用,请先移除后再操作！');
            }

            GoodsBrand::update([
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
     * @param array $get
     * @return bool
     */
    public static function change(array $get): bool
    {
        try {
            GoodsBrand::update([
                $get['field'] => $get['status'],
                'update_time' => time()
            ], ['id'=>(int)$get['id']]);

            return true;
        } catch (Exception $e) {
            static::$error = $e->getMessage();
            return false;
        }
    }
}