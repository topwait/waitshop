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

namespace app\common\model\user;


use app\common\basics\Models;
use app\common\model\Region;
use think\model\relation\HasOne;

class UserAddress extends Models
{
    // 设置字段信息
    protected $schema = [
        'id'           => 'int',  //主键ID
        'user_id'      => 'int',  //用户ID
        'nickname'     => 'int',  //收货人名称
        'mobile'       => 'int',  //联系电话
        'province_id'  => 'int',  //省ID
        'city_id'      => 'int',  //市ID
        'district_id'  => 'int',  //区ID
        'address'      => 'int',  //详细地址
        'is_default'   => 'int',  //是否默认[0=否,1=是]
        'is_delete'    => 'int',  //是否删除[0=否,1=是]
        'create_time'  => 'int',  //创建时间
        'update_time'  => 'int',  //更新时间
        'delete_time'  => 'int',  //删除时间
    ];

    /**
     * 关联: 省
     */
    public function province(): HasOne
    {
        return $this->hasOne(Region::class, 'id', 'province_id');
    }

    /**
     * 关联: 市
     */
    public function city(): HasOne
    {
        return $this->hasOne(Region::class, 'id', 'city_id');
    }

    /**
     * 关联: 区
     */
    public function district(): HasOne
    {
        return $this->hasOne(Region::class, 'id', 'city_id');
    }
}