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

namespace app\admin\controller\goods;


use app\admin\logic\setting\FreightLogic;
use app\admin\logic\goods\BrandLogic;
use app\admin\logic\goods\CategoryLogic;
use app\admin\logic\goods\GoodsLogic;
use app\admin\logic\goods\SupplierLogic;
use app\admin\validate\goods\GoodsValidate;
use app\common\basics\Backend;
use app\common\utils\JsonUtils;
use think\response\Json;
use think\response\View;

/**
 * 商品管理-控制器层
 * Class Goods
 * @package app\admin\controller\goods
 */
class Goods extends Backend
{
    protected $allowAllAction = ['statistics'];

    /**
     * 商品列表
     *
     * @author windy
     * @return View|Json
     */
    public function lists()
    {
        if (request()->isAjax()) {
            $lists = GoodsLogic::lists($this->getData());
            return JsonUtils::success($lists);
        }

        return view('goods/goods/lists', [
            'statistics'   => GoodsLogic::statistics(),
            'treeCategory' => CategoryLogic::getTreeHtmlCategory()
        ]);
    }

    /**
     * 数据统计
     *
     * @author windy
     * @return Json
     */
    public function statistics(): Json
    {
        if (request()->isAjax()) {
            $totalCount = GoodsLogic::statistics();
            return JsonUtils::success($totalCount);
        }

        return JsonUtils::error('请求异常');
    }

    /**
     * 新增商品
     *
     * @author windy
     * @return View|Json
     */
    public function add()
    {
        if (request()->isAjax()) {
            (new GoodsValidate())->goCheck('add');
            $result = GoodsLogic::add($this->postData());
            if ($result === false) {
                return JsonUtils::error(CategoryLogic::getError());
            }
            return JsonUtils::ok('新增成功');
        }

        return view('goods/goods/add', [
            'freight'   => FreightLogic::all(),
            'brand'     => BrandLogic::all(),
            'supplier'  => SupplierLogic::all(),
            'category'  => json_encode(CategoryLogic::all(), JSON_UNESCAPED_UNICODE)
        ]);
    }

    /**
     * 编辑商品
     *
     * @author windy
     * @return View|Json
     */
    public function edit()
    {
        if (request()->isAjax()) {
            (new GoodsValidate())->goCheck('edit');
            $result = GoodsLogic::edit($this->postData());
            if ($result === false) {
                return JsonUtils::error(CategoryLogic::getError());
            }
            return JsonUtils::ok('编辑成功');
        }

        return view('goods/goods/edit', [
            'freight'  => FreightLogic::all(),
            'brand'    => BrandLogic::all(),
            'supplier' => SupplierLogic::all(),
            'category' => json_encode(CategoryLogic::all()),
            'detail'   => GoodsLogic::detail($this->getData('id'))
        ]);
    }

    /**
     * 删除商品
     *
     * @author windy
     * @return Json
     */
    public function del(): Json
    {
        if (request()->isAjax()) {
            (new GoodsValidate())->goCheck('id');
            $result = GoodsLogic::del($this->postData('id'));
            if ($result === false) {
                return JsonUtils::error(GoodsLogic::getError());
            }
            return JsonUtils::ok('已移入回收站');
        }

        return JsonUtils::error('请求异常');
    }

    /**
     * 销毁商品
     *
     * @author windy
     * @return Json
     */
    public function destroy(): Json
    {
        if (request()->isAjax()) {
            (new GoodsValidate())->goCheck('id');
            $result = GoodsLogic::destroy($this->postData('id'));
            if ($result === false) {
                return JsonUtils::error(GoodsLogic::getError());
            }
            return JsonUtils::ok('销毁成功');
        }

        return JsonUtils::error('请求异常');
    }

    /**
     * 移入到仓库中
     *
     * @author windy
     * @return Json
     */
    public function addWarehouse(): Json
    {
        if (request()->isAjax()) {
            (new GoodsValidate())->goCheck('id');
            $result = GoodsLogic::addWarehouse($this->postData('id'));
            if ($result === false) {
                return JsonUtils::error(GoodsLogic::getError());
            }
            return JsonUtils::ok('已移回到仓库中');
        }

        return JsonUtils::error('请求异常');
    }

    /**
     * 上架或下架
     *
     * @author windy
     * @return Json
     */
    public function upperOrLower(): Json
    {
        if (request()->isAjax()) {
            (new GoodsValidate())->goCheck('id');
            $id     = $this->postData('id', 0);
            $status = $this->postData('status', 0);

            $result = GoodsLogic::upperOrLower($id, $status);
            if ($result === false) {
                return JsonUtils::error(GoodsLogic::getError());
            }
            return JsonUtils::ok($status ? "已上架" : "已下架");
        }
        
        return JsonUtils::error('请求异常');
    }

    /**
     * 改变状态
     *
     * @author windy
     * @return Json
     */
    public function change(): Json
    {
        if (request()->isAjax()) {
            (new GoodsValidate())->goCheck('id');
            $result = GoodsLogic::change($this->postData());
            if ($result === false) {
                return JsonUtils::error(GoodsLogic::getError());
            }
            return JsonUtils::ok('更新成功');
        }

        return JsonUtils::error('请求异常');
    }

}