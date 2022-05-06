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

namespace app\admin\controller\finance;


use app\admin\logic\finance\WithdrawLogic;
use app\common\basics\Backend;
use app\common\utils\JsonUtils;
use think\response\Json;
use think\response\View;

/**
 * 佣金提现记录-控制器层
 * Class Withdraw
 * @package app\admin\controller\finance
 */
class Withdraw extends Backend
{
    /**
     * 提现列表
     *
     * @author windy
     * @return View|Json
     */
    public function lists()
    {
        if (request()->isAjax()) {
            $lists = WithdrawLogic::lists($this->getData());
            return JsonUtils::success($lists);
        }

        return view('finance/withdraw/lists', ['detail'=>WithdrawLogic::statistics()]);
    }

    /**
     * 提现详细
     *
     * @author windy
     * @return View|Json
     */
    public function detail(): View
    {
        $detail = WithdrawLogic::detail($this->getData('id'));
        return view('finance/withdraw/detail', ['detail'=>$detail]);
    }

    /**
     * 提现统计
     *
     * @author windy
     * @return Json
     */
    public function statistics(): Json
    {
        if (request()->isAjax()) {
            $detail = WithdrawLogic::statistics();
            return JsonUtils::success($detail);
        }
        return JsonUtils::error('请求异常');
    }

    /**
     * 提现审核
     *
     * @author windy
     * @return View|Json
     */
    public function audit()
    {
        if (request()->isAjax()) {
            $result = WithdrawLogic::audit($this->postData(), $this->adminId);
            if ($result === false) {
                return JsonUtils::error(WithdrawLogic::getError());
            }
            return JsonUtils::ok('操作成功');
        }

        return view('finance/withdraw/audit');
    }

    /**
     * 提现转账
     *
     * @author windy
     * @return View|Json
     */
    public function transfer()
    {
        if (request()->isAjax()) {
            $result = WithdrawLogic::transfer($this->postData(), $this->adminId);
            if ($result === false) {
                return JsonUtils::error(WithdrawLogic::getError());
            }
            return JsonUtils::ok('操作成功');
        }

        return view('finance/withdraw/transfer');
    }
}