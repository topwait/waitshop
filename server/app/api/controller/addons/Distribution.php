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


use app\api\logic\addons\DistributionLogic;
use app\api\validate\DistributionValidate;
use app\api\validate\PageValidate;
use app\api\validate\RequestValidate;
use app\common\basics\Api;
use app\common\utils\JsonUtils;
use think\response\Json;

/**
 * 分销接口-控制器层
 * Class Distribution
 * @package app\api\controller
 */
class Distribution extends Api
{
    /**
     * 填写邀请码绑定关系
     *
     * @author windy
     * @return Json
     */
    public function code(): Json
    {
        (new RequestValidate())->isPost();
        (new DistributionValidate())->goCheck('code', ['user_id'=>$this->userId]);

        $result = DistributionLogic::code($this->postData('code'), $this->userId);
        if ($result === false) {
            return JsonUtils::error(DistributionLogic::getError());
        }
        return JsonUtils::ok('绑定成功');
    }

    /**
     * 申请成为分销员
     *
     * @author windy
     * @return Json
     */
    public function apply(): Json
    {
        (new RequestValidate())->isPost();
        (new DistributionValidate())->goCheck('apply');

        $result = DistributionLogic::apply($this->postData(), $this->userId);
        if ($result === false) {
            return  JsonUtils::error(DistributionLogic::getError());
        }

        return JsonUtils::ok('已发起申请');
    }

    /**
     * 推广主页信息
     *
     * @author windy
     * @return Json
     */
    public function index(): Json
    {
        (new RequestValidate())->isGet();

        $detail = DistributionLogic::index($this->userId);
        return JsonUtils::success($detail);
    }

    /**
     * 推广粉丝
     *
     * @author windy
     * @return Json
     */
    public function fans(): Json
    {
        (new RequestValidate())->isGet();
        (new PageValidate())->goCheck();

        $lists = DistributionLogic::fans($this->getData(), $this->userId);
        return JsonUtils::success($lists);
    }

    /**
     * 分销订单
     *
     * @author windy
     * @return Json
     */
    public function order(): Json
    {
        (new RequestValidate())->isGet();
        (new PageValidate())->goCheck();

        $lists = DistributionLogic::order($this->getData(), $this->userId);
        return JsonUtils::success($lists);
    }

    /**
     * 佣金记录
     *
     * @author windy
     * @return Json
     */
    public function record(): Json
    {
        (new RequestValidate())->isGet();
        (new PageValidate())->goCheck();

        $lists = DistributionLogic::record($this->getData(), $this->userId);
        return JsonUtils::success($lists);
    }

}