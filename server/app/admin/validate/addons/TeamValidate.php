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
 * 拼团参数验证器
 * Class SharingValidate
 * @package app\admin\validate\addons
 */
class TeamValidate extends Validates
{
    protected $rule = [
        'id'              => 'require|isInteger',
        'goods_id'        => 'require|isInteger',
        'name'            => 'require',
        'intro'           => 'require',
        'image'           => 'require',
        'banner'          => 'require',
        'min_buy'         => 'require|number',
        'max_buy'         => 'require|number',
        'people_num'      => 'require|number|egt:2',
        'is_automatic'    => 'require|in:0,1',
        'is_coupon'       => 'require|in:0,1',
        'is_distribution' => 'require|in:0,1',
        'effective_time'  => 'require|isInteger|egt:1',
        'activity_time'   => 'require|checkActivityTime',
    ];

    protected $scene = [
        'id'   => ['id'],
        'add'  => [
            'goods_id', 'name', 'intro', 'image', 'banner', 'min_buy', 'max_buy',
            'people_num', 'is_automatic', 'is_coupon', 'is_distribution', 'effective_time',
            'activity_time'
        ],
        'edit' => [
            'id', 'goods_id', 'name', 'intro', 'image', 'banner', 'min_buy', 'max_buy',
            'people_num', 'is_automatic', 'is_coupon', 'is_distribution', 'effective_time',
            'activity_time'
        ],
    ];


    /**
     * 验证活动时间规则
     *
     * @author windy
     * @param $value
     * @return bool|string
     */
    public function checkActivityTime($value)
    {
        $datetime = explode(' - ', $value);
        $start_time = strtotime($datetime[0]);
        $end_time   = strtotime($datetime[1]);

        if ($start_time >= $end_time) {
            return '活动开始时间不可大于结束时间哦';
        }

        if ($end_time <= time()) {
            return '活动结束时间不可小于当前时间';
        }

        return true;
    }
}