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


use app\admin\logic\auth\RoleLogic;
use app\admin\logic\auth\RuleLogic;
use app\admin\validate\auth\RoleValidate;
use app\common\basics\Backend;
use app\common\utils\JsonUtils;
use think\response\Json;
use think\response\View;

/**
 * 角色管理-控制器层
 * Class Role
 * @package app\admin\controller\auth
 */
class Role extends Backend
{
    /**
     * 角色列表
     *
     * @author windy
     * @return View|Json
     */
    public function lists()
    {
        if (request()->isAjax()) {
            $lists = RoleLogic::lists($this->getData());
            return JsonUtils::success($lists);
        }

        return view('auth/role/lists');
    }

    /**
     * 角色新增
     *
     * @author windy
     * @return View|Json
     */
    public function add()
    {
        if (request()->isAjax()) {
            (new RoleValidate())->goCheck('add');
            $result = RoleLogic::add($this->postData());
            if ($result === false) {
                return JsonUtils::error(RoleLogic::getError());
            }
            return JsonUtils::ok('新增成功');
        }

        return view('auth/role/add', [
            'treeJsonRule' => json_encode(RuleLogic::getTreeJsonRule())
        ]);
    }

    /**
     * 角色编辑
     *
     * @author windy
     * @return View|Json
     */
    public function edit()
    {
        if (request()->isAjax()) {
            (new RoleValidate())->goCheck('edit');
            $result = RoleLogic::edit($this->postData());
            if ($result === false) {
                return JsonUtils::error(RoleLogic::getError());
            }
            return JsonUtils::ok('编辑成功');
        }

        $detail = RoleLogic::detail($this->getData('id'));
        return view('auth/role/edit', [
            'detail'       => $detail,
            'checked'      => json_encode(RoleLogic::getChecked($detail['rule_ids'])),
            'treeJsonRule' => json_encode(RuleLogic::getTreeJsonRule())
        ]);
    }

    /**
     * 角色删除
     *
     * @author windy
     * @return Json
     */
    public function del(): Json
    {
        if (request()->isAjax()) {
            (new RoleValidate())->goCheck('id');
            $result = RoleLogic::del($this->postData('id'));
            if ($result === false) {
                return JsonUtils::error(RoleLogic::getError());
            }
            return JsonUtils::ok('删除成功');
        }

        return JsonUtils::error('请求异常');
    }
}