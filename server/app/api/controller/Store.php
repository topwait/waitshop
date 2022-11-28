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


use app\api\logic\StoreLogic;
use app\api\validate\IDMustBePositiveInt;
use app\api\validate\PageValidate;
use app\api\validate\RequestValidate;
use app\common\basics\Api;
use app\common\utils\JsonUtils;
use Exception;
use think\response\Json;

/**
 * 门店接口-控制器层
 * Class Store
 * @package app\api\controller
 */
class Store extends Api
{
    public $notNeedLogin = ['lists', 'detail'];

    /**
     * 门店列表
     *
     * @author windy
     * @return Json
     */
    public function lists(): Json
    {
        (new RequestValidate())->isGet();
        (new PageValidate())->goCheck();

        $lists = StoreLogic::lists($this->getData());
        return JsonUtils::success($lists);
    }

    /**
     * 门店详细
     *
     * @author windy
     * @return Json
     */
    public function detail(): Json
    {
        (new RequestValidate())->isGet();
        (new IDMustBePositiveInt())->goCheck();

        $detail = StoreLogic::detail($this->getData());
        return JsonUtils::success($detail);
    }

    /**
     * 核销主页面
     *
     * @author windy
     * @return Json
     */
    public function writeOffIndex(): Json
    {
        (new RequestValidate())->isGet();

        try {
            $detail = StoreLogic::writeOffIndex($this->userId);
            return JsonUtils::success($detail);
        } catch (Exception $e) {
            return JsonUtils::error($e->getMessage());
        }
    }

    /**
     * 待核销订单
     *
     * @author windy
     * @return Json
     */
    public function writeOffOrder(): Json
    {
        (new RequestValidate())->isGet();
        (new PageValidate())->goCheck();

        $lists = StoreLogic::writeOffOrder($this->getData(), $this->userId);
        return JsonUtils::success($lists);
    }

    /**
     * 核销详情
     *
     * @author windy
     * @return Json
     */
    public function writeOffDetail(): Json
    {
        (new RequestValidate())->isGet();

        try {
            $detail = StoreLogic::writeOffDetail($this->getData('code'), $this->userId);
            return JsonUtils::success($detail);
        } catch (Exception $e) {
            return JsonUtils::error($e->getMessage());
        }
    }

    /**
     * 核销记录
     *
     * @author windy
     * @return Json
     */
    public function writeOffRecord(): Json
    {
        (new RequestValidate())->isGet();
        (new PageValidate())->goCheck();

        $lists = StoreLogic::writeOffRecord($this->getData(), $this->userId);
        return JsonUtils::success($lists);
    }

    /**
     * 核销确认
     *
     * @author windy
     * @return Json
     */
    public function writeOffSubmit(): Json
    {
        (new RequestValidate())->isPost();
        (new IDMustBePositiveInt())->goCheck();

        $result = StoreLogic::writeOffSubmit($this->postData('id'), $this->userId);
        if ($result === false) {
            return JsonUtils::error(StoreLogic::getError());
        }
        return JsonUtils::ok('核销成功');
    }
}