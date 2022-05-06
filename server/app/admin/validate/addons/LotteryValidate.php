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

/**
 * 抽奖参数验证器
 * Class LotteryValidate
 * @package app\admin\validate\addons
 */
class LotteryValidate extends Validates
{
    protected $rule = [
        'type'        => 'require|number',
        'name'        => 'require|max:200',
        'image'       => 'require|max:200',
        'number'      => 'require|number',
        'reward'      => 'require|number',
        'tips'        => 'require|max:200',
        'sort'        => 'require|number',
        'probability' => 'require|float',
    ];

    protected $message = [
        'type.require'        => '请选择奖品类型',
        'type.number'         => 'type参数必须为数字',
        'name.require'        => '请输入奖品名称',
        'name.max'            => '奖品名称不能大于200个字符',
        'image.require'       => '请选择奖品图片',
        'image.max'           => '选择的图片路径超过长度',
        'number.require'      => '请设定奖品数量',
        'number.number'       => '奖品数量必须为数字',
        'reward.require'      => '请设定奖励奖励内容',
        'reward.number'       => '奖励内容必须为数字',
        'tips.require'        => '请设定奖励提示语',
        'tips.max'            => '提示内容不能大于200个字符',
        'sort.require'        => '请输入排序号',
        'sort.number'         => '排序号必须为数字',
        'probability.require' => '请输入中奖比例值',
        'probability.float'   => '中奖比例只允许是浮点数',
    ];

    protected $scene = [
        'edit' => []
    ];
}