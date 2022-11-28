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

namespace app\common\model\auth;


use app\common\basics\Models;
use app\common\utils\UrlUtils;

/**
 * 管理员模型
 * Class Admin
 * @package app\common\model\auth
 */
class Admin extends Models
{
    // 设置字段信息
    protected $schema = [
        'id'          => 'int',     //主键ID
        'username'    => 'string',  //账号
        'nickname'    => 'string',  //昵称
        'password'    => 'string',  //密码
        'salt'        => 'string',  //盐巴
        'avatar'      => 'string',  //头像
        'email'       => 'string',  //邮箱
        'phone'       => 'string',  //手机号
        'role_id'     => 'int',     //管理员角色ID
        'is_disable'  => 'int',     //是否禁用[0=否, 1=是]
        'is_delete'   => 'int',     //是否删除[0=否,1=是]
        'login_ip'    => 'string',  //登录IP
        'login_time'  => 'string',  //登录时间
        'create_time' => 'int',     //创建时间
        'update_time' => 'int',     //更新时间
        'delete_time' => 'int',     //删除时间
    ];

    /**
     * 关联角色模型
     */
    public function role()
    {
        return $this->hasOne(AdminRole::class, 'id', 'role_id');
    }

    /**
     * 获取器: 格式化登录时间
     *
     * @author windy
     * @param $value
     * @return string
     */
    public function getLoginTimeAttr($value)
    {
        return date('Y-m-d H:i:s', $value);
    }

    /**
     * 修改器: 头像路径处理
     *
     * @author windy
     * @param $value
     * @return string
     */
    public function setAvatarAttr($value)
    {
        return $value ? UrlUtils::getRelativeUrl($value) : '';
    }

    /**
     * 获取器: 头像路径处理
     *
     * @author windy
     * @param $value
     * @return string
     */
    public function getAvatarAttr($value)
    {
        return $value ? UrlUtils::getAbsoluteUrl($value) : '';
    }
}