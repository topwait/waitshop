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

namespace app\admin\controller\store;


use app\admin\logic\store\ClerkLogic;
use app\admin\logic\store\StoreLogic;
use app\common\basics\Backend;
use app\common\utils\JsonUtils;
use think\response\Json;
use think\response\View;

/**
 * 门店员工-控制器层
 * Class Clerk
 * @package app\admin\controller\store
 */
class Clerk extends Backend
{
    /**
     * 门店员工列表
     *
     * @author windy
     * @return View|Json
     */
    public function lists()
    {
        if (request()->isAjax()) {
            $lists = ClerkLogic::lists($this->getData());
            return JsonUtils::success($lists);
        }

        return view('store/clerk/lists');
    }

    /**
     * 新增门店员工
     *
     * @author windy
     * @return View|Json
     */
    public function add()
    {
        if (request()->isAjax()) {
            $result = ClerkLogic::add($this->postData());
            if ($result === false) {
                return JsonUtils::error(ClerkLogic::getError());
            }
            return JsonUtils::ok('新增成功');
        }

        return view('store/clerk/add', [
            'storeList' => StoreLogic::all()
        ]);
    }

    /**
     * 编辑门店员工
     *
     * @author windy
     * @return View|Json
     */
    public function edit()
    {
        if (request()->isAjax()) {
            $result = ClerkLogic::edit($this->postData());
            if ($result === false) {
                return JsonUtils::error(ClerkLogic::getError());
            }
            return JsonUtils::ok('编辑成功');
        }

        return view('store/clerk/edit', [
            'storeList' => StoreLogic::all(),
            'detail'   => ClerkLogic::detail($this->getData('id'))
        ]);
    }

    /**
     * 删除门店员工
     *
     * @author windy
     * @return Json
     */
    public function del(): Json
    {
        if (request()->isAjax()) {
            $result = ClerkLogic::del($this->getData());
            if ($result === false) {
                return JsonUtils::error(ClerkLogic::getError());
            }
            return JsonUtils::ok('删除成功');
        }

        return JsonUtils::error('请求异常');
    }

    /**
     * 停用员工
     *
     * @author windy
     * @return Json
     */
    public function stop(): Json
    {
        if (request()->isAjax()) {
            $result = ClerkLogic::stop($this->postData('id'));
            if ($result === false) {
                return JsonUtils::error(ClerkLogic::getError());
            }
            return JsonUtils::ok('停用成功');
        }

        return JsonUtils::error('请求异常');
    }

    /**
     * 启用员工
     *
     * @author windy
     * @return Json
     */
    public function start(): Json
    {
        if (request()->isAjax()) {
            $result = ClerkLogic::start($this->postData('id'));
            if ($result === false) {
                return JsonUtils::error(ClerkLogic::getError());
            }
            return JsonUtils::ok('启用成功');
        }

        return JsonUtils::error('请求异常');
    }
}