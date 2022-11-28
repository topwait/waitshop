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


use app\admin\logic\addons\LotteryLogic;
use app\common\basics\Backend;
use app\common\enum\LotteryEnum;
use app\common\utils\ConfigUtils;
use app\common\utils\JsonUtils;
use think\response\Json;
use think\response\View;

class Lottery extends Backend
{
    /**
     * 奖品列表
     *
     * @author windy
     * @return View|Json
     */
    public function lists()
    {
        if (request()->isAjax()) {
            $lists = LotteryLogic::lists($this->getData());
            return JsonUtils::success($lists);
        }

        return view('addons/lottery/lists');
    }

    /**
     * 奖品编辑
     *
     * @author windy
     * @return View|Json
     */
    public function edit()
    {
        if (request()->isAjax()) {
            $result = LotteryLogic::edit($this->postData());
            if ($result == false) {
                return JsonUtils::error(LotteryLogic::getError());
            }
            return JsonUtils::ok("编辑成功");
        }

        return view('addons/lottery/edit', [
            'types'  => LotteryEnum::getEnumName(),
            'detail' => LotteryLogic::detail($this->getData('id'))
        ]);
    }

    /**
     * 抽奖记录
     *
     * @author windy
     * @return View|Json
     */
    public function record()
    {
        if (request()->isAjax()) {
            $lists = LotteryLogic::record($this->getData());
            return JsonUtils::success($lists);
        }

        return view('addons/lottery/record');
    }

    /**
     * 抽奖配置页面
     */
    public function configure()
    {
        $detail = ConfigUtils::get('lottery', [
            'is_open' => 0,
            'limit'   => 0,
            'rules'   => ''
        ]);

        return view('addons/lottery/setting', ['detail'=>$detail]);
    }

    /**
     * 抽奖配置修改
     *
     * @author windy
     * @return View|Json
     */
    public function setting()
    {
        if (request()->isAjax()) {
            $result = LotteryLogic::setting($this->postData());
            if ($result == false) {
                return JsonUtils::error(LotteryLogic::getError());
            }
            return JsonUtils::ok('配置成功');
        }

        return JsonUtils::error();
    }

}