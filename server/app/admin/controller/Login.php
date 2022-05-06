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

namespace app\admin\controller;


use app\admin\logic\LoginLogic;
use app\admin\validate\LoginValidate;
use app\common\basics\Backend;
use app\common\utils\ConfigUtils;
use app\common\utils\JsonUtils;
use think\response\Json;
use think\response\View;

/**
 * 登录-控制器层
 * Class Login
 * @package app\admin\controller
 */
class Login extends Backend
{
    protected $notNeedLogin = ['index', 'checkLogin'];

    /**
     * 登录页面
     *
     * @author windy
     * @return View
     */
    public function index(): View
    {
        $action = in_array(request()->action(), $this->notNeedLogin);
        if ($this->isLogin() and !$action) {
            $this->redirect(url('Index/index'), 302);
        }

        return view('public/login', [
            'title' => ConfigUtils::get('backstage')['backstage_login_title'] ?? 'WaitShop 商业版'
        ]);
    }

    /**
     * 验证登录
     *
     * @author windy
     * @return Json
     */
    public function checkLogin(): Json
    {
        if (request()->isAjax()) {
            (new LoginValidate())->goCheck();
            $result = LoginLogic::login($this->postData());
            if ($result === false) {
                return JsonUtils::error(LoginLogic::getError());
            }
            return JsonUtils::ok('登录成功');
        }

        return JsonUtils::error('请求异常');
    }

    /**
     * 退出登录
     *
     * @author windy
     */
    public function logout()
    {
        session('adminUser', null);
        $this->redirect(url('Login/index'), 302);
    }
}