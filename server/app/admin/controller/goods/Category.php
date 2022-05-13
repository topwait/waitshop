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


use app\admin\logic\goods\CategoryLogic;
use app\admin\validate\ChangeValidate;
use app\admin\validate\goods\CategoryValidate;
use app\common\basics\Backend;
use app\common\model\goods\GoodsCategory;
use app\common\utils\JsonUtils;
use think\response\Json;
use think\response\View;

/**
 * 商品分类-控制器层
 * Class Category
 * @package app\admin\controller\product\
 */
class Category extends Backend
{
    /**
     * 商品分类列表
     *
     * @author windy
     * @return View|Json
     */
    public function lists()
    {
        if (request()->isAjax()) {
            $lists = CategoryLogic::all();
            return JsonUtils::success($lists);
        }

        return view('goods/category/lists');
    }

    /**
     * 新增商品分类
     *
     * @author windy
     * @return View|Json
     */
    public function add()
    {
        if (request()->isAjax()) {
            (new CategoryValidate())->goCheck('add');
            $result = CategoryLogic::add($this->postData());
            if ($result === false) {
                return JsonUtils::error(CategoryLogic::getError());
            }
            return JsonUtils::ok('新增成功');
        }

        return view('goods/category/add', [
            'treeCategory' => CategoryLogic::getTreeHtmlCategory()
        ]);
    }

    /**
     * 编辑商品分类
     *
     * @author windy
     * @return View|Json
     */
    public function edit()
    {
        if (request()->isAjax()) {
            (new CategoryValidate())->goCheck('edit');
            $result = CategoryLogic::edit($this->postData());
            if ($result === false) {
                return JsonUtils::error(CategoryLogic::getError());
            }
            return JsonUtils::ok('编辑成功');
        }

        return view('goods/category/edit', [
            'childrenIds'  => GoodsCategory::getChildrenIds($this->getData('id')),
            'treeCategory' => CategoryLogic::getTreeHtmlCategory(),
            'detail'       => CategoryLogic::detail($this->getData('id'))
        ]);
    }

    /**
     * 删除商品分类
     *
     * @author windy
     * @return View|Json
     */
    public function del(): Json
    {
        if (request()->isAjax()) {
            (new CategoryValidate())->goCheck('id');
            $result = CategoryLogic::del($this->postData('id'));
            if ($result === false) {
                return JsonUtils::error(CategoryLogic::getError());
            }
            return JsonUtils::ok('删除成功');
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
            (new ChangeValidate())->goCheck();
            $result = CategoryLogic::change($this->postData());
            if ($result === false) {
                return JsonUtils::error(CategoryLogic::getError());
            }
            return JsonUtils::ok('更新成功');
        }

        return JsonUtils::error('请求异常');
    }
}