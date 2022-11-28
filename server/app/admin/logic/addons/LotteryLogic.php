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
use app\common\enum\LotteryEnum;
use app\common\model\addons\LotteryPrize;
use app\common\model\addons\LotteryRecord;
use app\common\utils\ConfigUtils;
use app\common\utils\UrlUtils;
use Exception;

/**
 * 抽奖活动-逻辑层
 * Class LotteryLogic
 * @package app\admin\logic\addons
 */
class LotteryLogic extends Logic
{
    /**
     * 奖品列表
     *
     * @author windy
     * @param array $get
     * @return array
     * @throws @\think\db\exception\DbException
     */
    public static function lists(array $get) {
        $lists = (new LotteryPrize)
            ->order('sort desc, id desc')
            ->paginate([
                'page'      => $get['page'],
                'list_rows' => $get['limit'],
                'var_page'  => 'page',
            ])->toArray();

        foreach ($lists['data'] as &$item) {
            $item['type'] = LotteryEnum::getEnumName($item['type']);
            $item['probability'] = '%'.$item['probability'];
        }

        return ['count'=>$lists['total'], 'list'=>$lists['data']];
    }

    /**
     * 奖品详情
     *
     * @author windy
     * @param int $id
     * @return array
     */
    public static function detail(int $id): array
    {
        return (new LotteryPrize)->findOrEmpty($id)->toArray();
    }

    /**
     * 编辑奖品信息
     *
     * @author windy
     * @param array $post
     * @return bool
     */
    public static function edit(array $post): bool
    {
        try {
            LotteryPrize::update([
                'type'   => $post['type'],
                'name'   => $post['name'],
                'image'  => $post['image'],
                'number' => $post['number'],
                'reward' => $post['reward'],
                'tips'   => $post['tips'],
                'sort'   => $post['sort'],
                'probability' => $post['probability'],
                'update_time' => time()
            ], ['id'=>intval($post['id'])]);

            return true;
        } catch (Exception $e) {
            static::$error = $e->getMessage();
            return false;
        }
    }

    /**
     * 抽奖记录
     *
     * @author windy
     * @param array $get
     * @return array
     * @throws @\think\db\exception\DbException
     */
    public static function record(array $get): array
    {
        self::setSearch([
            'datetime' => ['datetime@create_time'],
            'keyword'  => [
                'userSn'   => ['%like%', 'U.sn'],
                'nickname' => ['%like%', 'U.nickname'],
                'mobile'   => ['=', 'U.mobile'],
            ]
        ]);

        $lists = (new LotteryRecord())
            ->field('LR.id,LR.prize_type,LR.prize_name,
                LR.prize_image,LR.prize_value,LR.prize_tips,
                LR.create_time,U.sn,U.avatar,U.nickname,U.mobile')
            ->alias('LR')
            ->where(self::$searchWhere)
            ->join('user U', 'U.id=LR.user_id')
            ->order('LR.id desc')
            ->paginate([
                'page'      => $get['page'],
                'list_rows' => $get['limit'],
                'var_page'  => 'page',
            ])->toArray();

        foreach ($lists['data'] as &$item) {
            $item['avatar']      = UrlUtils::getAbsoluteUrl($item['avatar']);
            $item['prize_image'] = UrlUtils::getAbsoluteUrl($item['prize_image']);
        }

        return ['count'=>$lists['total'], 'list'=>$lists['data']];
    }

    /**
     * 抽奖设置
     *
     * @author windy
     * @param array $post
     * @return bool
     */
    public static function setting(array $post): bool
    {
        try {
            ConfigUtils::set('lottery', [
                'is_open' => $post['is_open'],
                'limit'   => $post['limit'],
                'rules'   => $post['rules']
            ]);

            return true;
        } catch (Exception $e) {
            static::$error = $e->getMessage();
            return false;
        }
    }

}