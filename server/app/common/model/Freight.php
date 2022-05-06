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

namespace app\common\model;


use app\common\basics\Models;
use think\model\relation\HasMany;

class Freight extends Models
{
    // 设置字段信息
    protected $schema = [
        'id'          => 'int',    //主键ID
        'name'        => 'string', //运费模板名称
        'method'      => 'int',    //计费方式[10=按件数, 20=按重量, 30=按体积]
        'remarks'     => 'string', //备注信息
        'sort'        => 'int',    //排序
        'create_time' => 'int'     //创建时间
    ];

    /**
     * 多关联: 配送地区规则模型
     */
    public function rule()
    {
        return $this->hasMany('FreightRule', 'freight_id', 'id');
    }
}