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
use app\common\model\order\OrderGoods;
use app\common\model\user\User;
use app\common\utils\UrlUtils;
use think\model\relation\HasOne;

/**
 * 商品评论模型
 * Class GoodsComment
 * @package app\common\model\store
 */
class GoodsComment extends Models
{
    // 设置字段信息
    protected $schema = [
        'id'              => 'int',     //主键ID
        'user_id'         => 'int',     //用户ID
        'goods_id'        => 'int',     //商品ID
        'order_id'        => 'int',     //订单ID
        'order_goods_id'  => 'int',     //订单商品ID
        'goods_comment'   => 'int',     //商品评分[1~5]
        'service_comment' => 'int',     //服务评分[1~5]
        'express_comment' => 'int',     //快递评分[!~5]
        'images'          => 'string',  //评论图片
        'content'         => 'string',  //客户评论内容
        'reply'           => 'string',  //商家回复内容
        'is_reply'        => 'int',     //商家是否回复[0=否, 1=是]
        'is_show'         => 'int',     //是否显示评论[0=否, 1=是]
        'is_delete'       => 'int',     //是否删除[0=否, 1=是]
        'create_time'     => 'int',     //创建时间
        'update_time'     => 'int',     //更新时间
        'delete_time'     => 'int',     //删除时间
    ];

    /**
     * 单关联: 用户模型
     */
    public function user(): HasOne
    {
        return $this->hasOne(User::class, 'id', 'user_id')
            ->field(['id', 'nickname', 'avatar']);
    }

    /**
     * 单关联: 订单模型
     */
    public function orderGoods(): HasOne
    {
        return $this->hasOne(OrderGoods::class, 'id', 'order_goods_id')
            ->field('id,name,image,spec_value_str,actual_price,count');
    }

    /**
     * 获取器: 修改评论图片格式
     *
     * @author windy
     * @param $value
     * @return array|false|string[]
     */
    public function getImagesAttr($value)
    {
        if (!$value) {
            return [];
        }

        $data = json_decode($value, true);
        foreach ($data as &$url) {
            $url = UrlUtils::getAbsoluteUrl($url);
        }

        return $data;
    }

    /**
     * 修改器：修改评论图片格式
     *
     * @author windy
     * @param $value
     * @return array|string
     */
    public function setImagesAttr($value)
    {
        if (!$value) {
            return '';
        } else {
            $data = [];
            foreach ($value as $uri) {
                $data[] = UrlUtils::getRelativeUrl($uri);
            }
            return json_encode($data);
        }
    }
}