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

namespace app\api\validate;


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
        'code' => 'require|checkCode',

        'nickname'     => 'require|max:10',
        'telephone'    => 'require|mobile',
        'apply_reason' => 'max:250',
    ];

    protected $message = [
        'code.require' => '请输入邀请码',

        'nickname.require'      => '请输入真实姓名',
        'nickname.max'          => '真实姓名不能超出10个字符',
        'telephone.require'     => '请输入联系电话',
        'telephone.mobile'      => '联系电话不合法',
        'apply_reason.max'      => '申请理由不能超出250个字符',
    ];

    protected $scene = [
        'code'  => ['code'],
        'apply' => ['nickname', 'telephone', 'apply_reason']
    ];

    /**
     * 验证邀请码
     * @param $value
     * @param $rule
     * @param $data
     * @return bool|string
     */
    protected function checkCode($value, $rule, $data)
    {
        unset($rule);
        $model = new User();
        if ($model->where(['id'=>$data['user_id']])->value('first_leader')) {
            return '已有邀请人';
        }

        $first_leader = $model->field(['id,is_distribution,ancestor_relation'])
            ->where(['distribution_code' => $value])
            ->findOrEmpty()->toArray();

        if (empty($first_leader)) {
            return '请填写有效的邀请码';
        }

        if ($first_leader['is_distribution'] == 0) {
            return '对方不是分销会员';
        }

        if ($first_leader['id'] == $data['user_id']) {
            return '不能邀请自己';
        }

        $ancestor_relation = explode(',', $first_leader['ancestor_relation']);
        if (!empty($ancestor_relation) && in_array($data['user_id'], $ancestor_relation)) {
            return '不能填写所有下级的任意一人的邀请码';
        }

        return true;
    }
}