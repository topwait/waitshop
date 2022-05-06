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
 * 热搜模型
 * Class HotSearch
 * @package app\common\model
 */
class HotSearch extends Models
{
    // 设置字段信息
    protected $schema = [
        'id'          => 'int',    //主键ID
        'keyword'     => 'string', //关键词名称
        'count'       => 'int',    //搜索次数
        'is_sys'      => 'int',    //是否系统设定[1=是, 0=否]
        'create_time' => 'int'     //创建时间
    ];

    /**
     * 设置热门搜索词
     *
     * @author windy
     * @param string $keyword
     * @return bool
     */
    public static function setHotSearch(string $keyword)
    {
        $keyword = trim($keyword);
        if (!$keyword || $keyword=='') return true;

        $model = new self;
        $hotSearch = $model->where(['keyword'=>$keyword])->findOrEmpty()->toArray();

        if ($hotSearch) {
            self::update([
                'count' => ['inc', 1]
            ], ['id'=>$hotSearch['id']]);
        } else {
            self::create([
                'keyword' => $keyword,
                'count'   => 1,
                'is_sys'  => 0
            ]);
        }

        return true;
    }
}