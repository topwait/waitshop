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

namespace app\api\logic;


use app\common\basics\Logic;
use app\common\model\Cart;
use app\common\model\goods\Goods;
use app\common\model\goods\GoodsCategory;
use app\common\utils\ArrayUtils;
use app\common\utils\ConfigUtils;

/**
 * 商品分类接口-逻辑层
 * Class GoodsCategoryLogic
 * @package app\api\logic
 */
class GoodsCategoryLogic extends Logic
{
    /**
     * 分类风格
     *
     * @author windy
     * @return array
     */
    public static function skin(): array
    {
        return ConfigUtils::get('diy')['categorySkin'] ?? [
                'type' => 10,
                'skin' => 'destiny'
            ];
    }

    /**
     * 分类列表
     *
     * @author windy
     * @return array
     * @throws @\think\db\exception\DataNotFoundException
     * @throws @\think\db\exception\DbException
     * @throws @\think\db\exception\ModelNotFoundException
     */
    public static function lists(): array
    {
        $model = new GoodsCategory();
        $result = $model->field(['id', 'pid', 'name', 'image'])
            ->where(['is_show'=>1, 'is_delete'=>0])
            ->order('sort desc, id desc')
            ->select()->toArray();

        return ArrayUtils::toTreeJson($result);
    }

    /**
     * 分类商品
     *
     * @author windy
     * @param array $get
     * @param int $userId
     * @return array
     * @throws @\think\db\exception\DbException
     */
    public static function goods(array $get, int $userId=0): array
    {
        $childrenIds = GoodsCategory::getChildrenIds(intval($get['cid'] ?? 0));
        $lists = (new Goods)->field(['id,name,image,market_price,min_price'])
            ->where([
                ['category_id', 'in', $childrenIds],
                ['is_delete', '=', 0],
                ['is_show', '=', 1],
                ['stock', '>', 0]
            ])
            ->withAttr(['cartNum'=>function($value, $data) use($userId) {
                unset($value);
                return (new Cart)->where(['user_id'=>(int)$userId, 'goods_id'=>$data['id']])->sum('num');
            }])->append(['cartNum'])->paginate([
                'page'      => $get['page'] ?? 1,
                'list_rows' => $get['limit'] ?? 20,
                'var_page'  => 'page'
            ])->toArray();

        $lists['current_cid'] = intval($get['cid'] ?? 0);
        return $lists;
    }
}