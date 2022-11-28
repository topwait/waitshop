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

namespace app\admin\logic\store;


use app\common\basics\Logic;
use app\common\model\store\StoreVerify;

/**
 * 核销记录-逻辑层
 * Class VerifyLogic
 * @package app\admin\logic\shop
 */
class VerifyLogic extends Logic
{
    /**
     * 核销记录列表
     *
     * @author windy
     * @param array $get
     * @return array
     * @throws @\think\db\exception\DbException
     */
    public static function lists(array $get): array
    {
        self::setSearch([
            '%like%' => ['staff@SC.nickname', 'order_sn@O.order_sn'],
            '='      => ['store_id@SV.store_id']
        ]);

        $lists = (new StoreVerify())->alias('SV')
            ->field([
                'SV.id',
                'S.name as shop',
                'SC.nickname as staff',
                'O.order_sn',
                'O.order_type',
                'SV.create_time',
            ])
            ->order('SV.id desc')
            ->where(self::$searchWhere)
            ->join('order O', 'O.id = SV.order_id')
            ->join('store S', 'S.id = SV.store_id')
            ->join('store_clerk SC', 'SC.id = SV.staff_id')
            ->paginate([
                'page'      => $get['page'] ?? 1,
                'list_rows' => $get['limit'] ?? 20,
                'var_page'  => 'page',
            ])->toArray();

        return ['count'=>$lists['total'], 'list'=>$lists['data']];
    }
}