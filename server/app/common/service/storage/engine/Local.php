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

namespace app\common\service\storage\engine;


use Exception;
use think\facade\Filesystem;

/**
 * 本地上传
 * Class Local
 * @package app\common\service\storage\engine
 */
class Local extends Server
{
    /**
     * 本地的存储引擎配置信息
     * @var array
     */
    private $config;

    /**
     * 初始化
     *
     * @author windy
     * Local constructor.
     * @param $config
     */
    public function __construct($config)
    {
        parent::__construct();
        $this->config = $config;
    }

    /**
     * 上传文件
     *
     * @param string $saveDir (保存路径, 如：/uploads/images/)
     * @return bool
     * @author windy
     */
    public function upload(string $saveDir): bool
    {
        try {
            // 1、设置上传文件信息
            $this->setUploadFile();
            $this->fileInfo['savePath'] = $saveDir.$this->fileInfo['fileName'];

            // 2、处理上传路径
            $pathArr = explode('/', $this->fileInfo['fileName']);
            $saveDir = $saveDir . $pathArr[0] . '/';

            // 2、执行上传文件
            (Filesystem::instance())->disk('public')
                ->putFileAs($saveDir, $this->file, $pathArr[1]);

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
     * @param string $url (http文件地址, 如: http://aa.jpg)
     * @param null $key (要保存的路径名称)
     * @return bool|null
     */
    public function fetch(string $url, $key=null): ?bool
    {
        try {
            $content = file_get_contents($url);
            file_put_contents($key, $content);
            return $key;
        } catch (Exception $e) {
            $this->error = $e->getMessage();
            return false;
        }
    }

    /**
     * 删除文件
     *
     * @author windy
     * @param $fileName (文件路径,如: /uploads/a.jpg)
     * @return bool
     */
    public function delete($fileName): bool
    {
        $filePath = public_path() . $fileName;
        return !file_exists($filePath) ?: unlink($filePath);
    }
}