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


use app\admin\logic\goods\GoodsLogic;
use app\common\basics\Logic;
use app\common\enum\TeamEnum;
use app\common\model\addons\Team;
use app\common\model\addons\TeamFound;
use app\common\model\addons\TeamSku;
use app\common\utils\UrlUtils;
use Exception;
use think\facade\Db;

/**
 * 拼团活动-逻辑层
 * Class ActivityLogic
 * @package app\admin\logic\marketing\sharing
 */
class TeamActivityLogic extends Logic
{
    /**
     * 拼团活动列表
     *
     * @author windy
     * @param array $get
     * @return array
     * @throws @\think\db\exception\DbException
     */
    public static function lists(array $get): array
    {
        self::setSearch([
            '='        => ['status'],
            '%like%'   => ['keyword@name'],
            'datetime' => ['datetime@create_time'],
        ]);

        $model = new Team();
        $lists = $model->field(true)
            ->where(['is_delete'=>0])
            ->where(self::$searchWhere)
            ->order(['status'=>'asc'])
            ->append(['original_price'])
            ->paginate([
                'page'      => $get['page'],
                'list_rows' => $get['limit'],
                'var_page'  => 'page',
            ])->toArray();

        foreach ($lists['data'] as &$item) {
            $item['start_time'] = date('Y-m-d H:i:s', $item['start_time']);
            $item['end_time'] = date('Y-m-d H:i:s', $item['end_time']);
        }

        return ['count'=>$lists['total'], 'list'=>$lists['data']];
    }

    /**
     * 拼团活动详细
     *
     * @author windy
     * @param int $id
     * @return array
     */
    public static function detail(int $id): array
    {
        $model = new Team();
        $teamDetail = $model->field(true)->with(['teamSku'])->findOrEmpty($id)->toArray();
        $goodsDetail = GoodsLogic::detail($teamDetail['goods_id']);

        foreach ($goodsDetail['sku'] as &$goodsSkuItem) {
            $goodsSkuItem['team_sku_id'] = 0;
            $goodsSkuItem['team_price'] = '';
            $goodsSkuItem['team_stock'] = '';
            $goodsSkuItem['sales_volume']   = 0;
            foreach ($teamDetail['teamSku'] as $teamSkuItem) {
                if ($goodsSkuItem['id'] === $teamSkuItem['sku_id']) {
                    $goodsSkuItem['team_sku_id']  = $teamSkuItem['id'];
                    $goodsSkuItem['team_price']   = $teamSkuItem['team_price'];
                    $goodsSkuItem['team_stock']   = $teamSkuItem['team_stock'];
                    $goodsSkuItem['sales_volume'] = $teamSkuItem['sales_volume'];
                }
            }
        }

        $teamDetail['spec']    = $goodsDetail['spec'];
        $teamDetail['teamSku'] = $goodsDetail['sku'];
        $teamDetail['activity_time'] =
            date('Y-m-d H:i:s', $teamDetail['start_time']) .' - ' .
            date('Y-m-d H:i:s', $teamDetail['end_time']);

        unset($teamDetail['start_time']);
        unset($teamDetail['end_time']);
        return $teamDetail;
    }

    /**
     * 新增拼团活动
     *
     * @author windy
     * @param array $post
     * @return bool
     */
    public static function add(array $post): bool
    {
        Db::startTrans();
        try {
            $datetime = explode(' - ', $post['activity_time']);

            if ($datetime[1] >= time() + (60 * 20)) {
                throw new Exception("结束时间最少保留20分钟");
            }

            $status = TeamEnum::TEAM_SYS_END;
            if ($datetime[0] <= time() && $datetime[1] >= time()) {
                $status = TeamEnum::TEAM_CONDUCT_IN;
            } elseif ($datetime[0] > time() && $datetime[1] > time()) {
                $status = TeamEnum::TEAM_NOT_STARTED;
            }

            $team = Team::create([
                'goods_id'        => $post['goods_id'],
                'name'            => $post['name'],
                'intro'           => $post['intro'],
                'image'           => $post['image'],
                'banner'          => $post['banner'],
                'min_buy'         => $post['min_buy'],
                'max_buy'         => $post['max_buy'],
                'min_team_price'  => min(array_column($post['sku'], 'team_price')),
                'max_team_price'  => max(array_column($post['sku'], 'team_price')),
                'people_num'      => $post['people_num'],
                'status'          => $status,
                'is_delete'       => 0,
                'is_automatic'    => $post['is_automatic'],
                'is_coupon'       => $post['is_coupon'],
                'is_distribution' => $post['is_distribution'],
                'effective_time'  => $post['effective_time'],
                'start_time'      => strtotime($datetime[0]),
                'end_time'        => strtotime($datetime[1]),
            ]);

            $teamSku = [];
            foreach ($post['sku'] as $item) {
                $teamSku[] = [
                    'team_id'     => $team['id'],
                    'goods_id'    => $post['goods_id'],
                    'sku_id'      => $item['sku_id'],
                    'team_price'  => $item['team_price'],
                    'team_stock'  => $item['team_stock'],
                    'sales_volume'  => 0,
                ];
            }

            (new TeamSku())->saveAll($teamSku);

            Db::commit();
            return true;
        } catch (Exception $e) {
            Db::rollback();
            static::$error = $e->getMessage();
            return false;
        }
    }

