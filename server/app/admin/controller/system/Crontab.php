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

namespace app\admin\controller\system;


use app\admin\logic\system\CrontabLogic;
use app\common\basics\Backend;
use app\common\utils\JsonUtils;
use think\response\Json;
use think\response\View;

/**
 * 定时任务配置-控制器层
 * Class Crontab
 * @package app\admin\controller\set
 */
class Crontab extends Backend
{
    /**
     * 定时任务列表
     *
     * @author windy
     * @return View|Json
     */
    public function lists()
    {
        if (request()->isAjax()) {
            $lists = CrontabLogic::lists($this->getData());
            return JsonUtils::success($lists);
        }

        return view('system/crontab/lists');
    }

    /**
     * 新增计划任务
     *
     * @author windy
     * @return View|Json
     */
    public function add()
    {
        if (request()->isAjax()) {
            $result = CrontabLogic::add($this->postData());
            if ($result === false) {
                return JsonUtils::error(CrontabLogic::getError());
            }
            return JsonUtils::ok('新增成功');
        }

        return view('system/crontab/add');
    }

    /**
     * 编辑计划任务
     *
     * @author windy
     * @return View|Json
     */
    public function edit()
    {
        if (request()->isAjax()) {
            $result = CrontabLogic::edit($this->postData());
            if ($result === false) {
                return JsonUtils::error(CrontabLogic::getError());
            }
            return JsonUtils::ok('编辑成功');
        }

        return view('system/crontab/edit', [
            'detail' => CrontabLogic::detail($this->getData('id'))
        ]);
    }

    /**
     * 销毁任务
     *
     * @author windy
     * @return Json
     */
    public function del(): Json
    {
        if (request()->isAjax()) {
            $result = CrontabLogic::destroy($this->postData('id'));
            if ($result === false) {
                return JsonUtils::error(CrontabLogic::getError());
            }
            return JsonUtils::ok('删除成功');
        }

        return JsonUtils::error('请求异常');
    }

    /**
     * 停止执行
     *
     * @author windy
     * @return Json
     */
    public function stop(): Json
    {
        if (request()->isAjax()) {
            $result = CrontabLogic::stop($this->postData('id'));
            if ($result === false) {
                return JsonUtils::error(CrontabLogic::getError());
            }
            return JsonUtils::ok('停止成功');
        }

        return JsonUtils::error('请求异常');
    }

    /**
     * 开启执行
     *
     * @author windy
     * @return Json
     */
    public function run(): Json
    {
        if (request()->isAjax()) {
            $result = CrontabLogic::run($this->postData('id'));
            if ($result === false) {
                return JsonUtils::error(CrontabLogic::getError());
            }
            return JsonUtils::ok('启动成功');
        }

        return JsonUtils::error('请求异常');
    }
}