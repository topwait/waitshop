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

namespace app\common\service\message;


use app\common\enum\NoticeEnum;
use app\common\model\ConfigSms;
use app\common\model\log\LogSms;
use app\common\service\sms\SmsDriver;
use Exception;

/**
 * 短信消息服务类
 * Class SmsMessageService
 * @package app\common\service\message
 */
class SmsMessageService
{
    /**
     * 短信记录id
     */
    private $smsId;

    /**
     * 按场景发送短信
     *
     * @author windy
     * @param array $params
     * @param array $template
     * @return bool|string
     */
    public function send(array $params, array $template)
    {
        try {
            // 校验数据
            if (empty($params['mobile']) || $params['mobile'] == '' || empty($params['scene'])) {
                return false;
            }

            // 创建短信记录
            $content = $this->getContent($params, $template);
            $code = $this->getCode($params['scene'], $params['params'], $template);
            $this->smsId = LogSms::write($params['scene'], $params['mobile'], $content, $code);

            // 发送短信通知
            $result = (new SmsDriver())->sendSms([
                'mobile'  => $params['mobile'],
                'tplCode' => $template['sms_template']['template_code'],
                'params'  => $this->getSmsParams($params, $template)
            ]);

            // 是否发送成功
            if ($result === true) {
                LogSms::success($this->smsId, $result);
            } else {
                throw new Exception($result);
            }

            return true;
        } catch (Exception $e) {
            if ($this->smsId) {
                LogSms::fail($this->smsId, $e->getMessage());
            }
            return $e->getMessage();
        }
    }

    /**
     * 获取短信内容(替换模板变量)
     *
     * @author windy
     * @param array $params
     * @param array $template
     * @return mixed
     */
    private function getContent(array $params, array $template)
    {
        $content = $template['sms_template']['content'];
        foreach ($params['params'] as $item => $val) {
            $search_replace = '{' . $item . '}';
            $content = str_replace($search_replace, $val, $content);
        }
        return $content;
    }

    /**
     * 获取短信验证码(某些场景不需要)
     *
     * @author windy
     * @param $scene
     * @param $params
     * @param $template
     * @return array|mixed|string
     */
    private function getCode($scene, $params, $template)
    {
        $code = '';
        if (in_array($scene, NoticeEnum::SMS_NEED_CODE)) {
            $code = array_intersect_key($params, $template['variable']);
            if ($code) {
                return array_shift($code);
            }
        }
        return $code;
    }

    /**
     * 获取短信参数(对腾讯短信作特殊处理)
     *
     * @author windy
     * @param $params
     * @param $template
     * @return array|mixed
     */
    private function getSmsParams($params, $template): array
    {
        // 获取当前短信引擎
        $smsConfig = (new ConfigSms())->where(['is_enable'=>1])->findOrEmpty()->toArray();
        if (!$smsConfig || $smsConfig['alias'] != 'Tencent') {
            return $params['params'];
        }

        // 获取变量名数组
        $arr = [];
        $content = $template['sms_template']['content'];
        foreach ($params['params'] as $item => $val) {
            $search = '{' . $item . '}';
            if(strpos($content, $search) !== false && !in_array($item, $arr)) {
                $arr[] = $item;
            }
        }

        // 调整好顺序的变量名数组
        $arr2 = [];
        if (!empty($arr)) {
            foreach ($arr as $v) {
                $key = strpos($content, $v);
                $arr2[$key] = $v;
            }
        }

        // 以小到大的排序的数组
        ksort($arr2);
        $arr3 = array_values($arr2);

        // 获取到变量数组的对应的值
        $arr4 = [];
        foreach ($arr3 as $v2) {
            if(isset($params['params'][$v2])) {
                $arr4[] = $params['params'][$v2];
            }
        }
        return $arr4;
    }
}