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

namespace app\admin\controller\user;


use app\admin\logic\user\GradeLogic;
use app\admin\logic\user\UserLogic;
use app\common\basics\Backend;
use app\common\utils\JsonUtils;
use think\response\Json;
use think\response\View;

/**
 * 用户管理-控制器层
 * Class User
 * @package app\admin\controller\user
 */
class User extends Backend
{
    /**
     * 用户列表
     *
     * @author windy
     * @return View|Json
     */
    public function lists()
    {
        if (request()->isAjax()) {
            $lists = UserLogic::lists($this->getData());
            return JsonUtils::success($lists);
        }

        return view('user/user/lists', [
            'grade' => GradeLogic::all()
        ]);
    }

    /**
     * 用户资料
     *
     * @author windy
     * @return View
     */
    public function info(): View
    {
        return view('user/user/info', [
            'detail' => UserLogic::info($this->getData('id'))
        ]);
    }

    /**
     * 充值金额
     *
     * @author windy
     * @return View|Json
     */
    public function recharge()
    {
        if (request()->isAjax()) {
            $result = UserLogic::recharge($this->postData(), $this->adminId);
            if ($result === false) {
                return JsonUtils::error(UserLogic::getError());
            }
            return JsonUtils::ok('充值成功');
        }

        return view('user/user/recharge', [
            'detail' => UserLogic::info($this->getData('id'))
        ]);
    }

    /**
     * 调整用户等级
     *
     * @author windy
     * @return View|Json
     */
    public function grade()
    {
        if (request()->isAjax()) {
            $result = UserLogic::grade($this->postData());
            if ($result === false) {
                return JsonUtils::error(UserLogic::getError());
            }
            return JsonUtils::ok('调整成功');
        }

        return view('user/user/grade', [
            'detail' => UserLogic::info($this->getData('id')),
            'grade'  => GradeLogic::all()
        ]);
    }
}