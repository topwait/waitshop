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


use app\admin\logic\goods\GoodsLogic;
use app\admin\logic\addons\team\TeamActivityLogic;
use app\admin\validate\addons\TeamValidate;
use app\common\basics\Backend;
use app\common\utils\JsonUtils;
use think\response\Json;
use think\response\View;

/**
 * 拼团活动-控制器层
 * Class Activity
 * @package app\admin\controller\marketing\sharing
 */
class TeamActivity extends Backend
{
    /**
     * 拼团活动列表
     *
     * @author windy
     * @return View|Json
     */
    public function lists()
    {
        if (request()->isAjax()) {
            $lists = TeamActivityLogic::lists($this->getData());
            return JsonUtils::success($lists);
        }

        return view('addons/team/activity/lists');
    }

    /**
     * 新增拼团活动
     *
     * @author windy
     * @return View|Json
     */
    public function add()
    {
        if (request()->isAjax()) {
            (new TeamValidate())->goCheck('add');
            $result = TeamActivityLogic::add($this->postData());
            if ($result === false) {
                return JsonUtils::error(TeamActivityLogic::getError());
            }
            return JsonUtils::ok('新增成功');
        }

        return view('addons/team/activity/add', [
            'goodsDetail' => GoodsLogic::detail($this->getData('goods_id'))
        ]);
    }

    /**
     * 编辑拼团活动
     *
     * @author windy
     * @return View|Json
     */
    public function edit()
    {
        if (request()->isAjax()) {
            (new TeamValidate())->goCheck('edit');
            $result = TeamActivityLogic::edit($this->postData());
            if ($result === false) {
                return JsonUtils::error(TeamActivityLogic::getError());
            }
            return JsonUtils::ok('编辑成功');
        }

        return view('addons/team/activity/edit', [
            'detail' => TeamActivityLogic::detail($this->getData('id'))
        ]);
    }

    /**
     * 删除拼团活动
     *
     * @author windy
     * @return Json
     */
    public function del(): Json
    {
        if (request()->isAjax()) {
            (new TeamValidate())->goCheck('id');
            $result = TeamActivityLogic::del($this->postData('id'));
            if ($result === false) {
                return JsonUtils::error(TeamActivityLogic::getError());
            }
            return JsonUtils::ok('删除成功');
        }

        return JsonUtils::error('请求异常');
    }

    /**
     * 结束拼团活动
     *
     * @author windy
     * @return Json
     */
    public function end(): Json
    {
        if (request()->isAjax()) {
            (new TeamValidate())->goCheck('id');
            $result = TeamActivityLogic::end($this->postData('id'));
            if ($result === false) {
                return JsonUtils::error(TeamActivityLogic::getError());
            }
            return JsonUtils::ok('结束成功');
        }

        return JsonUtils::error('请求异常');
    }

    /**
     * 指定活动的开团列表
     *
     * @author windy
     * @return View|Json
     */
    public function found()
    {
        if (request()->isAjax()) {
            $lists = TeamActivityLogic::found($this->getData());
            return JsonUtils::success($lists);
        }

        return view('addons/team/activity/found', ['id'=>$this->getData('id')]);
    }
}