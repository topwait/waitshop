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


use app\api\logic\DiyLogic;
use app\api\validate\IDMustBePositiveInt;
use app\api\validate\RequestValidate;
use app\common\basics\Api;
use app\common\utils\JsonUtils;
use think\response\Json;

/**
 * 页面设计接口-控制器层
 * Class Diy
 * @package app\api\controller
 */
class Diy extends Api
{
    public $notNeedLogin = ['index'];

    /**
     * 自定义页面
     *
     * @author windy
     * @return Json
     * @throws @\think\Exception
     */
    public function index(): Json
    {
        (new RequestValidate())->isGet();

        $pageId = $this->request->get('id', 0);
        $data = DiyLogic::getPageData($pageId, $this->userId);
        if ($data === false) {
            return JsonUtils::error(DiyLogic::getError());
        }

        return JsonUtils::success($data);
    }
}