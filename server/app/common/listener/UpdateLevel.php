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

namespace app\common\listener;


use app\common\model\user\User;
use app\common\model\user\UserGrade;

/**
 * 用户等级升级钩子
 * Class UpdateLevel
 * @package app\common\listener
 */
class UpdateLevel
{
    /**
     * 升级处理方法
     *
     * @author windy
     * @param array $params
     * @throws @\think\db\exception\DataNotFoundException
     * @throws @\think\db\exception\DbException
     * @throws @\think\db\exception\ModelNotFoundException
     */
    public function handle(array $params)
    {
        // 接收参数
        $userId = intval($params['user_id']);

        // 获取用户信息
        $user = (new User())->field(['id,growth_value'])->findOrEmpty($userId)->toArray();
        $totalGrowthValue = $user['growth_value'];

        // 获取所有等级
        $grades = (new UserGrade())
            ->field('id,name,weight,upgrade')
            ->where(['is_disable'=>0, 'is_delete'=>0])
            ->order('weight desc')
            ->select()
            ->toArray();

        // 循环验证是否满足升级条件
        foreach ($grades as $level) {
            $upgradeGrowthValue  = $level['upgrade']['total_growth_value']  ?? 0; //累计成长值数
            if ($totalGrowthValue >= $upgradeGrowthValue) {
                User::update([
                    'grade_id'    => $level['id'],
                    'update_time' => time()
                ], ['id'=>$userId]);
                break;
            }
        }
    }
}