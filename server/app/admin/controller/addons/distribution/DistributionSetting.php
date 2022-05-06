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

namespace app\admin\controller\addons\distribution;


use app\admin\logic\addons\distribution\DistributionSettingLogic;
use app\common\basics\Backend;
use app\common\utils\ConfigUtils;
use app\common\utils\JsonUtils;
use think\response\Json;
use think\response\View;

/**
 * 分销设置-控制器层
 * Class DistributionSetting
 * @package app\admin\controller\addons\distribution
 */
class DistributionSetting extends Backend
{
    /**
     * 分销参数
     *
     * @author windy
     * @return View
     */
    public function index(): View
    {
        return view('addons/distribution/setting', [
            'detail' => ConfigUtils::get('distribution')
        ]);
    }

    /**
     * 设置分销参数
     *
     * @author windy
     * @return Json
     */
    public function set(): Json
    {
        if (request()->isAjax()) {
            $result = DistributionSettingLogic::setting($this->postData());
            if ($result === false) {
                return JsonUtils::error(DistributionSettingLogic::getError());
            }
            return JsonUtils::ok('设置成功');
        }

        return JsonUtils::error('请求异常');
    }
}