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


use app\admin\logic\addons\SignLogic;
use app\admin\validate\addons\SignValidate;
use app\common\basics\Backend;
use app\common\utils\ConfigUtils;
use app\common\utils\JsonUtils;
use think\response\Json;
use think\response\View;

/**
 * 每日签到-控制器层
 * Class Sign
 * @package app\admin\controller\addons
 */
class Sign extends Backend
{
    /**
     * 连续签到配置
     *
     * @author windy
     * @return View|Json
     */
    public function lists()
    {
        if (request()->isAjax()) {
            $lists = SignLogic::lists($this->getData());
            return JsonUtils::success($lists);
        }

        $detail = ConfigUtils::get('sign', ['is_open'=>0, 'explain'=>'']);
        return view('addons/sign/index', ['detail'=>$detail]);
    }

    /**
     * 新增连续签到
     *
     * @author windy
     * @return View|Json
     */
    public function add()
    {
        if (request()->isAjax()) {
            (new SignValidate())->goCheck('add');
            $result = SignLogic::add($this->postData());
            if ($result === false) {
               return JsonUtils::error(SignLogic::getError());
            }
            return JsonUtils::ok('新增成功');
        }

        return view('addons/sign/add');
    }

    /**
     * 编辑连续签到
     *
     * @author windy
     * @return View|Json
     */
    public function edit()
    {
        if (request()->isAjax()) {
            (new SignValidate())->goCheck('edit');
            $result = SignLogic::edit($this->postData());
            if ($result === false) {
                return JsonUtils::error(SignLogic::getError());
            }
            return JsonUtils::ok('编辑成功');
        }

        return view('addons/sign/edit', ['detail'=>SignLogic::detail($this->getData('id'))]);
    }

    /**
     * 删除连续签到
     *
     * @author windy
     * @return Json
     */
    public function del(): Json
    {
        if (request()->isAjax()) {
            (new SignValidate())->goCheck('id');
            $result = SignLogic::del($this->postData('id'));
            if ($result === false) {
                return JsonUtils::error(SignLogic::getError());
            }
            return JsonUtils::ok('删除成功');
        }

        return JsonUtils::error('请求异常');
    }

    /**
     * 签到记录
     *
     * @author windy
     * @return Json
     */
    public function record(): Json
    {
        if (request()->isAjax()) {
            $lists = SignLogic::record($this->getData());
            return JsonUtils::success($lists);
        }

        return JsonUtils::error('请求异常');
    }

    /**
     * 基础配置
     *
     * @author windy
     * @return Json
     */
    public function setting(): Json
    {
        if (request()->isAjax()) {
            $result = SignLogic::setting($this->postData());
            if ($result === false) {
                return JsonUtils::error(SignLogic::getError());
            }
            return JsonUtils::ok('保存成功');
        }

        return JsonUtils::error('请求异常');
    }
}