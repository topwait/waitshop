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

namespace app\api\logic;


use app\common\basics\Logic;
use app\common\enum\LinkEnum;
use app\common\model\ConfigPolicy;
use app\common\model\diy\DiyMe;
use app\common\model\diy\DiyNav;
use app\common\model\diy\DiyOrder;
use app\common\model\goods\Goods;
use app\common\model\HotSearch;
use app\common\model\addons\Ad;
use app\common\model\MessageTemplate;
use app\common\utils\ConfigUtils;
use app\common\utils\UrlUtils;

/**
 * 首页接口-逻辑层
 * Class IndexLogic
 * @package app\api\logic
 */
class IndexLogic extends Logic
{
    /**
     * 首页数据
     *
     * @author windy
     * @return array
     * @throws @\think\db\exception\DataNotFoundException
     * @throws @\think\db\exception\DbException
     * @throws @\think\db\exception\ModelNotFoundException
     */
    public static function index(): array
    {
        // 热销榜单
        $hot = (new Goods())->field(['id,name,image,market_price,min_price'])
            ->where([
                ['is_show', '=', 1],
                ['is_delete', '=', 0]
            ])->order('sales_volume', 'desc')
              ->order('click_count', 'desc')
              ->limit(4)->select()->toArray();

        // 好物推荐
        $best = (new Goods())->field(['id,name,image,market_price,min_price'])
            ->where([
                ['is_show', '=', 1],
                ['is_delete', '=', 0],
                ['is_best', '=', 1]
            ])->order('sales_volume', 'desc')
              ->order('click_count', 'desc')
              ->limit(6)->select()->toArray();

        return [
            'hot'    => $hot,
            'best'   => $best
        ];
    }

    /**
     * 配置
     *
     * @return array
     * @throws @\think\db\exception\DataNotFoundException
     * @throws @\think\db\exception\DbException
     * @throws @\think\db\exception\ModelNotFoundException
     */
    public static function config(): array
    {
        // 底部导航按鈕
        $detail['diyBottomNav'] = (new DiyNav())
            ->field('id,name,selected_icon,unselected_icon')
            ->order('sort asc, id asc')
            ->select()
            ->toArray();

        // 页面颜色风格
        $detail['diyBottomStyle'] = ConfigUtils::get('diy')['bottomStyle'] ?? [
            'selectedColor'   => '#FF5058', // 底部按钮选中
            'unselectedColor' => '#666666'  // 底部按钮未选
        ];

        return $detail;
    }

    /**
     * 热门搜索词
     *
     * @author windy
     * @return array
     * @throws @\think\db\exception\DataNotFoundException
     * @throws @\think\db\exception\DbException
     * @throws @\think\db\exception\ModelNotFoundException
     */
    public static function hotSearch(): array
    {
        // type: [1=固定关键词, 2=真实热搜词]
        $config = ConfigUtils::get('search', ['type'=>1, 'keyword'=>'']);

        if ($config['type'] == 2) {
            $model = new HotSearch();
            return $model->field(['id,keyword'])
                ->order('count', 'desc')
                ->order('id', 'desc')
                ->limit(15)
                ->select()->toArray();
        } else {
            $data = [];
            if (!$config['keyword'] || $config['keyword'] == "") {
                return $data;
            }

            $keyword = explode(",", $config['keyword']);
            foreach ($keyword as $k => $val) {
                $data[] = [
                    'id'      => $k+1,
                    'keyword' => $val
                ];
            }

            return $data;
        }
    }

    /**
     * 广告列表
     *
     * @author windy
     * @param int $position
     * @return array
     * @throws @\think\db\exception\DataNotFoundException
     * @throws @\think\db\exception\DbException
     * @throws @\think\db\exception\ModelNotFoundException
     */
    public static function ad(int $position): array
    {
        $model = new Ad();
        return $model->field(true)
            ->order('sort desc, id desc')
            ->where([
                'position' => intval($position),
                'is_show' => 1,
                'is_delete' => 0
            ])->select()->toArray();
    }

    /**
     * 政策协议
     *
     * @author windy
     * @param string $type
     * @return array
     */
    public static function policy(string $type): array
    {
        $model = new ConfigPolicy();
        return $model->field(true)->where(['type'=>$type])->findOrEmpty()->toArray();
    }

    /**
     * 获取微信小程序订阅模板ID
     *
     * @author windy
     * @return array
     * @throws @\think\db\exception\DataNotFoundException
     * @throws @\think\db\exception\DbException
     * @throws @\think\db\exception\ModelNotFoundException
     */
    public static function subscribe(): array
    {
        $lists = (new MessageTemplate())
            ->field('mnp_template')
            ->where([['mnp_template', '<>', '']])
            ->limit(3)
            ->select()
            ->toArray();

        $template_id = [];
        foreach ($lists as $item) {
            if (isset($item['mnp_template']['status']) && $item['mnp_template']['status'] != 1) {
                continue;
            }
            $template_id[] = $item['mnp_template']['template_id'] ?? '';
        }

        return $template_id;
    }

    /**
     * 获取客服信息
     *
     * @author windy
     * @return array
     */
    public static function customerService(): array
    {
        $detail = ConfigUtils::get('service', [
            // 客服QQ
            'qq'    => '',
            // 客服微信
            'wx'    => '',
            // 客服电话
            'phone' => '',
            // 客服图片
            'image' => '',
            // 营业时间
            'business_hours' => ''
        ]);

        $detail['image'] = UrlUtils::getAbsoluteUrl($detail['image']);
        return $detail;
    }
}