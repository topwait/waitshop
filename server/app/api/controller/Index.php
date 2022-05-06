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


use app\api\logic\IndexLogic;
use app\api\validate\RequestValidate;
use app\common\basics\Api;
use app\common\utils\JsonUtils;
use think\response\Json;

/**
 * 主页接口-控制器
 * Class Index
 * @package app\api\controller
 */
class Index extends Api
{
    public $notNeedLogin = ['index', 'hotSearch', 'config', 'ad', 'policy', 'customerService'];

    /**
     * 首页
     *
     * @author windy
     * @return Json
     */
    public function index(): Json
    {
        (new RequestValidate())->isGet();

        $detail = IndexLogic::index();
        return JsonUtils::success($detail);
    }

    /**
     * 配置
     *
     * @author windy
     * @return Json
     */
    public function config(): Json
    {
        (new RequestValidate())->isGet();

        $detail = IndexLogic::config();
        return JsonUtils::success($detail);
    }

    /**
     * 热搜词
     *
     * @author windy
     * @return Json
     */
    public function hotSearch(): Json
    {
        (new RequestValidate())->isGet();

        $lists = IndexLogic::hotSearch();
        return JsonUtils::success($lists);
    }

    /**
     * 广告
     *
     * @author windy
     * @return Json
     */
    public function ad(): Json
    {
        (new RequestValidate())->isGet();

        $lists = IndexLogic::ad($this->getData('position'));
        return JsonUtils::success($lists);
    }

    /**
     * 政策协议
     *
     * @author windy
     * @return Json
     */
    public function policy(): Json
    {
        (new RequestValidate())->isGet();

        $detail = IndexLogic::policy($this->getData('type'));
        return JsonUtils::success($detail);
    }

    /**
     * 微信小程序订阅消息
     *
     * @author windy
     * @return Json
     */
    public function subscribe(): Json
    {
        (new RequestValidate())->isGet();

        $detail = IndexLogic::subscribe();
        return JsonUtils::success($detail);
    }

    /**
     * 客服信息
     *
     * @author windy
     * @return Json
     */
    public function customerService(): Json
    {
        (new RequestValidate())->isGet();

        $detail = IndexLogic::customerService();
        return JsonUtils::success($detail);
    }
}