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

namespace app\admin\logic;


use app\common\basics\Logic;
use app\common\enum\TeamEnum;
use app\common\model\addons\Team;
use app\common\model\goods\Goods;
use app\common\model\goods\GoodsCategory;
use app\common\model\user\User;

/**
 * 通用数据-逻辑层
 * Class DataLogic
 * @package app\admin\logic
 */
class DataLogic extends Logic
{
    /**
     * 用户列表
     *
     * @author windy
     * @param array $get
     * @return array
     * @throws @\think\db\exception\DbException
     */
    public static function getUser(array $get): array
    {
        // 查询条件
        self::setSearch([
            '%like%' => ['keyword'],
        ]);

        // 执行查询
        $model = new User();
        $lists = $model->field('id,sn,nickname,avatar,money,grade_id,total_order_amount,create_time')
            ->where(['is_delete'=>0, 'is_disable'=>0])
            ->where(self::$searchWhere)
            ->paginate([
                'page'      => $get['page'],
                'list_rows' => $get['limit'],
                'var_page'  => 'page',
            ])->toArray();

        return ['count'=>$lists['total'], 'list'=>$lists['data']];
    }

    /**
     * 商品列表数据
     *
     * @author windy
     * @param array $get
     * @return array|bool
     * @throws @\think\db\exception\DbException
     */
    public static function getGoods(array $get)
    {
        // 查询条件
        self::setSearch([
           '%like%' => ['name']
        ]);

        // 分类搜索
        $where = [];
        if (!empty($get['category']) && $get['category']) {
            $where[] = ['category_id', 'in', GoodsCategory::getChildrenIds($get['category'])];
        }

        // 执行查询
        $model = new Goods();
        $lists = $model->field([
            'id,category_id,name,image,min_price,market_price,sales_volume,sort,update_time'
        ])->where(self::$searchWhere)
            ->where($where)
            ->where('is_show', '=', isset($get['status']) ? $get['status'] : 1)
            ->order('id', 'desc')
            ->order('sort', 'desc')
            ->append(['category'])
            ->paginate([
                'page'      => $get['page'],
                'list_rows' => $get['limit'],
                'var_page'  => 'page',
            ])->toArray();

        return ['count'=>$lists['total'], 'list'=>$lists['data']];
    }

    /**
     * 拼团商品数据
     *
     * @author windy
     * @param array $get
     * @return array
     * @throws @\think\db\exception\DbException
     */
    public static function getTeamGoods(array $get): array
    {
        // 查询条件
        self::setSearch([
            '%like%' => ['name']
        ]);

        $lists = (new Team())
            ->field(['id,name,image,people_num,min_buy,max_buy,min_team_price,total_join_number,update_time'])
            ->where([
                ['start_time', '<', time()],
                ['end_time', '>', time()],
                ['status', '=', TeamEnum::TEAM_CONDUCT_IN],
                ['is_delete', '=', 0]
            ])
            ->where(self::$searchWhere)
            ->order('id', 'desc')
            ->append(['category'])
            ->paginate([
                'page'      => $get['page'],
                'list_rows' => $get['limit'],
                'var_page'  => 'page',
            ])->toArray();

        return ['count'=>$lists['total'], 'list'=>$lists['data']];
    }
}