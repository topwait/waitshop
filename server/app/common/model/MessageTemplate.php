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
 * 消息模板模型
 * Class MessageTemplate
 * @package app\common\model
 */
class MessageTemplate extends Models
{
    // 设置字段信息
    protected $schema = [
        'id'                => 'int',    //主键ID
        'name'              => 'int',    //场景名称
        'scene'             => 'int',    //场景编号
        'sms_template'      => 'string', //短信模板
        'mnp_template'      => 'string', //小程序模板
        'official_template' => 'string', //公众号模板
    ];

    /**
     * 获取器: 处理变量场景
     *
     * @author windy
     * @param $value
     * @return array
     */
    public function getVariableAttr($value): array
    {
        if ($value) {
            return json_decode($value, true);
        }
        return [];
    }

    /**
     * 获取器: 处理短信模板
     *
     * @author windy
     * @param $value
     * @return array
     */
    public function getSmsTemplateAttr($value): array
    {
        if ($value) {
            return json_decode($value, true);
        }
        return [];
    }

    /**
     * 获取器: 处理公众号模板
     *
     * @author windy
     * @param $value
     * @return array
     */
    public function getOfficialTemplateAttr($value): array
    {
        if ($value) {
            return json_decode($value, true);
        }
        return [];
    }

    /**
     * 获取器: 处理小程序模板
     *
     * @author windy
     * @param $value
     * @return array
     */
    public function getMnpTemplateAttr($value): array
    {
        if ($value) {
            return json_decode($value, true);
        }
        return [];
    }
}