    /**
     * 编辑拼团活动
     *
     * @author windy
     * @param array $post
     * @return bool
     */
    public static function edit(array $post): bool
    {
        Db::startTrans();
        try {
            $datetime = explode(' - ', $post['activity_time']);
            if ($datetime[1] >= time() + (60 * 20)) {
                throw new Exception("结束时间最少保留20分钟");
            }

            Team::update([
                'goods_id'        => $post['goods_id'],
                'name'            => $post['name'],
                'intro'           => $post['intro'],
                'image'           => $post['image'],
                'banner'          => $post['banner'],
                'min_buy'         => $post['min_buy'],
                'max_buy'         => $post['max_buy'],
                'min_team_price'  => min(array_column($post['sku'], 'team_price')),
                'max_team_price'  => max(array_column($post['sku'], 'team_price')),
                'people_num'      => $post['people_num'],
                'is_delete'       => 0,
                'is_automatic'    => $post['is_automatic'],
                'is_coupon'       => $post['is_coupon'],
                'is_distribution' => $post['is_distribution'],
                'effective_time'  => $post['effective_time'],
                'start_time'      => strtotime($datetime[0]),
                'end_time'        => strtotime($datetime[1]),
            ], ['id'=>$post['id']]);

            $oldTeamSku  = [];
            $delTeamSku  = [];
            $addTeamSku  = [];
            $editTeamSku = [];
            foreach ($post['sku'] as $item) {
                if (!$item['team_sku_id']) {
                    $addTeamSku[] = [
                        'team_id'    => $post['id'],
                        'goods_id'   => $post['goods_id'],
                        'sku_id'     => $item['sku_id'],
                        'team_price' => $item['team_price'],
                        'team_stock' => $item['team_stock']
                    ];
                } else {
                    $oldTeamSku[] = intval($item['team_sku_id']);
                    $editTeamSku[] = [
                        'id'         => $item['team_sku_id'],
                        'team_id'    => $post['id'],
                        'goods_id'   => $post['goods_id'],
                        'sku_id'     => $item['sku_id'],
                        'team_price' => $item['team_price'],
                        'team_stock' => $item['team_stock']
                    ];
                }
            }

            if ($oldTeamSku) {
                $teamSkuIds = (new TeamSku)->where(['team_id' => $post['id']])->column('id');
                $delTeamSku = array_diff($teamSkuIds, $oldTeamSku);
            }

            if (!empty($addTeamSku)) {
                (new TeamSku())->saveAll($addTeamSku);
            }

            if (!empty($editTeamSku)) {
                (new TeamSku())->saveAll($editTeamSku);
            }

            if (!empty($delTeamSku)) {
                (new TeamSku())->whereIn('id', $delTeamSku)->delete();
            }

            Db::commit();
            return true;
        } catch (Exception $e) {
            Db::rollback();
            static::$error = $e->getMessage();
            return false;
        }
    }

    /**
     * 删除拼团活动
     *
     * @author windy
     * @param int $id
     * @return bool
     */
    public static function del(int $id): bool
    {
        try {
            $team = (new Team())->findOrEmpty($id)->toArray();

            if (!$team || $team['is_delete']) {
                throw new Exception('活动已不存在,请刷新列表');
            }

            if ($team['status'] == TeamEnum::TEAM_CONDUCT_IN) {
                throw new Exception('此拼团正在进行中,请结束后再操作');
            }

            Team::update([
                'status'      => TeamEnum::TEAM_SYS_END,
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
     * 结束拼团活动
     *
     * @author windy
     * @param int $id
     * @return bool
     */
    public static function end(int $id): bool
    {
        try {
            $sharing = (new Team())->findOrEmpty($id)->toArray();

            if (!$sharing || $sharing['is_delete']) {
                throw new Exception('活动已不存在,请刷新列表');
            }

            if ($sharing['status'] == TeamEnum::TEAM_NOT_STARTED) {
                throw new Exception('此拼团活动尚未开启,谈何结束呢？');
            }

            Team::update([
                'status'   => TeamEnum::TEAM_SYS_END,
                'update_time' => time()
            ], ['id'=>$id]);

            return true;
        } catch (Exception $e) {
            static::$error = $e->getMessage();
            return false;
        }
    }

    /**
     * 指定拼团活动的开团列表
     *
     * @author windy
     * @param array $get
     * @return array
     */
    public static function found(array $get): array
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
            ->where(['SF.team_id'=>$get['team_id']])
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
                    'name' => $item['snapshot']['name'],
                    'image' => $item['snapshot']['image']
                ];
            }
        }

        return ['count'=>$lists['total'], 'list'=>$lists['data']];
    }
}