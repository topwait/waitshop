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

namespace app\admin\controller\diy;


use app\admin\logic\diy\PageLogic;
use app\admin\logic\goods\CategoryLogic;
use app\common\basics\Backend;
use app\common\utils\JsonUtils;
use app\common\model\diy\DiyPage;
use think\response\Json;
use think\response\View;

/**
 * 页面设计-控制器层
 * Class Page
 * @package app\admin\controller\diy
 */
class Page extends Backend
{
    /**
     * 页面列表
     *
     * @author windy
     * @return View|Json
     */
    public function lists()
    {
        if (request()->isAjax()) {
            $get = $this->request->get();
            $lists = PageLogic::lists($get);
            if ($lists === false) {
                return JsonUtils::error(PageLogic::getError());
            }
            return JsonUtils::success($lists);
        }

        return view('diy/page/lists');
    }

    /**
     * 新增页面
     *
     * @author windy
     * @return View|Json
     */
    public function add()
    {
        if (request()->isAjax()) {
            $post = $this->request->post('data');
            if (PageLogic::add($post) === false) {
                return JsonUtils::error(PageLogic::getError());
            }
            return JsonUtils::ok('新增成功');
        }

        return view('diy/page/edit', [
            'defaultData' => json_encode(DiyPage::getDefaultItems(), JSON_UNESCAPED_UNICODE),
            'jsonData'    => json_encode(['page' =>DiyPage::getDefaultPage(), 'items' => []], JSON_UNESCAPED_UNICODE),
            'opts'        => json_encode([
                'goodsCategory'   => CategoryLogic::getTreeHtmlCategory(),
                'sharingCategory' => [],
                'articleCategory' => []
            ], JSON_UNESCAPED_UNICODE)
        ]);
    }

    /**
     * 编辑页面
     *
     * @author windy
     * @return View|Json
     */
    public function edit()
    {
        if (request()->isAjax()) {
            $post = $this->request->post('data');
            $page_id = $this->request->get('id');
            if (PageLogic::edit($post, $page_id) === false) {
                return JsonUtils::error(PageLogic::getError());
            }
            return JsonUtils::ok('更新成功');
        }

        $id = $this->request->get('id');
        $detail = PageLogic::detail($id);
        return view('diy/page/edit', [
            'defaultData' => json_encode(DiyPage::getDefaultItems(), JSON_UNESCAPED_UNICODE),
            'jsonData'    => json_encode($detail['page_data'], JSON_UNESCAPED_UNICODE),
            'opts'        => json_encode([
                'goodsCategory'   => CategoryLogic::getTreeHtmlCategory(),
                'sharingCategory' => [],
                'articleCategory' => []
            ], JSON_UNESCAPED_UNICODE)
        ]);
    }

    /**
     * 删除页面
     *
     * @author windy
     * @return Json
     */
    public function del(): Json
    {
        if (request()->isAjax()) {
            $pageId = $this->request->post('id');
            if (PageLogic::del($pageId) === false) {
                return JsonUtils::error(PageLogic::getError());
            }
            return JsonUtils::ok('删除成功');
        }

        return JsonUtils::error();
    }

    /**
     * 设置为首页
     *
     * @author windy
     * @return Json
     */
    public function home(): Json
    {
        if (request()->isAjax()) {
            $page_id = $this->request->post('id');
            if (PageLogic::home($page_id) === false) {
                $error = PageLogic::getError() ?: '设置失败';
                return JsonUtils::error($error);
            }
            return JsonUtils::ok('设置成功');
        }

        return JsonUtils::error('请求异常');
    }

    /**
     * 链接展示页面
     *
     * @author windy
     * @return View|Json
     */
    public function link(): View
    {
        return view('diy/page/link');
    }
}