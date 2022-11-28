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

namespace app\common\utils;


use think\exception\HttpResponseException;
use think\Response;
use think\response\Json;

class JsonUtils
{
    /**
     * 规定返回的数据格式
     *
     * @author windy
     * @param int $code (状态码)
     * @param string $msg (提示)
     * @param array $data (返回数据集)
     * @return array
     */
    private static function result(int $code, string $msg='OK', array $data=[]) :array
    {
        return [
            'code' => $code,
            'msg'  => $msg,
            'data' => $data
        ];
    }

    /**
     * 获取成功
     *
     * @author windy
     * @param array $data (数据集)
     * @param string $msg (提示)
     * @param int $code (状态码)
     * @return Json
     */
    public static function success(array $data=[], string $msg='SUCCESS', int $code=0) : Json
    {
        $data = self::result($code, $msg, $data);
        return json($data);
    }

    /**
     * 操作成功
     *
     * @author windy
     * @param string $msg (提示)
     * @param array $data (数据集)
     * @param int $code (状态码)
     * @return Json
     */
    public static function ok(string $msg='OK', array $data=[], int $code=0) : Json
    {
        $data = self::result($code, $msg, $data);
        return json($data);
    }

    /**
     * 错误返回
     *
     * @author windy
     * @param string $msg (提示)
     * @param array $data (数据集)
     * @param int $code (状态码)
     * @return Json
     */
    public static function error(string $msg='Bad Request', array $data=[], int $code=1) : Json
    {
        $data = self::result($code, $msg, $data);
        return json($data, 200);
    }

    /**
     * 抛出JSON
     *
     * @author windy
     * @param string $msg
     * @param array $data
     * @param int $code
     */
    public static function throw(string $msg='Internal Server Error', array $data=[], int $code=500)
    {
        $data = self::result($code, $msg, $data);
        $response = Response::create($data, 'json', 200);
        throw new HttpResponseException($response);
    }
}
