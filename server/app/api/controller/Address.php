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

namespace app\api\controller;


use app\api\logic\AddressLogic;
use app\api\validate\AddressValidate;
use app\api\validate\RequestValidate;
use app\common\basics\Api;
use app\common\utils\JsonUtils;
use think\response\Json;

/**
 * 收货地址接口-控制器层
 * Class Address
 * @package app\api\controller
 */
class Address extends Api
{
    /**
     * 收货地址列表
     *
     * @author windy
     * @return Json
     */
    public function lists(): Json
    {
        (new RequestValidate())->isGET();

        $lists = AddressLogic::lists($this->userId);
        return JsonUtils::success($lists);
    }

    /**
     * 货地址详细
     *
     * @author windy
     * @return Json
     */
    public function detail(): Json
    {
        (new RequestValidate())->isGET();
        (new AddressValidate())->goCheck('id');

        $detail = AddressLogic::detail($this->getData('id'), $this->userId);
        if ($detail === false) {
            return JsonUtils::error(AddressLogic::getError());
        }
        return JsonUtils::success($detail);
    }

    /**
     * 对应省,市,区的id
     *
     * @author windy
     * @return Json
     */
    public function region(): Json
    {
        (new RequestValidate())->isGET();

        $detail = AddressLogic::region($this->getData());
        if ($detail === false) {
            return JsonUtils::error(AddressLogic::getError());
        }
        return JsonUtils::success($detail);
    }
    
    /**
     * 新增收货地址
     *
     * @author windy
     * @return Json
     */
    public function add(): Json
    {
        (new RequestValidate())->isPost();
        (new AddressValidate())->goCheck('add');

        $result = AddressLogic::add($this->postData(), $this->userId);
        if ($result === false) {
            return JsonUtils::error(AddressLogic::getError());
        }
        return JsonUtils::ok('新增成功');
    }

    /**
     * 编辑收货地址
     *
     * @author windy
     * @return Json
     */
    public function edit(): Json
    {
        (new RequestValidate())->isPost();
        (new AddressValidate())->goCheck('edit');

        $result = AddressLogic::edit($this->postData(), $this->userId);
        if ($result === false) {
            return JsonUtils::error(AddressLogic::getError());
        }
        return JsonUtils::ok('编辑成功');
    }

    /**
     * 销毁收货地址
     *
     * @author windy
     * @return Json
     */
    public function del(): Json
    {
        (new RequestValidate())->isPost();
        (new AddressValidate())->goCheck('id');

        $result = AddressLogic::del($this->postData('id'), $this->userId);
        if ($result === false) {
            return JsonUtils::error(AddressLogic::getError());
        }
        return JsonUtils::ok('删除成功');
    }

    /**
     * 设置默认地址
     *
     * @author windy
     * @return Json
     */
    public function setDefault(): Json
    {
        (new RequestValidate())->isPost();
        (new AddressValidate())->goCheck('id');

        $result = AddressLogic::setDefault($this->postData('id'), $this->userId);
        if ($result === false) {
            return JsonUtils::error(AddressLogic::getError());
        }
        return JsonUtils::ok('设置成功');
    }
}