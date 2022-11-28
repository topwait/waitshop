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

namespace app\admin\logic\system;


use app\common\basics\Logic;
use app\common\model\Crontab;
use Exception;

/**
 * 定时任务配置-逻辑层
 * Class CrontabLogic
 * @package app\admin\logic\set
 */
class CrontabLogic extends Logic
{
    /**
     * 计划任务列表
     *
     * @author windy
     * @param array $get
     * @return array|false
     * @throws @\think\db\exception\DbException
     */
    public static function lists(array $get)
    {
        $model = new Crontab();
        $lists = $model->field(true)
            ->order('id asc')
            ->paginate([
                'page'      => $get['page'],
                'list_rows' => $get['limit'],
                'var_page'  => 'page',
            ])->toArray();

        foreach ($lists['data'] as &$item) {
            $item['params'] = $item['params'] ?: '无';
            $item['rules'] = $item['rules'] ?: '无';
            $item['error'] = $item['error'] ?: '无';
            $item['exe_time'] = $item['exe_time'].'ms';
            $item['max_time'] = $item['max_time'].'ms';
            $item['last_time'] = date('Y-m-d H:i:s', $item['last_time']);
        }

        return ['count'=>$lists['total'], 'list'=>$lists['data']];
    }

    /**
     * 任务详细
     *
     * @author windy
     * @param int $id
     * @return array
     */
    public static function detail(int $id): array
    {
        $model = new Crontab();
        return $model->field(true)->findOrEmpty($id)->toArray();
    }

    /**
     * 新增计划任务
     *
     * @author windy
     * @param array $post
     * @return bool
     */
    public static function add(array $post): bool
    {
        try {
            Crontab::create([
                'name'    => $post['name'],
                'command' => $post['command'] ?? '',
                'params'  => $post['params'] ?? '',
                'rules'   => $post['rules'] ?? '',
                'remarks' => $post['remarks'] ?? '',
                'status'  => $post['status']
            ]);

            return true;
        } catch (Exception $e) {
            static::$error = $e->getMessage();
            return false;
        }
    }

    /**
     * 编辑计划任务
     *
     * @author windy
     * @param array $post
     * @return bool
     */
    public static function edit(array $post): bool
    {
        try {
            Crontab::update([
                'name'    => $post['name'],
                'command' => $post['command'] ?? '',
                'params'  => $post['params'] ?? '',
                'rules'   => $post['rules'] ?? '',
                'remarks' => $post['remarks'] ?? '',
                'status'  => $post['status']
            ], ['id'=>intval($post['id'])]);

            return true;
        } catch (Exception $e) {
            static::$error = $e->getMessage();
            return false;
        }
    }

    /**
     * 销毁任务
     *
     * @author windy
     * @param int $id
     * @return bool
     */
    public static function destroy(int $id): bool
    {
        try {
            Crontab::destroy($id);
            return true;
        } catch (Exception $e) {
            static::$error = $e->getMessage();
            return false;
        }
    }

    /**
     * 停止执行
     *
     * @author windy
     * @param int $id
     * @return bool
     */
    public static function stop(int $id): bool
    {
        try {
            Crontab::update([
                'status'      => 2,
                'update_time' => time()
            ], ['id'=>intval($id)]);

            return true;
        } catch (Exception $e) {
            static::$error = $e->getMessage();
            return false;
        }
    }

    /**
     * 开始执行
     *
     * @author windy
     * @param int $id
     * @return bool
     */
    public static function run(int $id): bool
    {
        try {
            Crontab::update([
                'status'      => 1,
                'error'       => '',
                'exe_time'    => 0,
                'max_time'    => 0,
                'update_time' => time()
            ], ['id'=>intval($id)]);

            return true;
        } catch (Exception $e) {
            static::$error = $e->getMessage();
            return false;
        }
    }
}