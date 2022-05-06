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

namespace app\admin\controller\addons\distribution;


use app\admin\logic\addons\distribution\DistributionGoodsLogic;
use app\admin\logic\goods\CategoryLogic;
use app\admin\logic\goods\GoodsLogic;
use app\common\basics\Backend;
use app\common\utils\JsonUtils;
use think\response\Json;
use think\response\View;

/**
 * 分销商品-控制器层
 * Class DistributionGoods
 * @package app\admin\controller\addons\distribution
 */
class DistributionGoods extends Backend
{
    /**
     * 分销商品列表
     *
     * @author windy
     * @return View|Json
     */
    public function lists()
    {
        if (request()->isAjax()) {
            $lists = DistributionGoodsLogic::lists($this->getData());
            return JsonUtils::success($lists);
        }

        return view('addons/distribution/goods/lists', [
            'treeCategory' => CategoryLogic::getTreeHtmlCategory()
        ]);
    }

    /**
     * 新增分销商品
     *
     * @author windy
     * @return View|Json
     */
    public function add()
    {
        if (request()->isAjax()) {
            $result = DistributionGoodsLogic::add($this->postData());
            if ($result === false) {
                return JsonUtils::error(DistributionGoodsLogic::getError());
            }
            return JsonUtils::ok('新增成功');
        }

        return view('addons/distribution/goods/add', [
            'goodsDetail' => GoodsLogic::detail($this->getData('goods_id'))
        ]);
    }

    /**
     * 编辑分销商品
     *
     * @author windy
     * @return View|Json
     */
    public function edit()
    {
        if (request()->isAjax()) {
            $result = DistributionGoodsLogic::edit($this->postData());
            if ($result === false) {
                return JsonUtils::error(DistributionGoodsLogic::getError());
            }
            return JsonUtils::ok('更新成功');
        }

        return view('addons/distribution/goods/edit', [
            'detail' => DistributionGoodsLogic::detail($this->getData('id'))
        ]);
    }

    /**
     * 删除分销商品
     *
     * @author windy
     * @return Json
     */
    public function del(): Json
    {
        if (request()->isAjax()) {
            $result = DistributionGoodsLogic::del($this->postData('id'));
            if ($result === false) {
                return JsonUtils::error(DistributionGoodsLogic::getError());
            }
            return JsonUtils::ok('移除成功');
        }

        return JsonUtils::error('请求异常');
    }
}