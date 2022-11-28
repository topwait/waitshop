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

namespace app\admin\logic\diy;


use app\common\basics\Logic;
use app\common\enum\LinkEnum;
use app\common\model\diy\DiyMe;
use Exception;

/**
 * 我的功能设计-逻辑层
 * Class MeLogic
 * @package app\admin\logic\diy
 */
class MeLogic extends Logic
{
    /**
     * 我的功能列表
     *
     * @author windy
     * @param array $get
     * @return array
     * @throws @\think\db\exception\DbException
     */
    public static function lists(array $get): array
    {
        $lists = (new DiyMe())
            ->withoutField('is_delete,delete_time')
            ->order('sort asc, id asc')
            ->where(['is_delete'=>0])
            ->paginate([
                'page'      => $get['page'] ?? 1,
                'list_rows' => $get['limit'] ?? 20,
                'var_page'  => 'page'
            ])->toArray();

        foreach ($lists['data'] as &$item) {
            if ($item['link_type'] == 1) {
                $item['link_url'] = LinkEnum::getLink(intval($item['link_url']))['name'] ?? '未知';
            }
        }

        return ['count'=>$lists['total'], 'list'=>$lists['data']];
    }

    /**
     * 我的功能详情
     *
     * @author windy
     * @param int $id
     * @return array
     */
    public static function detail(int $id): array
    {
        return (new DiyMe())
            ->withoutField('is_delete,delete_time')
            ->findOrEmpty(intval($id))
            ->toArray();
    }

    /**
     * 新增我的功能
     *
     * @author windy
     * @param array $post
     * @return bool
     */
    public static function add(array $post): bool
    {
        try {
            $linkUrl = $post['link_url'] ?? '';
            if ($post['link_type'] == 1) {
                $linkUrl = intval($post['link_mod']);
            }

            DiyMe::create([
                'name'        => $post['name'],
                'image'       => $post['image'],
                'sort'        => $post['sort'],
                'link_type'   => $post['link_type'],
                'link_url'    => $linkUrl,
                'is_show'     => $post['is_show'],
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
     * 编辑我的功能
     *
     * @author windy
     * @param array $post
     * @return bool
     */
    public static function edit(array $post): bool
    {
        try {
            $linkUrl = $post['link_url'] ?? '';
            if ($post['link_type'] == 1) {
                $linkUrl = intval($post['link_mod']);
            }

            DiyMe::update([
                'name'        => $post['name'],
                'image'       => $post['image'],
                'sort'        => $post['sort'],
                'link_type'   => $post['link_type'],
                'link_url'    => $linkUrl,
                'is_show'     => $post['is_show'],
                'update_time' => time()
            ], ['id'=>intval($post['id'])]);

            return true;
        } catch (Exception $e) {
            static::$error = $e->getMessage();
            return false;
        }
    }

    /**
     * 删除我的功能
     *
     * @author windy
     * @param int $id
     * @return bool
     */
    public static function del(int $id): bool
    {
        try {
            DiyMe::update([
                'is_delete'   => 1,
                'delete_time' => time()
            ], ['id'=>intval($id)]);

            return true;
        } catch (Exception $e) {
            static::$error = $e->getMessage();
            return false;
        }
    }

    /**
     * 修改我的功能状态
     *
     * @param int $id
     * @return bool
     */
    public static function status(int $id): bool
    {
        try {
            $detail = (new DiyMe())
                ->field('id,name,is_show')
                ->where(['id'=>intval($id)])
                ->where(['is_delete'=>0])
                ->findOrEmpty()
                ->toArray();

            if (!$detail) {
                throw new Exception('数据不存在');
            }

            DiyMe::update([
                'is_show'     => !$detail['is_show'],
                'update_time' => time()
            ], ['id'=>intval($id)]);

            return true;
        } catch (Exception $e) {
            static::$error = $e->getMessage();
            return false;
        }
    }
}