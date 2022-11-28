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

namespace app\common\model\store;


use app\common\basics\Models;
use app\common\utils\UrlUtils;

/**
 * 门店模型
 * Class Shop
 * @package app\common\model\shop
 */
class Store extends Models
{
    // 设置字段信息
    protected $schema = [
        'id'             => 'int',      //主键ID
        'name'           => 'string',   //门店名称
        'logo'           => 'string',   //门店logo
        'contacts'       => 'string',   //负责人名
        'mobile'         => 'string',   //联系电话
        'intro'          => 'string',   //门店简介
        'business_hours' => 'string',   //营业时间
        'province_id'    => 'int',      //省ID
        'city_id'        => 'int',      //市ID
        'district_id'    => 'int',      //区ID
        'address'        => 'string',   //详细地址
        'longitude'      => 'string',   //经度
        'latitude'       => 'string',   //纬度
        'sort'           => 'int',      //门店排序
        'status'         => 'int',      //门店状态: 0=停用, 1=正常
        'is_delete'      => 'int',      //是否删除: 0=否, 1=是
        'create_time'    => 'int',      //创建时间
        'update_time'    => 'int',      //更新时间
        'delete_time'    => 'int',      //删除时间
    ];


    /**
     * 获取器：处理logo路径
     *
     * @author windy
     * @param $value
     * @return string
     */
    public function getLogoAttr($value): string
    {
        if ($value) {
            return UrlUtils::getAbsoluteUrl($value);
        }
        return '';
    }
}