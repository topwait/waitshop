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

namespace app\admin\controller;


use app\common\basics\Backend;
use app\common\utils\ConfigUtils;
use app\common\model\File;
use app\common\utils\JsonUtils;
use app\common\utils\UrlUtils;
use app\common\service\storage\Driver as StorageDriver;
use Exception;
use think\App;
use think\response\Json;

/**
 * 上传-控制器层
 * Class Upload
 * @package app\admin\controller
 */
class Upload extends Backend
{
    /**
     * 配置
     * @var array
     */
    private $config;

    /**
     * 初始化
     *
     * Upload constructor.
     * @param App $app
     */
    public function __construct(App $app)
    {
        parent::__construct($app);
        $this->config = ConfigUtils::get('storage', [
            'default' => 'local',
            'engine'  => array('local'=>[])
        ]);
    }

    /**
     * 上传图片
     *
     * @author windy
     * @param int $group_id (分组ID)
     * @param string $save_dir (保存路径)
     * @return Json
     */
    public function image(int $group_id=0, $save_dir='uploads/image/'): Json
    {
        try {
            $limitSize = (1024 * 1024 * 10);
            validate(['iFile' => 'fileSize:' . $limitSize . '|fileExt:jpg,jpeg,png,gif,bmp'])->check(request()->file());

            $StorageDriver = new StorageDriver($this->config);
            if ($StorageDriver->upload($save_dir)) {
                // 1、获取上传文件的信息
                $fileInfo = $StorageDriver->getFileInfo();

                // 2、把上传文件记录到库
                $fileModel = $this->addUploadFile($group_id, $fileInfo, 1);

                // 3、返回成功的结果
                return JsonUtils::ok('上传成功', [
                    'file_id' => $fileModel['id'],
                    'name'    => $fileInfo['name'],
                    'uri'     => $fileInfo['savePath'],
                    'image'   => UrlUtils::getAbsoluteUrl($fileInfo['savePath'])
                ]);
            }

            return JsonUtils::error($StorageDriver->getError());
        } catch (Exception $e) {
            return JsonUtils::error($e->getMessage());
        }
    }

    /**
     * 上传视频
     *
     * @author windy
     * @param int $group_id
     * @param string $save_dir
     * @return Json
     */
    public function video(int $group_id=0, $save_dir='uploads/video/'): Json
    {
        try {
            $limitSize = (1024 * 1024 * 30);
            validate(['iFile' => 'fileSize:' . $limitSize . '|fileExt:mp4,mp3,avi,rmvb'])->check(request()->file());

            $StorageDriver = new StorageDriver($this->config);
            if ($StorageDriver->upload($save_dir)) {
                // 1、获取上传文件的信息
                $fileInfo = $StorageDriver->getFileInfo();
                // 2、把上传文件记录到库
                $fileModel = $this->addUploadFile($group_id, $fileInfo, 2);
                // 3、返回成功的结果
                return JsonUtils::ok('上传成功', [
                    'file_id' => $fileModel['id'],
                    'name'    => $fileInfo['name'],
                    'uri'     => $fileInfo['savePath'],
                    'video'   => UrlUtils::getAbsoluteUrl($fileInfo['savePath'])
                ]);
            }

            return JsonUtils::error($StorageDriver->getError());
        } catch (Exception $e) {
            return JsonUtils::error($e->getMessage());
        }
    }

    /**
     * 上传其他
     *
     * @author windy
     * @param string $save_dir (保存路径)
     * @return Json
     */
    public function other($save_dir='uploads/other/'): Json
    {
        try {
            $StorageDriver = new StorageDriver($this->config);
            if ($StorageDriver->upload($save_dir)) {
                // 1、获取上传文件的信息
                $fileInfo = $StorageDriver->getFileInfo();

                // 2、返回成功的结果
                return JsonUtils::ok('上传成功', [
                    'name'    => $fileInfo['name'],
                    'uri'     => $fileInfo['savePath'],
                    'image'   => UrlUtils::getAbsoluteUrl($fileInfo['savePath'])
                ]);
            }

            return JsonUtils::error($StorageDriver->getError());
        } catch (Exception $e) {
            return JsonUtils::error($e->getMessage());
        }
    }

    /**
     * 上传证书
     *
     * @author windy
     * @param string $save_dir (保存路径)
     * @return Json
     */
    public function pem($save_dir='uploads/pem/'): Json
    {
        try {
            $limitSize = (1024 * 1024 * 30);
            validate(['iFile' => 'fileSize:' . $limitSize . '|fileExt:pem,txt'])->check(request()->file());

            $StorageDriver = new StorageDriver($this->config, 'local');
            if ($StorageDriver->upload($save_dir)) {
                // 1、获取上传文件的信息
                $fileInfo = $StorageDriver->getFileInfo();

                // 2、返回成功的结果
                return JsonUtils::ok('上传成功', [
                    'name' => $fileInfo['name'],
                    'uri'  => $fileInfo['savePath'],
                ]);
            }

            return JsonUtils::error($StorageDriver->getError());
        } catch (Exception $e) {
            return JsonUtils::error($e->getMessage());
        }
    }

    /**
     * 添加文件库上传记录
     *
     * @author windy
     * @param int $group_id (组ID)
     * @param array $fileInfo (文件信息)
     * @param string $fileType (文件类型[image, video])
     * @return File
     */
    private function addUploadFile(int $group_id, array $fileInfo, string $fileType): File
    {
        return File::create([
            'group_id'  => $group_id > 0 ? intval($group_id) : 0,
            'file_type' => $fileType,
            'file_url'  => $fileInfo['savePath'],
            'file_name' => $fileInfo['name'],
            'file_ext'  => $fileInfo['ext'],
            'file_size' => $fileInfo['size'],
            'file_mime' => $fileInfo['mime']
        ]);
    }
}