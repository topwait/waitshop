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

namespace app\common\listener;


use app\common\model\MessageTemplate;
use app\common\service\message\OaMessageService;
use app\common\service\message\SmsMessageService;
use app\common\service\message\WxMessageService;
use Exception;

/**
 * 消息通知钩子
 * Class Notice
 * @package app\common\listener
 */
class Notice
{
    /**
     * 场景通知处理函数
     *
     * @author windy
     * @author windy
     * @param array $params
     * @return bool
     * @throws Exception
     */
    public function handle(array $params): bool
    {
        $result = false;
        try {
            // 验证场景参数
            if (empty($params['scene'])) {
                throw new Exception('缺少scene场景参数');
            }

            // 获取场景模板
            $template = (new MessageTemplate())->where(['scene' => $params['scene']])->findOrEmpty()->toArray();
            if (empty($template)) {
                throw new Exception('消息场景不存在');
            }

            // 发送短信通知
            if (isset($template['sms_template']['status']) and $template['sms_template']['status'] == 1) {
                $result = (new SmsMessageService())->send($params, $template);
            }

            // 公众号消息通知
            if (isset($template['oa_template']['status']) && $template['oa_template']['status'] == 1) {
                $result = (new OaMessageService($params['user_id']))->send($params, $template);
            }

            // 小程序订阅消息
            if (isset($template['mnp_template']['status']) && $template['mnp_template']['status'] == 1) {
                $result = (new WxMessageService($params['user_id']))->send($params, $template['mnp_template']);
            }

            // 验证发送结果
            if ($result !== true) {
                throw new Exception($result);
            }

            return true;
        } catch (Exception $e) {}

        return $result;
    }
}