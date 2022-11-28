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
use app\common\utils\UrlUtils;

class ConfigPayment extends Models
{
    // 设置字段信息
    protected $schema = [
        'id'           => 'int',     //主键ID
        'name'         => 'int',     //支付名称
        'image'        => 'int',     //图标
        'desc'         => 'string',  //支付描述
        'params'       => 'string',  //支付参数JSON
        'sort'         => 'int',     //支付排序
        'is_enable'    => 'string',  //是否启用[0=否, 1=是]
        'update_time'  => 'int',     //更新时间
    ];

    /**
     * 获取器: 处理参数格式
     *
     * @author windy
     * @param $value
     * @return array
     */
    public function getParamsAttr($value): array
    {
        if ($value) {
            return json_decode($value, true);
        }
        return [];
    }

    /**
     * 获取器：处理图标路径
     *
     * @author windy
     * @param $value
     * @return string
     */
    public function getIconAttr($value): string
    {
        if ($value) {
            return UrlUtils::getAbsoluteUrl($value);
        }
        return '';
    }

    /**
     * 获取: 支付参数
     *
     * @author windy
     * @param $alias
     * @return array
     */
    public static function getParams($alias): array
    {
        $model = new self;
        $result = $model->field(true)->where(['alias'=>$alias])
                    ->findOrEmpty()->toArray();

        return $result ? $result['params'] : [];
    }
}