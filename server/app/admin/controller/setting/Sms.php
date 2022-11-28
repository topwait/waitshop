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

namespace app\admin\controller\setting;


use app\admin\logic\setting\SmsLogic;
use app\admin\validate\setting\SmsValidate;
use app\common\basics\Backend;
use app\common\utils\JsonUtils;
use think\response\Json;
use think\response\View;

/**
 * 短信配置-控制器层
 * Class Sms
 * @package app\admin\controller\setting
 */
class Sms extends Backend
{
    /**
     * 短信渠道列表
     *
     * @author windy
     * @return View|Json
     */
    public function lists()
    {
        if (request()->isAjax()) {
            $lists = SmsLogic::lists($this->getData());
            return JsonUtils::success($lists);
        }

        return view('setting/sms/lists');
    }

    /**
     * 设置短信参数
     *
     * @author windy
     * @return View|Json
     */
    public function set()
    {
        if (request()->isAjax()) {
            (new SmsValidate())->goCheck();
            $result = SmsLogic::set($this->postData());
            if ($result === false) {
                return JsonUtils::error(SmsLogic::getError());
            }
            return JsonUtils::ok('配置成功');
        }

        return view('setting/sms/'.strtolower($this->getData('alias')), [
            'detail' => SmsLogic::detail($this->getData('alias'))
        ]);
    }
}