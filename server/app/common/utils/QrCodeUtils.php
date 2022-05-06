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

namespace app\common\utils;


use Endroid\QrCode\Color\Color;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\ErrorCorrectionLevel\ErrorCorrectionLevelLow;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\RoundBlockSizeMode\RoundBlockSizeModeMargin;
use Endroid\QrCode\Writer\PngWriter;

class QrCodeUtils
{
    /**
     * 获取支付二维码
     *
     * @author windy
     * @param $url
     * @return string
     * @throws @\Exception
     */
    public static function getNativeCode($url): string
    {
        $saveDir = 'uploads/qrcode/';
        $codeSrc = md5(make_order_no().mt_rand(10000, 99999)).'.png';
        $codeUrl = public_path() . '/' . $saveDir . $codeSrc;
        !file_exists($saveDir) && mkdir($saveDir, 0777, true);

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
        $result->saveToFile($codeUrl);

        // 生成base64
        $base64 = '';
        if ($fp = fopen($codeUrl, "rb", 0)) {
            $content = fread($fp, filesize($codeUrl));
            fclose($fp);
            $base64 = chunk_split(base64_encode($content));
            $base64 = 'data:image/png;base64,' . $base64;
        }

        // 删除生成文件
        if (strstr($codeUrl, $saveDir)) {
            unlink($codeUrl);
        }

        // 返回结果
        return $base64;
    }
}