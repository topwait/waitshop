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

namespace app\admin\controller\diy;


use app\admin\logic\diy\StyleLogic;
use app\common\basics\Backend;
use app\common\utils\JsonUtils;
use think\response\Json;
use think\response\View;

/**
 * 风格装饰设计-控制器层
 * Class DiyStyle
 * @package app\admin\controller\diy
 */
class Style extends Backend
{
    /**
     * 风格装饰页面
     *
     * @author windy
     * @return View
     */
    public function index(): View
    {
        return view('diy/style/index', [
            'detail' => StyleLogic::detail()
        ]);
    }

    /**
     * 风格装饰修改
     *
     * @author windy
     * @return Json
     */
    public function set(): Json
    {
        if (request()->isAjax()) {
            $result = StyleLogic::set($this->postData());
            if ($result === false) {
                return JsonUtils::error(StyleLogic::getError());
            }
            return JsonUtils::ok('配置成功');
        }

        return JsonUtils::error('请求异常');
    }
}