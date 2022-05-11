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
use app\common\utils\ArrayUtils;
use app\common\model\auth\AdminRule;
use Exception;

/**
 * 系统菜单-逻辑层
 * Class RuleLogic
 * @package app\admin\logic\auth
 */
class RuleLogic extends Logic
{
    /**
     * 树形菜单(HTML)
     *
     * @author windy
     * @return array
     */
    public static function getTreeHtmlRule(): array
    {
        return ArrayUtils::toTreeHtml(self::getRuleAll());
    }

    /**
     * 树形菜单(JSON)
     *
     * @author windy
     * @return array
     */
    public static function getTreeJsonRule(): array
    {
        $data = self::getRuleAll();
        return ArrayUtils::toTreeJson($data);
    }

    /**
     * 所有菜单
     *
     * @author windy
     * @return array
     * @throws @\think\db\exception\DataNotFoundException
     * @throws @\think\db\exception\DbException
     * @throws @\think\db\exception\ModelNotFoundException
     */
    public static function getRuleAll(): array
    {
        $model = new AdminRule();
        return $model->field(true)
            ->order( 'sort', 'asc')
            ->select()->toArray();
    }

    /**
     * 菜单详情
     *
     * @author windy
     * @param int $id
     * @return array
     */
    public static function detail(int $id): array
    {
        $model = new AdminRule();
        return $model->field(true)->findOrEmpty((int)$id)->toArray();
    }

    /**
     * 菜单新增
     *
     * @author windy
     * @param array $post
     * @return bool
     */
    public static function add(array $post): bool
    {
        try {
            if (intval($post['pid']) > 0) {
                $model = new AdminRule();
                $rule = $model->field('id')->findOrEmpty(intval($post['pid']));
                if (!$rule) {
                    static::$error = '父级不可用';
                    return false;
                }
            }

            AdminRule::create([
                'pid'         => $post['pid'],
                'title'       => $post['title'],
                'icon'        => $post['icon'] ?? '',
                'uri'         => $post['uri'],
                'sort'        => $post['sort'],
                'is_menu'     => $post['is_menu'],
                'is_disable'  => $post['is_disable'],
                'create_time' => time(),
                'update_time' => time()
            ]);

            return true;
        } catch (Exception $e) {
            static::$error = $e->getMessage();
            return false;
        }
    }

    /**
     * 菜单编辑
     *
     * @author windy
     * @param array $post
     * @return bool
     */
    public static function edit(array $post): bool
    {
        try {
            if ($post['id'] == $post['pid']) {
                static::$error = '父级不能是自己';
                return false;
            }

            $icon = "";
            if (!empty($post['icon']) && $post['icon'] != 'layui-icon layui-icon-circle-dot') {
                $icon = $post['icon'];
            }

            AdminRule::update([
                'pid'         => $post['pid'],
                'title'       => $post['title'],
                'icon'        => $icon,
                'uri'         => $post['uri'],
                'sort'        => $post['sort'],
                'is_menu'     => $post['is_menu'],
                'is_disable'  => $post['is_disable'],
                'update_time' => time()
            ], ['id'=>(int)$post['id']]);

            return true;
        } catch (Exception $e) {
            static::$error = $e->getMessage();
            return false;
        }
    }

    /**
     * 菜单删除
     *
     * @author windy
     * @param int $id
     * @return bool
     */
    public static function del(int $id): bool
    {
        try {
            $rule = (new AdminRule())
                ->field('id')
                ->where('pid', '=', intval($id))
                ->findOrEmpty()
                ->toArray();

            if ($rule) {
                throw new Exception('请先移除子级菜单');
            }

            AdminRule::destroy(intval($id));

            return true;
        } catch (Exception $e) {
            static::$error = $e->getMessage();
            return false;
        }
    }
}