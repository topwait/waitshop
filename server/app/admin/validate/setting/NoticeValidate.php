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

namespace app\admin\validate\setting;


use app\common\basics\Validates;

/**
 * 通知配置参数验证器
 * Class NoticeValidate
 * @package app\admin\validate\setting
 */
class NoticeValidate extends Validates
{
    protected $rule = [
        'id'            => 'require|isInteger',
        'template_code' => 'require',
        'content'       => 'require',
        'name'          => 'require',
        'first'         => 'require',
        'template_sn'   => 'require',
        'template_id'   => 'require',
        'remark'        => 'require',
        'status'        => 'require|in:0,1',
    ];

    protected $scene = [
        'sms' => ['id', 'template_code', 'content'],
        'oa'  => ['id', 'name', 'first', 'template_sn', 'template_id', 'remark', 'status'],
        'mnp' => ['id', 'name', 'template_sn', 'template_id', 'status'],
    ];
}