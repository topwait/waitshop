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

namespace app\api\controller;


use app\api\logic\UserLogic;
use app\api\validate\PageValidate;
use app\api\validate\RequestValidate;
use app\common\basics\Api;
use app\common\utils\JsonUtils;
use think\response\Json;

/**
 * 用户管理接口-控制器层
 * Class User
 * @package app\api\controller
 */
class User extends Api
{
    public $notNeedLogin = ['design'];

    /**
     * 装修设计
     *
     * @author windy
     * @return Json
     */
    public function design(): Json
    {
        (new RequestValidate())->isGet();

        $detail = UserLogic::design($this->userId);
        return JsonUtils::success($detail);
    }

    /**
     * 个人中心
     *
     * @author windy
     * @return Json
     */
    public function center(): Json
    {
        (new RequestValidate())->isGet();

        $detail = UserLogic::center($this->userId);
        return JsonUtils::success($detail);
    }

    /**
     * 用户信息
     *
     * @author windy
     * @return Json
     */
    public function info(): Json
    {
        (new RequestValidate())->isGet();

        $detail = UserLogic::info($this->userId);
        return JsonUtils::success($detail);
    }

    /**
     * 设置用户信息
     *
     * @author windy
     * @return Json
     */
    public function setUser(): Json
    {
        (new RequestValidate())->isPost();

        $result = UserLogic::setUser($this->postData(), $this->userId);
        if ($result === false) {
            return JsonUtils::error(UserLogic::getError());
        }
        return JsonUtils::ok('更新成功');
    }

    /**
     * 用户钱包信息
     *
     * @author windy
     * @return Json
     */
    public function wallet(): Json
    {
        (new RequestValidate())->isGet();

        $result = UserLogic::wallet($this->userId);
        return JsonUtils::success($result);
    }

    /**
     * 用户积分信息
     *
     * @author windy
     * @return Json
     */
    public function integral(): Json
    {
        (new RequestValidate())->isGet();

        $result = UserLogic::integral($this->getData(), $this->userId);
        return JsonUtils::success($result);
    }

    /**
     * 账户明细
     *
     * @author windy
     * @return Json
     */
    public function account(): Json
    {
        (new RequestValidate())->isGet();
        (new PageValidate())->goCheck();

        $lists = UserLogic::account($this->getData(), $this->userId);
        return JsonUtils::success($lists);
    }

    /**
     * 用户等级列表
     *
     * @author windy
     * @return Json
     */
    public function grade(): Json
    {
        (new RequestValidate())->isGet();

        $lists = UserLogic::grade($this->userId);
        return JsonUtils::success($lists);
    }
}