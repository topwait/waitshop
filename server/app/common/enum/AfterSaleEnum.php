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

namespace app\common\enum;


/**
 * 售后枚举类
 * Class AfterSaleEnum
 * @package app\common\enum
 */
class AfterSaleEnum
{
    // PS: 商家拒绝;商家拒收;撤销申请; 均为售后失败

    //售后状态
    const STATUS_APPLY_REFUND           = 1; //申请退款
    const STATUS_REFUSE_REFUND          = 2; //商家拒绝
    const STATUS_WAIT_RETURN_GOODS      = 3; //商品待退货
    const STATUS_WAIT_RECEIVE_GOODS     = 4; //商家待收货
    const STATUS_REFUSE_RECEIVE_GOODS   = 5; //商家拒收货
    const STATUS_WAIT_REFUND            = 6; //等待退款
    const STATUS_SUCCESS_REFUND         = 7; //退款成功
    const STATUS_REVOKE_APPLY           = 8; //撤销申请

    // 退款类型
    const TYPE_ONLY_REFUND      = 1; //仅退款
    const TYPE_REFUND_RETURN    = 2; //退款退货

    /**
     * 获取售后状态描述
     *
     * @author windy
     * @param $state
     * @return string|string[]
     */
    public static function getStatusDesc($state)
    {
        $data = [
            self::STATUS_APPLY_REFUND         => '待审核',
            self::STATUS_REFUSE_REFUND        => '商家拒绝',
            self::STATUS_WAIT_RETURN_GOODS    => '商品待退货',
            self::STATUS_WAIT_RECEIVE_GOODS   => '商家待收货',
            self::STATUS_REFUSE_RECEIVE_GOODS => '商家拒收货',
            self::STATUS_WAIT_REFUND          => '等待退款',
            self::STATUS_SUCCESS_REFUND       => '退款成功',
            self::STATUS_REVOKE_APPLY         => '已撤销',
        ];
        if ($state === true) {
            return $data;
        }
        return $data[$state] ?? '';
    }

    /**
     * 售后类型描述
     *
     * @author windy
     * @param $type
     * @return string|string[]
     */
    public static function getRefundTypeDesc($type)
    {
        $data = [
            self::TYPE_ONLY_REFUND   => '仅退款',
            self::TYPE_REFUND_RETURN => '退款退货',
        ];
        if ($type === true) {
            return $data;
        }
        return $data[$type] ?? '';
    }

    /**
     * 售后原因列表
     *
     * @author windy
     * @return string[]
     */
    public static function getReasonLists(): array
    {
        return [
            '7天无理由退换货',
            '大小尺寸与商品描述不符',
            '颜色/图案/款式不符',
            '做工粗糙/有瑕疵',
            '质量问题',
            '卖家发错货',
            '少件（含缺少配件）',
            '不喜欢/不想要',
            '快递/物流一直未送到',
            '空包裹',
            '快递/物流无跟踪记录',
            '货物破损已拒签',
            '其他',
        ];
    }
}