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

/**
 * 生成密文
 *
 * @author windy
 * @param $str (字符串)
 * @param string $salt (盐)
 * @return string
 */
function encrypt_password($str, $salt = 'Miss'): string
{
    return md5(md5($str) . $salt);
}

/**
 * 生成订单号
 *
 * @author windy
 * @param string $prefix
 * @return string
 */
function make_order_no($prefix=''): string
{
    $yCode = array('A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J');
    $orderSn =
        $yCode[intval(date('Y')) - 2022] . ($prefix ?: '') . strtoupper(dechex(date('m'))) . date(
            'd') . substr(time(), -5) . substr(microtime(), 2, 5) . sprintf(
            '%02d', rand(0, 99));

    return $prefix ? $prefix.$orderSn : $orderSn;
}

/**
 * 生成邀请码
 *
 * @author windy
 * @param $table
 * @return string
 */
function make_invite_code($table): string
{
    $letter_all = range('A', 'Z');
    shuffle($letter_all);
    //排除I、O字母
    $letter_array = array_diff($letter_all, ['I', 'O', 'D']);
    //排除1、0
    $num_array = range('2', '9');
    shuffle($num_array);

    $pattern = array_merge($num_array, $letter_array, $num_array);
    shuffle($pattern);
    $pattern = array_values($pattern);

    $code = '';
    for ($i = 0; $i < 6; $i++) {
        $code .= $pattern[mt_rand(0, count($pattern) - 1)];
    }

    $code = strtoupper($code);
    $check = $table->where(['distribution_code'=>$code])->findOrEmpty()->toArray();
    if ($check) {
        return make_invite_code($table);
    }
    return $code;
}

/**
 * 生成随机数字编码
 *
 * @author windy
 * @param $table (表模型)
 * @param string $field (字段)
 * @param int $length (生成长度)
 * @param string $prefix (前缀)
 * @return string
 */
function create_random_code($table, $field='sn', $length=8, $prefix=''): string
{
    $rand_str = '';
    for ($i = 0; $i < $length; $i++) {
        $rand_str .= mt_rand(0, 9);
    }
    $code = $prefix . $rand_str;
    if ($table->where([$field=>$code])->findOrEmpty()->toArray()) {
        return create_random_code($table, $field, $length, $prefix='');
    }
    return $code;
}

/**
 * 保留2为小数的价格(不会四舍五入,直接舍去)
 * @author windy
 * @param float $value
 * @return float
 */
function convert_to_price(float $value): float
{
    return sprintf("%.2f", substr(sprintf("%.3f", (float)$value), 0, -1));
}

/**
 * 正方形图剪为圆形
 *
 * @author windy
 * @param $imgUri
 * @return bool|string
 */
function convert_circle($imgUri)
{
    $w = imagesx($imgUri);
    $h = imagesy($imgUri);
    $w = min($w, $h);
    $h = $w;
    $img = imagecreatetruecolor($w, $h);
    imagesavealpha($img, true);
    $bg = imagecolorallocatealpha($img, 255, 255, 255, 127);
    imagefill($img, 0, 0, $bg);

    $r = $w / 2; //圆半径
    for ($x = 0; $x < $w; $x++) {
        for ($y = 0; $y < $h; $y++) {
            $rgbColor = imagecolorat($imgUri, $x, $y);
            if (((($x - $r) * ($x - $r) + ($y - $r) * ($y - $r)) < ($r * $r))) {
                imagesetpixel($img, $x, $y, $rgbColor);
            }
        }
    }
    unset($imgUri);
    return $img;
}

/**
 * 图片转Base64
 *
 * @author windy
 * @param string $path (文件绝对路径)
 * @return string
 */
function image_to_base64(string $path): string
{
    if($fp = fopen($path,"rb", 0)) {
        $gambit = fread($fp, filesize($path));
        fclose($fp);
        $base64 = chunk_split(base64_encode($gambit));
        return 'data:image/png;base64,' . $base64;
    }
    return '';
}

