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

namespace app\common\utils;


class TokenUtils
{
    /**
     * 生成随机令牌key
     *
     * @author windy
     * @param string $salt (加密盐)
     * @return string Token
     */
    public static function generateToken($salt=''): string
    {
        // 32个字符组成一组随机字符串
        $randChars = self::getRandChar(32);
        // 用三组字符串, 进行md5加密
        $timestamp = $_SERVER['REQUEST_TIME_FLOAT'];
        // salt 盐
        $salt = 'eWLuSr1GAdsRG1aX'.$salt;
        // 返回加密令牌
        return md5($randChars . $timestamp . $salt);
    }

    /**
     * 生成随机字符串
     *
     * @author windy
     * @param $length
     * @return string|null
     */
    public static function getRandChar($length): ?string
    {
        $str = null;
        $strPol = "ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789abcdefghijklmnopqrstuvwxyz";
        $max = strlen($strPol) - 1;

        for ($i = 0;
             $i < $length;
             $i++) {
            $str .= $strPol[rand(0, $max)];
        }

        return $str;
    }
}