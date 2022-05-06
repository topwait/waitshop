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

namespace app\admin\controller\auth;


use app\admin\logic\auth\AdminLogic;
use app\admin\logic\auth\RoleLogic;
use app\admin\validate\auth\AdminValidate;
use app\common\basics\Backend;
use app\common\utils\JsonUtils;
use think\response\Json;
use think\response\View;

/**
 * 管理员管理-控制器层
 * Class Admin
 * @package app\admin\controller\auth
 */
class Admin extends Backend
{
    /**
     * 管理员列表
     *
     * @author windy
     * @return View|Json
     */
    public function lists()
    {
        if (request()->isAjax()) {
            $lists = AdminLogic::lists($this->getData());
            return JsonUtils::success($lists);
        }

        return view('auth/admin/lists');
    }

    /**
     * 基本信息
     *
     * @author windy
     * @return View|Json
     */
    public function info()
    {
        if (request()->isAjax()) {
            (new AdminValidate())->goCheck('edit');
            $result = AdminLogic::info($this->postData(), $this->adminId);
            if ($result === false) {
                return JsonUtils::error(AdminLogic::getError());
            }
            return JsonUtils::ok('更新成功');
        }

        return view('auth/admin/info', [
            'detail'   => AdminLogic::detail($this->adminId),
            'treeRole' => RoleLogic::getRoleAll()
        ]);
    }

    /**
     * 新增管理员
     *
     * @author windy
     * @return View|Json
     */
    public function add()
    {
        if (request()->isAjax()) {
            (new AdminValidate())->goCheck('add');
            $result = AdminLogic::add($this->postData());
            if ($result === false) {
                return JsonUtils::error(AdminLogic::getError());
            }
            return JsonUtils::ok('新增成功');
        }

        return view('auth/admin/add', [
            'treeRole' => RoleLogic::getRoleAll()
        ]);
    }

    /**
     * 编辑管理员
     *
     * @author windy
     * @return View|Json
     */
    public function edit()
    {
        if (request()->isAjax()) {
            (new AdminValidate())->goCheck('edit');
            $result = AdminLogic::edit($this->postData());
            if ($result === false) {
                return JsonUtils::error(AdminLogic::getError());
            }
            return JsonUtils::ok('编辑成功');
        }

        return view('auth/admin/edit', [
            'detail'   => AdminLogic::detail($this->getData('id')),
            'treeRole' => RoleLogic::getRoleAll()
        ]);
    }

    /**
     * 删除管理员
     *
     * @author windy
     * @return Json
     */
    public function del(): Json
    {
        if (request()->isAjax()) {
            (new AdminValidate())->goCheck('id');
            $result = AdminLogic::del($this->getData('id'));
            if ($result === false) {
                return JsonUtils::error(AdminLogic::getError());
            }
            return JsonUtils::ok('删除成功');
        }

        return JsonUtils::error('请求异常');
    }
}