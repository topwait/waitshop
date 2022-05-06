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

namespace app\admin\validate\addons;


use app\common\basics\Validates;
use app\common\model\user\User;

/**
 * 分销参数验证器
 * Class DistributionValidate
 * @package app\api\validate
 */
class DistributionValidate extends Validates
{
    protected $rule = [
        'id'        => 'require|isInteger',
        'status'    => 'require|in:0,1',
        'uid'       => 'require|isInteger',
        'leader_sn' => 'require|checkCode',
        'sn'        => 'require'
    ];

    protected $message = [
        'id.require'        => 'id参数缺失',
        'isInteger.require' => 'id参数必须为正整数',
        'uid.require'       => 'uid参数缺失',
        'uid.isInteger'     => 'uid参数必须为正整数',
        'leader_sn.require' => 'leader_sn参数缺失',
        'status.require'    => 'status参数缺失',
        'status.in'         => 'status参数不是合法值',
        'require.require'   => 'sn参数缺失',
    ];

    protected $scene = [
        // 主键
        'id'      => ['id'],
        // 用户
        'uid'     => ['uid'],
        // 编号
        'sn'      => ['sn'],
        // 调整上级
        'leader'  => ['uid', 'leader'],
        // 审核申请
        'audit'   => ['id', 'status']
    ];

    /**
     * 验证邀请码
     * @author windy
     * @param $value
     * @param $rule
     * @param $data
     * @return bool|string
     */
    protected function checkCode($value, $rule, $data)
    {
        if ($value <= 0) {
            return true;
        }

        unset($rule);
        $model = new User();

        $first_leader = $model->field(['id,is_distribution,ancestor_relation'])
            ->where(['id' => $value])
            ->findOrEmpty()->toArray();

        if (empty($first_leader)) {
            return '请填写有效的推荐人ID';
        }

        if ($first_leader['is_distribution'] == 0) {
            return '对方不是分销会员';
        }

        if ($first_leader['id'] == $data['uid']) {
            return '上级推荐人不能是自己';
        }

        $ancestor_relation = explode(',', $first_leader['ancestor_relation']);
        if (!empty($ancestor_relation) && in_array($data['uid'], $ancestor_relation)) {
            return '不能填写所有下级的任意一人的编号';
        }

        return true;
    }
}