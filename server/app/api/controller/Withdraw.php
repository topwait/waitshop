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


use app\api\logic\WithdrawLogic;
use app\api\validate\IDMustBePositiveInt;
use app\api\validate\PageValidate;
use app\api\validate\RequestValidate;
use app\api\validate\WithdrawValidate;
use app\common\basics\Api;
use app\common\utils\JsonUtils;
use think\response\Json;

/**
 * 佣金提现接口-控制器层
 * Class Withdraw
 * @package app\api\controller
 */
class Withdraw extends Api
{
    /**
     * 提现配置信息
     *
     * @author windy
     * @return Json
     */
    public function config(): Json
    {
        (new RequestValidate())->isGet();

        $config = WithdrawLogic::config($this->userId);
        return JsonUtils::success($config);
    }

    /**
     * 提现申请
     *
     * @author windy
     * @return Json
     */
    public function apply(): Json
    {
        (new RequestValidate())->isPost();
        (new WithdrawValidate())->goCheck();

        $result = WithdrawLogic::apply($this->postData(), $this->userId);
        if ($result === false) {
            return JsonUtils::error(WithdrawLogic::getError());
        }

        return JsonUtils::ok('提交成功');
    }

    /**
     * 提现记录
     *
     * @author windy
     * @return Json
     */
    public function record(): Json
    {
        (new RequestValidate())->isGet();
        (new PageValidate())->goCheck();

        $lists = WithdrawLogic::record($this->getData(), $this->userId);
        return JsonUtils::success($lists);
    }

    /**
     * 提现详细
     *
     * @author windy
     * @return Json
     */
    public function detail(): Json
    {
        (new RequestValidate())->isGet();
        (new IDMustBePositiveInt())->goCheck();

        $detail = WithdrawLogic::detail($this->getData('id'));
        return JsonUtils::success($detail);
    }
}