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


use app\api\logic\GoodsCommentLogic;
use app\api\validate\GoodsCommentValidate;
use app\api\validate\IDMustBePositiveInt;
use app\api\validate\PageValidate;
use app\api\validate\RequestValidate;
use app\common\basics\Api;
use app\common\utils\JsonUtils;
use think\response\Json;

/**
 * 商品评论接口-控制器层
 * Class GoodsComment
 * @package app\api\controller
 */
class GoodsComment extends Api
{
    public $notNeedLogin = ['whole'];

    /**
     * 根据商品ID获取全部评论
     *
     * @author windy
     * @return Json
     */
    public function whole(): Json
    {
        (new RequestValidate())->isGet();
        (new IDMustBePositiveInt())->goCheck();

        $lists = GoodsCommentLogic::whole($this->getData());
        return JsonUtils::success($lists);
    }

    /**
     * 评论列表
     *
     * @author windy
     * @return Json
     */
    public function lists(): Json
    {
        (new RequestValidate())->isGet();
        (new PageValidate())->goCheck();

        $type = $this->getData('type') ?? 0;
        if ($type == 0) {
            $lists = GoodsCommentLogic::stay($this->getData(), $this->userId);
        } else {
            $lists = GoodsCommentLogic::already($this->getData(), $this->userId);
        }

        return JsonUtils::success($lists);
    }

    /**
     * 商品信息
     *
     * @author windy
     * @return Json
     */
    public function goods(): Json
    {
        (new RequestValidate())->isGet();
        (new IDMustBePositiveInt())->goCheck();

        $detail = GoodsCommentLogic::goods($this->getData('id'));
        return JsonUtils::success($detail);
    }

    /**
     * 发表评论
     *
     * @author windy
     * @return Json
     */
    public function add(): Json
    {
        (new RequestValidate())->isPost();
        (new GoodsCommentValidate())->goCheck();

        $result = GoodsCommentLogic::add($this->postData(), $this->userId);
        if ($result === false) {
            return JsonUtils::error(GoodsCommentLogic::getError());
        }
        return JsonUtils::ok('评论成功');
    }
}