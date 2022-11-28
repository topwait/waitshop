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

namespace app\admin\controller\user;


use app\admin\logic\user\GradeLogic;
use app\common\basics\Backend;
use app\common\utils\JsonUtils;
use think\response\Json;
use think\response\View;

/**
 * 会员等级-控制器层
 * Class Grade
 * @package app\admin\controller\user
 */
class Grade extends Backend
{
    /**
     * 等级列表
     *
     * @author windy
     * @return View|Json
     */
    public function lists()
    {
        if (request()->isAjax()) {
            $lists = GradeLogic::lists($this->getData());
            return JsonUtils::success($lists);
        }

        return view('user/grade/lists');
    }

    /**
     * 新增等级
     *
     * @author windy
     * @return View|Json
     */
    public function add()
    {
        if (request()->isAjax()) {
            $result = GradeLogic::add($this->postData());
            if ($result === false) {
                return JsonUtils::error(GradeLogic::getError());
            }
            return JsonUtils::ok('新增成功');
        }

        return view('user/grade/add', [
            'grade'  => range(1, 50)
        ]);
    }

    /**
     * 编辑等级
     *
     * @author windy
     * @return View|Json
     */
    public function edit()
    {
        if (request()->isAjax()) {
            $result = GradeLogic::edit($this->postData());
            if ($result === false) {
                return JsonUtils::error(GradeLogic::getError());
            }
            return JsonUtils::ok('编辑成功');
        }

        return view('user/grade/edit', [
            'grade'  => range(1, 50),
            'detail' => GradeLogic::detail($this->getData('id'))
        ]);
    }

    /**
     * 删除等级
     *
     * @author windy
     * @return Json
     */
    public function del(): Json
    {
        if (request()->isAjax()) {
            $result = GradeLogic::del($this->postData('id'));
            if ($result === false) {
                return JsonUtils::error(GradeLogic::getError());
            }
            return JsonUtils::ok('删除成功');
        }

        return JsonUtils::error('请求异常');
    }
}