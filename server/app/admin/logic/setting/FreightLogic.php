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

namespace app\admin\logic\setting;


use app\common\basics\Logic;
use app\common\enum\FreightEnum;
use app\common\model\Freight;
use app\common\model\FreightRule;
use app\common\model\Region;
use app\common\utils\ArrayUtils;
use Exception;
use think\facade\Cache;

/**
 * 运费模板配置-逻辑层
 * Class FreightLogic
 * @package app\admin\logic\setting
 */
class FreightLogic extends Logic
{
    /**
     * 运费模板列表
     *
     * @author windy
     * @param array $get
     * @return array|bool
     * @throws @\think\db\exception\DbException
     */
    public static function lists(array $get)
    {
        $model = new Freight();
        $lists = $model->field(true)
            ->order(['id', 'sort'=>'desc'])
            ->paginate([
                'page'      => $get['page'],
                'list_rows' => $get['limit'],
                'var_page'  => 'page',
            ])->toArray();

        foreach ($lists['data'] as &$item) {
            $item['remarks'] = $item['remarks'] ? $item['remarks'] : '无';
            $item['method'] = FreightEnum::getEnumName($item['method']);
        }

        return ['count'=>$lists['total'], 'list'=>$lists['data']];
    }

    /**
     * 所有运费模板
     *
     * @author windy
     * @return array|false
     * @throws @\think\db\exception\DataNotFoundException
     * @throws @\think\db\exception\DbException
     * @throws @\think\db\exception\ModelNotFoundException
     */
    public static function all(): array
    {
        $model = new Freight();
        return $model->field(true)
            ->order(['id', 'sort'=>'desc'])
            ->select()->toArray();
    }

    /**
     * 配送地区
     *
     * @author windy
     * @return array|string
     * @throws @\think\db\exception\DataNotFoundException
     * @throws @\think\db\exception\DbException
     * @throws @\think\db\exception\ModelNotFoundException
     */
    public static function region()
    {
        $region = Cache::get('wait_region');
        if (!$region) {
            $model = new Region();
            $lists = $model->field(['id', 'parent_id as pid', 'name as title'])
                ->whereIn('level', [0,1,2])->select()->toArray();
            $region = ArrayUtils::toTreeJson($lists)[0]['children'];
            Cache::set('wait_region', $region);
        }
        return $region;
    }

    /**
     * 配送模板详细
     *
     * @author windy
     * @param int $id
     * @return array
     */
    public static function detail(int $id): array
    {
        $model = new Freight();
        return $model->field(true)
            ->with(['rule'])
            ->findOrEmpty((int)$id)->toArray();
    }

    /**
     * 新增运费模板
     *
     * @author windy
     * @param array $post
     * @return bool
     */
    public static function add(array $post): bool
    {
        try {
            $freight = Freight::create([
                'name'    => $post['name'],
                'method'  => $post['method'],
                'remarks' => $post['remarks'],
                'sort'    => $post['sort'] ?? 0,
                'create_time' => time()
            ]);

            $lists = [];
            foreach ($post['region'] as $key => $item) {
                $lists[] = [
                    'freight_id'    => $freight['id'],
                    'first_unit'    => $post['first_unit'][$key],
                    'first_fee'     => $post['first_fee'][$key],
                    'continue_unit' => $post['continue_unit'][$key],
                    'continue_fee'  => $post['continue_fee'][$key],
                    'region'        => $post['region'][$key],
                    'address'       => $post['address'][$key],
                    'create_time'   => time(),
                ];
            }

            if (!empty($lists)) (new FreightRule())->saveAll($lists);

            return true;
        } catch (Exception $e) {
            static::$error = $e->getMessage();
            return false;
        }
    }

    /**
     * 编辑运费模板
     *
     * @author windy
     * @param array $post
     * @return bool
     */
    public static function edit(array $post): bool
    {
        try {
            Freight::update([
                'name'    => $post['name'],
                'method'  => $post['method'],
                'remarks' => $post['remarks'],
                'sort'    => $post['sort'] ?? 0
            ], ['id'=>(int)$post['id']]);

            $lists = [];
            foreach ($post['region'] as $key => $item) {
                $lists[] = [
                    'freight_id'    => $post['id'],
                    'first_unit'    => $post['first_unit'][$key],
                    'first_fee'     => $post['first_fee'][$key],
                    'continue_unit' => $post['continue_unit'][$key],
                    'continue_fee'  => $post['continue_fee'][$key],
                    'region'        => $post['region'][$key],
                    'address'       => $post['address'][$key],
                    'create_time'   => time(),
                ];
            }

            (new FreightRule)->where(['freight_id'=>(int)$post['id']])->delete();

            if (!empty($lists)) (new FreightRule)->saveAll($lists);

            return true;
        } catch (Exception $e) {
            static::$error = $e->getMessage();
            return false;
        }
    }

    /**
     * 删除运费模板
     *
     * @author windy
     * @param int $id
     * @return bool
     */
    public static function destroy(int $id): bool
    {
        try {
            (new Freight)->where(['id'=>$id])->delete();
            (new FreightRule)->where(['freight_id'=>$id])->delete();
            return true;
        } catch (Exception $e) {
            static::$error = $e->getMessage();
            return false;
        }
    }
}