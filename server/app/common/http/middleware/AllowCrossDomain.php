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

namespace app\common\http\middleware;


use Closure;

/**
 * 自定义跨域中间件
 * Class AllowCrossDomain
 * @package app\common\http\middleware
 */
class AllowCrossDomain
{
    /**
     * 跨域处理
     *
     * @author windy
     * @param $request
     * @param Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $response = $next($request);
        $response->header([
            'Access-Control-Allow-Origin'      => '*',
            'Access-Control-Allow-Headers'     => '*',
            'Access-Control-Allow-Methods'     => 'GET, POST, PATCH, PUT, DELETE',
            'Access-Control-Allow-Credentials' => 'true',
            'Access-Control-Max-Age'           => '1728000',
        ]);
        if (strtoupper($request->method()) === 'OPTIONS') {
            $response->code(204);
        }
        return $response;
    }
}