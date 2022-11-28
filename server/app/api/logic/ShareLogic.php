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

namespace app\api\logic;


use app\common\basics\Logic;
use app\common\enum\ClientEnum;
use app\common\model\goods\Goods;
use app\common\model\user\User;
use app\common\utils\UrlUtils;
use app\common\utils\WeChatUtils;
use EasyWeChat\Factory;
use EasyWeChat\Kernel\Http\StreamResponse;
use Endroid\QrCode\Color\Color;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\ErrorCorrectionLevel\ErrorCorrectionLevelLow;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\RoundBlockSizeMode\RoundBlockSizeModeMargin;
use Endroid\QrCode\Writer\PngWriter;
use Exception;

/**
 * 分享海报接口-逻辑层
 * Class ShareLogic
 * @package app\api\logic
 */
class ShareLogic extends Logic
{
    /**
     * 生成用户推广海报
     *
     * @author windy
     * @param int $userId (用户ID)
     * @param int $client (客户端ID)
     * @param string $url (跳转地址)
     * @return array|false
     */
    public static function userPoster(int $userId, int $client, string $url)
    {
        try {
            // 设置配置
            $fontPath = public_path() . '/fonts/SourceHanSansCN-Regular.otf';
            $config = [
                'avatar'    => ['w' => 80, 'h' => 80, 'x' => 30, 'y' => 680],
                'nickname'  => ['color' => '#333333', 'font-family' => $fontPath, 'font-size' => 20, 'x' => 120, 'y' => 730],
                'title'     => ['color' => '#333333', 'font-family' => $fontPath, 'font-size' => 20, 'x' => 30, 'y' => 810, 'w' => 360],
                'notice'    => ['color' => '#333333', 'font-family' => $fontPath, 'font-size' => 20, 'x' => 30, 'y' => 880],
                'code_text' => ['color' => '#FF2C3C', 'font-family' => $fontPath, 'font-size' => 20, 'x' => 355, 'y' => 930],
                'qr'        => ['w' => 170, 'h' => 170, 'x' => 370, 'y' => 730],
            ];

            // 获取用户
            $user = (new User())->findOrEmpty($userId)->toArray();

            // 存储路径
            $headPath = UrlUtils::getRelativeUrl($user['avatar']);
            $rootPath = 'uploads/temp/';
            $savePath = 'uploads/temp/'.md5($client.$userId).'.png';
            $qrPath   = 'uploads/temp/'.md5($client.$user['distribution_code']).'.png';

            // 创建目录
            if(!file_exists($rootPath)) {
                mkdir($rootPath,0777,true);
            }

            // 生成二维码
            switch ($client) {
                case ClientEnum::MNP:
                    $fileName = md5($client.$user['distribution_code']).'.png';
                    self::makeMnpQrCode($rootPath, $fileName, $user['distribution_code'], $url);
                    break;
                case ClientEnum::OA:
                case ClientEnum::H5:
                    $params = ['id'=>$userId, 'invite_code'=>$user['distribution_code']];
                    $reqUrl = url($url, $params)->domain(true);
                    self::makeWebQrCode($qrPath, $reqUrl);
                    break;
                case ClientEnum::IOS:
                case ClientEnum::ANDROID:
                    $params = ['id'=>$userId, 'invite_code'=>$user['distribution_code'], 'app'=>1];
                    $reqUrl = url($url, $params)->domain(true);
                    self::makeWebQrCode($qrPath, $reqUrl);
            }

            // 创建海报图
            $canvas = imagecreatefromstring(file_get_contents(public_path() . 'static/images/share_bg.png'));
            self::writeImg($canvas, public_path() . $headPath, $config['avatar'], true);
            self::writeImg($canvas, public_path() . $qrPath, $config['qr']);
            self::writeText($canvas, $user['nickname'], $config['nickname']);
            self::writeText($canvas, '邀请您一起来赚大钱', $config['title']);
            self::writeText($canvas, '长按识别二维码 >>', $config['notice']);

            // 合成图片
            imagepng($canvas, public_path() . $savePath);
            unlink(public_path() . $qrPath);

            // 转base64
            $base64 = image_to_base64(public_path() . $savePath);

            // 删除原件
            unlink(public_path() . $savePath);

            // 返回结果
            return ['url' => $base64];
        } catch (Exception $e) {
            static::$error = $e->getMessage();
            return false;
        }
    }

