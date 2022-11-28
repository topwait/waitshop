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

namespace app\admin\logic\addons;


use app\common\basics\Logic;
use app\common\enum\AdEnum;
use app\common\model\addons\Ad;
use Exception;

/**
 * 营销广告-逻辑层
 * Class AdLogic
 * @package app\admin\logic\marketing
 */
class AdLogic extends Logic
{
    /**
     * 广告列表
     *
     * @author windy
     * @param array $get
     * @return array
     * @throws @\think\db\exception\DbException
     */
    public static function lists(array $get): array
    {
        $model = new Ad();
        $lists = $model->field(true)
            ->order('sort', 'desc')
            ->order('id', 'asc')
            ->where(['is_delete'=>0])
            ->paginate([
                'page'      => $get['page'] ?? 1,
                'list_rows' => $get['limit'] ?? 20,
                'var_page'  => 'page'
            ])->toArray();

        foreach ($lists['data'] as &$item) {
            $item['position'] = AdEnum::getPositionDesc($item['position']);
            $item['link_type'] = AdEnum::getLinkTypeDesc($item['link_type']);
        }

        return ['count'=>$lists['total'], 'list'=>$lists['data']];
    }

    /**
     * 广告详细
     *
     * @author windy
     * @param int $id
     * @return array
     */
    public static function detail(int $id): array
    {
        $model = new Ad();
        return $model->field(true)->findOrEmpty(intval($id))->toArray();
    }

    /**
     * 新增广告
     *
     * @author windy
     * @param array $post
     * @return bool
     */
    public static function add(array $post): bool
    {
        try {
            Ad::create([
                'position'  => $post['position'],
                'name'      => $post['name'],
                'image'     => $post['image'],
                'sort'      => $post['sort'] ?? 0,
                'link_type' => $post['link_type'] ?? 10,
                'link_url'  => $post['link_url'] ?? '',
                'is_show'   => $post['is_show'],
                'is_delete' => 0
            ]);

            return true;
        } catch (Exception $e) {
            static::$error = $e->getMessage();
            return false;
        }
    }

    /**
     * 编辑广告
     *
     * @author windy
     * @param array $post
     * @return bool
     */
    public static function edit(array $post): bool
    {
        try {
            Ad::update([
                'position'  => $post['position'],
                'name'      => $post['name'],
                'image'     => $post['image'],
                'sort'      => $post['sort'] ?? 0,
                'link_url'  => $post['link_url'] ?? 10,
                'link_type' => $post['link_type'] ?? '',
                'is_show'   => $post['is_show']
            ], ['id'=>(int)$post['id']]);

            return true;
        } catch (Exception $e) {
            static::$error = $e->getMessage();
            return false;
        }
    }

    /**
     * 删除广告
     *
     * @author windy
     * @param int $id (广告ID)
     * @return bool
     */
    public static function del(int $id): bool
    {
        try {
            Ad::update([
                'is_show'   => 0,
                'is_delete' => 1,
                'delete_time' => time()
            ], ['id'=>intval($id)]);

            return true;
        } catch (Exception $e) {
            static::$error = $e->getMessage();
            return false;
        }
    }
}