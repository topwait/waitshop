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

namespace app\common\model\goods;


use app\common\basics\Models;

/**
 * 供应商模型
 * Class GoodsSupplier
 * @package app\common\model\store
 */
class GoodsSupplier extends Models
{
    // 设置字段信息
    protected $schema = [
        'id'          => 'int',     //主键ID
        'name'        => 'string',  //供应商名称
        'nickname'    => 'string',  //联系人名称
        'mobile'      => 'string',  //联系电话
        'address'     => 'string',  //联系地址
        'remarks'     => 'string',  //备注信息
        'sort'        => 'int',     //排序
        'is_delete'   => 'int',     //是否删除[0=否,1=是]
        'create_time' => 'int',     //创建时间
        'update_time' => 'int',     //更新时间
        'delete_time' => 'int',     //删除时间
    ];
}