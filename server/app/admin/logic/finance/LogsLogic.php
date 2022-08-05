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

namespace app\admin\logic\finance;


use app\common\basics\Logic;
use app\common\enum\LogGrowthEnum;
use app\common\enum\LogIntegralEnum;
use app\common\enum\LogWalletEnum;
use app\common\model\addons\DistributionOrder;
use app\common\model\log\LogGrowth;
use app\common\model\log\LogIntegral;
use app\common\model\log\LogWallet;
use app\common\utils\UrlUtils;

/**
 * 财务记录日志-控制器层
 * Class Logs
 * @package app\admin\controller\finance
 */
class LogsLogic extends Logic
{
    /**
     * 佣金流水记录
     *
     * @author windy
     * @param array $get
     * @return array
     */
    public static function commission(array $get): array
    {
        self::setSearch([
            '='        => ['status@DO.status'],
            'datetime' => ['datetime@DO.create_time'],
            'keyword'  => [
                'orderSn'  => ['%like%', 'O.order_sn'],
                'userSn'   => ['%like%', 'U.sn'],
                'nickname' => ['%like%', 'U.nickname'],
                'mobile'   => ['=', 'U.mobile']
            ]
        ]);

        $lists = (new DistributionOrder())
            ->field('DO.*,U.sn,U.avatar,U.nickname,U.mobile,O.order_sn')
            ->alias('DO')
            ->join('user U', 'U.id = DO.user_id')
            ->join('order O', 'O.id = DO.order_id')
            ->where(self::$searchWhere)
            ->order('DO.id desc')
            ->paginate([
                'page'      => $get['page'] ?? 1,
                'list_rows' => $get['limit'] ?? 20,
                'var_page'  => 'page'
            ])->toArray();

        foreach ($lists['data'] as &$item) {
            $item['avatar'] = UrlUtils::getAbsoluteUrl($item['avatar']);
            unset($item['user_id']);
            unset($item['order_id']);
            unset($item['order_goods_id']);
            unset($item['goods_id']);
            unset($item['sku_id']);
            unset($item['update_time']);
        }

        return ['count'=>$lists['total'], 'list'=>$lists['data']];
    }

    /**
     * 资金流水记录
     *
     * @author windy
     * @param array $get
     * @return array
     */
    public static function wallet(array $get): array
    {
        self::setSearch([
            '='        => ['sourceType@LW.source_type', 'changeType@LW.change_type'],
            'datetime' => ['datetime@LW.create_time'],
            'keyword'  => [
                'userSn'   => ['%like%', 'U.sn'],
                'nickname' => ['%like%', 'U.nickname'],
                'mobile'   => ['=', 'U.mobile']
            ]
        ]);

        $lists = (new LogWallet())
            ->field('LW.*,U.sn,U.avatar,U.nickname,U.mobile')
            ->alias('LW')
            ->join('user U', 'U.id = LW.user_id')
            ->order('LW.id desc')
            ->where(self::$searchWhere)
            ->paginate([
                'page'      => $get['page'] ?? 1,
                'list_rows' => $get['limit'] ?? 20,
                'var_page'  => 'page'
            ])->toArray();

        foreach ($lists['data'] as &$item) {
            $item['avatar'] = UrlUtils::getAbsoluteUrl($item['avatar']);
            $item['source_type'] = LogWalletEnum::getLogDesc($item['source_type']);
            $item['source_sn'] = $item['source_sn'] ? $item['source_sn'] : '无';
            unset($item['user_id']);
            unset($item['admin_id']);
            unset($item['source_id']);
        }

        return ['count'=>$lists['total'], 'list'=>$lists['data']];
    }

    /**
     * 积分流水记录
     *
     * @author windy
     * @param array $get
     * @return array
     */
    public static function integral(array $get): array
    {
        self::setSearch([
            '='        => ['sourceType@LI.source_type', 'changeType@LI.change_type'],
            'datetime' => ['datetime@LI.create_time'],
            'keyword'  => [
                'userSn'   => ['%like%', 'U.sn'],
                'nickname' => ['%like%', 'U.nickname'],
                'mobile'   => ['=', 'U.mobile']
            ]
        ]);

        $lists = (new LogIntegral())
            ->field('LI.*,U.sn,U.avatar,U.nickname,U.mobile')
            ->alias('LI')
            ->join('user U', 'U.id = LI.user_id')
            ->order('LI.id desc')
            ->where(self::$searchWhere)
            ->paginate([
                'page'      => $get['page'] ?? 1,
                'list_rows' => $get['limit'] ?? 20,
                'var_page'  => 'page'
            ])->toArray();

        foreach ($lists['data'] as &$item) {
            $item['avatar'] = UrlUtils::getAbsoluteUrl($item['avatar']);
            $item['source_type'] = LogIntegralEnum::getLogDesc($item['source_type']);
            $item['source_sn'] = $item['source_sn'] ? $item['source_sn'] : '无';
            unset($item['user_id']);
            unset($item['admin_id']);
            unset($item['source_id']);
        }

        return ['count'=>$lists['total'], 'list'=>$lists['data']];
    }

    /**
     * 成长值流水记录
     *
     * @author windy
     * @param array $get
     * @return array
     */
    public static function growth(array $get): array
    {
        self::setSearch([
            '='        => ['sourceType@LG.source_type', 'changeType@LG.change_type'],
            'datetime' => ['datetime@LG.create_time'],
            'keyword'  => [
                'userSn'   => ['%like%', 'U.sn'],
                'nickname' => ['%like%', 'U.nickname'],
                'mobile'   => ['=', 'U.mobile']
            ]
        ]);

        $lists = (new LogGrowth())
            ->field('LG.*,U.sn,U.avatar,U.nickname,U.mobile')
            ->alias('LG')
            ->join('user U', 'U.id = LG.user_id')
            ->order('LG.id desc')
            ->where(self::$searchWhere)
            ->paginate([
                'page'      => $get['page'] ?? 1,
                'list_rows' => $get['limit'] ?? 20,
                'var_page'  => 'page'
            ])->toArray();

        foreach ($lists['data'] as &$item) {
            $item['avatar'] = UrlUtils::getAbsoluteUrl($item['avatar']);
            $item['source_type'] = LogGrowthEnum::getLogDesc($item['source_type']);
            $item['source_sn'] = $item['source_sn'] ? $item['source_sn'] : '无';
            unset($item['user_id']);
            unset($item['admin_id']);
            unset($item['source_id']);
        }

        return ['count'=>$lists['total'], 'list'=>$lists['data']];
    }
}