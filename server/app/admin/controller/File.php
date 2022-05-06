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

namespace app\admin\controller;


use app\admin\logic\FileLogic;
use app\common\basics\Backend;
use app\common\utils\JsonUtils;
use think\response\Json;
use think\response\View;

/**
 * 文件管理-控制器层
 * Class File
 * @package app\admin\controller
 */
class File extends Backend
{
    /**
     * 文件列表
     *
     * @author windy
     * @return View|Json
     */
    public function lists()
    {
        if ($this->request->isAjax()) {
            $gid  = $this->getData('group_id', -1);
            $type = $this->getData('type', 0);
            $page = $this->getData('page', 1);
            $result = FileLogic::lists($gid, $type, $page);
            return JsonUtils::success($result);
        }

        return view('public/file', [
            'type'      => $this->getData('type'),
            'groupData' => FileLogic::getFileGroup($this->getData('type'))
        ]);
    }

    /**
     * 文件重命名
     *
     * @author windy
     * @return Json
     */
    public function rename(): Json
    {
        if (request()->isAjax()) {
            $result = FileLogic::rename($this->postData());
            if ($result === false) {
                return JsonUtils::error(FileLogic::getError());
            }
            return JsonUtils::ok('修改成功');
        }

        return JsonUtils::error('请求异常');
    }

    /**
     * 文件软删除
     *
     * @author windy
     * @return Json
     */
    public function recycle(): Json
    {
        if (request()->isAjax()) {
            $result = FileLogic::recycle($this->postData());
            if ($result === false) {
                return JsonUtils::error(FileLogic::getError());
            }
            return JsonUtils::ok('删除成功');
        }

        return JsonUtils::error('请求异常');
    }

    /**
     * 移动文件
     *
     * @author windy
     * @return Json
     */
    public function move(): Json
    {
        if (request()->isAjax()) {
            $result = FileLogic::move($this->postData());
            if ($result === false) {
                return JsonUtils::error(FileLogic::getError());
            }
            return JsonUtils::ok('移动成功');
        }
        return JsonUtils::error('请求异常');
    }

    /** ================= 文件组 =================*/

    /**
     * 新增文件分组
     *
     * @author windy
     * @return Json
     */
    public function addGroup(): Json
    {
        if (request()->isAjax()) {
            $result = FileLogic::addFileGroup($this->postData());
            if ($result === false) {
                return JsonUtils::error(FileLogic::getError());
            }
            $data = ['id'=>$result, 'name'=>$this->postData('group_name')];
            return JsonUtils::ok('新增成功', $data);
        }
        return JsonUtils::error('请求异常');
    }

    /**
     * 编辑文件分组
     *
     * @author windy
     * @return Json
     */
    public function editGroup(): Json
    {
        if (request()->isAjax()) {
            $result = FileLogic::editFileGroup($this->postData());
            if ($result === false) {
                return JsonUtils::error(FileLogic::getError());
            }
            return JsonUtils::ok('编辑成功');
        }

        return JsonUtils::error('请求异常');
    }

    /**
     * 删除文件分组
     *
     * @author windy
     * @return Json
     */
    public function delGroup(): Json
    {
        if (request()->isAjax()) {
            $result = FileLogic::delGroup($this->postData('group_id'));
            if ($result === false) {
                return JsonUtils::error(FileLogic::getError());
            }
            return JsonUtils::ok('删除成功');
        }

        return JsonUtils::error('请求异常');
    }
}