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

namespace app\admin\logic\store;


use app\common\basics\Logic;
use app\common\model\Region;
use app\common\model\store\Store;
use app\common\utils\UrlUtils;
use Exception;

/**
 * 门店管理-逻辑层
 * Class ShopLogic
 * @package app\admin\logic\shop
 */
class StoreLogic extends Logic
{
    /**
     * 门店列表
     *
     * @author windy
     * @param array $get
     * @return array
     * @throws @\think\db\exception\DbException
     */
    public static function lists(array $get): array
    {
        self::setSearch([
            '%like%' => ['name'],
            '='      => ['status']
        ]);

        $model = new Store();
        $lists = $model->withoutField('longitude,latitude,intro,is_delete,update_time,delete_time')
            ->order('sort desc, id desc')
            ->where(['is_delete'=>0])
            ->where(self::$searchWhere)
            ->paginate([
                'page'      => $get['page'],
                'list_rows' => $get['limit'],
                'var_page'  => 'page',
            ])->toArray();

        foreach ($lists['data'] as &$item) {
            $item['address'] = Region::getAddress([
                'province' => $item['province_id'],
                'city'     => $item['city_id'],
                'district' => $item['district_id'],
            ]) . $item['address'];

            unset($item['province_id']);
            unset($item['city_id']);
            unset($item['district_id']);
        }

        return ['count'=>$lists['total'], 'list'=>$lists['data']];
    }

    /**
     * 所有门店
     *
     * @author windy
     * @return array
     * @throws @\think\db\exception\DataNotFoundException
     * @throws @\think\db\exception\DbException
     * @throws @\think\db\exception\ModelNotFoundException
     */
    public static function all(): array
    {
        $model = new Store();
        return $model->field('id,name')
            ->order('sort desc, id desc')
            ->where(['is_delete'=>0])
            ->select()->toArray();
    }

    /**
     * 门店详细
     *
     * @author windy
     * @param int $id (门店ID)
     * @return array
     */
    public static function detail(int $id): array
    {
        $model = new Store();
        return $model->withoutField('is_delete,update_time,delete_time')
            ->findOrEmpty(intval($id))->toArray();
    }

    /**
     * 新增门店
     *
     * @author windy
     * @param array $post
     * @return bool
     */
    public static function add(array $post): bool
    {
        try {
            Store::create([
                'name'           => trim($post['name']),
                'logo'           => UrlUtils::getRelativeUrl($post['logo']),
                'contacts'       => trim($post['contacts']),
                'mobile'         => trim($post['mobile']),
                'intro'          => $post['intro'] ?? '',
                'business_hours' => $post['business_hours'],
                'province_id'    => $post['province_id'],
                'city_id'        => $post['city_id'],
                'district_id'    => $post['district_id'],
                'address'        => $post['address'],
                'longitude'      => $post['longitude'],
                'latitude'       => $post['latitude'],
                'sort'           => $post['sort'],
                'status'         => $post['status'],
                'is_delete'      => 0,
            ]);

            return true;
        } catch (Exception $e) {
            static::$error = $e->getMessage();
            return false;
        }
    }

    /**
     * 编辑门店
     *
     * @author windy
     * @param array $post
     * @return bool
     */
    public static function edit(array $post): bool
    {
        try {
            Store::update([
                'name'           => trim($post['name']),
                'logo'           => UrlUtils::getRelativeUrl($post['logo']),
                'contacts'       => trim($post['contacts']),
                'mobile'         => trim($post['mobile']),
                'intro'          => $post['intro'] ?? '',
                'business_hours' => $post['business_hours'],
                'province_id'    => $post['province_id'],
                'city_id'        => $post['city_id'],
                'district_id'    => $post['district_id'],
                'address'        => $post['address'],
                'longitude'      => $post['longitude'],
                'latitude'       => $post['latitude'],
                'sort'           => $post['sort'],
                'status'         => $post['status']
            ], ['id'=>intval($post['id'])]);

            return true;
        } catch (Exception $e) {
            static::$error = $e->getMessage();
            return false;
        }
    }

    /**
     * 删除门店
     *
     * @author windy
     * @param int $id (门店ID)
     * @return bool
     */
    public static function del(int $id): bool
    {
        try {
            Store::update([
                'is_delete' => 1,
                'status'    => 0,
                'update_time' => time()
            ], ['id'=>intval($id)]);

            return true;
        } catch (Exception $e) {
            static::$error = $e->getMessage();
            return false;
        }
    }

    /**
     * 停用门店
     *
     * @author windy
     * @param int $id (门店ID)
     * @return bool
     */
    public static function stop(int $id): bool
    {
        try {
            Store::update([
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
     * 启用门店
     *
     * @author windy
     * @param int $id (门店ID)
     * @return bool
     */
    public static function start(int $id): bool
    {
        try {
            Store::update([
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