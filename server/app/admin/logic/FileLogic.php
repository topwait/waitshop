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

namespace app\admin\logic;


use app\common\basics\Logic;
use app\common\model\File;
use app\common\model\FileGroup;
use app\common\utils\UrlUtils;
use Exception;

/**
 * 文件管理-逻辑层
 * Class FileLogic
 * @package app\admin\logic
 */
class FileLogic extends Logic
{
    /**
     * 文件列表
     *
     * @author windy
     * @param int $group_id (组ID)
     * @param int $type (文件类型: 1=图片, 2=视频)
     * @param int $page (当前页码)
     * @param int $limit (每页多少条)
     * @return array
     * @throws @\think\db\exception\DbException
     */
    public static function lists(int $group_id=-1, int $type=0, int $page=1, int $limit=48): array
    {
        // 条件
        $where = [
            ['is_delete', '=', 0],
            ['file_type', '=', intval($type)]
        ];
        if ($group_id !== -1) {
            $where[] = ['group_id', '=', $group_id];
        }

        // 查询
        $model = new File();
        $lists = $model->field(true)->where($where)
            ->order('id', 'desc')
            ->paginate([
                'page'      => $page,
                'list_rows' => $limit,
                'var_page'  => 'page'
            ])->toArray();

        foreach ($lists['data'] as &$item) {
            $item['file_path'] = UrlUtils::getRelativeUrl($item['file_url']);
        }

        return $lists;
    }

    /**
     * 文件重命名
     *
     * @author windy
     * @param array $post
     * @return bool
     */
    public static function rename(array $post): bool
    {
        try {
            File::update([
                'file_name'   => $post['file_name'],
                'update_time' => time()
            ], ['id' => intval($post['file_id'])]);
            return true;
        } catch (Exception $e) {
            static::$error = $e->getMessage();
            return false;
        }
    }

    /**
     * 文件删除
     *
     * @author windy
     * @param array $post
     * @return bool
     */
    public static function recycle(array $post): bool
    {
        try {
            File::update([
                'is_delete'   => 1,
                'delete_time' => time()
            ], array(['id', 'in', $post['files']]));
            return true;
        } catch (Exception $e) {
            static::$error = $e->getMessage();
            return false;
        }
    }

    /**
     * 文件移动
     *
     * @author windy
     * @param $post
     * @return bool
     */
    public static function move(array $post): bool
    {
        try {
            File::update([
                'group_id'    => intval($post['group_id']),
                'update_time' => time()
            ], array(['id', 'in', $post['files']]));
            return true;
        } catch (Exception $e) {
            static::$error = $e->getMessage();
            return false;
        }
    }


    /**
     * 获取文件组
     *
     * @author windy
     * @param int $type (类型: 1=图片, 2=视频)
     * @return array
     */
    public static function getFileGroup(int $type): array
    {
        try {
            // 条件
            $where = [
                ['is_delete', '=', 0],
                ['type', '=', intval($type)]
            ] ;
            // 查询
            $model = new FileGroup();
            return $model->field(true)->where($where)
                ->select()->toArray();
        } catch (Exception $e) {
            static::$error = $e->getMessage();
            return [];
        }
    }

    /**
     * 新增文件分组
     *
     * @author windy
     * @param array $post
     * @return bool|int
     */
    public static function addFileGroup(array $post)
    {
        try {
             $group = FileGroup::create([
                'name'        => $post['group_name'],
                'create_time' => time(),
                'update_time' => time()
            ]);

            return $group['id'];
        } catch (Exception $e) {
            static::$error = $e->getMessage();
            return false;
        }
    }

    /**
     * 编辑文件分组名
     *
     * @author windy
     * @param array $post
     * @return bool
     */
    public static function editFileGroup(array $post): bool
    {
        try {
            FileGroup::update([
                'name'        => $post['group_name'],
                'update_time' => time()
            ], ['id'=>(int)$post['group_id']]);

            return true;
        } catch (Exception $e) {
            static::$error = $e->getMessage();
            return false;
        }
    }

    /**
     * 删除文件分组
     *
     * @author windy
     * @param int $id (主键ID)
     * @return FileGroup|bool
     */
    public static function delGroup(int $id)
    {
        try {
            $where[] = is_numeric($id) ? ['id', '=', (int)$id] : ['id', 'in', $id];
            FileGroup::update([
                'is_delete'   => 1,
                'delete_time' => time()
            ], $where);

            return true;
        } catch (Exception $e) {
            static::$error = $e->getMessage();
            return false;
        }
    }
}