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

namespace app\common\basics;


use app\common\model\log\LogVisit;
use app\common\model\user\UserToken;
use app\common\utils\JsonUtils;
use app\common\utils\TimeUtils;
use Exception;
use think\App;

/**
 * API接口基类
 * Class Api
 * @package app\common\basics
 */
abstract class Api
{
    /**
     * Request实例
     */
    protected $request;

    /**
     * 应用实例
     */
    protected $app;

    /**
     * 用户ID
     * @var int
     */
    protected $userId = 0;

    /**
     * 客户端
     * @var int
     */
    protected $client = 0;

    /**
     * 不校验登录的方法
     * @var array
     */
    public $notNeedLogin = [];

    /**
     * 构造方法
     *
     * @author windy
     * @access public
     * @param App $app 应用对象
     */
    public function __construct(App $app)
    {
        $this->app     = $app;
        $this->request = $this->app->request;

        // 控制器初始化
        $this->initialize();
    }

    /**
     * 初始化
     * @author windy
     */
    protected function initialize()
    {
        $this->visit();
        $this->isTokenUser();
    }

    /**
     * 验证token是否有效
     * @author windy
     */
    public function isTokenUser(): bool
    {
        // 接收并验证令牌
        $token = request()->header('token') ?? '';
        $tokenRes = UserToken::getUserIdByToken($token);

        // 判断是否是免登录的方法
        if (in_array(request()->action(), $this->notNeedLogin)) {
            $this->userId = $tokenRes['user_id'] ?? 0;
            $this->client = intval(request()->header('client') ?? 0);
            return true;
        }

        // 验证是否是尚未登陆状态
        if (empty($token) && !$this->notNeedLogin) {
            JsonUtils::throw('缺少token参数', [], 1008);
        }

        // 根据token授权验证
        if (!$tokenRes) {
            JsonUtils::throw('登录超时，请重新登录', [], 1009);
        }

        // 保存信息到全局属性
        $this->userId = $tokenRes['user_id'];
        $this->client = intval(request()->header('client'));
        return true;
    }

    /**
     * 获取get请求数据
     *
     * @author windy
     * @param $key
     * @param $default
     * @return mixed
     */
    protected function getData($key=null, $default=null)
    {
        if ($key === null) {
            return $this->request->get();
        } else {
            return $default ? $this->request->get($key, $default) : $this->request->get($key);
        }
    }

    /**
     * 获取post请求数据
     *
     * @author windy
     * @param $key
     * @param $default
     * @return mixed
     */
    protected function postData($key=null, $default=null)
    {
        if ($key === null) {
            return $this->request->post();
        } else {
            return $default ? $this->request->post($key, $default) : $this->request->post($key);
        }
    }

    /**
     * 记录访问日志
     *
     * @author windy
     */
    protected function visit()
    {
        try {
            $ip = $this->request->ip();
            $client = $this->request->header('client', 1);
            $visit = (new LogVisit())->where([
                ['ip', '=', $ip],
                ['client', '=', intval($client)],
                ['create_time', '>=', TimeUtils::today()[0]],
                ['create_time', '<=', TimeUtils::today()[1]]
            ])->findOrEmpty()->toArray();

            if ($visit) {
                LogVisit::update([
                    'count'       => ['inc', 1],
                    'update_time' => time()
                ], ['id' => $visit['id']]);
            } else {
                LogVisit::create([
                    'ip'          => $ip,
                    'client'      => intval($client),
                    'count'       => 1,
                    'create_time' => time(),
                    'update_time' => time()
                ]);
            }
        } catch (Exception $e) {}
    }
}