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

namespace app\admin\controller\content;


use app\admin\logic\content\ArticleCategoryLogic;
use app\admin\validate\content\ArticleCategoryValidate;
use app\common\basics\Backend;
use app\common\utils\JsonUtils;
use think\response\Json;
use think\response\View;

/**
 * 文章分类-控制器层
 * Class ArticleCategory
 * @package app\admin\controller\content
 */
class ArticleCategory extends Backend
{
    /**
     * 文章分类列表
     *
     * @author windy
     * @return View|Json
     */
    public function lists()
    {
        if (request()->isAjax()) {
            $lists = ArticleCategoryLogic::lists($this->getData());
            return JsonUtils::success($lists);
        }

        return view('content/category/lists');
    }

    /**
     * 新增文章分类
     *
     * @author windy
     * @return View|Json
     */
    public function add()
    {
        if (request()->isAjax()) {
            (new ArticleCategoryValidate())->goCheck('add');
            $result = ArticleCategoryLogic::add($this->postData());
            if ($result === false) {
                return JsonUtils::error(ArticleCategoryLogic::getError());
            }
            return JsonUtils::ok('新增成功');
        }

        return view('content/category/add');
    }

    /**
     * 编辑文章分类
     *
     * @author windy
     * @return View|Json
     */
    public function edit()
    {
        if (request()->isAjax()) {
            (new ArticleCategoryValidate())->goCheck('edit');
            $result = ArticleCategoryLogic::edit($this->postData());
            if ($result === false) {
                return JsonUtils::error(ArticleCategoryLogic::getError());
            }
            return JsonUtils::ok('编辑成功');
        }

        $detail = ArticleCategoryLogic::detail($this->getData('id'));
        return view('content/category/edit', ['detail'=>$detail]);
    }

    /**
     * 删除文章分类
     *
     * @author windy
     * @return Json
     */
    public function del(): Json
    {
        if (request()->isAjax()) {
            (new ArticleCategoryValidate())->goCheck('id');
            $result = ArticleCategoryLogic::del($this->postData('id'));
            if ($result === false) {
                return JsonUtils::error(ArticleCategoryLogic::getError());
            }
            return JsonUtils::ok('删除成功');
        }

        return JsonUtils::error('请求异常');
    }
}