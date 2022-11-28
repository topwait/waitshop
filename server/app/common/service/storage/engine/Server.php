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

abstract class Server
{
    protected $error;    //错误信息
    protected $file;     //文件句柄
    protected $fileInfo; //文件信息

    // 需继承重写的方法
    protected function __construct() {}
    abstract protected function fetch(string $url, $key);
    abstract protected function upload(string $saveDir);
    abstract protected function delete($fileName);

    // 获取信息数据的方法
    public function getError() { return $this->error; }
    public function getFileInfo() { return $this->fileInfo; }

    /**
     * 置上传的文件信息
     *
     * @author windy
     * @return bool
     * @throws Exception
     */
    protected function setUploadFile(): bool
    {
        try {
            // 1、接收上传的文件
            $this->file = request()->file('iFile') ?? null;

            // 2、判断是否有文件
            if (empty($this->file)) {
                throw new Exception('未找到上传文件的信息');
            }

            // 3、记录上传文件信息
            $this->fileInfo = [
                'ext'      => $this->file->extension(),
                'size'     => $this->file->getSize(),
                'mime'     => $this->file->getMime(),
                'name'     => $this->file->getOriginalName(),
                'realPath' => $this->file->getRealPath(),
            ];
            $this->fileInfo['fileName'] = $this->buildSaveName();

            return true;
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    /**
     * 生成保存文件名
     *
     * @author windy
     * @return string
     */
    protected function buildSaveName(): string
    {
        // 要上传文件的本地路径
        $realPath = $this->getFileInfo()['realPath'];
        // 扩展名
        $ext = strtolower($this->getFileInfo()['ext']);
        // 自动生成文件名
        return date('Ymd') . '/' . date('His')
            . substr(md5($realPath), 0, 8)
            . substr(md5(microtime()), 5, 10)
            . str_pad(rand(0, 9999), 5, '0', STR_PAD_LEFT)
            . ".{$ext}";
    }
}