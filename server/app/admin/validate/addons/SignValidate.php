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

class SignValidate extends Validates
{
    protected $rule = [
        'id'            => 'require|isInteger',
        'days'          => 'require|isInteger',
        'give_integral' => 'require|number',
        'give_growth'   => 'require|number'
    ];

    protected $message = [
        'id.require'            => '缺少id参数',
        'id.isInteger'          => 'id必须为正整数',
        'days.require'          => '连续签到必须大于0',
        'give_integral.require' => '缺少give_integral参数',
        'give_integral.number'  => '赠送积分必须是数字',
        'give_growth.require'   => '缺少give_growth参数',
        'give_growth.number'    => '赠送成长值必须是数字',
    ];

    protected $scene = [
        'id'   => ['id'],
        'add'  => ['days', 'give_integral', 'give_growth'],
        'edit' => ['id', 'days', 'give_integral', 'give_growth'],
    ];
}