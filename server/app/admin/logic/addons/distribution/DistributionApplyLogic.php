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

namespace app\admin\logic\addons\distribution;


use app\common\basics\Logic;
use app\common\model\addons\DistributionApply;
use app\common\model\user\User;
use Exception;

/**
 * 分销申请-逻辑层
 * Class ApplyLogic
 * @package app\admin\logic\marketing\distribution
 */
class DistributionApplyLogic extends Logic
{
    /**
     * 分销申请列表
     *
     * @author windy
     * @param array $get
     * @return array
     * @throws @\think\db\exception\DbException
     */
    public static function lists(array $get): array
    {
        self::setSearch([
            '=' => ['type@status']
        ]);

        $model = new DistributionApply();
        $lists = $model->field(true)
            ->where(self::$searchWhere)
            ->order('id', 'desc')
            ->with(['user'])
            ->paginate([
                'page'      => $get['page'],
                'list_rows' => $get['limit'],
                'var_page'  => 'page',
            ])->toArray();

        foreach ($lists['data'] as &$item) {
            if ($item['invite_code']) {
                $item['upperUser'] = (new User())->field(['id,avatar,nickname,mobile'])
                    ->where(['distribution_code'=>$item['invite_code']])
                    ->findOrEmpty()->toArray();
            }
        }

        return ['count'=>$lists['total'], 'list'=>$lists['data']];
    }

    /**
     * 统计数据
     *
     * @author windy
     * @return array
     */
    public static function statistics(): array
    {
        $model = new DistributionApply();
        $detail['stay']  = $model->where(['status'=>0])->count();
        $detail['ok']    = $model->where(['status'=>1])->count();
        $detail['fail']  = $model->where(['status'=>2])->count();
        return $detail;
    }

    /**
     * 审核申请
     *
     * @author windy
     * @param array $post
     * @return bool
     */
    public static function audit(array $post): bool
    {
        try {
            $apply = (new DistributionApply())->findOrEmpty(intval($post['id']))->toArray();

            if (!$apply) {
                throw new Exception('数据不存在');
            }

            if ($apply['status'] != 0) {
                throw new Exception('申请已被处理, 请刷新列表');
            }

            if ($post['status'] == 1) {
                User::update([
                    'is_distribution' => 1,
                    'update_time'     => time()
                ], ['id'=>$apply['user_id']]);
            }

            DistributionApply::update([
                'status'       => $post['status'],
                'audit_reason' => $post['audit_reason'] ?? '',
                'update_time'  => time()
            ], ['id'=>intval($post['id'])]);

            return true;
        } catch (Exception $e) {
            static::$error = $e->getMessage();
            return false;
        }
    }
}