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

namespace app\common\service\storage\engine;

use Exception;
use Qcloud\Cos\Client;

/**
 * 腾讯云存储引擎 (COS)
 * Class Qiniu
 * @package app\common\library\storage\engine
 */
class Qcloud extends Server
{
    private $config;
    private $cosClient;

    /**
     * 构造方法
     *
     * @author windy
     * @param $config
     */
    public function __construct($config)
    {
        parent::__construct();
        $this->config = $config;
        $this->createCosClient();
    }

    /**
     * 创建COS控制类
     *
     * @author windy
     */
    private function createCosClient()
    {
        $this->cosClient = new Client([
            'region' => $this->config['region'],
            'credentials' => [
                'secretId' => $this->config['secret_id'],
                'secretKey' => $this->config['secret_key'],
            ],
        ]);
    }

    /**
     * 执行上传, 最大支持上传5G文件
     *
     * @author windy
     * @param $saveDir (保存路径)
     * @return bool|mixed
     */
    public function upload($saveDir): bool
    {
        try {
            // 1、设置上传文件信息
            $this->setUploadFile();
            $this->fileInfo['savePath'] = $saveDir.$this->fileInfo['fileName'];

            // 2、执行上传
             $this->cosClient->putObject([
                'Bucket' => $this->config['bucket'],
                'Key' => $saveDir . $this->fileInfo['fileName'],
                'Body' => fopen($this->fileInfo['realPath'], 'rb')
            ]);

            return true;
        } catch (Exception $e) {
            $this->error = $e->getMessage();
            return false;
        }
    }

    /**
     * 抓取远程资源 (大支持上传5G文件)
     *
     * @author windy
     * @param $url
     * @param null $key
     * @return mixed|void
     */
    public function fetch($url, $key=null): bool
    {
        try {
            $this->cosClient->putObject([
                'Bucket' => $this->config['bucket'],
                'Key'    => $key,
                'Body'   => fopen($url, 'rb')
            ]);
            return true;
        } catch (Exception $e) {
            $this->error = $e->getMessage();
            return false;
        }
    }

    /**
     * 删除文件
     *
     * @author windy
     * @param $fileName
     * @return bool|mixed
     */
    public function delete($fileName): bool
    {
        try {
             $this->cosClient->deleteObject(array(
                'Bucket' => $this->config['bucket'],
                'Key' => $fileName
            ));
            return true;
        } catch (Exception $e) {
            $this->error = $e->getMessage();
            return false;
        }
    }
}
