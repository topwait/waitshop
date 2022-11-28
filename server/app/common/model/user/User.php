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

namespace app\common\model\user;


use app\common\basics\Models;
use app\common\utils\UrlUtils;
use think\model\relation\HasOne;

class User extends Models
{
    // 设置字段信息
    protected $schema = [
        'id'                    => 'int',     //主键ID
        'sn'                    => 'string',  //编码
        'nickname'              => 'string',  //昵称
        'avatar'                => 'string',  //头像
        'mobile'                => 'string',  //电话
        'password'              => 'string',  //登录密码
        'salt'                  => 'string',  //加密盐
        'sex'                   => 'int',     //性别[0=未知, 1=男, 2=女]
        'grade_id'              => 'int',     //用户等级,默认0
        'login_ip'              => 'string',  //登录IP
        'login_time'            => 'int',     //登录时间
        'integral'              => 'int',     //积分
        'money'                 => 'float',   //余额
        'earnings'              => 'float',   //拥挤收益
        'growth_value'          => 'int',     //成长值
        'total_order_amount'    => 'float',   //累计消费金额
        'total_recharge_amount' => 'float',   //累计充值金额
        'is_disable'            => 'int',     //是否禁用[0=否,1=是]
        'is_delete'             => 'int',     //是否删除[0=否,1=是]
        'create_time'           => 'int',     //创建时间
        'update_time'           => 'int',     //更新时间
        'delete_time'           => 'int',     //删除时间
    ];

    /**
     * 单关联：用户等级
     */
    public function grade(): HasOne
    {
        return $this->hasOne(UserGrade::class, 'id', 'grade_id')
            ->field('id,name');
    }

    /**
     * 获取器: 处理用户头像路径
     *
     * @author windy
     * @param $value
     * @return string
     */
   public function getAvatarAttr($value): string
   {
        if ($value) {
            return UrlUtils::getAbsoluteUrl($value);
        }
        return '';
   }
}