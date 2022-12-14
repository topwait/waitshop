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

namespace app\admin\controller\finance;


use app\admin\logic\finance\LogsLogic;
use app\common\basics\Backend;
use app\common\utils\JsonUtils;
use think\response\Json;
use think\response\View;

/**
 * 财务记录日志-控制器层
 * Class Logs
 * @package app\admin\controller\finance
 */
class Logs extends Backend
{
    /**
     * 资金流水记录
     *
     * @author windy
     * @return View|Json
     */
    public function wallet()
    {
        if (request()->isAjax()) {
            $lists = LogsLogic::wallet($this->getData());
            return JsonUtils::success($lists);
        }

        return view('finance/logs/wallet');
    }

    /**
     * 积分流水记录
     *
     * @author windy
     * @return View|Json
     */
    public function integral()
    {
        if (request()->isAjax()) {
            $lists = LogsLogic::integral($this->getData());
            return JsonUtils::success($lists);
        }

        return view('finance/logs/integral');
    }

    /**
     * 成长值流水记录
     *
     * @author windy
     * @return View|Json
     */
    public function growth()
    {
        if (request()->isAjax()) {
            $lists = LogsLogic::growth($this->getData());
            return JsonUtils::success($lists);
        }

        return view('finance/logs/growth');
    }
}