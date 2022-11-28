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
use app\common\enum\CouponEnum;
use app\common\model\addons\Coupon;
use app\common\model\addons\CouponList;
use app\common\model\goods\Goods;
use app\common\model\diy\DiyPage;
use app\common\model\goods\GoodsCategory;
use app\common\utils\UrlUtils;

/**
 * 页面设计接口-逻辑层
 * Class DiyLogic
 * @package app\api\logic
 */
class DiyLogic extends Logic
{
    /**
     * Diy数据
     *
     * @author windy
     * @param int $page_id
     * @param int $userId
     * @return array|bool
     * @throws \Exception
     */
    public static function getPageData(int $page_id, int $userId=0)
    {
        // 页面详情
        $detail = $page_id > 0 ? DiyPage::getDetail($page_id) : DiyPage::getHomePage();
        // 页面diy元素
        $items = $detail['page_data']['items'];
        // 页面顶部导航
        isset($detail['page_data']['page']) && $items['page'] = $detail['page_data']['page'];
        // 获取动态数据
        foreach ($items as $key => $item) {
            switch ($item['type']) {
                case 'goods':
                    $items[$key]['data'] = self::getGoodsList($item);
                    break;
                case 'coupon':
                    $items[$key]['data'] = self::getCoupon($item, $userId);
                    break;
            }
        }
        // 返回数据
        return ['page' => $items['page'], 'items' => $items, 'domain'=>UrlUtils::getAbsoluteUrl('/')];
    }

    /**
     * 普通商品数据
     *
     * @author windy
     * @param array $item
     * @return array
     * @throws \Exception
     */
    private static function getGoodsList(array $item): array
    {

        $model = new Goods();
        if ($item['config']['source'] === 'choice') {
            // 手动: 获取指定数据
            $ids = array_column($item['data'], 'id');
            return $model->field(['id,name,image,min_price,market_price,sales_volume'])
                ->whereIn('id', $ids)
                ->select()->toArray();
        } else {
            // 自动: 获取随机数据
            $sort = [
                'all'   => ['id' => 'desc'],
                'sales' => ['sales_volume' => 'desc'],
                'price' => ['min_price' => 'asc']
            ];

            $where = [
                ['is_delete', '=', 0],
                ['is_show', '=', 1]
            ];

            if ($item['config']['auto']['category'] > 0) {
                $cid = intval($item['config']['auto']['category']);
                $childrenIds = (new GoodsCategory())
                    ->where("find_in_set(".$cid.",relation)")
                    ->column('id');
                $where[] = ['category_id', 'in', $childrenIds];
            }

            return $model->field(['id,name,image,min_price,market_price,sales_volume'])
                ->where($where)
                ->limit((int)$item['config']['auto']['showNum'])
                ->order($sort[$item['config']['auto']['goodsSort']])
                ->select()->toArray();
        }
    }

    /**
     * 优惠券数据
     *
     * @author windy
     * @param array $item
     * @param int $userId
     * @return array
     * @throws @\think\db\exception\DataNotFoundException
     * @throws @\think\db\exception\DbException
     * @throws @\think\db\exception\ModelNotFoundException
     */
    private static function getCoupon(array $item, int $userId): array
    {
        $lists = (new Coupon())
            ->field(['id,name,type,min_price,reduce_price,discount'])
            ->where([
                ['get_method', '=', CouponEnum::GET_METHOD_DIRECT],
                ['status', '=', CouponEnum::OK_PUBLISH_STATUS],
                ['is_delete', '=', 0]
            ])
            ->limit($item['config']['limit'])
            ->select()->toArray();

        foreach ($lists as &$item) {
            $item['reduce_price'] = intval($item['reduce_price']);
            $couponList =(new CouponList())->where(['coupon_id'=>$item['id'], 'user_id'=>$userId])->findOrEmpty();
            $item['is_receive'] = $couponList->isEmpty() ? 0 : 1;
        }

        return $lists;
    }
}