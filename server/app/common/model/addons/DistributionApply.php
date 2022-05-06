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

namespace app\common\model\addons;


use app\common\basics\Models;

/**
 * 分销申请模型
 * Class DistributionApply
 * @package app\common\model\marketing
 */
class DistributionApply extends Models
{
    // 设置字段信息
    protected $schema = [
        'id'             => 'int',    //主键ID
        'user_id'        => 'int',    //用户ID
        'nickname'       => 'int',    //真实姓名
        'telephone'      => 'int',    //联系电话
        'apply_reason'   => 'int',    //申请理由
        'refuse_reason'  => 'int',    //拒绝理由
        'invite_code'    => 'string', //邀请码
        'province_id'    => 'int',    //省
        'city_id'        => 'int',    //市
        'district_id'    => 'int',    //区
        'status'         => 'int',    //状态[0=待审核, 1=审核通过, 2=审核拒绝]
        'create_time'    => 'int',    //创建时间
        'update_time'    => 'int'     //更新时间
    ];

    /**
     * 单关联: 用户模型
     */
    public function user()
    {
        return $this->hasOne('app\common\model\user\User', 'id', 'user_id')
            ->field(['id', 'nickname', 'avatar', 'mobile', 'sex', 'money', 'first_leader']);
    }
}