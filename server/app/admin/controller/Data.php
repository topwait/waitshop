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


use app\admin\logic\DataLogic;
use app\admin\logic\goods\CategoryLogic;
use app\common\basics\Backend;
use app\common\utils\JsonUtils;
use think\response\Json;
use think\response\View;

/**
 * 通用数据管理-控制器层
 * Class Data
 * @package app\admin\controller
 */
class Data extends Backend
{
    /**
     * 用户列表
     * 
     * @author windy
     * @return View|Json
     */
    public function user()
    {
        if (request()->isAjax()) {
            $lists = DataLogic::getUser($this->getData());
            return JsonUtils::success($lists);
        }

        return view('data/user', [
            'type' => $this->getData('type', 'radio')
        ]);
    }

    /**
     * 普通商品列表
     *
     * @author windy
     * @return View|Json
     */
    public function goods()
    {
        if (request()->isAjax()) {
            $lists = DataLogic::getGoods($this->getData());
            return JsonUtils::success($lists);
        }

        return view('data/goods', [
            'type'  => $this->getData('type'),
            'scene' => $this->getData('scene'),
            'treeCategory' => CategoryLogic::getTreeHtmlCategory()
        ]);
    }

    /**
     * 拼团商品列表
     *
     * @author windy
     * @return View|Json
     */
    public function teamGoods()
    {
        if (request()->isAjax()) {
            $lists = DataLogic::getTeamGoods($this->getData());
            return JsonUtils::success($lists);
        }

        return view('data/teamGoods', [
            'type'  => $this->getData('type'),
            'scene' => $this->getData('scene')
        ]);
    }
}