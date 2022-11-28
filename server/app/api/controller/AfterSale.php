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


use app\api\logic\AfterSaleLogic;
use app\api\validate\AfterSaleValidate;
use app\api\validate\IDMustBePositiveInt;
use app\api\validate\PageValidate;
use app\api\validate\RequestValidate;
use app\common\basics\Api;
use app\common\utils\JsonUtils;
use think\response\Json;

/**
 * 售后接口-控制器层
 * Class AfterSale
 * @package app\api\controller
 */
class AfterSale extends Api
{
    /**
     * 售后列表
     *
     * @author windy
     * @return Json
     */
    public function lists(): Json
    {
        (new RequestValidate())->isGet();
        (new PageValidate())->goCheck();

        $lists = AfterSaleLogic::lists($this->getData(), $this->userId);
        return JsonUtils::success($lists);
    }

    /**
     * 售后商品
     *
     * @author windy
     * @return Json
     */
    public function goods(): Json
    {
        (new RequestValidate())->isGet();
        (new IDMustBePositiveInt())->goCheck();

        $detail = AfterSaleLogic::goods($this->getData('id'));
        return JsonUtils::success($detail);
    }

    /**
     * 售后详细
     *
     * @author windy
     * @return Json
     */
    public function detail(): Json
    {
        (new RequestValidate())->isGet();
        (new IDMustBePositiveInt())->goCheck();

        $detail = AfterSaleLogic::detail($this->getData('id'));
        return JsonUtils::success($detail);
    }

    /**
     * 售后日志
     *
     * @author windy
     * @return Json
     */
    public function logs(): Json
    {
        (new RequestValidate())->isGet();
        (new IDMustBePositiveInt())->goCheck();

        $lists = AfterSaleLogic::logs($this->getData('id'), $this->userId);
        return JsonUtils::success($lists);
    }

    /**
     * 申请售后
     *
     * @author windy
     * @return Json
     */
    public function apply(): Json
    {
        (new RequestValidate())->isPost();
        (new AfterSaleValidate())->goCheck('apply');

        $result = AfterSaleLogic::apply($this->postData(), $this->userId);
        if ($result === false) {
            return JsonUtils::error(AfterSaleLogic::getError());
        }
        return JsonUtils::ok('申请成功');
    }

    /**
     * 撤销申请
     *
     * @author windy
     * @return Json
     */
    public function revoke(): Json
    {
        (new RequestValidate())->isPost();
        (new IDMustBePositiveInt())->goCheck();

        $result = AfterSaleLogic::revoke($this->postData('id'), $this->userId);
        if ($result === false) {
            return JsonUtils::error(AfterSaleLogic::getError());
        }
        return JsonUtils::ok('撤销成功');
    }

    /**
     * 快递凭证
     *
     * @author windy
     * @return Json
     */
    public function express(): Json
    {
        (new RequestValidate())->isPost();
        (new AfterSaleValidate())->goCheck('express');

        $result = AfterSaleLogic::express($this->postData(), $this->userId);
        if ($result === false) {
            return JsonUtils::error(AfterSaleLogic::getError());
        }
        return JsonUtils::ok('操作成功');
    }
}