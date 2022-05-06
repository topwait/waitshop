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

namespace app\api\controller;


use app\api\logic\ShareLogic;
use app\api\validate\RequestValidate;
use app\common\basics\Api;
use app\common\utils\JsonUtils;
use think\response\Json;

/**
 * 分享海报接口-控制器层
 * Class Share
 * @package app\api\controller
 */
class Share extends Api
{
    /**
     * 生成分享海报图
     *
     * @author windy
     * @return Json
     */
    public function poster()
    {
        (new RequestValidate())->isGet();

        $result = false;
        switch ($this->getData('type')??'') {
            case 'user':
                $url = $this->getData('url');
                $result = ShareLogic::userPoster($this->userId, $this->client, $url);
                break;
            case 'goods':
                $gid = $this->getData('id');
                $url = $this->getData('url');
                $result = ShareLogic::goodsPoster($gid, $this->userId, $this->client, $url);
                break;
            case 'team':
                $tid = $this->getData('id');
                $url = $this->getData('url');
                $result = ShareLogic::teamPoster($tid, $this->userId, $this->client, $url);
                break;
            case 'seckill':
                $tid = $this->getData('id');
                $url = $this->getData('url');
                $result = ShareLogic::seckillPoster($tid, $this->userId, $this->client, $url);
        }

        if ($result === false) {
            return JsonUtils::error(ShareLogic::getError());
        }

        return JsonUtils::ok('获取成功', $result);
    }
}