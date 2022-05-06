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


use app\admin\logic\addons\distribution\DistributionMemberLogic;
use app\admin\validate\addons\DistributionValidate;
use app\common\basics\Backend;
use app\common\utils\JsonUtils;
use think\response\Json;
use think\response\View;

/**
 * 分销会员-控制器层
 * Class Member
 * @package app\admin\controller\marketing\distribution
 */
class DistributionMember extends Backend
{
    /**
     * 分销会员列表
     *
     * @author windy
     * @return View|Json
     */
    public function lists()
    {
        if (request()->isAjax()) {
            $get = $this->request->get();
            $lists = DistributionMemberLogic::lists($get);
            if ($lists === false) {
                return JsonUtils::error(DistributionMemberLogic::getError());
            }
            return JsonUtils::success($lists);
        }

        return view('addons/distribution/member/lists');
    }

    /**
     * 推广的下级会员列表
     *
     * @author windy
     * @return View|Json
     */
    public function fans()
    {
        if (request()->isAjax()) {
            $lists = DistributionMemberLogic::fans($this->getData());
            if ($lists === false) {
                return JsonUtils::error(DistributionMemberLogic::getError());
            }
            return JsonUtils::success($lists);
        }

        return view('addons/distribution/member/fans', [
            'uid' => $this->request->get('uid')
        ]);
    }

    /**
     * 分销会员详细
     *
     * @author windy
     * @return View
     */
    public function detail(): View
    {
        (new DistributionValidate())->goCheck('uid');
        return view('addons/distribution/member/detail', [
            'detail' => DistributionMemberLogic::detail($this->getData('uid'))
        ]);
    }

    /**
     * 佣金明细
     *
     * @author windy
     * @return View|Json
     */
    public function commission()
    {
        if (request()->isAjax()) {
            (new DistributionValidate())->goCheck('uid');
            $lists = DistributionMemberLogic::commission($this->getData());
            return JsonUtils::success($lists);
        }

        return view('addons/distribution/member/commission', ['uid'=>$this->getData('uid')]);
    }

    /**
     * 冻结会员分销资格
     *
     * @author windy
     * @return Json
     */
    public function freeze(): Json
    {
        if (request()->isAjax()) {
            (new DistributionValidate())->goCheck('uid');
            $result = DistributionMemberLogic::freeze($this->postData('uid'));
            if ($result === false) {
                return JsonUtils::error(DistributionMemberLogic::getError());
            }
            return JsonUtils::ok('冻结成功');
        }

        return JsonUtils::error('请求异常');
    }

    /**
     * 解冻会员分销资格
     *
     * @author windy
     * @return Json
     */
    public function thaw(): Json
    {
        if (request()->isAjax()) {
            (new DistributionValidate())->goCheck('uid');
            $result = DistributionMemberLogic::thaw($this->postData('uid'));
            if ($result === false) {
                return JsonUtils::error(DistributionMemberLogic::getError());
            }
            return JsonUtils::ok('解冻成功');
        }

        return JsonUtils::error('请求异常');
    }

    /**
     * 新增分销会员
     *
     * @author windy
     * @return Json
     */
    public function addMember(): Json
    {
        if (request()->isAjax()) {
            (new DistributionValidate())->goCheck('uid');
            $result = DistributionMemberLogic::addMember($this->postData('uid'));
            if ($result === false) {
                return JsonUtils::error(DistributionMemberLogic::getError());
            }
            return JsonUtils::ok('新增成功');
        }

        return JsonUtils::error('请求异常');
    }

    /**
     * 更新分销会员上级
     *
     * @author windy
     * @return Json
     */
    public function updateLeader(): Json
    {
        if (request()->isAjax()) {
            (new DistributionValidate())->goCheck('leader');
            $uid      = $this->postData('uid');
            $leaderSn = $this->postData('leader_sn');
            $result = DistributionMemberLogic::updateLeader($uid, $leaderSn);
            if ($result === false) {
                return JsonUtils::error(DistributionMemberLogic::getError());
            }
            return JsonUtils::ok('更新上级成功');
        }

        return JsonUtils::error('请求异常');
    }
}