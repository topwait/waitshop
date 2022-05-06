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


use app\api\logic\addons\CouponLogic;
use app\api\validate\IDMustBePositiveInt;
use app\api\validate\PageValidate;
use app\api\validate\RequestValidate;
use app\common\basics\Api;
use app\common\utils\JsonUtils;
use think\App;
use think\response\Json;

/**
 * 优惠券接口-控制器层
 * Class Coupon
 * @package app\api\controller\marketing
 */
class Coupon extends Api
{
    public $notNeedLogin = ['lists'];

    /**
     * 初始化
     *
     * Coupon constructor.
     * @param App $app
     */
    public function __construct(App $app)
    {
        parent::__construct($app);
    }

    /**
     * 优惠券列表
     *
     * @author windy
     * @return Json
     */
    public function lists(): Json
    {
        (new RequestValidate())->isGet();
        (new PageValidate())->goCheck();

        $lists = CouponLogic::lists($this->getData(), $this->userId);
        if ($lists === false) {
            return JsonUtils::error(CouponLogic::getError());
        }
        return JsonUtils::success($lists);
    }

    /**
     * 领取优惠券
     *
     * @author windy
     * @return Json
     */
    public function receive(): Json
    {
        (new RequestValidate())->isPost();
        (new IDMustBePositiveInt())->goCheck();

        $lists = CouponLogic::receive($this->postData('id'), $this->userId);
        if ($lists === false) {
            return JsonUtils::error(CouponLogic::getError());
        }
        return JsonUtils::ok('领取成功');
    }

    /**
     * 我的优惠券
     *
     * @author windy
     * @return Json
     */
    public function myCoupon(): Json
    {
        (new RequestValidate())->isGet();
        (new PageValidate())->goCheck();

        $lists = CouponLogic::myCoupon($this->getData(), $this->userId);
        if ($lists === false) {
            return JsonUtils::error(CouponLogic::getError());
        }
        return JsonUtils::success($lists);
    }

    /**
     * 我的未使用失效优惠券
     *
     * @author windy
     * @return Json
     */
    public function invalidCoupon(): Json
    {
        (new RequestValidate())->isGet();
        (new PageValidate())->goCheck();

        $lists = CouponLogic::invalidCoupon($this->getData(), $this->userId);
        if ($lists === false) {
            return JsonUtils::error(CouponLogic::getError());
        }
        return JsonUtils::success($lists);
    }

    /**
     * 订单可用优惠券
     *
     * @author windy
     * @return Json
     * @throws @\think\Exception
     */
    public function orderCoupon(): Json
    {
        (new RequestValidate())->isPost();

        $lists = CouponLogic::orderCoupon($this->postData(), $this->userId);
        if ($lists === false) {
            return JsonUtils::error( CouponLogic::getError());
        }
        return JsonUtils::success($lists);
    }
}