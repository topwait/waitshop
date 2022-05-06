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


use app\api\logic\addons\LotteryLogic;
use app\api\validate\PageValidate;
use app\api\validate\RequestValidate;
use app\common\basics\Api;
use app\common\utils\JsonUtils;
use think\response\Json;

/**
 * 幸运抽奖接口-控制器层
 * Class Lottery
 * @package app\api\controller\addons
 */
class Lottery extends Api
{
    /**
     * 抽奖奖品
     *
     * @author windy
     * @return Json
     */
    public function prize(): Json
    {
        (new RequestValidate())->isGet();

        $result = LotteryLogic::prize($this->userId);
        return JsonUtils::success($result);
    }

    /**
     * 抽奖记录
     *
     * @author windy
     * @return Json
     */
    public function record(): Json
    {
        (new RequestValidate())->isGet();
        (new PageValidate())->goCheck();

        $lists = LotteryLogic::record($this->getData(), $this->userId);
        return JsonUtils::success($lists);
    }

    /**
     * 开始抽奖
     *
     * @author windy
     * @return Json
     */
    public function draw(): Json
    {
        (new RequestValidate())->isPost();

        $result = LotteryLogic::draw($this->userId);
        if ($result === false) {
            return JsonUtils::error(LotteryLogic::getError());
        }
        return JsonUtils::ok('抽奖成功', $result);
    }

}