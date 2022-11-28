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
use app\common\model\Region;
use app\common\model\user\UserAddress;
use app\common\utils\ArrayUtils;
use Exception;
use think\facade\Cache;

/**
 * 收货地址接口-逻辑层
 * Class AddressLogic
 * @package app\api\logic
 */
class AddressLogic extends Logic
{
    /**
     * 收货地址列表
     *
     * @author windy
     * @param int $userId
     * @return array
     * @throws @\think\db\exception\DataNotFoundException
     * @throws @\think\db\exception\DbException
     * @throws @\think\db\exception\ModelNotFoundException
     */
    public static function lists(int $userId) :array
    {
        $model = new UserAddress();
        $lists = $model
            ->withoutField(['is_delete', 'create_time', 'update_time', 'delete_time'])
            ->withJoin(['province', 'city', 'district'], 'left')
            ->where('user_id', '=', $userId)
            ->where('is_delete', '=', 0)
            ->limit(10)->select()->toArray();

        foreach ($lists as &$item) {
            $item['district'] = $item['district']['name'] ?? '未知';
            $item['province'] = $item['province']['name'] ?? '未知';
            $item['city']     = $item['city']['name'] ?? '未知';
            $item['region']   = $item['province'] . $item['city'] . $item['district'];
            unset($item['province']);
            unset($item['district']);
            unset($item['city']);
        }

        return $lists;
    }

    /**
     * 收货地址详细
     *
     * @author windy
     * @param int $id
     * @param int $userId
     * @return array|bool
     */
    public static function detail(int $id, int $userId)
    {
        try {
            $model = new UserAddress();
            $detail = $model
                ->withoutField(['user_id', 'is_delete', 'create_time', 'update_time', 'delete_time'])
                ->with(['province', 'city', 'district'])
                ->where('id', '=', (int)$id)
                ->where('user_id', '=', $userId)
                ->where('is_delete', '=', 0)
                ->findOrEmpty()->toArray();

            if (!$detail) {
                throw new Exception('收货地址不存在');
            } else {
                $detail['city'] = $detail['city']['name'] ?? '未知';
                $detail['province'] = $detail['province']['name'] ?? '未知';
                $detail['district'] = $detail['district']['name'] ?? '未知';
                $detail['region'] = $detail['province'] . $detail['city'] . $detail['district'];
                unset($detail['city']);
                unset($detail['province']);
                unset($detail['district']);
            }

            return $detail;
        } catch (Exception $e) {
            static::$error = $e->getMessage();
            return false;
        }
    }

    /**
     * 对应省,市,区的id
     *
     * @author windy
     * @param array $post
     * @return array
     */
    public static function region(array $post): array
    {
        $model = new Region();
        $detail['province'] = $model->where(['name'=>$post['province']])->value('id');
        $detail['city']     = $model->where(['name'=>$post['city']])->value('id');
        $detail['district'] = $model->where(['name'=>$post['district']])->value('id');
        return $detail;
    }

    /**
     * 默认地址(不存在随机拿一个)
     *
     * @author windy
     * @param int $userId
     * @return array
     */
    public static function default(int $userId): array
    {
        $model = new UserAddress();
        $detail = $model
            ->withoutField(['is_delete', 'create_time', 'update_time', 'delete_time'])
            ->with(['province', 'city', 'district'])
            ->where('is_default', '=', 1)
            ->where('user_id', '=', $userId)
            ->where('is_delete', '=', 0)
            ->findOrEmpty()->toArray();

        if (!$detail or empty($detail)) {
            $detail = $model
                ->withoutField(['is_delete', 'create_time', 'update_time', 'delete_time'])
                ->with(['province', 'city', 'district'])
                ->where('user_id', '=', $userId)
                ->where('is_delete', '=', 0)
                ->findOrEmpty()->toArray();
        }

        if ($detail) {
            $detail['province'] = $detail['province']['name'] ?? '未知';
            $detail['district'] = $detail['district']['name'] ?? '未知';
            $detail['city'] = $detail['city']['name'] ?? '未知';
            $detail['region'] = $detail['province'] . $detail['city'] . $detail['district'];

            unset($detail['city']);
            unset($detail['district']);
            unset($detail['province']);
        }

        return $detail;
    }

