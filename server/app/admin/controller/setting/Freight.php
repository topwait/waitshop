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

namespace app\admin\controller\setting;


use app\admin\logic\setting\FreightLogic;
use app\common\basics\Backend;
use app\common\utils\JsonUtils;
use think\response\Json;
use think\response\View;

/**
 * 运费模板配置-控制器层
 * Class Freight
 * @package app\admin\controller\set
 */
class Freight extends Backend
{
    /**
     * 运费模板列表
     *
     * @author windy
     * @return View|Json
     */
    public function lists()
    {
        if (request()->isAjax()) {
            $lists = FreightLogic::lists($this->getData());
            return JsonUtils::success($lists);
        }

        return view('setting/freight/lists');
    }

    /**
     * 配送地区
     *
     * @author windy
     * @return View
     */
    public function region(): View
    {
        $checked = explode(',', $this->getData('region'));
        $region = FreightLogic::region();

        return view('setting/freight/region', [
            'checked' => json_encode($checked, JSON_UNESCAPED_UNICODE),
            'region'  => json_encode($region, JSON_UNESCAPED_UNICODE)
        ]);
    }

    /**
     * 新增运费模板
     *
     * @author windy
     * @return View|Json
     */
    public function add()
    {
        if (request()->isAjax()) {
            $result = FreightLogic::add($this->postData());
            if ($result === false) {
                return JsonUtils::error(FreightLogic::getError());
            }
            return JsonUtils::ok('新增成功');
        }

        return view('setting/freight/add');
    }

    /**
     * 编辑运费模板
     *
     * @author windy
     * @return View|Json
     */
    public function edit()
    {
        if (request()->isAjax()) {
            $result = FreightLogic::edit($this->postData());
            if ($result === false) {
                return JsonUtils::error(FreightLogic::getError());
            }
            return JsonUtils::success('编辑成功');
        }

        return view('setting/freight/edit', [
            'detail' => FreightLogic::detail($this->getData('id'))
        ]);
    }

    /**
     * 删除运费模板
     *
     * @author windy
     * @return Json
     */
    public function del(): Json
    {
        if (request()->isAjax()) {
            $result = FreightLogic::destroy($this->getData('id'));
            if ($result === false) {
                return JsonUtils::error(FreightLogic::getError());
            }
            return JsonUtils::success('删除成功');
        }
        
        return JsonUtils::error('请求异常');
    }
}