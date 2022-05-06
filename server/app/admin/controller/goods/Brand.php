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


use app\admin\logic\goods\BrandLogic;
use app\admin\validate\ChangeValidate;
use app\admin\validate\goods\BrandValidate;
use app\common\basics\Backend;
use app\common\utils\JsonUtils;
use think\response\Json;
use think\response\View;

/**
 * 品牌管理-控制器层
 * Class Brand
 * @package app\admin\controller\product
 */
class Brand extends Backend
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
            $lists = BrandLogic::lists($this->getData());
            return JsonUtils::success($lists);
        }

        return view('goods/brand/lists');
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
            (new BrandValidate())->goCheck('add');
            $result = BrandLogic::add($this->postData());
            if ($result === false) {
                return JsonUtils::error(BrandLogic::getError());
            }
            return JsonUtils::ok('新增成功');
        }

        return view('goods/brand/add', [
            'letter' => range('A','Z')
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
            (new BrandValidate())->goCheck('edit');
            $result = BrandLogic::edit($this->postData());
            if ($result === false) {
                return JsonUtils::error(BrandLogic::getError());
            }
            return JsonUtils::ok('编辑成功');
        }

        return view('goods/brand/edit', [
            'letter' => range('A','Z'),
            'detail' => BrandLogic::detail($this->getData('id'))
        ]);
    }

    /**
     * 删除商品分类
     *
     * @author windy
     * @return View|Json
     */
    public function del()
    {
        if (request()->isAjax()) {
            (new BrandValidate())->goCheck('id');
            $result = BrandLogic::del($this->postData('id'));
            if ($result === false) {
                return JsonUtils::error(BrandLogic::getError());
            }
            return JsonUtils::ok('删除成功');
        }

        return JsonUtils::error('请求异常');
    }

    /**
     * 切换商品状态
     *
     * @author windy
     * @return Json
     */
    public function change(): Json
    {
        if (request()->isAjax()) {
            (new ChangeValidate())->goCheck();
            $result = BrandLogic::change($this->postData());
            if ($result === false) {
                return JsonUtils::error(BrandLogic::getError());
            }
            return JsonUtils::ok('更新成功');
        }

        return JsonUtils::error('请求异常');
    }
}