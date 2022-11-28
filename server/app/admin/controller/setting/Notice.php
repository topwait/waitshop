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

namespace app\admin\controller\setting;


use app\admin\logic\setting\NoticeLogic;
use app\admin\validate\setting\NoticeValidate;
use app\common\basics\Backend;
use app\common\utils\JsonUtils;
use think\response\Json;
use think\response\View;

/**
 * 消息通知配置-控制器层
 * Class Notice
 * @package app\admin\controller\setting
 */
class Notice extends Backend
{
    /**
     * 消息模板列表
     *
     * @author windy
     * @return View
     */
    public function lists(): View
    {
        return view('setting/notice/lists', ['lists'=>NoticeLogic::lists()]);
    }

    /**
     * 设置消息模板
     *
     * @author windy
     * @return View|Json
     */
    public function set()
    {
        if (request()->isAjax()) {
            (new NoticeValidate())->goCheck($this->postData('scene'));
            $result = NoticeLogic::set($this->postData());
            if ($result === false) {
                return JsonUtils::error(NoticeLogic::getError());
            }
            return JsonUtils::ok('配置成功');
        }

        $id = $this->getData('id');
        $scene = $this->getData('scene');
        return view('setting/notice/'.$scene, ['detail'=>NoticeLogic::detail($id)]);
    }
}