    /**
     * 根据地址ID获取收货地址
     *
     * @author windy
     * @param int $id
     * @param int $userId
     * @return array
     * @throws @\think\db\exception\DataNotFoundException
     * @throws @\think\db\exception\DbException
     * @throws @\think\db\exception\ModelNotFoundException
     */
    public static function getAddressById(int $id, int $userId): array
    {
        $model = new UserAddress();
        $detail = $model
            ->withoutField(['is_delete', 'create_time', 'update_time', 'delete_time'])
            ->with(['province', 'city', 'district'])
            ->where('id', '=', $id)
            ->where('user_id', '=', $userId)
            ->where('is_delete', '=', 0)
            ->findOrEmpty()->toArray();

        if ($detail) {
            $detail['province'] = $detail['province']['name'] ?? '未知';
            $detail['city']     = $detail['city']['name'] ?? '未知';
            $detail['district'] = $detail['district']['name'] ?? '未知';
            $detail['region']   = $detail['province'] . $detail['city'] . $detail['district'];
        }

        return $detail;
    }

    /**
     * 新增收货地址
     *
     * @author windy
     * @param array $post
     * @param int $userId
     * @return bool
     */
    public static function add(array $post, int $userId) :bool
    {
        try {
            UserAddress::create([
                'user_id'     => $userId,
                'nickname'    => $post['nickname'],
                'mobile'      => $post['mobile'],
                'province_id' => $post['province_id'],
                'city_id'     => $post['city_id'],
                'district_id' => $post['district_id'],
                'address'     => $post['address'],
                'is_default'  => $post['is_default'],
            ]);

            return true;
        } catch (Exception $e) {
            static::$error = $e->getMessage();
            return false;
        }
    }

    /**
     * 编辑收货地址
     *
     * @author windy
     * @param array $post
     * @param int $userId
     * @return bool
     */
    public static function edit(array $post, int $userId) :bool
    {
        try {
            if ($post['is_default']) {
                UserAddress::update(['is_default'=>0], ['user_id'=>$userId]);
            }

            UserAddress::update([
                'nickname'    => $post['nickname'],
                'mobile'      => $post['mobile'],
                'province_id' => $post['province_id'],
                'city_id'     => $post['city_id'],
                'district_id' => $post['district_id'],
                'address'     => $post['address'],
                'is_default'  => $post['is_default'],
            ], ['id'=>$post['id'], 'user_id'=>$userId]);

            return true;
        } catch (Exception $e) {
            static::$error = $e->getMessage();
            return false;
        }
    }

    /**
     * 删除收货地址
     *
     * @author windy
     * @param int $id
     * @param int $userId
     * @return bool
     */
    public static function del(int $id, int $userId) :bool
    {
        try {
            $model = new UserAddress();
            $model->where(['id'=>$id, 'user_id'=>$userId])
                ->update([
                    'is_delete'   => 1,
                    'delete_time' => time()
                ]);

            return true;
        } catch (Exception $e) {
            static::$error = $e->getMessage();
            return false;
        }
    }

    /**
     * 设置默认地址
     *
     * @author windy
     * @param int $id
     * @param int $userId
     * @return bool
     */
    public static function setDefault(int $id, int $userId) :bool
    {
        try {
            UserAddress::update(['is_default'=>0], ['user_id'=>$userId]);
            UserAddress::update(['is_default'=>1], ['id'=>$id, 'user_id'=>$userId]);
            return true;
        } catch (Exception $e) {
            static::$error = $e->getMessage();
            return false;
        }
    }
}