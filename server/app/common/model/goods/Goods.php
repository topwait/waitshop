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

namespace app\common\model\goods;


use app\common\basics\Models;
use app\common\utils\UrlUtils;
use think\model\relation\HasMany;

class Goods extends Models
{
    // 设置字段信息
    protected $schema = [
        'id'                  => 'int',     //主键ID
        'name'                => 'string',  //商品名称
        'category_id'         => 'int',     //分类ID
        'brand_id'            => 'int',     //品牌ID
        'supplier_id'         => 'int',     //供应商ID
        'freight_id'          => 'int',     //运费模板ID
        'code'                => 'string',  //商品编码
        'video'               => 'string',  //商品视频
        'image'               => 'string',  //商品主图
        'banner'              => 'string',  //商品轮播图
        'intro'               => 'string',  //商品简介卖点
        'content'             => 'string',  //商品详细
        'max_price'           => 'float',   //最大售卖价格
        'min_price'           => 'float',   //最小售卖价格
        'market_price'        => 'float',   //最大市场价格
        'spec_type'           => 'int',     //规格类型[1=单规格, 2=多规格]
        'stock'               => 'int',     //商品总库存
        'sort'                => 'int',     //商品排序号
        'stock_warn'          => 'int',     //预警库存
        'give_integral'       => 'int',     //赠送积分
        'sales_volume'        => 'int',     //商品销量
        'click_count'         => 'int',     //商品点击量
        'is_distribution'     => 'int',     //是否参与分销[0=否, 1=是]
        'is_integral'         => 'int',     //积分抵扣[0=否, 1=是]
        'is_show'             => 'int',     //是否上架
        'is_new'              => 'int',     //新品推荐
        'is_best'             => 'int',     //好物优选
        'is_like'             => 'int',     //猜你喜欢
        'is_delete'   => 'int',     //是否删除[0=否,1=是]
        'create_time' => 'int',     //创建时间
        'update_time' => 'int',     //更新时间
        'delete_time' => 'int',     //删除时间
    ];

    /**
     * 关联: 商品规格模型
     */
    public function spec(): HasMany
    {
        return $this->hasMany( GoodsSpec::class, 'goods_id', 'id');
    }

    /**
     * 关联: 商品SKU模型
     */
    public function sku(): HasMany
    {
        return $this->hasMany(GoodsSku::class, 'goods_id', 'id');
    }

    /**
     * 获取器: 转换视频路径
     *
     * @author windy
     * @param $value
     * @return string
     */
    public function getVideoAttr($value): string
    {
        if (!$value) {
            return '';
        }
        return UrlUtils::getAbsoluteUrl($value);
    }

    /**
     * 修改器: 转换视频路径
     *
     * @author windy
     * @param $value
     * @return string
     */
    public function setVideoAttr($value): string
    {
        if (!$value) {
            return '';
        }
        return UrlUtils::getRelativeUrl($value);
    }

    /**
     * 获取器: 内容图片路径
     *
     * @author windy
     * @param $value
     * @return string
     */
    public function getContentAttr($value): string
    {
        $domain  = UrlUtils::getStorageUrl() . '/';
        $pattern = '/(<img .*?src=")(.*?)(.*?>)/is';
        return preg_replace($pattern, "\${1}$domain\${2}\${3}", $value);
    }

    /**
     * 获取器：获取三级分类
     *
     * @author windy
     * @param $value
     * @param $data
     * @return array
     * @throws @\think\db\exception\DataNotFoundException
     * @throws @\think\db\exception\DbException
     * @throws @\think\db\exception\ModelNotFoundException
     */
    public function getCategoryAttr($value, $data): array
    {
        unset($value);
        $categoryArr = [];

        $parentIds = GoodsCategory::getParentIds($data['category_id']);
        $categoryModel = new GoodsCategory();
        $categoryArray = $categoryModel
            ->field('id,name')
            ->whereIn('id', $parentIds)
            ->order('level asc')
            ->select()
            ->toArray();

        foreach ($categoryArray as $item) {
            array_push($categoryArr, $item);
        }


//        if ($data['first_category_id'] > 0) {
//            $first = $categoryModel->where(['id'=>(int)$data['first_category_id']])->findOrEmpty();
//            array_push($categoryArr, $first);
//        }
//        if ($data['second_category_id'] > 0) {
//            $second = $categoryModel->where(['id'=>(int)$data['second_category_id']])->findOrEmpty();
//            array_push($categoryArr, $second);
//        }
//        if ($data['third_category_id'] > 0) {
//            $third = $categoryModel->where(['id'=>(int)$data['third_category_id']])->findOrEmpty();
//            array_push($categoryArr, $third);
//        }

        return $categoryArr;
    }

    /**
     * 获取器: 获取商品评论(2条)
     *
     * @author windy
     * @param $value
     * @param $data
     * @return array
     * @throws @\think\db\exception\DataNotFoundException
     * @throws @\think\db\exception\DbException
     * @throws @\think\db\exception\ModelNotFoundException
     */
    public function getCommentAttr($value, $data)
    {
        unset($value);
        $where = [
            ['goods_id', '=', $data['id']],
            ['is_show', '=', 1],
            ['is_delete', '=', 0],
        ];

        $model = new GoodsComment();
        $count = $model->where($where)->count();
        $lists = $model->field('id,user_id,content,create_time')
            ->where($where)
            ->with(['user'])
            ->order('id desc')
            ->limit(2)
            ->select()
            ->toArray();

        $data = [];
        foreach ($lists as $item) {
            $data[] = [
                'id'          => $item['id'],
                'uid'         => $item['user_id'],
                'nickname'    => $item['user']['nickname'],
                'avatar'      => UrlUtils::getAbsoluteUrl($item['user']['avatar']),
                'content'     => $item['content'],
                'create_time' => date('Y-m-d', strtotime($item['create_time'])),
            ];
        }

        return ['count'=>$count, 'list'=>$data];
    }
}