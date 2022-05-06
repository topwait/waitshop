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

namespace app\common\service\logistics;


use app\common\utils\ConfigUtils;
use Exception;

class Driver
{
    /**
     * 配置参数
     * @var array
     */
    protected $config;

    /**
     * 物流引擎
     * @var mixed
     */
    protected $engine;

    /**
     * 构造方法
     *
     * @author windy
     * SmsDriver constructor.
     * @param array $config
     * @throws Exception
     */
    public function __construct(array $config)
    {
        $this->config = $config;

        if (!$this->config) {
            throw new Exception('找不到物流相关配置');
        }

        $this->engine = $this->getEngineClass();
    }

    /**
     * 查询物流
     *
     * @param string $code
     * @param string $number
     * @param array $data
     * @return mixed
     */
    public function query(string $code, string $number, $data=[])
    {
        return $this->engine->query($code, $number, $data);
    }

    /**
     * 获取当前引擎驱动
     *
     * @return mixed
     * @throws Exception
     */
    private function getEngineClass()
    {
        $engineName = $this->config['way'];
        $classSpace = __NAMESPACE__ . '\\engine\\' . ucfirst($engineName);

        if (!class_exists($classSpace)) {
            throw new Exception('未找到物流引擎类: ' . $engineName);
        }

        return new $classSpace($this->config[$this->config['way']]);
    }
}