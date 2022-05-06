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
use app\common\model\goods\GoodsComment;
use Exception;

/**
 * 商品评论-逻辑层
 * Class CommentLogic
 * @package app\admin\logic\goods
 */
class CommentLogic extends Logic
{
    /**
     * 公共查询
     *
     * @author windy
     * @param int $type
     * @return array
     */
    public static function queryWhere(int $type): array
    {
        $where = [];
        switch ($type) {
            case 0: // 所有
                $where[] = ['is_delete', '=', 0];
                break;
            case 1: // 待回复
                $where[] = ['is_delete', '=', 0];
                $where[] = ['is_reply', '=', 0];
                break;
            case 2: // 已隐藏
                $where[] = ['is_show', '=', 0];
                $where[] = ['is_delete', '=', 0];
                break;
            case 3: // 回收站
                $where[] = ['is_delete', '=', 1];
                break;
        }
        return $where;
    }

    /**
     * 评论列表
     *
     * @author windy
     * @param array $get
     * @return array|bool
     * @throws @\think\db\exception\DbException
     */
    public static function lists(array $get) :array
    {
        $where[] = self::queryWhere($get['type']);

        $model = new GoodsComment();
        $lists = $model->field(true)->where($where)
            ->with(['user', 'orderGoods'])
            ->order(['id' => 'desc'])
            ->paginate([
                'page' => $get['page'] ?? 1,
                'list_rows' => $get['limit'] ?? 20,
                'var_page' => 'page'
            ])->toArray();

        foreach ($lists['data'] as &$item) {
            $item['goods_comment'] = [
                'checked' => range(1, $item['goods_comment']),
                'normal'  => 5 - $item['goods_comment'] > 0 ? range(1, 5 - $item['goods_comment']) : []
            ];
            $item['service_comment'] = [
                'checked' => range(1, $item['service_comment']),
                'normal'  => 5 - $item['service_comment'] > 0 ? range(1, 5 - $item['service_comment']) : []
            ];
            $item['express_comment'] = [
                'checked' => range(1, $item['express_comment']),
                'normal'  => 5 - $item['express_comment'] ? range(1, 5 - $item['express_comment']) : []
            ];
        }

        return ['count'=>$lists['total'], 'list'=>$lists['data']];
    }

    /**
     * 数据统计
     *
     * @author windy
     * @return array
     */
    public static function statistics(): array
    {
        $model = new GoodsComment();
        $reply    = $model->where(self::queryWhere(1))->count();   //待回复
        $hidden   = $model->where(self::queryWhere(2))->count();   //已隐藏
        $recovery = $model->where(self::queryWhere(3))->count();   //回收站

        return [
            'reply'     => $reply,
            'hidden'    => $hidden,
            'recovery'  => $recovery,
        ];
    }

    /**
     * 评论详细
     *
     * @author windy
     * @param int $id
     * @return array
     */
    public static function detail(int $id): array
    {
        $model = new GoodsComment();
        return $model->field(true)->findOrEmpty((int)$id)->toArray();
    }

    /**
     * 回复评论内容
     *
     * @author windy
     * @param array $post
     * @return bool
     */
    public static function reply(array $post): bool
    {
        try {
            GoodsComment::update(['reply' => $post['reply'], 'is_reply'=>1, 'update_time' => time()], ['id' => $post['id']]);
            return true;
        } catch (Exception $e) {
            static::$error = $e->getMessage();
            return false;
        }
    }

    /**
     * 隐藏评论
     *
     * @author windy
     * @param array $id
     * @return bool
     */
    public static function hide(array $id): bool
    {
        try {
            (new GoodsComment())->whereIn('id', $id)->update(['is_show'=>0, 'update_time'=>time()]);
            return true;
        } catch (Exception $e) {
            static::$error = $e->getMessage();
            return false;
        }
    }

    /**
     * 显示评论
     *
     * @author windy
     * @param int $id
     * @return bool
     */
    public static function show(int $id): bool
    {
        try {
            (new GoodsComment())->whereIn('id', $id)->update(['is_show'=>1, 'update_time'=>time()]);
            return true;
        } catch (Exception $e) {
            static::$error = $e->getMessage();
            return false;
        }
    }

    /**
     * 删除评论
     *
     * @author windy
     * @param array $id
     * @return bool
     */
    public static function del(array $id): bool
    {
        try {
            (new GoodsComment())->whereIn('id', $id)->update(['is_delete'=>1, 'delete_time'=>time()]);
            return true;
        } catch (Exception $e) {
            static::$error = $e->getMessage();
            return false;
        }
    }

    /**
     * 恢复评论
     *
     * @author windy
     * @param int $id
     * @return bool
     */
    public static function recovery(int $id): bool
    {
        try {
            (new GoodsComment())->where(['id'=>intval($id)])->update(['is_delete'=>0, 'delete_time'=>0]);
            return true;
        } catch (Exception $e) {
            static::$error = $e->getMessage();
            return false;
        }
    }
}