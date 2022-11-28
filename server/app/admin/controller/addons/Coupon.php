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

namespace app\admin\controller\addons;


use app\admin\logic\addons\CouponLogic;
use app\common\basics\Backend;
use app\common\utils\JsonUtils;
use think\App;
use think\response\Json;
use think\response\View;

/**
 * 优惠券-控制器层
 * Class Coupon
 * @package app\admin\controller\addons
 */
class Coupon extends Backend
{
    /**
     * 优惠券列表
     *
     * @author windy
     * @return View|Json
     */
    public function lists()
    {
        if ($this->request->isAjax()) {
            $lists = CouponLogic::lists($this->getData());
            return JsonUtils::success($lists);
        }

        return view('addons/coupon/lists', [
            'statistics' => CouponLogic::statistics()
        ]);
    }

    /**
     * 数据统计
     *
     * @author windy
     * @return Json
     */
    public function statistics(): Json
    {
        if ($this->request->isAjax()) {
            $result = CouponLogic::statistics();
            return JsonUtils::success($result);
        }

        return JsonUtils::error('请求异常');
    }

    /**
     * 新增优惠券
     *
     * @author windy
     * @return View|Json
     */
    public function add()
    {
        if ($this->request->isAjax()) {
            $result = CouponLogic::add($this->postData());
            if ($result === false) {
                $error = CouponLogic::getError() ?: '新增失败';
                return JsonUtils::error($error);
            }
            return JsonUtils::ok('新增成功');
        }

        return view('addons/coupon/add');
    }

    /**
     * 编辑优惠券
     *
     * @author windy
     * @return View|Json
     */
    public function edit()
    {
        if ($this->request->isAjax()) {
            $result = CouponLogic::edit($this->postData());
            if ($result === false) {
                $error = CouponLogic::getError() ?: '编辑失败';
                return JsonUtils::error($error);
            }
            return JsonUtils::ok('编辑成功');
        }

        return view('addons/coupon/edit', [
            'detail' => CouponLogic::detail($this->getData('id'))
        ]);
    }

    /**
     * 删除优惠券
     *
     * @author windy
     * @return Json
     */
    public function del(): Json
    {
        if ($this->request->isAjax()) {
            $result = CouponLogic::del($this->postData('id'));
            if ($result === false) {
                $error = CouponLogic::getError() ?: '删除失败';
                return JsonUtils::error($error);
            }
            return JsonUtils::ok('删除成功');
        }

        return JsonUtils::error('请求异常');
    }

    /**
     * 发布优惠券
     *
     * @author windy
     * @return Json
     */
    public function publish(): Json
    {
        if ($this->request->isAjax()) {
            $result = CouponLogic::publish($this->postData('id'));
            if ($result === false) {
                $error = CouponLogic::getError() ?: '发布失败';
                return JsonUtils::error($error);
            }
            return JsonUtils::ok('发布成功');
        }

        return JsonUtils::error('请求异常');
    }

    /**
     * 关闭优惠券
     *
     * @author windy
     * @return Json
     */
    public function close(): Json
    {
        if ($this->request->isAjax()) {
            $result = CouponLogic::close($this->postData('id'));
            if ($result === false) {
                $error = CouponLogic::getError() ?: '关闭失败';
                return JsonUtils::error($error);
            }
            return JsonUtils::ok('关闭成功');
        }

        return JsonUtils::error('请求异常');
    }
}