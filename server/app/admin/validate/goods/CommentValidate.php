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

namespace app\admin\validate\goods;


use app\common\basics\Validates;

/**
 * 评论参数验证器
 * Class CommentValidate
 * @package app\admin\validate\product
 */
class CommentValidate extends Validates
{
    protected $rule = [
        'id'    => 'require',
        'reply' => 'require|min:2|max:500'
    ];

    protected $message = [
        'id.require'    => '缺少id参数',
        'reply.require' => '缺少reply参数',
        'reply.min'     => '回复的内容不能少于2个字符',
        'reply.max'     => '回复的内容不能大于500个字符',
    ];

    protected $scene = [
        'id'    => ['id'],
        'reply' => ['id', 'reply']
    ];
}