    /**
     * 生成商品分享
     *
     * @param string $type (类型: goods)
     * @param int $goodsId (商品ID)
     * @param int $userId  (用户ID)
     * @param int $client  (客户端)
     * @param string $url  (调整地址)
     * @return bool|array
     * @author windy
     */
    public static function goodsPoster(string $type, int $goodsId, int $userId, int $client, string $url)
    {
        try {
            // 设置配置
            $fontPath = public_path() . '/fonts/SourceHanSansCN-Regular.otf';
            $config = [
                'image'         => ['w' => 560, 'h' => 560, 'x' => 40, 'y' => 100],
                'avatar'        => ['w' => 64, 'h' => 64, 'x' => 40, 'y' => 20],
                'nickname'      => ['color' => '#555555', 'font-family' => $fontPath, 'font-size' => 19, 'x' => 120, 'y' => 60],
                'title'         => ['color' => '#333333', 'font-family' => $fontPath, 'font-size' => 20, 'w' => 360, 'x' => 40, 'y' => 785],
                'sell_symbol'   => ['color' => '#FF2C3C', 'font-family' => $fontPath, 'font-size' => 22, 'w' => 140, 'x' => 40, 'y' => 722],
                'sell_price'    => ['color' => '#FF2C3C', 'font-family' => $fontPath, 'font-size' => 30, 'w' => 140, 'x' => 66, 'y' => 722],
                'market_price'  => ['color' => '#999999', 'font-family' => $fontPath, 'font-size' => 20, 'w' => 140, 'x' => 180, 'y' => 722],
                'notice'        => ['color' => '#888888', 'font-family' => $fontPath, 'font-size' => 18, 'x' => 432, 'y' => 895],
                'qr'            => ['w' => 165,'h' => 165, 'x' => 436, 'y' => 700]
            ];

            // 获取用户
            $user = (new User())->findOrEmpty($userId)->toArray();

            // 获取商品
            $goods = ['id'=>0, 'name'=>'', 'image'=>'', 'min_price'=>0, 'max_price'=>0];
            switch ($type) {
                case 'goods':
                    $goods = (new Goods())->field('id,name,image,min_price as min_price,market_price as max_price')->findOrEmpty($goodsId)->toArray();
                    break;
            }

            // 存储路径
            $headPath = UrlUtils::getRelativeUrl($user['avatar']);
            $rootPath = 'uploads/temp/';
            $savePath = 'uploads/temp/'.md5($client.$goods['id'].$userId).'.png';
            $qrPath   = 'uploads/temp/'.md5($client.$goods['id'].$user['distribution_code']).'.png';
            $bgPath   = public_path().'static/images/share_goods_bg.png';

            // 创建目录
            if(!file_exists(public_path().$rootPath)) {
                mkdir(public_path().$rootPath,0777,true);
            }

            // 生成二维码
            switch ($client) {
                case ClientEnum::MNP:
                    $fileName = md5($client.$goods['id'].$user['distribution_code']).'.png';
                    self::makeMnpQrCode($rootPath, $fileName, $user['distribution_code'], $url);
                    break;
                case ClientEnum::OA:
                case ClientEnum::H5:
                    $params = ['goods_id'=>$goodsId, 'invite_code'=>$user['distribution_code']];
                    $reqUrl = url($url, $params,'')->domain(true);
                    self::makeWebQrCode($qrPath, $reqUrl);
                    break;
                case ClientEnum::IOS:
                case ClientEnum::ANDROID:
                    $params = ['goods_id'=>$goodsId, 'invite_code'=>$user['distribution_code'], 'app'=>1];
                    $reqUrl = url($url, $params, '')->domain(true);
                    self::makeWebQrCode($qrPath, $reqUrl);
            }

            // 创建海报图
            $canvas = imagecreatefromstring(file_get_contents($bgPath));
            self::writeImg($canvas, public_path() . $headPath, $config['avatar'], true);
            self::writeImg($canvas, public_path() . $qrPath, $config['qr']);
            self::writeImg($canvas, public_path() . UrlUtils::getRelativeUrl($goods['image']), $config['image']);
            self::writeText($canvas, '来自'.$user['nickname'].'的分享', $config['nickname']);
            self::writeText($canvas, '长按识别二维码', $config['notice']);
            self::writeText($canvas, '￥', $config['sell_symbol']);
            self::writeText($canvas, $goods['min_price'], $config['sell_price']);
            self::writeText($canvas,  '￥'.$goods['max_price'], $config['market_price']);

            $confTitle = $config['title'];
            $goodsName = auto_adapt($confTitle['font-size'], 0, $confTitle['font-family'], $goods['name'], $confTitle['w'], $confTitle['y'], getimagesize($bgPath));
            self::writeText($canvas, $goodsName, $config['title']);

            // 合成图片
            imagepng($canvas, public_path() . $savePath);
            unlink(public_path() . $qrPath);

            // 转base64
            $base64 = image_to_base64(public_path() . $savePath);

            // 删除原件
            unlink(public_path() . $savePath);

            // 返回结果
            return ['url' => $base64];
        } catch (Exception $e) {
            static::$error = $e->getMessage();
            return false;
        }
    }

