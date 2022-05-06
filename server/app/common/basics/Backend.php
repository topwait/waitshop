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

namespace app\common\basics;


use app\common\model\auth\AdminRole;
use app\common\model\auth\AdminRule;
use app\common\utils\JsonUtils;
use app\common\utils\TimeUtils;
use app\common\utils\UrlUtils;
use Exception;
use think\App;
use think\exception\HttpResponseException;
use think\facade\View;

/**
 * 后台基类
 * Class Backend
 * @package app\common\basics
 */
abstract class Backend
{
    /**
     * Request实例
     */
    protected $request;

    /**
     * 应用实例
     */
    protected $app;

    /**
     * 管理员信息
     */
    protected $adminUser;

    /**
     * 管理员ID
     */
    protected $adminId;

    /**
     * 不校验登录的方法
     */
    protected $notNeedLogin = [];

    /**
     * 白名单,不校验权限的路径
     */
    protected $allowAllAction  = [
        'Index/index',
        'Index/denied',
        'Login/logout',
        'Login/index',
        'Login/checkLogin'
    ];

    /**
     * 构造方法
     * @access public
     * @param  App  $app  应用对象
     */
    public function __construct(App $app)
    {
        $this->app     = $app;
        $this->request = $this->app->request;
        $this->setValue();

        if (!$this->isLogin()) {
            $this->redirect('/admin/login', 302);
        }

        if (!$this->isAuthority()) {
            if (request()->isAjax()) {
                JsonUtils::throw('权限不足', [], 403);
                return false;
            }

            $this->redirect(url('Index/denied'), 302);
        }

        return true;
    }

    /**
     * 设置常用变量
     * @author windy
     */
    protected function setValue()
    {
        View::assign('domainUrl', UrlUtils::getAbsoluteUrl('/'));
        View::assign('today', TimeUtils::today());                //今天时间戳
        View::assign('yesterday', TimeUtils::yesterday());        //昨天时间戳
        View::assign('dayToNow7', TimeUtils::dayToNow(7));   //7天前时间戳
        View::assign('dayToNow30', TimeUtils::dayToNow(30)); //30天前时间戳
        View::assign('month', TimeUtils::month());                //本月时间戳
        View::assign('year', TimeUtils::year());                  //本年时间戳
    }

    /**
     * 判断登录
     *
     * @author windy
     * @return bool
     */
    protected function isLogin(): bool
    {
        if (in_array(request()->action(), $this->notNeedLogin)) {
            return true;
        } else {
            $adminUser = session('adminUser');
            if ($adminUser) {
                $this->adminUser = $adminUser;
                $this->adminId  = intval($adminUser['id']);
                return true;
            }
            return false;
        }
    }

    /**
     * 验证权限
     *
     * @author windy
     * @return bool
     */
    protected function isAuthority(): bool
    {
        try {
            $requestUrl = request()->controller().'/'.request()->action();

            if (in_array($requestUrl, $this->notNeedLogin)   ||
                in_array($requestUrl, $this->allowAllAction) ||
                in_array(request()->action(), $this->allowAllAction)) {
                return true;
            }

            if ($this->adminUser['id'] === 1) {
                return true;
            }

            $roleModel = new AdminRole();
            $ruleModel = new AdminRule();

            $rule_ids = $roleModel->field(true)
                ->where(['id'=>intval($this->adminUser['role_id'])])
                ->where(['is_disable'=>0])
                ->value('rule_ids') ?? [];

            $rule_data = $ruleModel->field(true)
                ->whereIn('id', $rule_ids)
                ->order('sort asc, id asc')
                ->column('uri');

            if (!in_array($requestUrl, array_unique($rule_data))) return false;

            return true;
        } catch (Exception $e) {
            return false;
        }
    }

    /**
     * 重写跳转
     *
     * @author windy
     * @param mixed ...$args
     */
    protected function redirect(...$args) {
        throw new HttpResponseException(redirect(...$args));
    }

    /**
     * 获取get请求数据
     *
     * @author windy
     * @param $key
     * @param $default
     * @return mixed
     */
    protected function getData($key=null, $default=null)
    {
        if ($key === null) {
            return $this->request->get();
        } else {
            return $default ? $this->request->get($key, $default) : $this->request->get($key);
        }
    }

    /**
     * 获取post请求数据
     *
     * @author windy
     * @param $key
     * @param $default
     * @return mixed
     */
    protected function postData($key=null, $default=null)
    {
        if ($key === null) {
            return $this->request->post();
        } else {
            return $default ? $this->request->post($key, $default) : $this->request->post($key);
        }
    }
}