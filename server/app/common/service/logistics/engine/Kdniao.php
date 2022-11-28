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

namespace app\common\service\logistics\engine;


use Exception;

class Kdniao
{
    /**
     * 授权key
     */
    private $appKey;

    /**
     * EBussinessID
     */
    private $ebussinessid;

    /**
     * 初始化配置
     *
     * AliyunSms constructor.
     * @param array $config
     */
    public function __construct(array $config)
    {
        $this->appKey = $config['appkey'] ?? '';
        $this->ebussinessid = $config['ebussinessid'] ?? '';
    }

    /**
     * 查询物流
     *
     * @author windy
     * @param string $code
     * @param string $number
     * @param array $data
     * @return bool|mixed|string
     * @throws Exception
     */
    public function query(string $code, string $number, $data=[])
    {
        try {
            $param = json_encode([
                'OrderCode' => '',
                'ShipperCode' => $code,
                'LogisticCode' => $number,
                'CustomerName' => $data['CustomerName']
            ], JSON_UNESCAPED_UNICODE);

            $data = [
                'EBusinessID' => $this->ebussinessid,
                'RequestType' => '1002',
                'RequestData' => urlencode($param),
                'DataType' => '2',
                'DataSign' => urlencode(base64_encode(md5($param . $this->appKey))),
            ];

            $result = curl_post('https://api.kdniao.com/Ebusiness/EbusinessOrderHandle.aspx', $data);
            $result = json_decode($result, true);

            if (!$result) {
                throw new Exception('查询异常');
            }

            if (!isset($result['Traces'])) {
                throw new Exception(json_encode($result, JSON_UNESCAPED_UNICODE));
            }

            return $result;
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }
}