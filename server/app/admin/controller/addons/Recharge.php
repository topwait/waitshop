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


use app\admin\logic\addons\RechargeLogic;
use app\common\basics\Backend;
use app\common\utils\ConfigUtils;
use app\common\utils\JsonUtils;
use think\response\Json;
use think\response\View;

/**
 * 充值-控制器层
 * Class Recharge
 * @package app\admin\controller\addons
 */
class Recharge extends Backend
{
    /**
     * 配置页面
     *
     * @author windy
     * @return View
     */
    public function configure(): View
    {
        return view('addons/recharge/setting', [
            'detail' => ConfigUtils::get('recharge')
        ]);
    }

    /**
     * 修改配置
     *
     * @author windy
     * @return Json
     */
    public function setting(): Json
    {
        if (request()->isAjax()) {
            $result = RechargeLogic::setting($this->postData());
            if ($result === false) {
                return JsonUtils::error(RechargeLogic::getError());
            }
            return JsonUtils::ok("设置成功");
        }

        return JsonUtils::error();
    }

    /**
     * 充值订单
     *
     * @author windy
     * @return View|Json
     */
    public function order()
    {
        if (request()->isAjax()) {
            $lists = RechargeLogic::order($this->getData());
            return JsonUtils::success($lists);
        }

        return view('addons/recharge/order');
    }

    /**
     * 充值套餐列表
     *
     * @author windy
     * @return Json|View
     */
    public function packageList()
    {
        if (request()->isAjax()) {
            $lists = RechargeLogic::package('list', $this->getData());
            return JsonUtils::success($lists);
        }
        return view('addons/recharge/package_list');
    }

    /**
     * 充值套餐新增
     *
     * @author windy
     * @return Json|View
     */
    public function packageAdd()
    {
        if (request()->isAjax()) {
            $result = RechargeLogic::package('add', $this->postData());
            if ($result === false) {
                return JsonUtils::error(RechargeLogic::getError());
            }
            return JsonUtils::ok('新增成功');
        }
        return view('addons/recharge/package_add');
    }

    /**
     * 充值套餐编辑
     *
     * @author windy
     * @return Json|View
     */
    public function packageEdit()
    {
        if (request()->isAjax()) {
            $result = RechargeLogic::package('edit', $this->postData());
            if ($result === false) {
                return JsonUtils::error(RechargeLogic::getError());
            }
            return JsonUtils::ok('更新成功');
        }

        return view('addons/recharge/package_edit', [
            'detail' => RechargeLogic::package('detail', $this->getData())
        ]);
    }

    /**
     * 充值套餐删除
     *
     * @author windy
     * @return Json|View
     */
    public function packageDel()
    {
        if (request()->isAjax()) {
            $result = RechargeLogic::package('del', $this->postData());
            if ($result === false) {
                return JsonUtils::error(RechargeLogic::getError());
            }
            return JsonUtils::ok('删除成功');
        }

        return JsonUtils::error('请求异常');
    }
}