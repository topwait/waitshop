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


use app\admin\logic\store\StoreLogic;
use app\common\basics\Backend;
use app\common\utils\JsonUtils;
use think\response\Json;
use think\response\View;

/**
 * 门店管理-控制器层
 * Class store
 * @package app\admin\controller\store
 */
class Store extends Backend
{
    /**
     * 门店列表
     *
     * @author windy
     * @return View|Json
     */
    public function lists()
    {
        if (request()->isAjax()) {
            $lists = StoreLogic::lists($this->getData());
            return JsonUtils::success($lists);
        }

        return view('store/store/lists');
    }

    /**
     * 新增门店
     *
     * @author windy
     * @return View|Json
     */
    public function add()
    {
        if (request()->isAjax()) {
            $result = StoreLogic::add($this->postData());
            if ($result === false) {
                return JsonUtils::error(StoreLogic::getError());
            }
            return JsonUtils::ok('新增成功');
        }

        return view('store/store/add');
    }

    /**
     * 编辑门店
     *
     * @author windy
     * @return View|Json
     */
    public function edit()
    {
        if (request()->isAjax()) {
            $result = StoreLogic::edit($this->postData());
            if ($result === false) {
                return JsonUtils::error(StoreLogic::getError());
            }
            return JsonUtils::ok('编辑成功');
        }

        return view('store/store/edit', [
            'detail' => StoreLogic::detail($this->getData('id'))
        ]);
    }

    /**
     * 删除门店
     *
     * @author windy
     * @return Json
     */
    public function del(): Json
    {
        if (request()->isAjax()) {
            $result = StoreLogic::del($this->postData('id'));
            if ($result === false) {
                return JsonUtils::error(StoreLogic::getError());
            }
            return JsonUtils::ok('删除成功');
        }

        return JsonUtils::error('请求异常');
    }

    /**
     * 停用门店
     *
     * @author windy
     * @return Json
     */
    public function stop(): Json
    {
        if (request()->isAjax()) {
            $result = StoreLogic::stop($this->postData('id'));
            if ($result === false) {
                return JsonUtils::error(StoreLogic::getError());
            }
            return JsonUtils::ok('停用成功');
        }

        return JsonUtils::error('请求异常');
    }

    /**
     * 启用门店
     *
     * @author windy
     * @return Json
     */
    public function start(): Json
    {
        if (request()->isAjax()) {
            $result = StoreLogic::start($this->postData('id'));
            if ($result === false) {
                return JsonUtils::error(StoreLogic::getError());
            }
            return JsonUtils::ok('启用成功');
        }

        return JsonUtils::error('请求异常');
    }
}