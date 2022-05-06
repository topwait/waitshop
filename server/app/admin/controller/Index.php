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


use app\admin\logic\IndexLogic;
use app\common\basics\Backend;
use app\common\model\auth\AdminRole;
use app\common\model\auth\AdminRule;
use app\common\utils\ArrayUtils;
use app\common\utils\ConfigUtils;
use app\common\utils\JsonUtils;
use app\common\utils\UrlUtils;
use think\response\Json;
use think\response\View;

/**
 *
 * 框架-控制器层
 * Class Index
 * @package app\admin\controller
 */
class Index extends Backend
{
    /**
     * 无权限页面
     *
     * @author windy
     * @return View
     */
    public function denied(): View
    {
        return view('index/denied');
    }

    /**
     * 主框架
     *
     * @author windy
     * @return View
     * @throws @\think\db\exception\DataNotFoundException
     * @throws @\think\db\exception\DbException
     * @throws @\think\db\exception\ModelNotFoundException
     */
    public function index(): View
    {
        $roleModel = new AdminRole();
        $ruleModel = new AdminRule();

        $rule_ids = $roleModel->field(true)
            ->where(['id'=>intval($this->adminUser['role_id'])])
            ->where(['is_disable'=>0])
            ->value('rule_ids');

        $where = $this->adminId === 1 ? [] : [['id', 'in', $rule_ids]];
        $rule_data = $ruleModel->field(true)
            ->where($where)
            ->where(['is_menu'=>1])
            ->order('sort asc, id asc')
            ->select()->toArray();

        $logo = ConfigUtils::get('backstage')['backstage_side_logo'] ?? '';

        return view('index/index', [
            'menu' => ArrayUtils::toTreeJson($rule_data) ?? [],
            'logo' => UrlUtils::getAbsoluteUrl($logo)
        ]);
    }

    /**
     * 控制台
     *
     * @author windy
     * @return View
     */
    public function console(): View
    {
        return view('index/console', [
            'detail' => IndexLogic::console()
        ]);
    }

    /**
     * 图表数据
     *
     * @author windy
     * @return Json
     */
    public function chart(): Json
    {
        if (request()->isGet()) {
            $result = IndexLogic::chart();
            return JsonUtils::success($result);
        }

        return JsonUtils::error('请求异常');
    }
}