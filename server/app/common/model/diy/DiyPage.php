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

namespace app\common\model\diy;


use app\common\basics\Models;
use app\common\utils\UrlUtils;
use Exception;

/**
 * DIY页面模型
 * Class DiyPage
 * @package app\common\model\diy
 */
class DiyPage extends Models
{
    // 设置字段信息
    protected $schema = [
        'id'          => 'int',     //主键ID
        'page_type'   => 'int',     //页面类型
        'page_name'   => 'string',  //页面名称
        'page_data'   => 'string',  //页面数据
        'is_delete'   => 'int',     //是否删除[0=否,1=是]
        'create_time' => 'int',     //创建时间
        'update_time' => 'int',     //更新时间
        'delete_time' => 'int',     //删除时间
    ];

    /**
     * 获取器: 页面数据转数组
     *
     * @author windy
     * @param $json
     * @return array[]
     * @throws Exception
     */
    public function getPageDataAttr($json): array
    {
        if ($json) {
            $pages = json_decode($json, true);
            return self::handlePageItems($pages, 'get');
        }
        return ['page'=>self::getDefaultPage(), 'items' => []];
    }

    /**
     * 修改器: 页面数据转JSON
     *
     * @author windy
     * @param $value
     * @return string
     * @throws Exception
     */
    public function setPageDataAttr($value): string
    {
        $pages = $value ?: ['page'=>self::getDefaultPage(), 'items' => []];
        $pages = self::handlePageItems($pages, "set");
        return json_encode($pages, JSON_UNESCAPED_UNICODE);
    }

    /**
     * 获取Diy页面详细
     *
     * @author windy
     * @param int $page_id (页面ID)
     * @return array
     */
    public static function getDetail(int $page_id): array
    {
        $model = new self;
        return $model->field(true)->where(['id'=>$page_id])->findOrEmpty()->toArray();
    }

    /**
     * 获取DIY首页详细
     *
     * @author windy
     * @return array
     */
    public static function getHomePage(): array
    {
        $model = new self;
        return $model->field(true)->where(['page_type'=>10])
            ->findOrEmpty()->toArray();
    }

    /**
     * 处理页面组件数据
     * 根据写入或读取修改数据
     *
     * @author windy
     * @param array $pageData 页面数据
     * @param string $type 类型: get=获取, set=更新
     * @return array
     * @throws Exception
     */
    public static function handlePageItems(array $pageData, string $type): array
    {
        foreach ($pageData['items'] as &$item) {
            // 公共处理数据图片
            if (!empty($item['data']) and !empty($item['data'][0]['imgUrl'])) {
                foreach ($item['data'] as &$data) {
                    $data['imgUrl'] = UrlUtils::swapUrl($data['imgUrl'], $type);
                }
            }
            if (!empty($item['config']['icon'])) {
                $item['config']['icon'] = UrlUtils::swapUrl($item['config']['icon'], $type);
            }

            // 针对组件特殊处理
            switch ($item['type']) {
                // 轮播组件
                case 'banner':
                    $item['config']['backdrop'] = UrlUtils::swapUrl($item['config']['backdrop'], $type);
                    break;
                // 公告组件
                case 'notice':
                    $item['config']['icon'] = UrlUtils::swapUrl($item['config']['icon'], $type);
                    break;
                // 商品组件
                case 'goods':
                    $item['config']['show']['goodsName']    = $item['config']['show']['goodsName'] == 'true';
                    $item['config']['show']['goodsPrice']   = $item['config']['show']['goodsPrice'] == 'true';
                    $item['config']['show']['linePrice']    = $item['config']['show']['linePrice'] == 'true';
                    $item['config']['show']['goodsSales']   = $item['config']['show']['goodsSales'] == 'true';
                    break;
                case 'teamGoods':
                    if ($item['config']['source'] == 'auto') {
                        $item['data'] = [];
                    }
                    if ($item['config']['source'] == 'choice') {
                        if (empty($item['data'])) {
                            throw new Exception('请至少选择一个商品');
                        }
                    }
                    break;
            }
        }

        return $pageData;
    }

