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

namespace app\admin\controller\goods;


use app\admin\logic\goods\SupplierLogic;
use app\admin\validate\goods\SupplierValidate;
use app\common\basics\Backend;
use app\common\utils\JsonUtils;
use think\response\Json;
use think\response\View;

/**
 * 供应商管理-控制器层
 * Class Supplier
 * @package app\admin\controller\product
 */
class Supplier extends Backend
{
    /**
     * 供应商列表
     *
     * @author windy
     * @return View|Json
     */
    public function lists()
    {
        if (request()->isAjax()) {
            $lists = SupplierLogic::lists($this->getData());
            return JsonUtils::success($lists);
        }

        return view('goods/supplier/lists');
    }

    /**
     * 新增供应商
     *
     * @author windy
     * @return View|Json
     */
    public function add()
    {
        if (request()->isAjax()) {
            (new SupplierValidate())->goCheck('add');
            $result = SupplierLogic::add($this->postData());
            if ($result === false) {
                return JsonUtils::error(SupplierLogic::getError());
            }
            return JsonUtils::ok('新增成功');
        }

        return view('goods/supplier/add');
    }

    /**
     * 编辑供应商
     *
     * @author windy
     * @return View|Json
     */
    public function edit()
    {
        if (request()->isAjax()) {
            (new SupplierValidate())->goCheck('edit');
            $result = SupplierLogic::edit($this->postData());
            if ($result === false) {
                return JsonUtils::error(SupplierLogic::getError());
            }
            return JsonUtils::ok('编辑成功');
        }

        return view('goods/supplier/edit', [
            'detail' => SupplierLogic::detail($this->getData('id'))
        ]);
    }

    /**
     * 删除供应商
     *
     * @author windy
     * @return Json
     */
    public function del(): Json
    {
        if (request()->isAjax()) {
            (new SupplierValidate())->goCheck('id');
            $result = SupplierLogic::del($this->postData('id'));
            if ($result === false) {
                return JsonUtils::error(SupplierLogic::getError());
            }
            return JsonUtils::ok('删除成功');
        }

        return JsonUtils::error('请求异常');
    }
}