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

namespace app\common\utils;


use app\common\model\ConfigSms;
use app\common\service\sms\engine\AliyunSms;
use Exception;
use think\App;

/**
 * 短信服务
 * Class SmsUtils
 * @package app\common\utils
 */
class SmsUtils
{
    /**
     * 发送短信
     *
     * @author windy
     * @param $param
     */
    public static function sendSms($param)
    {
        try {
            $smsChannel = (new ConfigSms())->where(['is_enable' => 0])->select()->toArray();
            $smsChannel = $smsChannel[mt_rand(0, count($smsChannel) - 1)];

            switch ($smsChannel['alias']) {
                case 'Aliyun':
                    (new AliyunSms($smsChannel))
                        ->setPhoneNumbers($param['mobile'])
                        ->setTemplateCode($param['code'])
                        ->setTemplateParam($param['params'])
                        ->send();
                    break;
                case 'Tencent':
                    break;

            }
        } catch (Exception $e) {}
    }
}