    /**
     * 往背景图写入图片
     *
     * @author windy
     * @param $canvas (画布对象)
     * @param $imgUri (图片路径)
     * @param $config (配置)
     * @param false $isRound
     * @return Resource
     */
    private static function writeImg($canvas, $imgUri, $config, $isRound=false)
    {
        $image = imagecreatefromstring(file_get_contents($imgUri));
        $isRound ? $image = convert_circle($image) : null;

        imagecopyresampled($canvas, $image,
            $config['x'],
            $config['y'],
            0, 0,
            $config['w'],
            $config['h'],
            imagesx($image),
            imagesy($image)
        );

        return $canvas;
    }

    /**
     * 往背景图写入文本
     *
     * @author windy
     * @param $canvas (画布对象)
     * @param string $text (要写的文本)
     * @param array $config (配置)
     */
    private static function writeText($canvas, string $text, array $config)
    {
        // 颜色转换
        $color= str_split(substr($config['color'],1), 2);
        $color = array_map('hexdec', $color);
        if (empty($color[3]) || $color[3] > 127) {
            $color[3] = 0;
        }

        // 写入文本
        $fontColor = imagecolorallocatealpha($canvas, $color[0], $color[1], $color[2], $color[3]);
        imagettftext($canvas, $config['font-size'], 0, $config['x'], $config['y'], $fontColor, $config['font-family'], $text);
    }

    /**
     * 制作微信小程序二维码
     * 说明: 微信小程序端可用
     *
     * @author windy
     * @param string $path (保存路径)
     * @param string $fileName (文件名称)
     * @param string $code (分销码)
     * @param string $url (跳转地址)
     * @return bool|mixed|string
     * @throws Exception
     */
    private static function makeMnpQrCode(string $path, string $fileName, string $code, string $url)
    {
        try {
            $config = WeChatUtils::getMnpConfig();
            $app = Factory::miniProgram($config);
            $response = $app->app_code->get($url . '?invite_code=' . $code, ['width'=>170]);

            if ($response instanceof StreamResponse) {
                $response->saveAs($path,  $fileName);
                return true;
            }

            if (isset($response['errcode']) && 41030 === $response['errcode']) {
                throw new Exception('小程序二维码需正式发布才可用');
            }

            throw new Exception($response['errmsg']);
        } catch (\EasyWeChat\Kernel\Exceptions\Exception | Exception $e){
            throw new Exception($e->getMessage());
        }
    }

    /**
     * 制作网页版二维码
     * 说明: 公众号/H5/PC/APP端可用
     *
     * @author windy
     * @param string $qrPath
     * @param string $url
     * @throws Exception
     */
    private static function makeWebQrCode(string $qrPath, string $url)
    {
        try {
            $writer = new PngWriter();
            $qrCode = QrCode::create($url)
                ->setEncoding(new Encoding('UTF-8'))
                ->setErrorCorrectionLevel(new ErrorCorrectionLevelLow())
                ->setSize(200)
                ->setMargin(10)
                ->setRoundBlockSizeMode(new RoundBlockSizeModeMargin())
                ->setForegroundColor(new Color(0, 0, 0))
                ->setBackgroundColor(new Color(255, 255, 255));

            $result = $writer->write($qrCode);
            $result->saveToFile($qrPath);
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }
}