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

namespace app\api\service;


use app\common\model\Freight;
use app\common\model\user\UserAddress;
use Exception;

/**
 * 运费服务类
 * Class FreightService
 * @package app\common\service
 */
class FreightService
{
    /**
     * 计算运费
     *
     * @author windy
     * @param array $orderStatus
     * @param $userId
     * @param $address_id
     * @return float|int|mixed
     */
    public static function computeFreight(array $orderStatus, $userId, $address_id)
    {
        $userAddress = (new UserAddress())->where([
            ['id', '=', (int)$address_id],
            ['user_id', '=', (int)$userId],
        ])->findOrEmpty()->toArray();

        if (!$userAddress) {
            return 0;
        }

        $freightAmount = 0;
        foreach ($orderStatus['pStatusArray'] as $product) {
            if ($product['freight_id'] > 0) {
                $temp['template_id'] = $product['freight_id'];
                $temp['totalVolume'] = $product['count'] * $product['volume'];
                $temp['totalWeight'] = $product['count'] * $product['weight'];;
                $temp['totalCount']  = $product['count'];

                $unit = 0;
                $freight = self::getFreightsByAddress($temp, $userAddress);
                if ($freight) {
                    switch ($freight['method']) {
                        case 10: //按件数
                            $unit = $temp['totalCount'];
                            break;
                        case 20: //按重量
                            $unit = $temp['totalWeight'];
                            break;
                        case 30: //按体积
                            $unit = $temp['totalVolume'];
                            break;
                    }

                    if($unit > $freight['first_unit'] && $freight['continue_unit'] > 0){
                        $left = ceil(($unit - $freight['first_unit']) / $freight['continue_unit']);
                        $freightAmount += $freight['first_fee'] + $left * $freight['continue_fee'];
                    }else{
                        $freightAmount += $freight['first_fee'];
                    }
                }
            }
        }

        return $freightAmount;
    }

    /**
     * 通过用户地址获取运费模板
     *
     * @author windy
     * @param $temp
     * @param $userAddress
     * @return false|mixed
     */
    public static function getFreightsByAddress($temp, $userAddress): bool
    {
        try {
            $freights = (new Freight())->field(true)
                ->with('rule')
                ->where(['id'=>$temp['template_id']])
                ->findOrEmpty()->toArray();

            foreach ($freights['rule'] as $freight) {
                $region_ids = explode(',', $freight['region']);
                $freight['name'] = $freights['name'];
                $freight['method'] = $freights['method'];

                if (in_array($userAddress['district_id'], $region_ids)) {
                    return $freight;
                }

                if (in_array($userAddress['city_id'], $region_ids)) {
                    return $freight;
                }

                if (in_array($userAddress['province_id'], $region_ids)) {
                    return $freight;
                }
            }

            return false;
        } catch (Exception $e) {
            return false;
        }
    }
}