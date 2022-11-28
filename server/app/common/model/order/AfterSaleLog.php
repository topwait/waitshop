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

namespace app\common\model\order;


use app\common\basics\Models;

/**
 * 售后日志模型
 * Class AfterSaleLog
 * @package app\common\model\order
 */
class AfterSaleLog extends Models
{
    // 设置字段信息
    protected $schema = [
        'id'            => 'int',    //主键ID
        'type'          => 'int',    //操作类型[1=申请退款，2=商家拒绝，3=商品待退货，4=商家待收货，5=商家拒收货，6=等待退款，7=退款成功，8=撤销申请]',
        'after_sale_id' => 'int',    //售后主键
        'operate_id'    => 'int',    //操作员工
        'operate'       => 'string', //操作标题
        'content'       => 'string', //操作内容
        'remarks'       => 'string', //备注内容
        'create_time'   => 'int',    //创建时间
    ];

    /**
     * 记录售后日志
     *
     * @author windy
     * @param $afterSaleId (售后ID)
     * @param $type (售后类型)
     * @param $operateId (操作人ID)
     * @param $title (操作标题)
     * @param $content (操作内容)
     * @param $remarks (操作备注)
     */
    public static function record($afterSaleId, $type, $operateId, $title, $content, $remarks='')
    {
        self::create([
            'type'            => $type,
            'after_sale_id'   => $afterSaleId,
            'operate_id'      => $operateId,
            'operate'         => $title,
            'content'         => $content,
            'remarks'         => $remarks,
            'create_time'     => time()
        ]);
    }
}