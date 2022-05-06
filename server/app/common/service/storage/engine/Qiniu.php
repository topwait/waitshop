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
use Qiniu\Auth;
use Qiniu\Storage\UploadManager;
use Qiniu\Storage\BucketManager;

/**
 * 七牛云存储引擎
 * Class Qiniu
 * @package app\common\library\storage\engine
 */
class Qiniu extends Server
{
    /**
     * 七牛的存储引擎配置信息
     *
     * @author windy
     * @var array
     */
    private $config;

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
    }

    /**
     * 执行上传
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

            // 2、构建鉴权对象,指向定空间,实例上传对象
            $auth = new Auth($this->config['access_key'], $this->config['secret_key']);
            $token = $auth->uploadToken($this->config['bucket']);
            $uploadMgr = new UploadManager();

            // 3、执行上传
            $key = $saveDir . '/' . $this->fileInfo['fileName'];
            list(, $err) = $uploadMgr->putFile($token, $key, $this->file);

            // 4、校验是否上传失败
            if ($err !== null) {
                $this->error = $err->message();
                return false;
            }

            return true;
        } catch (Exception $e) {
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
     * @return bool
     */
    public function fetch($url, $key=null): bool
    {
        try {
            // 1、执行抓取资源
            if (substr($url, 0, 1) !== '/' || strstr($url, 'http://') || strstr($url, 'https://')) {
                $auth = new Auth($this->config['access_key'], $this->config['secret_key']);
                $bucketManager = new BucketManager($auth);
                list(, $err) = $bucketManager->fetch($url, $this->config['bucket'], $key);
            } else {
                $auth = new Auth($this->config['access_key'], $this->config['secret_key']);
                $token = $auth->uploadToken($this->config['bucket']);
                $uploadMgr = new UploadManager();
                list(, $err) = $uploadMgr->putFile($token, $key, $url);
            }

            // 2、校验是否上传失败
            if ($err !== null) {
                $this->error = $err->message();
                return false;
            }

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
            // 1、构建鉴权对象
            $auth = new Auth($this->config['access_key'], $this->config['secret_key']);
            $bucketMgr = new BucketManager($auth);

            // 2、执行删除
            $err = $bucketMgr->delete($this->config['bucket'], $fileName);

            // 3、校验是否删除失败
            if ($err !== null) {
                $this->error = $err;
                return false;
            }

            return true;
        } catch (Exception $e) {
            $this->error = $e->getMessage();
            return false;
        }
    }
}
