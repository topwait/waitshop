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

namespace app\admin\controller\addons;


use app\admin\logic\addons\SeckillLogic;
use app\admin\logic\goods\GoodsLogic;
use app\common\basics\Backend;
use app\common\utils\JsonUtils;
use think\response\Json;
use think\response\View;

/**
 * 秒杀活动-控制器层
 * Class Seckill
 * @package app\admin\controller\addons
 */
class Seckill extends Backend
{
    /**
     * 秒杀活动列表
     *
     * @author windy
     * @return View|Json
     */
    public function lists()
    {
        if (request()->isAjax()) {
            $lists = SeckillLogic::lists($this->getData());
            return JsonUtils::success($lists);
        }

        return view('addons/seckill/lists');
    }

    /**
     * 查看秒杀活动
     *
     * @author windy
     * @return View|Json
     */
    public function view(): View
    {
        return view('addons/seckill/view', [
            'detail' => SeckillLogic::detail($this->getData('id'))
        ]);
    }

    /**
     * 新增秒杀活动
     *
     * @author windy
     * @return View|Json
     */
    public function add()
    {
        if (request()->isAjax()) {
            $result = SeckillLogic::add($this->postData());
            if ($result === false) {
                return JsonUtils::error(SeckillLogic::getError());
            }
            return JsonUtils::ok('新增成功');
        }

        return view('addons/seckill/add', [
            'goodsDetail' => GoodsLogic::detail($this->getData('goods_id'))
        ]);
    }

    /**
     * 编辑秒杀活动
     *
     * @author windy
     * @return View|Json
     */
    public function edit()
    {
        if (request()->isAjax()) {
            $result = SeckillLogic::edit($this->postData());
            if ($result === false) {
                return JsonUtils::error(SeckillLogic::getError());
            }
            return JsonUtils::ok('更新成功');
        }

        return view('addons/seckill/edit', [
            'detail' => SeckillLogic::detail($this->getData('id'))
        ]);
    }

    /**
     * 删除秒杀活动
     *
     * @author windy
     * @return Json
     */
    public function del(): Json
    {
        if (request()->isAjax()) {
            $result = SeckillLogic::del($this->postData('id'));
            if ($result === false) {
                return JsonUtils::error(SeckillLogic::getError());
            }
            return JsonUtils::ok('删除成功');
        }

        return JsonUtils::error('请求异常');
    }

    /**
     * 结束秒杀活动
     *
     * @author windy
     * @return Json
     * @return Json
     */
    public function end(): Json
    {
        if (request()->isAjax()) {
            $result = SeckillLogic::end($this->postData('id'));
            if ($result === false) {
                return JsonUtils::error(SeckillLogic::getError());
            }
            return JsonUtils::ok('结束成功');
        }

        return JsonUtils::error('请求异常');
    }

}