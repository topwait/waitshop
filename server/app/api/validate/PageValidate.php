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

namespace app\api\validate;


use app\common\basics\Validates;

/**
 * 公用分页参数验证器
 * Class PageValidate
 * @package app\api\validate
 */
class PageValidate extends Validates
{
    protected $rule = [
        'page'  => 'number|minValue:1',
        'limit' => 'number|maxValue:100'
    ];

    protected $message = [
        'page.number'    => 'page参数必须为数字',
        'page.minValue'  => 'page参数最小值需为1',
        'limit.number'   => 'limit参数必须为数字',
        'limit.maxValue' => 'limit参数最大值为100'
    ];
}