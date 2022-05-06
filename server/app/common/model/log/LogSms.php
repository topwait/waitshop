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

namespace app\common\model\log;


use app\common\basics\Models;
use app\common\enum\SmsEnum;

/**
 * 短信日志模型
 * Class LogSms
 * @package app\common\model\log
 */
class LogSms extends Models
{
    // 设置字段信息
    protected $schema = [
        'id'          => 'int',    //主键ID
        'scene'       => 'int',    //场景编码
        'mobile'      => 'string', //手机号码
        'code'        => 'string', //验证码
        'content'     => 'string', //短信内容
        'results'     => 'string', //短信结果
        'is_verify'   => 'int',    //是否验证[0=否, 1=是]
        'status'      => 'int',    //是否选中[1=发送中, 2=发送成功, 3=发送失败]
        'create_time' => 'int',    //创建时间
        'update_time' => 'int',    //更新时间
    ];

    /**
     * 写入短信日志
     *
     * @author windy
     * @param int $scene
     * @param int $mobile
     * @param $content
     * @param $code
     * @return int
     */
    public static function write(int $scene, int $mobile, $content, $code): int
    {
        $time = time();
        $result = self::create([
            'scene'       => $scene,
            'mobile'      => $mobile,
            'code'        => $code,
            'content'     => $content,
            'status'      => SmsEnum::SEND_ING,
            'create_time' => $time,
            'update_time' => $time
        ]);
        return intval($result['id']);
    }

    /**
     * 更新短信状态为-发送成功
     *
     * @author windy
     * @param int $id
     * @param string $result
     */
    public static function success(int $id, $result)
    {
        self::update([
            'results' => json_encode($result, JSON_UNESCAPED_UNICODE),
            'status'  => SmsEnum::SEND_SUCCESS
        ], ['id'=>$id]);
    }

    /**
     * 更新短信状态为-发送失败
     *
     * @author windy
     * @param int $id
     * @param string $result
     */
    public static function fail(int $id, $result)
    {
        self::update([
            'results' => json_encode($result, JSON_UNESCAPED_UNICODE),
            'status'  => SmsEnum::SEND_FAIL
        ], ['id'=>$id]);
    }
}