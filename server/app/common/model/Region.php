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

/**
 * 全国地址模型
 * Class Region
 * @package app\common\model
 */
class Region extends Models
{
    // 设置字段信息
    protected $schema = [
        'id'           => 'int',     //主键ID
        'parent_id'    => 'int',     //父级ID
        'level'        => 'int',     //等级
        'name'         => 'string',  //地区名称
        'short'        => 'string',  //简称
        'city_code'    => 'string',  //地区编码
        'zip_code'     => 'string',  //邮政编码
        'gcj02_lng'    => 'string',  //经度(火星坐标系)
        'gcj02_lat'    => 'string',  //纬度(火星坐标系)
        'db09_lng'     => 'string',  //经度(百度坐标系)
        'db09_lat'     => 'string',  //纬度(百度坐标系)
    ];

    /**
     * 获取地址(省市区)
     *
     * @author windy
     * @param array $data
     * @return string
     */
    public static function getAddress(array $data)
    {
        $model = new self;
        $region = $model->field('id,level,name')
            ->whereIn('id', array(
                intval($data['province']),
                intval($data['city']),
                intval($data['district'])
            ))
            ->order('level', 'asc')
            ->column('name');

        return implode('', $region);
    }
}