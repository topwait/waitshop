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

namespace app\admin\logic;


use app\common\basics\Logic;
use app\common\enum\ClientEnum;
use app\common\enum\OrderEnum;
use app\common\model\goods\Goods;
use app\common\model\goods\GoodsComment;
use app\common\model\log\LogVisit;
use app\common\model\order\Order;
use app\common\model\user\User;
use app\common\utils\TimeUtils;

/**
 * 首页管理-逻辑层
 * Class IndexLogic
 * @package app\admin\logic
 */
class IndexLogic extends Logic
{
    /**
     * 控制台数据
     *
     * @author windy
     * @return array
     * @throws @\think\db\exception\DataNotFoundException
     * @throws @\think\db\exception\DbException
     * @throws @\think\db\exception\ModelNotFoundException
     */
    public static function console(): array
    {
        $orderModel = new Order();
        $userModel = new User();
        $goodsModel = new Goods();
        $commentModel = new GoodsComment();

        // 所有已付订单
        $detail['totalPayOrderCount'] = $orderModel->where([
            ['pay_status', '=', OrderEnum::OK_PAID_STATUS]
        ])->count();

        // 今日已付款订单数
        $detail['todayPayOrderCount'] = $orderModel->where([
            ['pay_status', '=', OrderEnum::OK_PAID_STATUS],
            ['pay_time', '>=', TimeUtils::today()[0]],
            ['pay_time', '<=', TimeUtils::today()[1]]
        ])->count();

        // 待发货订单数
        $detail['waitDeliveryOrderCount'] = $orderModel->where([
            ['order_status', '=', OrderEnum::STATUS_WAIT_DELIVERY],
            ['pay_status', '=', OrderEnum::OK_PAID_STATUS]
        ])->count();

        // 待核销订单数
        $detail['waitWriteOffOrderCount'] = $orderModel->where([
            ['order_status', '=', OrderEnum::STATUS_WAIT_DELIVERY],
            ['pay_status', '=', OrderEnum::OK_PAID_STATUS],
            ['delivery_type', '=', OrderEnum::DELIVER_TYPE_STORE],
            ['pick_up_status', '=', 0],
        ])->count();

        // 待退款订单数
        $detail['refundOrderCount'] = $orderModel->where([
            ['refund_status', '=', OrderEnum::REFUND_STATUS_APPLY],
            ['pay_status', '=', OrderEnum::OK_PAID_STATUS]
        ])->count();

        // 累计用户数
        $detail['totalUserCount'] = $userModel->count();

        // 本月新增用户
        $detail['monthUserCount'] = $userModel->where([
            ['create_time', '>=', TimeUtils::month()[0]],
            ['create_time', '<=', TimeUtils::month()[1]]
        ])->count();

        // 出售中商品数
        $detail['goodsCount'] = $goodsModel->where([
            ['is_show', '=', 1],
            ['is_delete', '=', 0]
        ])->count();

        // 今日收益金额
        $detail['todayProfit'] = $orderModel->where([
            ['pay_status', '=', OrderEnum::OK_PAID_STATUS],
            ['pay_time', '>=', TimeUtils::today()[0]],
            ['pay_time', '<=', TimeUtils::today()[1]]
        ])->sum('paid_amount');

        // 待回复评论数
        $detail['stayReplyCommentCount'] = $commentModel->where([
            ['is_reply', '=', 0],
            ['is_delete', '=', 0]
        ])->count('id');

        // 热销商品排名
        $detail['goodsRanking'] = $goodsModel
            ->field('id,name,image,sales_volume')
            ->where('is_delete', 0)
            ->order('sales_volume desc, id desc')
            ->limit(7)
            ->select()
            ->toArray();

        // 系统版本
        $detail['version'] = config('project.version');

        return $detail;
    }

    /**
     * 图表数据
     *
     * @author windy
     * @return array
     */
    public static function chart(): array
    {
        // 今日访问量
        $visit = [];
        $todayStart     = TimeUtils::today()[0];
        $yesterdayStart = TimeUtils::yesterday()[0];
        $logVisitModel  = new LogVisit();
        for ($i=0; $i<12; $i++) {
            $visit['today'][] = $logVisitModel
                ->where('create_time', '>=', $todayStart)
                ->where('create_time', '<', $todayStart + 7200)
                ->count();

            $visit['yesterday'][] = $logVisitModel
                ->where('create_time', '>=', $yesterdayStart)
                ->where('create_time', '<', $yesterdayStart + 7200)
                ->count();

            $todayStart     += 7200;
            $yesterdayStart += 7200;
        }

        // 今日客户端占比
        $ratio['h5']  = $logVisitModel->whereDay('create_time')->where(['client'=>ClientEnum::H5])->count();
        $ratio['oa']  = $logVisitModel->whereDay('create_time')->where(['client'=>ClientEnum::OA])->count();
        $ratio['mnp'] = $logVisitModel->whereDay('create_time')->where(['client'=>ClientEnum::MNP])->count();
        $ratio['app'] = $logVisitModel->whereDay('create_time')->whereIn('client', [ClientEnum::ANDROID, ClientEnum::IOS])->count();

        // 近7天新增用户
        $user['val']  = [];
        $user['date'] = [];
        $userModel = new User();
        foreach (TimeUtils::nearToDate(7) as $date) {
            $user['date'][] = date('m-d', strtotime($date));
            $user['val'][] = $userModel
                ->where('create_time', '>=', strtotime($date.' 00:00:00'))
                ->where('create_time', '<=', strtotime($date.' 23:59:59'))
                ->count();
        }

        // 返回结果
        return [
            'visit' => $visit,
            'ratio' => $ratio,
            'user'  => $user
        ];
    }

}