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


use app\api\validate\GoodsValidate;
use app\api\logic\GoodsLogic;
use app\api\validate\PageValidate;
use app\api\validate\RequestValidate;
use app\common\basics\Api;
use app\common\utils\JsonUtils;
use think\response\Json;

/**
 * 商品接口-控制器层
 * Class Goods
 * @package app\api\controller
 */
class Goods extends Api
{
    public $notNeedLogin = ['lists', 'detail', 'category', 'recommend'];

    /**
     * 商品列表
     *
     * @author windy
     * @return Json
     */
    public function lists(): Json
    {
        (new RequestValidate())->isGet();
        (new PageValidate())->goCheck();
        (new GoodsValidate())->goCheck('list');

        $lists = GoodsLogic::lists($this->getData());
        return JsonUtils::success($lists);
    }

    /**
     * 商品详细
     *
     * @author windy
     * @return Json
     */
    public function detail(): Json
    {
        (new RequestValidate())->isGet();
        (new GoodsValidate())->goCheck('id');

        $detail = GoodsLogic::detail($this->getData('id'), $this->userId);
        return JsonUtils::success($detail);
    }

    /**
     * 推荐商品
     *
     * @author windy
     * @return Json
     */
    public function recommend(): Json
    {
        (new RequestValidate())->isGet();
        (new GoodsValidate())->goCheck('type');

        $lists = GoodsLogic::recommend($this->getData());
        return JsonUtils::success($lists);
    }
}