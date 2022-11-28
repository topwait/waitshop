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

namespace app\api\controller;


use app\api\validate\RequestValidate;
use app\common\basics\Api;
use app\common\utils\ConfigUtils;
use app\common\utils\JsonUtils;
use app\common\service\storage\Driver as StorageDriver;
use app\common\utils\UrlUtils;
use Exception;
use think\App;
use think\response\Json;

/**
 * 上传文件接口-控制器层
 * Class Upload
 * @package app\api\controller
 */
class Upload extends Api
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
     * 用户上传图片
     *
     * @author windy
     * @return Json
     */
    public function image(): Json
    {
        try {
            (new RequestValidate())->isPost();
            $dir = $this->postData('dir') ?? '';

            $StorageDriver = new StorageDriver($this->config);
            if ($StorageDriver->upload('uploads/user/'.$dir)) {
                $fileInfo = $StorageDriver->getFileInfo();
                return JsonUtils::ok('上传成功', [
                    'image' => UrlUtils::getAbsoluteUrl($fileInfo['savePath'])
                ]);
            }

            return JsonUtils::error($StorageDriver->getError());
        } catch (Exception $e) {
            return JsonUtils::error($e->getMessage());
        }
    }
}