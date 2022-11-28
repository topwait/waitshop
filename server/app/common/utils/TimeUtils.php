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


class TimeUtils
{
    /**
     * 返回今日开始和结束的时间戳
     *
     * @author windy
     * @return array
     */
    public static function today(): array
    {
        list($y, $m, $d) = explode('-', date('Y-m-d'));
        return [
            mktime(0, 0, 0, $m, $d, $y),
            mktime(23, 59, 59, $m, $d, $y)
        ];
    }

    /**
     * 返回明天开始和结束时间戳
     *
     * @author windy
     * @return array
     */
    public static function tomorrow(): array
    {
        $date = date("Y-m-d",strtotime("+1 day"));
        return [
            strtotime($date.' 00:00:00'),
            strtotime($date.' 23:59:59')
        ];
    }

    /**
     * 返回昨日开始和结束的时间戳
     *
     * @author windy
     * @return array
     */
    public static function yesterday(): array
    {
        $yesterday = date('d') - 1;
        return [
            mktime(0, 0, 0, date('m'), $yesterday, date('Y')),
            mktime(23, 59, 59, date('m'), $yesterday, date('Y'))
        ];
    }

    /**
     * 返回本周开始和结束的时间戳
     *
     * @author windy
     * @return array
     */
    public static function week(): array
    {
        list($y, $m, $d, $w) = explode('-', date('Y-m-d-w'));
        if($w == 0) $w = 7; //修正周日的问题
        return [
            mktime(0, 0, 0, $m, $d - $w + 1, $y), mktime(23, 59, 59, $m, $d - $w + 7, $y)
        ];
    }

    /**
     * 返回上周开始和结束的时间戳
     *
     * @author windy
     * @return array
     */
    public static function lastWeek(): array
    {
        $timestamp = time();
        return [
            strtotime(date('Y-m-d', strtotime("last week Monday", $timestamp))),
            strtotime(date('Y-m-d', strtotime("last week Sunday", $timestamp))) + 24 * 3600 - 1
        ];
    }

    /**
     * 返回本月开始和结束的时间戳
     *
     * @return array
     * @author windy
     */
    public static function month(): array
    {
        list($y, $m, $t) = explode('-', date('Y-m-t'));
        return [
            mktime(0, 0, 0, $m, 1, $y),
            mktime(23, 59, 59, $m, $t, $y)
        ];
    }

    /**
     * 返回上个月开始和结束的时间戳
     *
     * @author windy
     * @return array
     */
    public static function lastMonth(): array
    {
        $y = date('Y');
        $m = date('m');
        $begin = mktime(0, 0, 0, $m - 1, 1, $y);
        $end = mktime(23, 59, 59, $m - 1, date('t', $begin), $y);

        return [$begin, $end];
    }

    /**
     * 返回今年开始和结束的时间戳
     *
     * @author windy
     * @return array
     */
    public static function year(): array
    {
        $y = date('Y');
        return [
            mktime(0, 0, 0, 1, 1, $y),
            mktime(23, 59, 59, 12, 31, $y)
        ];
    }

    /**
     * 返回去年开始和结束的时间戳
     *
     * @author windy
     * @return array
     */
    public static function lastYear(): array
    {
        $year = date('Y') - 1;
        return [
            mktime(0, 0, 0, 1, 1, $year),
            mktime(23, 59, 59, 12, 31, $year)
        ];
    }

    /**
     * 获取几天前零点到现在/昨日结束的时间戳
     *
     * @author windy
     * @param int $day 天数
     * @param bool $now 返回现在或者昨天结束时间戳
     * @return array
     */
    public static function dayToNow(int $day = 1, bool $now = true): array
    {
        $end = time();
        if (!$now) {
            list($foo, $end) = self::yesterday();
        }

        unset($foo);
        return [
            mktime(0, 0, 0, date('m'), date('d') - $day, date('Y')),
            $end
        ];
    }

    /**
     * 返回几天前的时间戳
     *
     * @author windy
     * @param int $day
     * @return int
     */
    public static function daysAgo(int $day = 1)
    {
        $nowTime = time();
        return $nowTime - self::daysToSecond($day);
    }

    /**
     * 返回几天后的时间戳
     *
     * @author windy
     * @param int $day
     * @return int
     */
    public static function daysAfter(int $day = 1)
    {
        $nowTime = time();
        return $nowTime + self::daysToSecond($day);
    }

    /**
     * 天数转换成秒数
     *
     * @author windy
     * @param int $day
     * @return int
     */
    public static function daysToSecond(int $day = 1)
    {
        return $day * 86400;
    }

    /**
     * 周数转换成秒数
     *
     * @author windy
     * @param int $week
     * @return int
     */
    public static function weekToSecond(int $week = 1)
    {
        return self::daysToSecond() * 7 * $week;
    }

    /**
     * 最近N天的日期
     *
     * @author windy
     * @param int $day
     * @return array
     */
    public static function nearToDate(int $day = 7): array
    {
        $time = time();
        $date = [];
        for ($i=1; $i<=$day; $i++){
            $date[$i-1] = date('Y-m-d' ,strtotime( '+' . ($i - $day) .' days', $time));
        }
        return $date;
    }

    /**
     * 当前毫秒数
     *
     * @author windy
     * @return float
     */
    public static function millisecond(): float
    {
        list($mse, $sec) = explode(' ', microtime());
        return (float)sprintf('%.0f', (floatval($mse) + floatval($sec)) * 1000);
    }

    /**
     * 返回今天是周几
     *
     * @author windy
     * @return mixed
     */
    public static function dayWeek(): string
    {
        $week = ['周日', '周一', '周二', '周三', '周四', '周五', '周六'];
        return $week[date('w')];
    }

    /**
     * 大写月份
     *
     * @author windy
     * @param int $month
     * @return string
     */
    public static function capMonth(int $month=0): string
    {
        $month = $month > 0 ? $month : intval(date('m'));
        switch ($month) {
            case 1:
                return '一月';
            case 2:
                return '二月';
            case 3:
                return '三月';
            case 4:
                return '四月';
            case 5:
                return '五月';
            case 6:
                return '六月';
            case 7:
                return '七月';
            case 8:
                return '八月';
            case 9:
                return '九月';
            case 10:
                return '十月';
            case 11:
                return '十一月';
            case 12:
                return '十二月';
        }
        return '未知月份';
    }

    /**
     * 格式化时间戳
     *
     * @param int $time
     * @return string
     */
    public static function formatTime(int $time): string
    {
        if ($time > 86400) {
            $days = intval($time / 86400);
            $hour = intval(($time - ($days * 86400)) / 3600);
            return $days.'天'.$hour.'时';
        } else {
            $hour   = intval($time / 3600);
            $minute = intval(($time - ($hour * 3600)) / 60);
            return $hour.'时'.$minute.'分';
        }
    }

}