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

namespace app\common\service\sms;


use app\common\model\ConfigSms;
use Exception;

class SmsDriver
{
    /**
     * 短信配置参数
     */
    protected $config;

    /**
     * 短信引擎
     */
    protected $engine;

    /**
     * 构造方法
     *
     * SmsDriver constructor.
     * @throws Exception
     */
    public function __construct()
    {
        $this->config = (new ConfigSms())->where(['is_enable'=>1])->findOrEmpty()->toArray();

        if (!$this->config) {
            throw new Exception('找不到短信相关配置');
        }

        if (empty($this->config['params'])) {
            throw new Exception('请配置短信相关参数');
        }

        $this->engine = $this->getEngineClass();
    }

    /**
     * 发送短信
     *
     * @author windy
     * @param array $data
     * @return mixed
     */
    public function sendSms(array $data)
    {
        return $this->engine
            ->setPhoneNumbers($data['mobile'])
            ->setTemplateCode($data['tplCode'])
            ->setTemplateParam($data['params'])
            ->send();
    }

    /**
     * 获取当前引擎驱动
     *
     * @author windy
     * @return mixed
     * @throws Exception
     */
    private function getEngineClass()
    {
        $engineName = $this->config['alias'];
        $classSpace = __NAMESPACE__ . '\\engine\\' . ucfirst($engineName).'Sms';

        if (!class_exists($classSpace)) {
            throw new Exception('未找到存储引擎类: ' . $engineName);
        }

        return new $classSpace($this->config['params']);
    }
}