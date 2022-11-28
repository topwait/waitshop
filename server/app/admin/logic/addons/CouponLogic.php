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

namespace app\admin\logic\addons;


use app\common\basics\Logic;
use app\common\enum\CouponEnum;
use app\common\model\addons\Coupon;
use app\common\model\goods\Goods;
use Exception;

/**
 * 优惠券-逻辑层
 * Class CouponLogic
 * @package app\admin\logic\addons
 */
class CouponLogic extends Logic
{
    /**
     * 公共查询条件
     * @author windy
     * @param $type
     * @return array
     */
    public static function queryWhere($type): array
    {
        $where = [];
        switch ($type) {
            case 1: // 待发布
                $where[] = ['status', '=', 0];
                $where[] = ['is_delete', '=', 0];
                break;
            case 2: // 进行中
                $where[] = ['status', '=', 1];
                $where[] = ['is_delete', '=', 0];
                break;
            case 3: // 已关闭
                $where[] = ['status', '=', 2];
                $where[] = ['is_delete', '=', 0];
                break;
        }
        return $where;
    }

    /**
     * 优惠券列表
     *
     * @author windy
     * @param array $get
     * @return array|false
     * @throws @\think\db\exception\DbException
     */
    public static function lists(array $get)
    {
        $model = new Coupon();
        $lists = $model->field(true)
            ->where(self::queryWhere($get['type'] ?? 1))
            ->order('id', 'desc')
            ->paginate([
                'page'      => $get['page'],
                'list_rows' => $get['limit'],
                'var_page'  => 'page',
            ])->toArray();

        foreach ($lists['data'] as &$item) {
            $item['min_price']       = '￥'.$item['min_price'];
            $item['type']            = CouponEnum::getTypeName($item['type']);
            $item['get_method']      = CouponEnum::getMethodName($item['get_method']);
            $item['use_goods_type']  = CouponEnum::getUseGoodsTypeDesc($item['use_goods_type']);
            $item['use_expire_time'] = !$item['use_expire_time'] ? '不限制' : '领取后'.$item['use_expire_time'].'天内可用';
        }

        return ['count'=>$lists['total'], 'list'=>$lists['data']];
    }

    /**
     * 统计数据
     *
     * @author windy
     * @return array
     */
    public static function statistics(): array
    {
        $model = new Coupon();
        $stay    = $model->where(self::queryWhere(1))->count();
        $conduct = $model->where(self::queryWhere(2))->count();
        $close   = $model->where(self::queryWhere(3))->count();

        return [
            'stay'    => $stay,
            'conduct' => $conduct,
            'close'   => $close
        ];
    }

    /**
     * 优惠券详细
     *
     * @author windy
     * @param int $id
     * @return array|bool
     * @throws @\think\db\exception\DataNotFoundException
     * @throws @\think\db\exception\DbException
     * @throws @\think\db\exception\ModelNotFoundException
     */
    public static function detail(int $id)
    {
        $model = new Coupon();
        $detail = $model->field(true)->findOrEmpty($id)->toArray();

        $detail['goods'] = [];
        if ($detail['use_goods_type'] !== 0) {
            $goodsModel = new Goods();
            $detail['goods'] = $goodsModel->field(['id,name,image,min_price,category_id'])
                ->whereIn('id', $detail['use_goods_ids'])
                ->append(['category'])
                ->select()->toArray();

            foreach ($detail['goods'] as &$item) {
                $item['category'] = $item['category'][0]['name'] ?? '未知';
            }
        }

        return $detail;
    }

    /**
     * 新增优惠券
     *
     * @author windy
     * @param array $post
     * @return bool
     */
    public static function add(array $post): bool
    {
        try {
            Coupon::create([
                'type'            => $post['type'],
                'name'            => $post['name'],
                'min_price'       => $post['min_price'],
                'reduce_price'    => $post['reduce_price'],
                'discount'        => $post['discount'],
                'total_num'       => $post['total_num'],
                'get_method'      => $post['get_method'],
                'get_num_type'    => $post['get_num_type'],
                'get_num'         => $post['get_num'],
                'use_goods_type'  => $post['use_goods_type'],
                'use_goods_ids'   => $post['use_goods_ids'] ?? [],
                'use_expire_time' => $post['use_expire_time'],
                'explain'         => $post['explain'] ?? '',
                'status'          => 0,
                'is_delete'       => 0,
                'publish_time'    => 0,
            ]);

            return true;
        } catch (Exception $e) {
            static::$error = $e->getMessage();
            return false;
        }
    }

    /**
     * 编辑优惠券
     *
     * @author windy
     * @param array $post
     * @return bool
     */
    public static function edit(array $post): bool
    {
        try {
            Coupon::update([
                'type'            => $post['type'],
                'name'            => $post['name'],
                'min_price'       => $post['min_price'],
                'reduce_price'    => $post['reduce_price'],
                'discount'        => $post['discount'],
                'total_num'       => $post['total_num'],
                'get_method'      => $post['get_method'],
                'get_num_type'    => $post['get_num_type'],
                'get_num'         => $post['get_num'],
                'use_goods_type'  => $post['use_goods_type'],
                'use_goods_ids'   => $post['use_goods_ids'] ?? [],
                'use_expire_time' => $post['use_expire_time'],
                'explain'         => $post['explain'] ?? '',
            ], ['id'=>$post['id']]);

            return true;
        } catch (Exception $e) {
            static::$error = $e->getMessage();
            return false;
        }
    }

    /**
     * 删除优惠券
     *
     * @author windy
     * @param int $id
     * @return false
     */
    public static function del(int $id): bool
    {
        try {
            Coupon::update([
                'status'      => CouponEnum::CLOSE_PUBLISH_STATUS,
                'is_delete'   => 1,
                'delete_time' => time()
            ], ['id'=>$id]);

            return true;
        } catch (Exception $e) {
            static::$error = $e->getMessage();
            return false;
        }
    }

    /**
     * 发布优惠券
     *
     * @author windy
     * @param int $id
     * @return false
     */
    public static function publish(int $id): bool
    {
        try {
            Coupon::update([
                'status'       => CouponEnum::OK_PUBLISH_STATUS,
                'publish_time' => time()
            ], ['id'=>$id]);

            return true;
        } catch (Exception $e) {
            static::$error = $e->getMessage();
            return false;
        }
    }

    /**
     * 关闭优惠券
     *
     * @author windy
     * @param int $id
     * @return false
     */
    public static function close(int $id): bool
    {
        try {
            Coupon::update([
                'status'      => CouponEnum::CLOSE_PUBLISH_STATUS,
                'update_time' => time()
            ], ['id'=>$id]);

            return true;
        } catch (Exception $e) {
            static::$error = $e->getMessage();
            return false;
        }
    }
}