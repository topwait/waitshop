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


use app\api\logic\addons\TeamLogic;
use app\api\validate\IDMustBePositiveInt;
use app\api\validate\PageValidate;
use app\api\validate\RequestValidate;
use app\common\basics\Api;
use app\common\utils\JsonUtils;
use think\response\Json;

/**
 * 拼团接口-控制器层
 * Class Sharing
 * @package app\api\controller\marketing
 */
class Team extends Api
{
    public $notNeedLogin = ['lists', 'detail'];

    /**
     * 正在参与拼团的商品列表
     *
     * @author windy
     * @return Json
     */
    public function lists(): Json
    {
        (new RequestValidate())->isGET();
        (new PageValidate())->goCheck();

        $lists = TeamLogic::lists($this->getData());
        return JsonUtils::success($lists);
    }

    /**
     * 正在参与拼团的商品详细
     *
     * @author windy
     * @return Json
     */
    public function detail(): Json
    {
        (new RequestValidate())->isGET();
        (new IDMustBePositiveInt())->goCheck();

        $detail = TeamLogic::detail($this->getData('id'), $this->userId);
        return JsonUtils::success($detail);
    }

    /**
     * 我参与拼团的记录
     *
     * @author windy
     * @return Json
     */
    public function record()
    {
        (new RequestValidate())->isGET();
        (new PageValidate())->goCheck();

        $lists = TeamLogic::record($this->getData(), $this->userId);
        return JsonUtils::success($lists);
    }

    /**
     * 订单结算
     *
     * @author windy
     * @return Json
     */
    public function settle(): Json
    {
        (new RequestValidate())->isPost();

        $settle = TeamLogic::settle($this->postData(), $this->userId);
        if ($settle === false) {
            return JsonUtils::error(TeamLogic::getError());
        }

        return JsonUtils::success($settle);
    }

    /**
     * 提交订单
     *
     * @author windy
     * @return Json
     */
    public function buy(): Json
    {
        (new RequestValidate())->isPost();

        $post = $this->request->post();
        $oProducts = $post['products'];
        unset($post['products']);

        $order = TeamLogic::buy($post, $oProducts, $this->userId);
        if ($order === false) {
            return JsonUtils::error(TeamLogic::getError());
        } else if($order['pass'] and $order['status']) {
            return JsonUtils::ok('OK', $order);
        }

        return JsonUtils::error(TeamLogic::getError(), $order);
    }
}