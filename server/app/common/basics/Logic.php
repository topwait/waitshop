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


use app\common\model\auth\Admin;

/**
 * 逻辑层基类
 * Class Logic
 * @package app\common\basics
 */
abstract class Logic
{
    private static $model;

    /**
     * 错误信息
     * @var string
     */
    protected static $error;

    /**
     * 返回状态码
     * @var int
     */
    protected static $returnCode = 1;

    /**
     * 搜索条件
     * @var array
     */
    protected static $searchWhere = [];

    /**
     * 返回错误信息
     *
     * @author windy
     * @access public
     * @return string|array
     */
    public static function getError()
    {
        return self::$error ?? 'Unknown';
    }

    /**
     * 返回指定状态码
     *
     * @author windy
     * @return int
     */
    public static function getReturnCode(): int
    {
        return self::$returnCode;
    }

    /**
     * 获取当前请求时间戳
     *
     * @author windy
     * @return int
     */
    public static function reqTime(): int
    {
        return time();
    }

    /**
     * 事务开启
     *
     * @author windy
     */
    public static function dbStartTrans(): void
    {
        self::$model = new Admin();
        self::$model->startTrans();
    }

    /**
     * 事务提交
     *
     * @author windy
     */
    public static function dbCommit(): void
    {
        self::$model->commit();
    }

    /**
     * 事务回滚
     *
     * @author windy
     */
    public static function dbRollback(): void
    {
        self::$model->rollback();
    }

    /**
     * 设置搜索条件
     * PS: 参数名@字段名: user@U.sn
     *
     * @author windy
     * @param array $search
     * @return array
     */
    public static function setSearch(array $search): array
    {
        $params = request()->param();
        if (empty($search)) {
            return [];
        }

        $where = [];
        foreach ($search as $whereType => $whereFields) {
            switch ($whereType) {
                case '=':
                case '<>':
                case '>':
                case '>=':
                case '<':
                case '<=':
                case 'in':
                    foreach ($whereFields as $whereField) {
                        $paramsName = strpos($whereField, '@') ? explode('@', $whereField) : $whereField;
                        $key   = is_array($paramsName) ? $paramsName[0] : $paramsName; //参数的名称
                        $field = is_array($paramsName) ? $paramsName[1] : $whereField; //字段的名称
                        if (!isset($params[$key]) || empty($params[$key])) {
                            continue;
                        }
                        $where[] = [$field, $whereType, $params[$key]];
                    }
                    break;
                case '%like%':
                    foreach ($whereFields as $whereField) {
                        $paramsName = strpos($whereField, '@') ? explode('@', $whereField) : $whereField;
                        $key = is_array($paramsName) ? $paramsName[0] : $paramsName;
                        $val = is_array($paramsName) ? $paramsName[1] : $whereField;
                        if (empty($params[$key])) {
                            continue;
                        }
                        $where[] = [$val, 'like', '%' . $params[$key] . '%'];
                    }
                    break;
                case '%like':
                    foreach ($whereFields as $whereField) {
                        $paramsName = strpos($whereField, '@') ? explode('@', $whereField) : $whereField;
                        $key = is_array($paramsName) ? $paramsName[0] : $paramsName;
                        $val = is_array($paramsName) ? $paramsName[1] : $whereField;
                        if (!isset($params[$key]) || (empty($params[$key]))) {
                            continue;
                        }
                        $where[] = [$val, 'like', '%' . $params[$key]];
                    }
                    break;
                case 'like%':
                    foreach ($whereFields as $whereField) {
                        $paramsName = strpos($whereField, '@') ? explode('@', $whereField) : $whereField;
                        $key = is_array($paramsName) ? $paramsName[0] : $paramsName;
                        $val = is_array($paramsName) ? $paramsName[1] : $whereField;
                        if (empty($params[$key] || !isset($params[$key]))) {
                            continue;
                        }
                        $where[] = [$val, 'like', $params[$key]];
                    }
                    break;
                case 'between':
                    foreach ($whereFields as $whereField => $paramsValue) {
                        $paramsName = explode('|', $paramsValue);
                        if (empty($params[$paramsName[0]]) || empty($params[$paramsName[1]])) {
                            break;
                        }
                        $start = $params[$paramsName[0]];
                        $end   = $params[$paramsName[1]];
                        $where[] = [$whereField, 'between', [$start, $end]];
                    }
                    break;
                case 'datetime':
                    foreach ($whereFields as $whereField) {
                        $paramsName = !strpos($whereField, '@') ? $whereField : explode('@', $whereField);
                        $key = is_array($paramsName) ? $paramsName[0] : $paramsName;
                        $val = is_array($paramsName) ? $paramsName[1] : $whereField;
                        if (!isset($params[$key]) || empty($params[$key])) {
                            continue;
                        }

                        list($start, $end) = explode(' - ', $params[$key]);
                        $where[] = [$val, '>=', strtotime($start)];
                        $where[] = [$val, '<=', strtotime($end)];
                    }
                    break;
                case 'keyword':
                    if (!isset($params['keyword_type']) || empty($params['keyword'])) {
                        break;
                    }
                    if (isset($params['keyword']) and $params['keyword']) {
                        $value = $whereFields[$params['keyword_type']];
                        switch ($value[0]) {
                            case '=':case '<>':case '>':case '>=':case '<':case '<=':case 'in':
                            $where[] = [$value[1], $value[0], $params['keyword']];
                            break;
                            case '%like%':
                                $where[] = [$value[1], 'like', '%' . $params['keyword'] . '%'];
                                break;
                            case '%like':
                                $where[] = [$value[1], 'like', '%' . $params['keyword']];
                                break;
                            case 'like%':
                                $where[] = [$value[1], 'like', $params['keyword'] . '%'];
                                break;
                        }
                    }
                    break;
            }
        }
        self::$searchWhere = $where;
        return $where;
    }
}