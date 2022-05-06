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

namespace app\common\service\logistics\engine;


use Exception;

class Kd100
{
    /**
     * 授权key
     */
    private $appKey;

    /**
     * customer
     */
    private $customer;

    /**
     * 初始化配置
     *
     * AliyunSms constructor.
     * @param array $config
     */
    public function __construct(array $config)
    {
        $this->appKey = $config['appkey'] ?? '';
        $this->customer = $config['customer'] ?? '';
    }

    /**
     * 查询物流
     *
     * @author windy
     * @param string $code
     * @param string $number
     * @param array $data
     * @return array
     * @throws Exception
     */
    public function query(string $code, string $number, $data=[]): array
    {
        try {
            $param = json_encode([
                'com' => $code,
                'num' => $number,
                'phone' => $data['phone'] ?? '',
                'from' => $data['from'] ?? '',
                'to' => $data['to'] ?? '',
                'resultv2' => 0,
                'show' => 0,
                'order' => 'desc'
            ], JSON_UNESCAPED_UNICODE);

            $data = array(
                'customer' => $this->customer,
                'sign' => strtoupper(md5($param . $this->appKey . $this->customer)),
                'param' => $param,
            );

            $sign = "";
            foreach ($data as $k => $v) {
                $sign .= "$k=" . urlencode($v) . "&";
            }
            $sign = substr($sign, 0, -1);

            $result = curl_get('http://poll.kuaidi100.com/poll/query.do?' . $sign);
            $result = json_decode($result, true);

            if (!$result) {
                throw new Exception('查询异常');
            }

            if ($result['message'] != 'ok') {
                throw new Exception($result['message']);
            }

            if (!isset($result['data'])) {
                throw new Exception(json_encode($result, JSON_UNESCAPED_UNICODE));
            }

            return $result['data'];
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }
}