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
use app\common\model\goods\Goods;
use app\common\model\goods\GoodsSpec;
use think\model\relation\HasMany;

/**
 * 拼团活动模型
 * Class Sharing
 * @package app\common\model\marketing
 */
class Team extends Models
{
    // 设置字段信息
    protected $schema = [
        'id'              => 'int',    //主键ID
        'goods_id'        => 'int',    //商品ID
        'name'            => 'string', //拼团名称
        'intro'           => 'string', //拼团简介
        'image'           => 'string', //商品主图
        'banner'          => 'string', //商品轮播
        'min_buy'         => 'int',    //每单最低购买数
        'max_buy'         => 'int',    //每单最高购买数
        'min_team_price'  => 'int',    //最低拼团价
        'max_team_price'  => 'int',    //最高拼团价
        'people_num'      => 'int',    //成团所需人数
        'total_found_number'   => 'int', //累计开团次数: 无论是否支付
        'total_join_number'    => 'int', //累计参与次数: 无论是否支付
        'total_success_number' => 'int', //累计拼成功次数
        'click_count'     => 'int',    //浏览量
        'status'          => 'int',    //状态[1=未开始, 2=进行中, 3=已结束, 4=手动结束]
        'is_automatic'    => 'int',    //自动成团[0=否, 1=是]
        'is_coupon'       => 'int',    //用优惠券[0=否, 1=是]
        'is_delete'       => 'int',    //是否删除[0=否, 1=是]
        'effective_time'  => 'int',    //成团有效期, 单位: 分钟
        'start_time'      => 'int',    //活动开始时间
        'end_time'        => 'int',    //活动结束时间
        'create_time'     => 'int',    //创建时间
        'update_time'     => 'int',    //更新时间
        'delete_time'     => 'int',    //删除时间
    ];

    /**
     * 多关联: 拼团规格模型
     */
    public function teamSku(): HasMany
    {
        return $this->hasMany(TeamSku::class, 'team_id', 'id');
    }

    /**
     * 关联: 商品规格模型
     */
    public function spec(): HasMany
    {
        return $this->hasMany( GoodsSpec::class, 'goods_id', 'goods_id');
    }


    /**
     * 获取器: 获取已拼团的数量(不分是否成功)
     *
     * @author windy
     * @param $value
     * @param $data
     * @return int
     */
    public function getTeamVolumeAttr($value, $data): int
    {
        unset($value);
        return (new TeamJoin())->where(['team_id'=>$data['id']])->count();
    }

    /**
     * 获取器: 获取商品原售价
     *
     * @author windy
     * @param $value
     * @param $data
     * @return mixed|string
     */
    public function getOriginalPriceAttr($value, $data): string
    {
        unset($value);
        $result = (new Goods())->field(['min_price', 'max_price'])->findOrEmpty($data['goods_id']);

        if (!$result->isEmpty()) {
            if ($result['min_price'] == $result['max_price']) {
                return $result['min_price'];
            } else {
                return $result['min_price'] .' ~ ' . $result['max_price'];
            }
        }

        return '未知';
    }
}