<?php
include_once 'proof.php';
include_once 'mysql.php';
include_once 'util.php';

// 常量定义
define('INSTALL_ROOT', __DIR__);
define('PUBLIC_ROOT', dirname(__DIR__));
define('APP_ROOT', dirname(dirname(__DIR__)));

// 参数实例化
$successTables = [];
$errMsg = null;
$step   = $_GET['step'] ?? 1;
$ajax   = $_POST['ajax'] ?? 0;
$proof  = new Proof();
$util   = new Util();

// 安装验证
if ($util->loadLock() && in_array($step, [1, 2, 3, 4])) {
    die('可能已经安装过本系统了，请删除根目录下面的install.lock文件再尝试！');
}

// 流程校验
if (!in_array($step, [1, 2, 3, 4]))  {
    die('你想干嘛？能不能好按流程操作？');
}

// 数据库校验
if ($step == 4) {
    $post = [
        'host'       => $_POST['host'] ?? '',
        'port'       => $_POST['port'] ?? '',
        'db'         => $_POST['db'] ?? '',
        'username'   => $_POST['username'] ?? '',
        'password'   => $_POST['password'] ?? '',
        'prefix'     => $_POST['prefix'] ?? '',
        'clear'      => empty($_POST['clear']) ? false : true,
        'admin_user' => $_POST['admin_user'] ?? '',
        'admin_pwd'  => $_POST['admin_pwd'] ?? '',
        'admin_pwd_confirm'  => $_POST['admin_pwd_confirm'] ?? '',
    ];

    // 连接数据库
    $mysql = new Mysql($post);

    // 异步验证
    if ($ajax) {
        // 参数验证
        $errMsg = $proof->checkParams($post);
        if ($errMsg) {
            exit(json_encode(['code'=>1, 'msg'=>$errMsg]));
        }
        // 数据库验证
        $mysqlErr = $mysql->checkDB();
        if ($mysqlErr !== true) {
            exit(json_encode(['code'=>1, 'msg'=>$mysqlErr]));
        }
        // 验证通过返回
        exit(json_encode(['code'=>0, 'msg'=>'success']));
    }

    // 同步验证
    $errMsg = $proof->checkParams($post);
    if ($errMsg) {
        $step = 3;
    } else {
        $mysqlErr = $mysql->checkDB();
        if ($mysqlErr !== true) {
            $errMsg = $mysqlErr;
            $step = 3;
        } else {
            // 写入到数据表
            $successTables = $mysql->install();
            if (is_string($successTables)) {
                die($successTables);
            }
            // 生成配置文件
            $util->makeEnv($post);
            // 生成锁定文件
            $util->makeLock();
        }
    }
}

// 输出模板
$nextStep = $step + 1;
include_once __DIR__ . "/template/main.php";