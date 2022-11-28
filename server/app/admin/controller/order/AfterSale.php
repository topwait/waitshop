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

namespace app\admin\controller\order;


use app\admin\logic\order\AfterSaleLogic;
use app\common\basics\Backend;
use app\common\utils\JsonUtils;
use think\response\Json;
use think\response\View;

/**
 * 订单售后-控制器层
 * Class AfterSales
 * @package app\admin\controller\order
 */
class AfterSale extends Backend
{
    /**
     * 售后列表
     *
     * @author windy
     * @return View|Json
     */
    public function lists()
    {
        if (request()->isAjax()) {
            $lists = AfterSaleLogic::lists($this->getData());
            return JsonUtils::success($lists);
        }

        return view('order/after_sales/lists', [
            'statistics' => AfterSaleLogic::statistics()
        ]);
    }

    /**
     * 售后详细
     *
     * @author windy
     * @return View
     */
    public function detail(): View
    {
        return view('order/after_sales/detail', [
            'detail'  => AfterSaleLogic::detail($this->getData('id')),
            'logList' => AfterSaleLogic::log($this->getData('id'))
        ]);
    }

    /**
     * 同意申请
     *
     * @author windy
     * @return Json
     */
    public function agree(): Json
    {
        if (request()->isAjax()) {
            $result = AfterSaleLogic::agree($this->postData(), $this->adminId);
            if ($result === false) {
                return JsonUtils::error(AfterSaleLogic::getError());
            }
            return JsonUtils::ok('操作成功');
        }

        return JsonUtils::error('请求异常');
    }

    /**
     * 拒绝申请
     *
     * @author windy
     * @return Json
     */
    public function refuse(): Json
    {
        if (request()->isAjax()) {
            $result = AfterSaleLogic::refuse($this->postData(), $this->adminId);
            if ($result === false) {
                return JsonUtils::error(AfterSaleLogic::getError());
            }
            return JsonUtils::ok('操作成功');
        }

        return JsonUtils::error('请求异常');
    }

    /**
     * 确认收货
     *
     * @author windy
     * @return Json
     */
    public function takeGoods(): Json
    {
        if (request()->isAjax()) {
            $result = AfterSaleLogic::takeGoods($this->postData(), $this->adminId);
            if ($result === false) {
                return JsonUtils::error(AfterSaleLogic::getError());
            }
            return JsonUtils::ok('收货成功');
        }

        return JsonUtils::error('请求异常');
    }

    /**
     * 拒绝收货
     *
     * @author windy
     * @return Json
     */
    public function refuseGoods(): Json
    {
        if (request()->isAjax()) {
            $result = AfterSaleLogic::refuseGoods($this->postData(), $this->adminId);
            if ($result === false) {
                return JsonUtils::error(AfterSaleLogic::getError());
            }
            return JsonUtils::ok('已拒绝收货');
        }

        return JsonUtils::error('请求异常');
    }

    /**
     * 确认退款
     *
     * @author windy
     * @return Json
     * @throws @\Exception
     */
    public function confirmRefund(): Json
    {
        if (request()->isAjax()) {
            $result = AfterSaleLogic::confirmRefund($this->postData(), $this->adminId);
            if ($result === false) {
                return JsonUtils::error(AfterSaleLogic::getError());
            }
            return JsonUtils::ok('退款成功');
        }

        return JsonUtils::error('请求异常');
    }
}