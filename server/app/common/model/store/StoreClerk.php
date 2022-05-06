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

namespace app\common\model\store;


use app\common\basics\Models;
use app\common\model\user\User;
use think\model\relation\HasOne;

/**
 * 门店员工模型
 * Class ShopClerk
 * @package app\common\model\shop
 */
class StoreClerk extends Models
{
    // 设置字段信息
    protected $schema = [
        'id'          => 'int',    //主键ID
        'user_id'     => 'int',    //用户ID
        'store_id'    => 'int',    //门店ID
        'nickname'    => 'string', //店员姓名
        'mobile'      => 'string', //店员电话
        'status'      => 'int',    //状态: 0=禁用, 1=正常
        'is_delete'   => 'int',    //是否删除: 0=否, 1=是
        'create_time' => 'int',    //创建时间
        'update_time' => 'int',    //更新时间
        'delete_time' => 'int',    //删除时间
    ];

    /**
     * 关联用户模型
     */
    public function user(): HasOne
    {
        return $this->hasOne(User::class, 'id', 'user_id')
            ->field(['id', 'sn', 'nickname', 'avatar', 'mobile']);
    }

    /**
     * 关联门店模型
     */
    public function store(): HasOne
    {
        return $this->hasOne(Store::class, 'id', 'store_id')
            ->field(['id', 'name', 'logo', 'mobile']);
    }
}