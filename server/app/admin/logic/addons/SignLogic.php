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

namespace app\admin\logic\addons;


use app\common\basics\Logic;
use app\common\model\addons\SignRecord;
use app\common\model\addons\SignRule;
use app\common\utils\ConfigUtils;
use Exception;

/**
 * 每日签到-逻辑层
 * Class SignLogic
 * @package app\admin\logic\addons
 */
class SignLogic extends Logic
{
    /**
     * 连续签到规则
     *
     * @author windy
     * @param array $get
     * @return array
     * @throws @\think\db\exception\DbException
     */
    public static function lists(array $get): array
    {
        $lists = (new SignRule)
            ->withoutField('is_delete,delete_time')
            ->order('days asc, id desc')
            ->paginate([
                'page'      => $get['page'],
                'list_rows' => $get['limit'],
                'var_page'  => 'page',
            ])->toArray();

        return ['count'=>$lists['total'], 'list'=>$lists['data']];
    }

    /**
     * 连续签到规则详细
     *
     * @author windy
     * @param int $id
     * @return array
     */
    public static function detail(int $id): array
    {
        return (new SignRule)
            ->withoutField('is_delete,delete_time')
            ->findOrEmpty(intval($id))
            ->toArray();
    }

    /**
     * 新增连续签到规则
     *
     * @author windy
     * @param array $post
     * @return bool
     */
    public static function add(array $post): bool
    {
        try {
            SignRule::create([
                'days'          => $post['days'],
                'give_integral' => $post['give_integral'],
                'give_growth'   => $post['give_integral'],
                'create_time'   => time(),
                'update_time'   => time()
            ]);

            return true;
        } catch (Exception $e) {
            static::$error = $e->getMessage();
            return false;
        }
    }

    /**
     * 编辑连续签到规则
     *
     * @author windy
     * @param array $post
     * @return bool
     */
    public static function edit(array $post): bool
    {
        try {
            SignRule::update([
                'days'          => $post['days'],
                'give_integral' => $post['give_integral'],
                'give_growth'   => $post['give_growth'],
                'update_time'   => time()
            ], ['id'=>intval($post['id'])]);

            return true;
        } catch (Exception $e) {
            static::$error = $e->getMessage();
            return false;
        }
    }

    /**
     * 删除连续签到规则
     *
     * @author windy
     * @param int $id
     * @return bool
     */
    public static function del(int $id): bool
    {
        try {
            SignRule::update([
                'is_delete'     => 1,
                'update_time'   => time()
            ], ['id'=>intval($id)]);

            return true;
        } catch (Exception $e) {
            static::$error = $e->getMessage();
            return false;
        }
    }

    /**
     * 获取签到记录
     *
     * @author windy
     * @param array $get
     * @return array
     * @throws @\think\db\exception\DbException
     */
    public static function record(array $get): array
    {
        $lists = (new SignRecord())
            ->field(true)
            ->order('id desc')
            ->paginate([
                'page'      => $get['page'],
                'list_rows' => $get['limit'],
                'var_page'  => 'page',
            ])->toArray();

        return ['count'=>$lists['total'], 'list'=>$lists['data']];
    }

    /**
     * 更新签到配置
     *
     * @author windy
     * @param array $post
     * @return bool
     */
    public static function setting(array $post): bool
    {
        try {
            ConfigUtils::set('sign', [
                'is_open' => $post['is_open'] ?? 0,
                'explain' => $post['explain'] ?? ''
            ], '签到配置');

            return true;
        } catch (Exception $e) {
            static::$error = $e->getMessage();
            return false;
        }
    }
}