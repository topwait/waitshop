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

namespace app\admin\logic\auth;


use app\common\basics\Logic;
use app\common\model\auth\Admin;
use app\common\model\auth\AdminRole;
use app\common\model\auth\AdminRule;
use app\common\utils\ArrayUtils;
use Exception;

/**
 * 管理员角色-逻辑层
 * Class RoleLogic
 * @package app\admin\logic\auth
 */
class RoleLogic extends Logic
{
    /**
     * 所有角色
     *
     * @author windy
     * @return array|bool
     * @throws @\think\db\exception\DataNotFoundException
     * @throws @\think\db\exception\DbException
     * @throws @\think\db\exception\ModelNotFoundException
     */
    public static function getRoleAll()
    {
        return (new AdminRole())
            ->field(true)
            ->where(['is_disable'=>0])
            ->select()
            ->toArray();
    }

    /**
     * 获取选中的角色权限ID
     * 主要是为了处理编辑时tree选中的问题
     *
     * @author windy
     * @param array $rule_ids
     * @return array|bool
     * @throws @\think\db\exception\DataNotFoundException
     * @throws @\think\db\exception\DbException
     * @throws @\think\db\exception\ModelNotFoundException
     */
    public static function getChecked(array $rule_ids): array
    {
        $model = new AdminRule();
        $lists = $model->field(true)
            ->whereIn('id', $rule_ids)
            ->select()
            ->toArray();

        $treeJson = ArrayUtils::toTreeJson($lists);
        return self::getCheckedRule($treeJson);
    }

    /**
     * 递归处理选角色选中权限ID
     * 只取出子级ID,父级ID不要
     *
     * @author windy
     * @param $treeJson
     * @return array
     */
    protected static function getCheckedRule($treeJson): array
    {
        static $data = array();
        foreach ($treeJson as $item) {
            if (!empty($item['children'])) {
                self::getCheckedRule($item['children']);
            } else {
                array_push($data, $item['id']);
            }
        }

        return $data;
    }

    /**
     * 角色列表
     *
     * @author windy
     * @param array $get
     * @return array|bool
     * @throws @\think\db\exception\DbException
     */
    public static function lists(array $get)
    {
        $lists = (new AdminRole())
            ->withoutField('rule_ids')
            ->paginate([
                'page'      => $get['page'] ?? 1,
                'list_rows' => $get['limit'] ?? 20,
                'var_page'  => 'page'
            ])->toArray();

        return ['count'=>$lists['total'], 'list'=>$lists['data']];
    }

    /**
     * 角色详细
     *
     * @author windy
     * @param int $id (主键ID)
     * @return array
     */
    public static function detail(int $id): array
    {
        return (new AdminRole())->field(true)->findOrEmpty(intval($id))->toArray();
    }

    /**
     * 新增角色
     *
     * @author windy
     * @param array $post
     * @return bool
     */
    public static function add(array $post) :bool
    {
        try {
             AdminRole::create([
                'name'       => $post['name'],
                'rule_ids'   => self::getHandleRuleIds($post),
                'describe'   => $post['describe'],
                'sort'       => $post['sort'],
                'is_disable' => $post['is_disable']
             ]);

             return true;
        } catch (Exception $e) {
            static::$error = $e->getMessage();
            return false;
        }
    }

    /**
     * 编辑角色
     *
     * @author windy
     * @param array $post
     * @return bool
     */
    public static function edit(array $post) :bool
    {
        try {
            AdminRole::update([
                'name'       => $post['name'],
                'rule_ids'   => self::getHandleRuleIds($post),
                'describe'   => $post['describe'],
                'sort'       => $post['sort'],
                'is_disable' => $post['is_disable']
            ], ['id'=>(int)$post['id']]);

            return true;
        } catch (Exception $e) {
            static::$error = $e->getMessage();
            return false;
        }
    }

    /**
     * 删除角色
     *
     * @author windy
     * @param int $id (主键ID)
     * @return bool
     */
    public static function del(int $id) :bool
    {
        try {
            // 校验是否存在使用情况
            $adminUser = (new Admin())->field('id,role_id')
                ->where(['is_delete'=>0, 'role_id'=>(int)$id])
                ->findOrEmpty()->toArray();

            if ($adminUser) {
                static::$error = '目前有管理员正在使用该角色, 请先移除!';
                return false;
            }

            // 执行删除
            AdminRole::destroy(intval($id));

            return true;
        } catch (Exception $e) {
            static::$error = $e->getMessage();
            return false;
        }
    }

    /**
     * 处理获取规格ID
     * 先删除角色相关字段,剩余的是规格字段
     * 规格字段是这样的: layuiTreeCheck_2: "2"; layuiTreeCheck_3: "5"
     * 返回规则ID逗号隔开
     *
     * @author windy
     * @param array $post
     * @return string
     */
    private static function getHandleRuleIds(array $post) :string
    {
        unset($post['describe']);
        unset($post['name']);
        unset($post['sort']);
        unset($post['is_disable']);
        return !empty($post) ? implode(',', array_unique(array_values($post))) : '';
    }
}