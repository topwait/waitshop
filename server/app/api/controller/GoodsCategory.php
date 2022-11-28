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


use app\api\logic\GoodsCategoryLogic;
use app\api\validate\PageValidate;
use app\api\validate\RequestValidate;
use app\common\basics\Api;
use app\common\utils\JsonUtils;
use think\response\Json;


/**
 * 商品分类接口-控制器层
 * Class Category
 * @package app\api\controller
 */
class GoodsCategory extends Api
{
    public $notNeedLogin = ['lists', 'skin', 'goods'];

    /**
     * 分类列表
     *
     * @author windy
     * @return Json
     */
    public function lists(): Json
    {
        (new RequestValidate())->isGet();

        $lists = GoodsCategoryLogic::lists();
        return JsonUtils::success($lists);
    }

    /**
     * 分类风格
     *
     * @author windy
     * @return Json
     */
    public function skin(): Json
    {
        (new RequestValidate())->isGet();

        $result = GoodsCategoryLogic::skin();
        return JsonUtils::success($result);
    }

    /**
     * 分类商品
     *
     * @author windy
     * @return Json
     */
    public function goods(): Json
    {
        (new RequestValidate())->isGet();
        (new PageValidate())->goCheck();

        $lists = GoodsCategoryLogic::goods($this->getData(), $this->userId);
        return JsonUtils::success($lists);
    }
}