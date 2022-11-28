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


use app\api\validate\RequestValidate;
use app\api\validate\SmsValidate;
use app\common\basics\Api;
use app\common\model\log\LogSms;
use app\common\utils\JsonUtils;
use Exception;
use think\response\Json;

/**
 * 短信发送接口-控制器层
 * Class Sms
 * @package app\api\controller
 */
class Sms extends Api
{
    public $notNeedLogin = ['send'];

    /**
     * 发送短信
     *
     * @author windy
     * @return Json
     */
    public function send(): Json
    {
        (new RequestValidate())->isPost();
        (new SmsValidate())->goCheck();

        try {
            event('Notice', [
                'scene' => intval($this->postData('scene')),
                'mobile' => intval($this->postData('mobile')),
                'params' => ['code' => create_random_code(new LogSms(), 'code', 4)]
            ]);

            return JsonUtils::ok('发送成功');
        } catch (Exception $e) {
            return JsonUtils::error($e->getMessage());
        }
    }
}