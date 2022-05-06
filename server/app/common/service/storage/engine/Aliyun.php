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

use OSS\OssClient;
use OSS\Core\OssException;

/**
 * 阿里云存储引擎 (OSS)
 * Class Qiniu
 * @package app\common\library\storage\engine
 */
class Aliyun extends Server
{
    /**
     * 阿里云OSS的存储引擎配置信息
     * @author windy
     * @var array
     */
    private $config;

    /**
     * 构造方法
     * @author windy
     * Aliyun constructor.
     * @param $config
     */
    public function __construct($config)
    {
        parent::__construct();
        $this->config = $config;
    }

    /**
     * 执行上传
     *
     * @author windy
     * @param $saveDir (保存路径)
     * @return bool|mixed
     * @throws @\Exception
     */
    public function upload($saveDir): bool
    {
        try {
            // 1、设置上传文件信息
            $this->setUploadFile();
            $this->fileInfo['savePath'] = $saveDir.$this->fileInfo['fileName'];

            // 2、构建上传对象
            $ossClient = new OssClient(
                $this->config['access_key_id'],
                $this->config['access_key_secret'],
                $this->config['domain'],
                true
            );

            // 3、执行上传
            $ossClient->uploadFile(
                $this->config['bucket'],
                $saveDir . $this->fileInfo['fileName'],
                $this->fileInfo['realPath']
            );

            return true;
        } catch (OssException $e) {
            $this->error = $e->getMessage();
            return false;
        }
    }

    /**
     * 抓取远程资源
     *
     * @author windy
     * @param $url
     * @param null $key
     * @return mixed|void
     */
    public function fetch($url, $key = null): bool
    {
        try {
            // 1、构建上传对象
            $ossClient = new OssClient(
                $this->config['access_key_id'],
                $this->config['access_key_secret'],
                $this->config['domain'],
                true
            );

            // 2、下载获取文件并保存
            $content = file_get_contents($url);
            $ossClient->putObject(
                $this->config['bucket'],
                $key,
                $content
            );

            return true;
        } catch (OssException $e) {
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
            // 1、构建对象
            $ossClient = new OssClient(
                $this->config['access_key_id'],
                $this->config['access_key_secret'],
                $this->config['domain'],
                true
            );

            // 2、执行删除
            $ossClient->deleteObject($this->config['bucket'], $fileName);

            return true;
        } catch (OssException $e) {
            $this->error = $e->getMessage();
            return false;
        }
    }
}
