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
use app\common\model\MessageTemplate;
use app\common\utils\ArrayUtils;
use Exception;

/**
 * 消息通知配置-逻辑层
 * Class NoticeLogic
 * @package app\admin\logic\setting
 */
class NoticeLogic extends Logic
{
    /**
     * 消息模板列表
     *
     * @author windy
     * @return array
     * @throws @\think\db\exception\DataNotFoundException
     * @throws @\think\db\exception\DbException
     * @throws @\think\db\exception\ModelNotFoundException
     */
    public static function lists(): array
    {
        $model = new MessageTemplate();
        return $model->field(true)->select()->toArray();
    }

    /**
     * 消息模板详细
     *
     * @author windy
     * @param int $id
     * @return array
     */
    public static function detail(int $id): array
    {
        $model = new MessageTemplate();
        return $model->field(true)->findOrEmpty($id)->toArray();
    }

    /**
     * 设置消息模板
     *
     * @author windy
     * @param array $post
     * @return bool
     */
    public static function set(array $post): bool
    {
        try {
            switch ($post['scene']) {
                case 'sms':
                    MessageTemplate::update([
                        'sms_template' => json_encode([
                            'template_code' => $post['template_code'],
                            'content'       => $post['content'],
                            'status'        => $post['status'],
                        ], JSON_UNESCAPED_UNICODE)
                    ], ['id'=>$post['id']]);
                    break;
                case 'oa':
                    MessageTemplate::update([
                        'oa_template' => json_encode([
                            'name'        => $post['name'],
                            'first'       => $post['first'],
                            'template_sn' => $post['template_sn'],
                            'template_id' => $post['template_id'],
                            'remark'      => $post['remark'],
                            'status'      => $post['status'],
                            'tpl'         => self::formatTplData($post)
                        ], JSON_UNESCAPED_UNICODE)
                    ], ['id'=>$post['id']]);
                    break;
                case 'mnp':
                    MessageTemplate::update([
                        'mnp_template' => json_encode([
                            'name'        => $post['name'],
                            'template_sn' => $post['template_sn'],
                            'template_id' => $post['template_id'],
                            'status'      => $post['status'],
                            'tpl'         => self::formatTplData($post)
                        ], JSON_UNESCAPED_UNICODE)
                    ], ['id'=>$post['id']]);
                    break;
            }

            return true;
        } catch (Exception $e) {
            static::$error = $e->getMessage();
            return false;
        }
    }

    /**
     * 处理模板参数格式
     *
     * @author windy
     * @param $post
     * @return array
     */
    private static function formatTplData($post): array
    {
        foreach ($post as &$value) {
            if (is_array($value)) {
                $value = array_values($value);
            }
        }
        return ArrayUtils::formToLinear($post);
    }
}