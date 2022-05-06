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

namespace app\common\service\message;


use app\common\enum\ClientEnum;
use app\common\model\user\UserAuth;
use app\common\utils\WeChatUtils;
use EasyWeChat\Factory;
use Exception;

/**
 * 公众号消息服务类
 * Class OaMessageService
 * @package app\common\service\message
 */
class OaMessageService
{
    /**
     * easyWeChat对象
     */
    private $app;

    /**
     * 用户Openid
     */
    private $openid;

    /**
     * 构造方法
     *
     * OaMessageService constructor.
     * @param $userId
     * @throws Exception
     */
    public function __construct(int $userId)
    {
        $this->openid = UserAuth::getUserOpenId($userId, ClientEnum::OA);
        if (!$this->openid) {
            throw new Exception('未找到用户openid');
        }
        $config = WeChatUtils::getOaConfig();
        $this->app = Factory::officialAccount($config);
    }

    /**
     * 发生微信公众号通知
     *
     * @author windy
     * @param array $params
     * @param array $template
     * @return bool|string
     * @throws @\GuzzleHttp\Exception\GuzzleException
     */
    public function send(array $params, array $template)
    {
        try {
            $oaTemplate = $this->getTemplate($params, $template);
            $this->app->template_message->send($oaTemplate);

            return true;
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    /**
     * 获取公众号消息模板
     *
     * @author windy
     * @param array $params
     * @param array $template
     * @return array
     */
    private function getTemplate(array $params, array $template)
    {
        // 定义模板格式
        $domain = request()->domain();
        $tpl = [
            'touser'      => $this->openid,
            'template_id' => $template['template_id'],
            'url'         => $domain.$params['url'],
            'data'        => [
                'first'   => $template['first'],
                'remark'  => $template['remark']
            ]
        ];

        // 设置模板参数
        foreach ($template['tpl'] as $item) {
            foreach ($params['params'] as $k => $v) {
                $search_replace = '{'.$k.'}';
                $item['tpl_content'] = str_replace($search_replace, $v, $item['tpl_content']);
            }
            $tpl['data'][$item['tpl_keyword']] = $item['tpl_content'];
        }

        return $tpl;
    }
}