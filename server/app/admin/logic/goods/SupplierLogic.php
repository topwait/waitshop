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

namespace app\admin\logic\goods;


use app\common\basics\Logic;
use app\common\model\goods\Goods;
use app\common\model\goods\GoodsSupplier;
use Exception;

/**
 * 供应商-逻辑层
 * Class SupplierLogic
 * @package app\admin\logic\goods
 */
class SupplierLogic extends Logic
{
    /**
     * 所有供应商
     *
     * @author windy
     * @return array
     * @throws @\think\db\exception\DataNotFoundException
     * @throws @\think\db\exception\DbException
     * @throws @\think\db\exception\ModelNotFoundException
     */
    public static function all(): array
    {
        $model = new GoodsSupplier;
        return $model->field(true)
            ->where(['is_delete' => 0])
            ->order(['sort', 'id' => 'desc'])
            ->select()->toArray();
    }

    /**
     * 供应商列表
     *
     * @author windy
     * @param array $get
     * @return array
     * @throws @\think\db\exception\DbException
     */
    public static function lists(array $get): array
    {
        $model = new GoodsSupplier;
        $lists = $model->field(true)->where(['is_delete' => 0])
            ->order(['sort', 'id' => 'desc'])
            ->paginate([
                'page'      => $get['page'] ?? 1,
                'list_rows' => $get['limit'] ?? 20,
                'var_page'  => 'page'
            ])->toArray();

        return ['count'=>$lists['total'], 'list'=>$lists['data']];
    }

    /**
     * 供应商详细
     *
     * @author windy
     * @param int $id (主键ID)
     * @return array
     */
    public static function detail(int $id): array
    {
        $model = new GoodsSupplier;
        return $model->field(true)->findOrEmpty($id)->toArray();
    }

    /**
     * 新增供应商
     *
     * @author windy
     * @param array $post
     * @return bool
     */
    public static function add(array $post): bool
    {
        try {
            GoodsSupplier::create([
                'name'      => $post['name'],
                'nickname'  => $post['nickname'],
                'mobile'    => $post['mobile'],
                'address'   => $post['address'],
                'remarks'   => $post['remarks'] ?? '',
                'sort'      => $post['sort'],
                'is_delete' => 0
            ]);

            return true;
        } catch (Exception $e) {
            static::$error = $e->getMessage();
            return false;
        }
    }

    /**
     * 编辑供应商
     *
     * @author windy
     * @param array $post
     * @return bool
     */
    public static function edit(array $post): bool
    {
        try {
            GoodsSupplier::update([
                'name'      => $post['name'],
                'nickname'  => $post['nickname'],
                'mobile'    => $post['mobile'],
                'address'   => $post['address'],
                'remarks'   => $post['remarks'] ?? '',
                'sort'      => $post['sort'],
                'is_delete' => 0
            ], ['id'=>(int)$post['id']]);

            return true;
        } catch (Exception $e) {
            static::$error = $e->getMessage();
            return false;
        }
    }

    /**
     * 删除供应商
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
                ->where('supplier_id', '=', intval($id))
                ->where('is_delete', '=', 0)
                ->findOrEmpty()
                ->toArray();

            if ($goods) {
                throw new Exception('当前供应商已被使用,请先移除后再操作！');
            }

            GoodsSupplier::update([
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