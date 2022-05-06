<?php
// +----------------------------------------------------------------------
// | Route路由配置
// +----------------------------------------------------------------------

use think\facade\Route;

// 主页路由
Route::get('/', function () {
    return '<h1>？？</h1>';
});

// 手机路由
Route::rule('mobile/:any', function () {
    return view(app()->getRootPath().'public/mobile/index.html');
})->pattern(['any' => '\w+']);