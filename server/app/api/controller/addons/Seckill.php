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


use app\api\logic\addons\SeckillLogic;
use app\api\validate\IDMustBePositiveInt;
use app\api\validate\PageValidate;
use app\api\validate\RequestValidate;
use app\common\basics\Api;
use app\common\utils\JsonUtils;
use think\response\Json;

/**
 * 秒杀接口-控制器层
 * Class Seckill
 * @package app\api\controller\addons
 */
class Seckill extends Api
{
    public $notNeedLogin = ['lists', 'detail'];

    /**
     * 秒杀商品列表
     *
     * @author windy
     * @return Json
     */
    public function lists(): Json
    {
        (new RequestValidate())->isGet();
        (new PageValidate())->goCheck();

        $lists = SeckillLogic::lists($this->getData());
        return JsonUtils::success($lists);
    }

    /**
     * 秒杀商品详细
     *
     * @author windy
     * @return Json
     */
    public function detail(): Json
    {
        (new RequestValidate())->isGet();
        (new IDMustBePositiveInt())->goCheck();

        $detail = SeckillLogic::detail($this->getData('id'), $this->userId);
        if ($detail === false) {
            return JsonUtils::error(SeckillLogic::getError());
        }
        return JsonUtils::success($detail);
    }

}