/**
 * 自适应海报标题文字
 *
 * @author windy
 * @param $size
 * @param $angle
 * @param $fontFamily
 * @param $string
 * @param $width
 * @param $height
 * @param $bgHeight
 * @return string
 */
function auto_adapt($size, $angle, $fontFamily, $string, $width, $height, $bgHeight): string
{
    $letters = [];
    for ($i = 0; $i < mb_strlen($string); $i++) {
        $letters[] = mb_substr($string, $i, 1);
    }

    $content = "";
    foreach ($letters as $letter) {
        $str = $content . " " . $letter;
        $box = imagettfbbox($size, $angle, $fontFamily, $str);
        $total_height = $box[1] + $height;
        if ($bgHeight[1] - $total_height < $size) {
            break;
        }
        if (($box[2] > $width) && ($content !== "")) {
            if ($bgHeight[1] - $total_height < $size * 2) {
                break;
            }
            $content .= "\n";
        }
        $content .= $letter;
    }
    return $content;
}

/**
 * 隐藏部分文字
 *
 * @author windy
 * @param $str
 * @return string
 */
function hideStar($str): string
{
    if (mb_strlen($str) >= 3) {
        return '**' . mb_substr($str, 2);
    }
    if (mb_strlen($str) == 1) {
        return '**' . $str;
    }
    if (mb_strlen($str) == 2) {
        return '**' . mb_substr($str, 1);
    }
    return $str;
}

/**
 * 隐藏手机号中间四位
 *
 * @author windy
 * @param $str
 * @return string
 */
function hidePhone($str): string
{
    return substr_replace($str, '****', 3, 4);
}

/**
 * 删除目录
 *
 * @author windy
 * @param $path
 */
function delete_dir($path)
{
    if(is_dir($path)) {
        $p = scandir($path);
        foreach ($p as $val) {
            if ($val != "." && $val != "..") {
                if (is_dir($path . $val)) {
                    delete_dir($path . $val . '/');
                    @rmdir($path . $val . '/');
                } else {
                    unlink($path . $val);
                }
            }
        }
        @rmdir($path);
    }
}

/**
 * 判断文件夹是否存在,不存在则创建
 *
 * @author windy
 * @param $dir
 * @param int $mode
 * @return bool
 */
function create_folder($dir, $mode = 0777): bool
{
    if (is_dir($dir) || @mkdir($dir, $mode)) return TRUE;
    if (!create_folder(dirname($dir), $mode)) return FALSE;
    return @mkdir($dir, $mode);
}

/**
 * gcj02坐标转bd09坐标
 *
 * @author windy
 * @param $lat
 * @param $lng
 * @return array
 */
function gcj02_to_bd09($lat, $lng): array
{
    $x_pi = 3.14159265358979324 * 3000.0 / 180.0;
    $x = $lng;
    $y = $lat;
    $z = sqrt($x * $x + $y * $y) + 0.00002 * sin($y * $x_pi);
    $theta = atan2($y, $x) + 0.000003 * cos($x * $x_pi);
    $lng = $z * cos($theta) + 0.0065;
    $lat = $z * sin($theta) + 0.006;
    return array($lng, $lat);
}

/**
 * db09坐标转gcj02坐标
 *
 * @author windy
 * @param $lng
 * @param $lat
 * @return array
 */
function db09_to_gcj02($lng, $lat): array
{
    $x_pi = 3.14159265358979324 * 3000.0 / 180.0;
    $x = $lng - 0.0065;
    $y = $lat - 0.006;
    $z = sqrt($x * $x + $y * $y) - 0.00002 * sin($y * $x_pi);
    $theta = atan2($y, $x) - 0.000003 * cos($x * $x_pi);
    $lng = $z * cos($theta);
    $lat = $z * sin($theta);
    return array($lng, $lat);
}

/**
 * wgs84坐标转bd09坐标
 *
 * @author windy
 * @param $wgsLng
 * @param $wgsLat
 * @return array
 */
