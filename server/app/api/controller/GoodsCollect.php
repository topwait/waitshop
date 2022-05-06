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


use app\api\logic\CollectLogic;
use app\api\validate\GoodsCollectValidate;
use app\api\validate\PageValidate;
use app\api\validate\RequestValidate;
use app\common\basics\Api;
use app\common\utils\JsonUtils;
use think\response\Json;

/**
 * 用户收藏接口-控制器层
 * Class Collect
 * @package app\api\controller
 */
class GoodsCollect extends Api
{
    /**
     * 用户收藏列表
     *
     * @author windy
     * @return Json
     */
    public function lists(): Json
    {
        (new RequestValidate())->isGet();
        (new PageValidate())->goCheck();

        $lists = CollectLogic::lists($this->getData(), $this->userId);
        return JsonUtils::success($lists);
    }

    /**
     * 收藏商品
     *
     * @author windy
     * @return Json
     */
    public function add(): Json
    {
        (new RequestValidate())->isPost();
        (new GoodsCollectValidate())->goCheck('add');

        $goodsId = $this->postData('goods_id');
        $status  = $this->postData('status');

        $result = CollectLogic::add($status, $goodsId, $this->userId);
        if ($result === false) {
            return JsonUtils::error(CollectLogic::getError());
        }

        $message = $status ? '收藏成功' : '取消成功';
        return JsonUtils::ok($message);
    }

    /**
     * 删除收藏
     *
     * @author windy
     * @return Json
     */
    public function del(): Json
    {
        (new RequestValidate())->isPost();
        (new GoodsCollectValidate())->goCheck('id');

        $result = CollectLogic::del($this->postData('id'));
        if ($result === false) {
            return JsonUtils::error(CollectLogic::getError());
        }

        return JsonUtils::ok('删除成功');
    }
}