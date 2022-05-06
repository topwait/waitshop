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

namespace app\common\model\addons;


use app\common\basics\Models;
use app\common\model\user\User;
use app\common\utils\UrlUtils;
use think\model\relation\HasOne;

/**
 * 拼团开团模型
 * Class SharingFound
 * @package app\common\model\marketing
 */
class TeamFound extends Models
{
    // 设置字段信息
    protected $schema = [
        'id'           => 'int',    //主键ID
        'user_id'      => 'int',    //用户ID
        'team_id'      => 'int',    //活动ID
        'people'       => 'int',    //几人团
        'join'         => 'int',    //几人参团
        'status'       => 'int',    //拼团状态：1=拼团中, 2=拼团成功, 3=拼团失败
        'snapshot'     => 'string', //拼团商品快照
        'kaituan_time' => 'int',    //开团时间
        'invalid_time' => 'int',    //失效时间
    ];

    /**
     * 单关联：拼团活动模型
     */
    public function team(): HasOne
    {
        return $this->hasOne(Team::class, 'id', 'team_id')
            ->withoutField('delete_time');
    }

    /**
     * 单关联: 用户模型
     */
    public function user(): HasOne
    {
        return $this->hasOne(User::class, 'id', 'user_id')
            ->field('id,sn,nickname,avatar,mobile');
    }

    /**
     * 获取器: 修改拼团商品快照格式
     *
     * @author windy
     * @param $value
     * @return array|mixed
     */
    public function getSnapshotAttr($value): array
    {
        if ($value) {
            $result = json_decode($value, true);
            $result['image'] = UrlUtils::getAbsoluteUrl($result['image']);
            if ($result['banner']) {
                $banner = [];
                $arr = json_decode($result['banner'], true);
                foreach ($arr as $img) {
                    $banner[] = UrlUtils::getAbsoluteUrl($img);
                }
                $result['banner'] = $banner;
            }
            return $result;
        }
        return [];
    }
}