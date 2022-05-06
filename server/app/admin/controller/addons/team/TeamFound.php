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

namespace app\admin\controller\addons\team;


use app\admin\logic\addons\team\TeamFoundLogic;
use app\common\basics\Backend;
use app\common\utils\JsonUtils;
use think\response\Json;
use think\response\View;

/**
 * 拼团发起-控制器层
 * Class Task
 * @package app\admin\controller\marketing\sharing
 */
class TeamFound extends Backend
{
    /**
     * 开团列表
     *
     * @author windy
     * @return View|Json
     */
    public function lists()
    {
        if (request()->isAjax()) {
            $lists = TeamFoundLogic::lists($this->getData());
            return JsonUtils::success($lists);
        }

        return view('addons/team/found/lists');
    }

    /**
     * 参团记录
     *
     * @author windy
     * @return View|Json
     */
    public function join(): Json
    {
        if (request()->isAjax()) {
            $lists = TeamFoundLogic::join($this->getData());
            return JsonUtils::success($lists);
        }

        return JsonUtils::error('请求异常');
    }

    /**
     * 团的详细
     *
     * @author windy
     * @return View
     */
    public function detail(): View
    {
        return view('addons/team/found/detail', [
            'detail' => TeamFoundLogic::detail($this->getData('id'))
        ]);
    }
}