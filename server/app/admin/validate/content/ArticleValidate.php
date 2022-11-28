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

namespace app\admin\validate\content;


use app\common\basics\Validates;

/**
 * 文章数据参数验证器
 * Class ArticleValidate
 * @package app\admin\validate\content
 */
class ArticleValidate extends Validates
{
    protected $rule = [
        'id'    => 'require|isInteger',
        'title' => 'require|max:100',
        'image' => 'require|max:200',
        'intro' => 'max:250',
        'sort'  => 'require|number',
        'is_notice' => 'require|in:0,1',
        'is_show'   => 'require|in:0,1',
    ];

    protected $message = [
        'id.require'    => '缺少id参数',
        'id.isInteger'  => 'id参数必须为正整数',
        'title.require' => '请填写文章标题',
        'title.max'     => '文章标题不能超出100个字符',
        'image.require' => '请选择封面图',
        'image.max'     => '封面图Url不能超出200个字符',
        'intro.max'     => '文章简介不能超出250个字符',
        'is_notice.require' => '请选择是否是商城公告',
        'is_notice.in'      => '商城公告选择不在合法值内',
        'is_show.require'   => '请选择是否显示',
        'is_show.in'        => '是否显示选择不在合法值内',
    ];

    protected $scene = [
        'id'   => ['id'],
        'add'  => ['title', 'image', 'intro', 'sort', 'is_notice', 'is_show'],
        'edit' => ['id', 'title', 'image', 'intro', 'sort', 'is_notice', 'is_show'],
    ];
}