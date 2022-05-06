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

namespace app\admin\logic\user;


use app\common\basics\Logic;
use app\common\model\user\UserGrade;
use app\common\utils\UrlUtils;
use Exception;

/**
 * 等级管理-逻辑层
 * Class GradeLogic
 * @package app\admin\logic\user
 */
class GradeLogic extends Logic
{
    /**
     * 会员等级所有
     *
     * @author windy
     * @return array|false
     */
    public static function all()
    {
        try {
            $model = new UserGrade();
            return $model->field(true)
                ->where(['is_disable'=>0, 'is_delete'=>0])
                ->select()->toArray();

        } catch (Exception $e) {
            static::$error = $e->getMessage();
            return false;
        }
    }

    /**
     * 会员等级列表
     *
     * @author windy
     * @param array $get
     * @return array|false
     * @throws @\think\db\exception\DbException
     */
    public static function lists(array $get)
    {
        $model = new UserGrade();
        $lists = $model->field(true)
            ->where(['is_delete'=>0])
            ->order('weight', 'asc')
            ->paginate([
                'page'      => $get['page'],
                'list_rows' => $get['limit'],
                'var_page'  => 'page',
            ])->toArray();

        foreach ($lists['data'] as &$item) {
            $item['equity'] = $item['equity'].'折';
        }

        return ['count'=>$lists['total'], 'list'=>$lists['data']];
    }

    /**
     * 会员等级详细
     *
     * @author windy
     * @param int $id
     * @return array|false
     */
    public static function detail(int $id)
    {
        $model = new UserGrade();
        return $model->field(true)->findOrEmpty((int)$id)->toArray();
    }

    /**
     * 新增会员等级
     *
     * @author windy
     * @param array $post
     * @return bool
     */
    public static function add(array $post): bool
    {
        try {
            $weight = (new UserGrade())->where(['weight'=>intval($post['weight']), 'is_delete'=>0])->findOrEmpty();
            if (!$weight->isEmpty()) {
                throw new Exception('权重等级'.$post['weight'].'已存在');
            }

            $upgrade = [
                'total_growth_value'  => $post['total_growth_value']  ?? 0
            ];

            UserGrade::create([
                'name'        => $post['name'],
                'weight'      => $post['weight'],
                'icon'        => UrlUtils::getRelativeUrl($post['icon']),
                'background'  => UrlUtils::getRelativeUrl($post['background']),
                'upgrade'     => json_encode($upgrade),
                'equity'      => $post['equity'],
                'is_disable'  => $post['is_disable']
            ]);

            return true;
        } catch (Exception $e) {
            static::$error = $e->getMessage();
            return false;
        }
    }

    /**
     * 编辑会员等级
     *
     * @author windy
     * @param array $post
     * @return bool
     */
    public static function edit(array $post): bool
    {
        try {
            $weight = (new UserGrade())
                ->where('id', '<>', intval($post['id']))
                ->where(['weight'=>intval($post['weight']), 'is_delete'=>0])
                ->findOrEmpty();
            if (!$weight->isEmpty()) {
                throw new Exception('权重等级'.$post['weight'].'已存在');
            }

            $upgrade = [
                'total_growth_value'  => $post['total_growth_value']  ?? 0
            ];

            UserGrade::update([
                'name'       => $post['name'],
                'weight'     => $post['weight'],
                'icon'        => UrlUtils::getRelativeUrl($post['icon']),
                'background'  => UrlUtils::getRelativeUrl($post['background']),
                'upgrade'     => json_encode($upgrade),
                'equity'     => $post['equity'],
                'is_disable' => $post['is_disable']
            ], ['id'=>(int)$post['id']]);

            return true;
        } catch (Exception $e) {
            static::$error = $e->getMessage();
            return false;
        }
    }

    /**
     * 删除会员等级
     *
     * @author windy
     * @param int $id
     * @return bool
     */
    public static function del(int $id): bool
    {
        try {
            UserGrade::update([
                'is_delete'   => 1,
                'delete_time' => time()
            ], ['id'=>intval($id)]);

            return true;
        } catch (Exception $e) {
            static::$error = $e->getMessage();
            return false;
        }
    }
}