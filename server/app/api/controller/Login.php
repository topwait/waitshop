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

namespace app\api\controller;


use app\api\logic\LoginLogic;
use app\api\validate\LoginValidate;
use app\api\validate\RequestValidate;
use app\common\basics\Api;
use app\common\utils\JsonUtils;
use think\response\Json;

/**
 * 登录接口-控制器层
 * Class Login
 * @package app\api\controller
 */
class Login extends Api
{
    public $notNeedLogin = ['config', 'register', 'login', 'getWxPhone'];

    /**
     * 登录配置参数
     *
     * @author windy
     * @return Json
     */
    public function config(): Json
    {
        (new RequestValidate())->isGet();

        $result = LoginLogic::config();
        return JsonUtils::success($result);
    }

    /**
     * 注册
     *
     * @author windy
     * @return Json
     */
    public function register(): Json
    {
        (new RequestValidate())->isPost();
        (new LoginValidate())->goCheck('register');

        $result = LoginLogic::register($this->postData());
        if ($result === false) {
            return JsonUtils::error(LoginLogic::getError());
        }

        return JsonUtils::ok('注册成功', $result);
    }

    /**
     * 登录
     *
     * @author windy
     * @return Json
     */
    public function login(): Json
    {
        (new RequestValidate())->isPost();
        (new LoginValidate())->goCheck('scene');

        $result = false;
        $post = $this->postData();
        switch ($post['scene']) {
            case 'silent':
                (new LoginValidate())->goCheck('silent');
                $result = LoginLogic::silentLogin($post);
                break;
            case 'mnp':
                (new LoginValidate())->goCheck('mnp');
                $result = LoginLogic::mnpLogin($post);
                break;
            case 'mobile':
                (new LoginValidate())->goCheck('mobile');
                $result = LoginLogic::mobileLogin($post);
                break;
            case 'account':
                (new LoginValidate())->goCheck('account');
                $result = LoginLogic::accountLogin($post);
                break;
        }

        if ($result === false) {
            return JsonUtils::error(LoginLogic::getError(), [], LoginLogic::getReturnCode());
        }

        return JsonUtils::ok('登录成功', $result);
    }

    /**
     * 微信端手机号码
     *
     * @author windy
     * @return Json
     */
    public function getWxPhone(): Json
    {
        $result = LoginLogic::getWxPhone($this->postData());
        if ($result === false) {
            return JsonUtils::error(LoginLogic::getError());
        }

        return JsonUtils::success($result);
    }

    /**
     * 绑定手机号
     *
     * @author windy
     * @return Json
     */
    public function bindMobile(): Json
    {
        $result = LoginLogic::bindMobile($this->postData('mobile'), $this->userId);
        if ($result === false) {
            return JsonUtils::error(LoginLogic::getError());
        }

        return JsonUtils::ok('绑定成功');
    }
}