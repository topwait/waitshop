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

namespace app\admin\controller\addons;


use app\admin\logic\addons\AdLogic;
use app\common\basics\Backend;
use app\common\enum\AdEnum;
use app\common\utils\JsonUtils;
use think\response\Json;
use think\response\View;

/**
 * 营销广告-控制器层
 * Class Ad
 * @package app\admin\controller\marketing
 */
class Ad extends Backend
{
    /**
     * 广告列表
     *
     * @author windy
     * @return View|Json
     */
    public function lists()
    {
        if (request()->isAjax()) {
            $lists = AdLogic::lists($this->getData());
            return JsonUtils::success($lists);
        }

        return view('addons/ad/lists');
    }

    /**
     * 新增广告
     *
     * @author windy
     * @return View|Json
     */
    public function add()
    {
        if (request()->isAjax()) {
            $result = AdLogic::add($this->postData());
            if ($result === false) {
                return JsonUtils::error(AdLogic::getError());
            }
            return JsonUtils::ok('新增成功');
        }

        return view('addons/ad/add', [
            'position' => AdEnum::getPositionDesc(true),
            'linkType' => AdEnum::getLinkTypeDesc(true)
        ]);
    }

    /**
     * 编辑广告
     *
     * @author windy
     * @return View|Json
     */
    public function edit()
    {
        if (request()->isAjax()) {
            $result = AdLogic::edit($this->postData());
            if ($result === false) {
                return JsonUtils::error(AdLogic::getError());
            }
            return JsonUtils::ok('编辑成功');
        }

        return view('addons/ad/edit', [
            'detail' => AdLogic::detail($this->getData('id')),
            'position' => AdEnum::getPositionDesc(true),
            'linkType' => AdEnum::getLinkTypeDesc(true)
        ]);
    }

    /**
     * 删除广告
     *
     * @author windy
     * @return Json
     */
    public function del(): Json
    {
        if (request()->isAjax()) {
            $result = AdLogic::del($this->postData('id'));
            if ($result === false) {
                return JsonUtils::error(AdLogic::getError());
            }
            return JsonUtils::ok('删除成功');
        }

        return JsonUtils::error('请求异常');
    }
}