function wgs84_to_bd09($wgsLng, $wgsLat): array
{
    $lng = $wgsLng - 105.0;
    $lat = $wgsLat - 35.0;

    //第一次转换
    $dLat = -100.0 + 2.0 * $lng + 3.0 * $lat + 0.2 * $lat * $lat + 0.1 * $lng * $lat + 0.2 * sqrt(abs($lng));
    $dLat += (20.0 * sin(6.0 * $lng * 3.1415926535897932384626) + 20.0 * sin(2.0 * $lng * 3.1415926535897932384626)) * 2.0 / 3.0;
    $dLat += (20.0 * sin($lat * 3.1415926535897932384626) + 40.0 * sin($lat / 3.0 * 3.1415926535897932384626)) * 2.0 / 3.0;
    $dLat += (160.0 * sin($lat / 12.0 * 3.1415926535897932384626) + 320 * sin($lat * 3.1415926535897932384626 / 30.0)) * 2.0 / 3.0;

    $dLng = 300.0 + $lng + 2.0 * $lat + 0.1 * $lng * $lng + 0.1 * $lng * $lat + 0.1 * sqrt(abs($lng));
    $dLng += (20.0 * sin($lng * 3.1415926535897932384626) + 40.0 * sin($lng / 3.0 * 3.1415926535897932384626)) * 2.0 / 3.0;
    $dLng += (150.0 * sin($lng / 12.0 *3.1415926535897932384626) + 300.0 * sin($lng / 30.0 * 3.1415926535897932384626)) * 2.0 / 3.0;
    $dLng += (20.0 * sin(6.0 * $lng * 3.1415926535897932384626) + 20.0 * sin(2.0 * $lng * 3.1415926535897932384626)) * 2.0 / 3.0;

    $radLat = $wgsLat / 180.0 * 3.1415926535897932384626;
    $magic = sin($radLat);
    $magic = 1 - 0.00669342162296594323 * $magic * $magic;
    $sqrtMagic = sqrt($magic);
    $dLat = ($dLat * 180.0) / ((6378245.0 * (1 - 0.00669342162296594323)) / ($magic * $sqrtMagic) * 3.1415926535897932384626);
    $dLng = ($dLng * 180.0) / (6378245.0 / $sqrtMagic * cos($radLat) * 3.1415926535897932384626);
    $mgLat = $wgsLat + $dLat;
    $mgLng = $wgsLng + $dLng;

    //第二次转换
    $z = sqrt($mgLng * $mgLng + $mgLat * $mgLat) + 0.00002 * sin($mgLat * (3.14159265358979324 * 3000.0 / 180.0));
    $theta = atan2($mgLat, $mgLng) + 0.000003 * cos($mgLng * (3.14159265358979324 * 3000.0 / 180.0));
    $bd_lng = $z * cos($theta) + 0.0065;
    $bdLat = $z * sin($theta) + 0.006;
    return [$bd_lng, $bdLat];
}


/**
 * 下载文件
 *
 * @author windy
 * @param String $fileUrl (远程文件路径)
 * @param String $saveTo (保存地址)
 * @param String $ext (保存后的扩展名)
 * @return string 保存后的文件路径
 */
function download_file(String $fileUrl, String $saveTo, String $ext): string
{
    if (!$fileUrl) {
        return '';
    }

    if (!file_exists($saveTo)) {
        mkdir($saveTo, 0775, true);
    }

    $content = file_get_contents($fileUrl);
    file_put_contents($saveTo . md5($fileUrl) . '.' . $ext, $content);
    return $saveTo . md5($fileUrl) . '.' . $ext;
}

/**
 * GET请求
 *
 * @author windy
 * @param string $url
 * @return bool|string
 */
function curl_get(string $url) {
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_HEADER, false);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    $data = curl_exec($curl);
    curl_close($curl);
    return $data;
}

/**
 * POST请求
 *
 * @author windy
 * @param string $url
 * @param array $data
 * @return bool|string
 */
function curl_post(string $url, array $data=[])
{
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
    $output = curl_exec($ch);
    curl_close($ch);
    return $output;
}