    /**
     * 页面diy元素默认数据
     *
     * @author windy
     * @return array
     */
    public static function getDefaultItems(): array
    {
        $domain = request()->domain();
        return [
            'search' => [
                'name' => '搜索框',
                'type' => 'search',
                'config' => [
                    'textAlign'    => 'left',
                    'searchStyle'  => 'square',
                    'textColor'    => '#999999',
                    'background'   => '#FFFFFF',
                    'inputBgColor' => '#F4F4F4',
                    'placeholder'  => '请输入关键字进行搜索'
                ]
            ],
            'banner' => [
                'name' => '图片轮播',
                'type' => 'banner',
                'config' => [
                    'mode'       => 'round',
                    'type'       => 'default',
                    'effect3d'   => 0,
                    'interval'   => 3800,
                    'backdrop'   => '',
                ],
                'data' => [
                    [
                        'imgUrl'  => '/static/images/diy/diy_banner.png',
                        'linkUrl' => ''
                    ],
                    [
                        'imgUrl'  => '/static/images/diy/diy_banner.png',
                        'linkUrl' => ''
                    ]
                ]
            ],
            'image'  => [
                'name'   => '图片组',
                'type'   => 'image',
                'config' => [
                    'marginTop'   => 0,
                    'marginLeft'  => 0,
                    'paddingLeft' => 0,
                    'paddingTop'  => 0,
                    'background'  => '#FFFFFF'
                ],
                'data' => [
                    [
                        'imgUrl'  => '/static/images/diy/diy_banner.png',
                        'imgName' => 'image-1.jpg',
                        'linkUrl' => ''
                    ],
                    [
                        'imgUrl'  => '/static/images/diy/diy_banner.png',
                        'imgName' => 'banner-2.jpg',
                        'linkUrl' => ''
                    ]
                ]
            ],
            'navBar' => [
                'name'   => '导航组',
                'type'   => 'navBar',
                'config' => [
                    'rowsNum'       => 4,
                    'borderRadius'  => 0,
                    'marginLeft'    => 0,
                    'marginTop'     => 0,
                    'background'    => '#FFFFFF',
                ],
                'data' => [
                    [
                        'imgUrl'  => $domain.'/static/images/diy/diy_circular.png',
                        'imgName' => 'icon-1.png',
                        'linkUrl' => '',
                        'text'    => '按钮文字1',
                        'color'   => '#666666'
                    ],
                    [
                        'imgUrl'  => $domain.'/static/images/diy/diy_circular.png',
                        'imgName' => 'icon-2.jpg',
                        'linkUrl' => '',
                        'text'    => '按钮文字2',
                        'color'   => '#666666'
                    ],
                    [
                        'imgUrl'  =>  $domain.'/static/images/diy/diy_circular.png',
                        'imgName' => 'icon-3.jpg',
                        'linkUrl' => '',
                        'text'    => '按钮文字3',
                        'color'   => '#666666'
                    ],
                    [
                        'imgUrl'  => $domain.'/static/images/diy/diy_circular.png',
                        'imgName' => 'icon-4.jpg',
                        'linkUrl' => '',
                        'text'    => '按钮文字4',
                        'color'   => '#666666'
                    ]
                ]
            ],
            'notice' => [
                'name' => '公告组',
                'type' => 'notice',
                'config' => [
                    'icon' => $domain.'/static/images/diy/notice/notice-01.png',
                    'marginTop'    => 0,
                    'marginAlign'  => 0,
                    'borderRadius' => 0,
                    'background'   => '#ffffff',
                    'textColor'    => '#000000'
                ],
                'data' => [
                    [
                        'title'   => '公告标题',
                        'linkUrl' => ''
                    ]
                ]
            ],
            'window' => [
                'name' => '图片橱窗',
                'type' => 'window',
                'config' => [
                    'number'       => 4,
                    'layout'       => 2,
                    'interval'     => 0,
                    'padding'      => 0,
                    'marginTop'    => 0,
                    'marginLeft'   => 0,
                    'borderRadius' => 0,
                    'background'   => '#ffffff',
                ],
                'data' => [
                    [
                        'imgUrl'  => $domain.'/static/images/diy/window/01.jpg',
                        'linkUrl' => ''
                    ],
                    [
                        'imgUrl'  => $domain.'/static/images/diy/window/02.jpg',
                        'linkUrl' => ''
                    ],
                    [
                        'imgUrl'  => $domain.'/static/images/diy/window/03.jpg',
                        'linkUrl' => ''
                    ],
                    [
                        'imgUrl'  => $domain.'/static/images/diy/window/04.jpg',
                        'linkUrl' => ''
                    ]
                ]
            ],
            'goods'  => [
                'name'   => '商品组',
                'type'   => 'goods',
                'config' => [
                    'display' => 'list', //[list, slide]
                    'source'  => 'auto', //[choice, auto]
                    'auto'    => [
                        'category'  => 0,
                        'showNum'   => 6,
                        'goodsSort' => 'all', //[all, sales, price]
                    ],
                    'show'    => [
                        'goodsName'    => true,
                        'goodsPrice'   => true,
                        'linePrice'    => true,
                        'goodsSales'   => true
                    ],
                    'column'       => 2,
                    'borderRadius' => 0,
                    'marginLeft'   => 0,
                    'marginTop'    => 0,
                    'background'   => '#F3F3F3'
                ],
                'data' => []
            ],
            'coupon' => [
                'name'   => '优惠券组',
                'type'   => 'coupon',
                'config' => [
                    'limit'        => 5,
                    'paddingTop'   => 10,
                    'marginLeft'   => 0,
                    'marginTop'    => 0,
                    'borderRadius' => 0,
                    'normalColor'  => '#ff2c3c',
                    'receiveColor' => '#dadada',
                    'background'   => '#ffffff'
                ],
                'data'   => []
            ],
            'teamGoods' => [
                'name'   => '拼团商品组',
                'type'   => 'teamGoods',
                'config' => [
                    'source' => 'auto', // choice; auto
                    'auto'   => [
                        'goodsSort' => 'all', // all; sales; price
                        'showNum'   => 6
                    ],
                    'show' => [
                        'teamPrice'  => '1',
                        'teamJoin'   => '1',
                        'teamGoBtn'  => '1'
                    ],
                    'icon'         => $domain.'/static/images/diy/ic_group.gif',
                    'marginLeft'   => 0,
                    'marginTop'    => 0,
                    'borderRadius' => 0,
                    'background'   => '#ffffff'
                ],
                'data' => []
            ],
            'seckillGoods' => [
                'name'   => '秒杀商品组',
                'type'   => 'seckillGoods',
                'config' => [
                    'source' => 'auto', // choice; auto
                    'auto'   => [
                        'goodsSort' => 'all', // all; sales; price
                        'showNum'   => 6
                    ],
                    'show' => [
                        'intro'        => '1',
                        'seckillPrice' => '1',
                        'linePrice'    => '1'
                    ],
                    'icon'         => $domain.'/static/images/diy/ic_spike.gif',
                    'marginLeft'   => 0,
                    'marginTop'    => 0,
                    'borderRadius' => 0,
                    'background'   => '#ffffff'
                ],
                // 手动选择
                'data' => []
            ],
            'blank' => [
                'name'   => '辅助空白',
                'type'   => 'blank',
                'config' => [
                    'height' => 20,
                    'background' => '#ffffff'
                ]
            ],
            'guide' => [
                'name'   => '辅助线',
                'type'   => 'guide',
                'config' => [
                    'paddingTop' => 10,
                    'marginLeft' => 10,
                    'lineHeight' => '1',
                    'lineStyle'  => 'solid',
                    'lineColor'  => "#000000",
                    'background' => '#ffffff',
                ]
            ]
        ];
    }

    /**
     * 页面标题栏默认数据
     *
     * @author windy
     * @return array
     */
    public static function getDefaultPage(): array
    {
        static $defaultPage = [];
        if (!empty($defaultPage)) return $defaultPage;
        return [
            'type'   => 'page',
            'name'   => '页面设置',
            'config' => [
                'name'        => '页面名称',
                'title'       => '页面标题',
                'shareTitle'  => '分享标题',
                'titleTextColor'       => 'black',
                'titleBackgroundColor' => '#FFFFFF',
            ]
        ];
    }

}
