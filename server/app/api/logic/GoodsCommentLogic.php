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
use app\common\enum\OrderEnum;
use app\common\model\goods\GoodsComment;
use app\common\model\order\OrderGoods;
use app\common\utils\UrlUtils;
use Exception;

/**
 * 商品评论接口-逻辑层
 * Class GoodsCommentLogic
 * @package app\api\logic
 */
class GoodsCommentLogic extends Logic
{
    /**
     * 根据商品ID获取所有评论
     *
     * @author windy
     * @param array $get
     * @return array
     * @throws @\think\db\exception\DbException
     */
    public static function whole(array $get): array
    {
        $where = [];
        if (!empty($get['tab']) && $get['tab'] > 0) {
            switch (intval($get['tab'])) {
                case 1:
                    $where[] = ['GC.images', '<>', ''];
                    break;
                case 2:
                    $where[] = ['GC.goods_comment', '>', 3];
                    $where[] = ['GC.service_comment', '>', 3];
                    $where[] = ['GC.express_comment', '>', 3];
                    break;
            }
        }

        $model = new GoodsComment();
        $lists = $model->alias('GC')
            ->field('GC.id,GC.images,OG.spec_value_str,U.avatar,
                U.nickname,GC.goods_comment,GC.content,GC.reply,GC.create_time')
            ->where([
                ['GC.goods_id', '=', intval($get['id'])],
                ['GC.is_show', '=', 1],
                ['GC.is_delete', '=', 0]
            ])
            ->where($where)
            ->join('order_goods OG', 'OG.id=GC.order_goods_id')
            ->join('user U', 'U.id=GC.user_id')
            ->paginate([
                'page'      => $get['page'] ?? 1,
                'list_rows' => $get['limit'] ?? 20,
                'var_page'  => 'page'
            ])->toArray();


        foreach ($lists['data'] as &$item) {
            $item['avatar'] = UrlUtils::getAbsoluteUrl($item['avatar']);
        }


        $condition = ['goods_id'=>intval($get['id']), 'is_show'=>1, 'is_delete'=>0];
        $tabs = [
            ['name'=>'全部', 'count'=>$model->where($condition)->count()],
            ['name'=>'晒图', 'count'=>$model->where($condition)->where('images', '<>', '')->count()],
            ['name'=>'好评', 'count'=>$model->where($condition)
                ->where('goods_comment', '>', 3)
                ->where('service_comment', '>', 3)
                ->where('express_comment', '>', 3)->count()],
        ];

        return ['tabs'=>$tabs, 'list'=>$lists];
    }

    /**
     * 待评论商品列表
     *
     * @author windy
     * @param array $get
     * @param int $userId
     * @return mixed
     */
    public static function stay(array $get, int $userId)
    {
        $model = new OrderGoods();
        return $model->field('OG.id,OG.name,OG.image,OG.spec_value_str,OG.actual_price,OG.count')
            ->alias('OG')
            ->join('Order O', 'O.id = OG.order_id')
            ->where([
                'O.user_id'      => $userId,
                'O.order_status' => OrderEnum::STATUS_FINISH,
                'OG.is_comment'  => 0
            ])
            ->paginate([
                'page'      => $get['page'] ?? 1,
                'list_rows' => $get['limit'] ?? 4,
                'var_page'  => 'page'
            ])->toArray();
    }

    /**
     * 已评论列表
     *
     * @author windy
     * @param array $get
     * @param int $user_id
     * @return array
     * @throws @\think\db\exception\DbException
     */
    public static function already(array $get, int $user_id): array
    {
        $model = new GoodsComment();
        return $model->field('id,goods_id,order_goods_id,images,content,reply,create_time')
            ->where([
                ['user_id', '=', $user_id],
                ['is_show', '=', 1],
                ['is_delete', '=', 0]
            ])
            ->with(['orderGoods'])
            ->paginate([
                'page'      => $get['page'] ?? 1,
                'list_rows' => $get['limit'] ?? 4,
                'var_page'  => 'page'
            ])->toArray();
    }

    /**
     * 评论商品信息
     *
     * @author windy
     * @param int $order_goods_id
     * @return array
     */
    public static function goods(int $order_goods_id): array
    {
        $model = new OrderGoods();
        return $model->field('id,goods_id,name,image,spec_value_str,actual_price,count')
            ->where(['id'=>intval($order_goods_id)])
            ->findOrEmpty()
            ->toArray();
    }

    /**
     * 提交评论
     *
     * @author windy
     * @param array $post
     * @param int $userId
     * @return bool
     */
    public static function add(array $post, int $userId): bool
    {
        try {
            $orderGoods = (new OrderGoods())
                ->field(true)
                ->where(['id'=>intval($post['order_goods_id'])])
                ->findOrEmpty()
                ->toArray();

            if (!$orderGoods) {
                throw new Exception('评论商品异常');
            }

            if ($orderGoods['is_comment']) {
                throw new Exception('该商品已评论,请务重复操作');
            }

            GoodsComment::create([
                'user_id'         => $userId,
                'goods_id'        => $orderGoods['goods_id'],
                'order_id'        => $orderGoods['order_id'],
                'order_goods_id'  => $orderGoods['id'],
                'goods_comment'   => intval($post['goods_comment']),
                'service_comment' => intval($post['service_comment']),
                'express_comment' => intval($post['express_comment']),
                'images'          => $post['images'] ?? [],
                'content'         => $post['content'] ?? ''
            ]);

            OrderGoods::update([
                'is_comment'  => 1,
                'update_time' => time()
            ], ['id'=>$orderGoods['id']]);

            return true;
        } catch (Exception $e) {
            static::$error = $e->getMessage();
            return false;
        }
    }
}