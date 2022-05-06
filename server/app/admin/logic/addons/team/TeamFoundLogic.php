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

namespace app\admin\logic\addons\team;


use app\common\basics\Logic;
use app\common\enum\OrderEnum;
use app\common\enum\TeamEnum;
use app\common\model\addons\TeamFound;
use app\common\model\addons\TeamJoin;
use app\common\utils\UrlUtils;

/**
 * 拼团发起-逻辑层
 * Class TaskLogic
 * @package app\admin\logic\marketing\sharing
 */
class TeamFoundLogic extends Logic
{
    /**
     * 开团列表
     *
     * @author windy
     * @param array $get
     * @return array
     * @throws @\think\db\exception\DbException
     */
    public static function lists(array $get): array
    {
        self::setSearch([
            '=' => ['status@SF.status'],
            'datetime' => ['datetime@create_time'],
            'keyword'  => [
                'sn'       => ['%like%', 'U.sn'],
                'nickname' => ['%like%', 'U.nickname'],
                'mobile'   => ['=', 'U.mobile']
            ]
        ]);

        $model = new TeamFound();
        $lists = $model->field('SF.*,U.sn,U.nickname,U.avatar,U.mobile')
            ->alias('SF')
            ->join('user U', 'U.id = SF.user_id')
            ->where(self::$searchWhere)
            ->order('SF.id desc')
            ->paginate([
                'page'      => $get['page'],
                'list_rows' => $get['limit'],
                'var_page'  => 'page',
            ])->toArray();

        foreach ($lists['data'] as &$item) {

            $item['avatar'] = UrlUtils::getAbsoluteUrl($item['avatar']);
            $item['kaituan_time'] = date('Y-m-d H:i:s', $item['kaituan_time']);
            $item['invalid_time'] = date('Y-m-d H:i:s', $item['invalid_time']);
            if (!empty($item['snapshot'])) {
                $item['snapshot'] = [
                    'name'  => $item['snapshot']['name'],
                    'image' => $item['snapshot']['image']
                ];
            }
        }

        return ['count'=>$lists['total'], 'list'=>$lists['data']];
    }

    /**
     * 团的参团记录列表
     *
     * @author windy
     * @param array $get
     * @return array
     * @throws @\think\db\exception\DbException
     */
    public static function join(array $get): array
    {
        $model = new TeamJoin();
        $lists = $model->withoutField('snapshot')
            ->with(['user', 'orders.orderGoods'])
            ->where(['found_id'=>$get['found_id']])
            ->order('identity desc, id asc')
            ->paginate([
                'page'      => $get['page'],
                'list_rows' => $get['limit'],
                'var_page'  => 'page',
            ])->toArray();

        foreach ($lists['data'] as &$item) {
            $item['orders']['order_status']  = OrderEnum::getOrderStatusDesc($item['orders']['order_status']);
            $item['orders']['pay_status']    = OrderEnum::getPayStatusDesc($item['orders']['pay_status']);
            $item['orders']['refund_status'] = OrderEnum::getRefundStatusDesc($item['orders']['refund_status']);
            $item['orderSnap'] = $item['orders']['orderGoods'][0]['snapshot'];
        }

        return ['count'=>$lists['total'], 'list'=>$lists['data']];
    }

    /**
     * 拼团详情
     *
     * @author windy
     * @param int $id
     * @return array
     */
    public static function detail(int $id): array
    {
        $model = new TeamFound();
        $detail = $model->field(true)->findOrEmpty($id)->toArray();
        $detail['kaituan_time'] = date('Y-m-d H:i:s', $detail['kaituan_time']);
        $detail['invalid_time'] = date('Y-m-d H:i:s', $detail['invalid_time']);
        $detail['status'] = TeamEnum::getFoundStatusDesc($detail['status']);
        return $detail;
    }
}