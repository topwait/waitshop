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

namespace app\common\model\diy;


use app\common\basics\Models;
use app\common\utils\UrlUtils;

class DiyNav extends Models
{
    // 设置字段信息
    protected $schema = [
        'id'              => 'int',    //主键
        'name'            => 'int',    //名称
        'sort'            => 'int',    //排序
        'selected_icon'   => 'string', //选中图标
        'unselected_icon' => 'string', //未选图标
        'create_time'     => 'int',    //创建时间
        'update_time'     => 'int'     //更新时间
    ];

    /**
     * 获取器: 选中图标处理
     *
     * @author windy
     * @param $value
     * @return string
     */
    public function getSelectedIconAttr($value) {
        return UrlUtils::getAbsoluteUrl($value);
    }

    /**
     * 获取器: 未选图标处理
     *
     * @author windy
     * @param $value
     * @return string
     */
    public function getUnselectedIconAttr($value) {
        return UrlUtils::getAbsoluteUrl($value);
    }
}