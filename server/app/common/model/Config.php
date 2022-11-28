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

namespace app\common\model;


use app\common\basics\Models;

/**
 * 配置模型
 * Class Config
 * @package app\common\model
 */
class Config extends Models
{
    // 设置字段信息
    protected $schema = [
        'id'          => 'int',     //主键ID
        'describe'    => 'string',  //配置描述
        'key'         => 'int',     //键
        'values'      => 'int',     //值
        'update_time' => 'int',     //更新时间
    ];

    /**
     * 获取器: 转义数组格式
     *
     * @author windy
     * @param $value
     * @return mixed
     */
    public function getValuesAttr($value)
    {
        return json_decode($value, true);
    }

    /**
     * 修改器: 转义成json格式
     *
     * @author windy
     * @param $value
     * @return string
     */
    public function setValuesAttr($value)
    {
        if (is_array($value)) {
            return json_encode($value, JSON_UNESCAPED_UNICODE);
        } else {
            return $value;
        }
    }
}