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

namespace app\admin\logic\system;


use app\common\basics\Logic;
use Exception;

class ClearLogic extends Logic
{
    /**
     * 清除系统缓存
     *
     * @author windy
     * @param array $post
     * @return bool
     */
    public static function cache(array $post): bool
    {
        try {
            if (empty($post['type'])) {
                throw new Exception('请至少选择一个类型');
            }

            foreach ($post['type'] as $type) {
                switch (intval($type)) {
                    case 1: //系统缓存
                        delete_dir(root_path().'runtime/cache/');
                        break;
                    case 2: //登录缓存
                        delete_dir(root_path().'runtime/session/');
                        break;
                    case 3: //模板缓存
                        delete_dir(public_path().'uploads/temp/');
                        delete_dir(public_path().'uploads/pem/');
                        break;
                    case 4: //临时图片
                        delete_dir(root_path().'runtime/admin/log/');
                        break;
                    case 5: //日志文件
                        delete_dir(root_path().'runtime/admin/log/');
                        delete_dir(root_path().'runtime/api/log/');
                        delete_dir(root_path().'runtime/log/');
                        break;
                }
            }

            return true;
        } catch (Exception $e) {
            static::$error = $e->getMessage();
            return false;
        }
    }
}