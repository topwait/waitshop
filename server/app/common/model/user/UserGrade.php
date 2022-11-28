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

namespace app\common\model\user;


use app\common\basics\Models;
use app\common\utils\UrlUtils;

/**
 * 会员等级模型
 * Class Grade
 * @package app\common\model\user
 */
class UserGrade extends Models
{
    // 设置字段信息
    protected $schema = [
        'id'             => 'int',     //等级ID
        'name'           => 'string',  //等级名称
        'weight'         => 'string',  //等级权重[1-50]
        'icon'           => 'string',  //等级图标
        'background'     => 'string',  //等级背景
        'upgrade'        => 'string',  //升级条件
        'equity'         => 'string',  //等级权益[折扣率0-100]
        'is_disable'     => 'int',     //是否禁用[0=否, 1=是]
        'is_delete'      => 'int',     //是否删除[0=否,1=是]
        'create_time'    => 'int',     //创建时间
        'update_time'    => 'int',     //更新时间
        'delete_time'    => 'int',     //删除时间
    ];

    /**
     * 修改器：处理等级图标路径
     *
     * @author windy
     * @param $value
     * @return string
     */
    public function getIconAttr($value)
    {
        if ($value) {
            return UrlUtils::getAbsoluteUrl($value);
        }
        return '';
    }

    /**
     * 修改器：处理等级背景图路径
     *
     * @author windy
     * @param $value
     * @return string
     */
    public function getBackgroundAttr($value)
    {
        if ($value) {
            return UrlUtils::getAbsoluteUrl($value);
        }
        return '';
    }

    /**
     * 修改器：处理升级条件
     *
     * @author windy
     * @param $value
     * @return array
     */
    public function getUpgradeAttr($value)
    {
        if ($value) {
            return json_decode($value, true);
        }

        return [];
    }
}