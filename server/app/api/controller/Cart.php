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


use app\api\logic\CartLogic;
use app\api\validate\CartValidate;
use app\api\validate\RequestValidate;
use app\common\basics\Api;
use app\common\utils\JsonUtils;
use think\response\Json;

/**
 * 购物车接口-控制器层
 * Class Cart
 * @package app\api\controller
 */
class Cart extends Api
{
    public $notNeedLogin = ['num'];

    /**
     * 用户购物车列表
     *
     * @author windy
     * @return Json
     */
    public function lists(): Json
    {
        (new RequestValidate())->isGET();

        $lists = CartLogic::lists($this->userId);
        return JsonUtils::success($lists);
    }

    /**
     * 加入到购物车
     *
     * @author windy
     * @return Json
     */
    public function add(): Json
    {
        (new RequestValidate())->isPost();
        (new CartValidate())->goCheck('add');

        $result = CartLogic::add($this->postData(), $this->userId);
        if ($result === false) {
            return JsonUtils::error(CartLogic::getError());
        }
        return JsonUtils::ok('加入成功');
    }

    /**
     * 销毁购物车中产品
     *
     * @author windy
     * @return Json
     */
    public function destroy(): Json
    {
        (new RequestValidate())->isPost();
        (new CartValidate())->goCheck('destroy');

        $result = CartLogic::destroy($this->postData(), $this->userId);
        if ($result === false) {
            return JsonUtils::error(CartLogic::getError());
        }

        return JsonUtils::ok('删除成功');
    }

    /**
     * 更新购买产品的数量
     *
     * @author windy
     * @return Json
     */
    public function change(): Json
    {
        (new RequestValidate())->isPost();
        (new CartValidate())->goCheck('change');

        $id  = $this->postData('id');
        $num = $this->postData('num');
        $result = CartLogic::change($id, $num, $this->userId);
        if ($result === false) {
            return JsonUtils::error(CartLogic::getError());
        }

        return JsonUtils::ok('操作成功');
    }

    /**
     * 选择或取消选择
     *
     * @author windy
     * @return Json
     */
    public function choice(): Json
    {
        (new RequestValidate())->isPost();
        (new CartValidate())->goCheck('choice');

        $result = CartLogic::choice($this->postData(), $this->userId);
        if ($result === false) {
            return JsonUtils::error(CartLogic::getError());
        }

        return JsonUtils::ok('操作成功');
    }

    /**
     * 购物车数量
     *
     * @author windy
     * @return Json
     */
    public function num(): Json
    {
        (new RequestValidate())->isGet();

        $result = CartLogic::num($this->userId);
        return JsonUtils::success($result);
    }
}