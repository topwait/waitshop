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

namespace app\common\basics;


use think\exception\HttpResponseException;
use think\Response;
use think\response\Json;
use think\Validate;

/**
 * 验证器基类
 * Class Validate
 * @package app\common\basics
 */
abstract class Validates extends Validate
{
    /**
     * 切面验证接收到的参数
     *
     * @author windy
     * @param null $scene (场景验证)
     * @param array $data (扩展的验证参数)
     * @return mixed
     */
    public function goCheck($scene=null, array $data=[])
    {
        // 1.接收参数
        if (request()->isGet()) {
            $params = request()->get();
        } else {
            $params = request()->post();
        }
        if (!empty($data)) {
            $params = array_merge($params, $data);
        }
        // 2.验证参数
        if (!($scene ? $this->scene($scene)->check($params) : $this->check($params))) {
            $exception = is_array($this->error)
                ? implode(';', $this->error) : $this->error;

            $data = array('code'=>1004, 'msg'=>$exception, 'data'=>[]);
            $response = Response::create($data, 'json', 200);
            throw new HttpResponseException($response);
        }
        // 3.成功返回数据
        return $params;
    }

    /**
     * 验证是否是正整数
     *
     * @author windy
     * @param $value(需验证的值)
     * @return bool
     */
    protected function isInteger($value): bool
    {
        if (is_numeric($value) && is_int($value + 0) && ($value + 0) > 0) {
            return true;
        }
        return false;
    }

    /**
     * 验证最小值
     *
     * @author windy
     * @param $value (需验证的值)
     * @param $rule (规则值)
     * @return bool
     */
    protected function minValue($value, $rule): bool
    {
        if ($value < $rule) {
            return false;
        }

        return true;
    }

    /**
     * 验证最大值
     *
     * @author windy
     * @param $value (需验证的值)
     * @param $rule (规则值)
     * @return bool
     */
    protected function maxValue($value, $rule): bool
    {
        if ($value > $rule) {
            return false;
        }

        return true;
    }
}