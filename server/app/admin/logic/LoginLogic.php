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

namespace app\admin\logic;


use app\common\basics\Logic;
use app\common\model\auth\Admin;
use Exception;

/**
 * 登录-逻辑层
 * Class LoginLogic
 * @package app\admin\logic
 */
class LoginLogic extends Logic
{
    /**
     * 登录
     *
     * @author windy
     * @param array $post
     * @return string
     */
    public static function login(array $post)
    {
        // 查询账户
        $model = new Admin();
        $adminUser = $model->field(true)
            ->where('is_delete', '=', 0)
            ->where(['username|email' => $post['username']])
           ->findOrEmpty()->toArray();

        // 验证账户
        if (!$adminUser) { static::$error = '登录账号或密码异常'; return false; }
        if ($adminUser['is_disable']) { static::$error = '账号被禁止登录,请联系管理员'; return false; }
        if ($adminUser['password'] !== encrypt_password($post['password'], $adminUser['salt'])) {
             static::$error = '账号或密码有误';
             return false;
        }

        // 登录成功
        try {
            $model->where(['id' => $adminUser['id']])->update([
                'login_ip'   => request()->ip(),
                'login_time' => time()
            ]);
            session('adminUser', $adminUser);
            return true;
        } catch (Exception $e) {
            static::$error = $e->getMessage();
            return false;
        }
    }
}