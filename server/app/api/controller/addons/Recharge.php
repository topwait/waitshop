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

namespace app\api\controller\addons;


use app\api\logic\addons\RechargeLogic;
use app\api\validate\IDMustBePositiveInt;
use app\api\validate\PageValidate;
use app\api\validate\RechargeValidate;
use app\api\validate\RequestValidate;
use app\common\basics\Api;
use app\common\utils\JsonUtils;
use think\response\Json;

/**
 * 充值接口-控制器层
 * Class Recharge
 * @package app\api\controller\addons
 */
class Recharge extends Api
{
    /**
     * 充值页面信息
     *
     * @author windy
     * @return Json
     */
    public function index(): Json
    {
        (new RequestValidate())->isGet();

        $detail = RechargeLogic::index($this->userId);
        return JsonUtils::success($detail);
    }

    /**
     * 下单充值
     *
     * @author windy
     * @return Json
     */
    public function buy(): Json
    {
        (new RequestValidate())->isPost();
        (new RechargeValidate())->goCheck();

        $result = RechargeLogic::buy($this->postData(), $this->userId);
        if ($result === false) {
            return JsonUtils::error(RechargeLogic::getError());
        }
        return JsonUtils::ok('下单成功', $result);
    }

    /**
     * 充值订单
     *
     * @author windy
     * @return Json
     */
    public function order(): Json
    {
        (new RequestValidate())->isGet();
        (new IDMustBePositiveInt())->goCheck();

        $detail = RechargeLogic::order($this->getData('id'));
        return JsonUtils::success($detail);
    }

    /**
     * 充值记录
     *
     * @author windy
     * @return Json
     */
    public function record(): Json
    {
        (new RequestValidate())->isGet();
        (new PageValidate())->goCheck();

        $lists = RechargeLogic::record($this->getData(), $this->userId);
        return JsonUtils::success($lists);
    }
}