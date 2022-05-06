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

namespace app\admin\controller\addons\distribution;


use app\admin\logic\addons\distribution\DistributionApplyLogic;
use app\admin\validate\addons\DistributionValidate;
use app\common\basics\Backend;
use app\common\utils\JsonUtils;
use think\response\Json;
use think\response\View;

/**
 * 分销申请-控制器层
 * Class Apply
 * @package app\admin\controller\marketing\distribution
 */
class DistributionApply extends Backend
{
    /**
     * 分销申请列表
     *
     * @author windy
     * @return View|Json
     */
    public function lists()
    {
        if (request()->isAjax()) {
            $lists = DistributionApplyLogic::lists($this->getData());
            if ($lists === false) {
                $error = DistributionApplyLogic::getError() ?: '获取失败';
                return JsonUtils::error($error);
            }
            return JsonUtils::success($lists);
        }

        return view('addons/distribution/apply/lists', [
            'statistics' => DistributionApplyLogic::statistics()
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
        if (request()->isAjax()) {
            $detail = DistributionApplyLogic::statistics();
            return JsonUtils::success($detail);
        }

        return JsonUtils::error();
    }

    /**
     * 分销申请审核
     *
     * @author windy
     * @return View|Json
     */
    public function audit()
    {
        if (request()->isAjax()) {
            (new DistributionValidate())->goCheck('audit');
            $lists = DistributionApplyLogic::audit($this->postData());
            if ($lists === false) {
                return JsonUtils::error(DistributionApplyLogic::getError());
            }
            return JsonUtils::success();
        }

        return view('addons/distribution/apply/audit');
    }
}