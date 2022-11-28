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

namespace app\admin\logic\setting;


use app\common\basics\Logic;
use app\common\model\Express;
use Exception;

/**
 * 物流公司配置-逻辑层
 * Class ExpressLogic
 * @package app\admin\logic\setting
 */
class ExpressLogic extends Logic
{
    /**
     * 物流公司列表
     *
     * @author windy
     * @param array $get
     * @return array
     * @throws @\think\db\exception\DbException
     */
    public static function lists(array $get): array
    {
        $lists = (new Express())
            ->field(true)
            ->order('sort desc, id desc')
            ->paginate([
                'page'      => $get['page'],
                'list_rows' => $get['limit'],
                'var_page'  => 'page',
            ])->toArray();

        foreach ($lists['data'] as &$item) {
            $item['code100']  = $item['code100']  ? $item['code100']  : '未设置';
            $item['codebird'] = $item['codebird'] ? $item['codebird'] : '未设置';
        }

        return ['count'=>$lists['total'], 'list'=>$lists['data']];
    }

    /**
     * 所有物流公司
     *
     * @author windy
     * @return array
     * @throws @\think\db\exception\DbException
     * @throws @\think\db\exception\ModelNotFoundException
     */
    public static function all(): array
    {
        return (new Express())
            ->field(true)
            ->order('sort desc, id desc')
            ->select()->toArray();
    }
}