<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>WaitShop安装</title>
    <link rel="stylesheet" href="./static/layui/css/layui.css">
    <link rel="stylesheet" href="./static/app.css">
    <script src="./static/layui/layui.js"></script>
    <script src="./static/app.js"></script>
    <?php if ($errMsg): ?>
        <script>
            alert('<?php echo $errMsg ?>', function () {
                goBack();
            });
        </script>
    <?php endif; ?>
</head>
<body>
    <div class="install">
        <div class="header"><h1>WaitShop安装向导</h1></div>
        <ul class="navigation">
            <li <?php if ($step == '1') { ?>class="active"<?php } ?>>许可协议</li>
            <li <?php if ($step == '2') { ?>class="active"<?php } ?>>环境检测</li>
            <li <?php if ($step == '3') { ?>class="active"<?php } ?>>参数配置</li>
            <li <?php if ($step == '4' || $step == '5') { ?>class="active"<?php } ?>>完成安装</li>
        </ul>
        <form class="main layui-form" name="form" method="post">
            <?php if ($step == '1') { ?>
                <!-- 【1】许可协议 -->
                <div class="step-1">
                    <div class="agreement">
                        <h1 style="text-align: center;">软件许可协议</h1>
                        <p class="mt16">感谢你信任并选择WaitShop开源商城系统，WaitShop团队精心打造的开源电商系统，并且已取得软件著作权（www.waitshop.cn）代码全开源，无后门，二开无忧好上手。</p>
                        <p class="mt6">为了正确并合法的使用本软件，请你在使用前务必阅读清楚下面的协议条款：</p>
                        <h3 class="mt16">一、本授权协议适用且仅适用于WaitShop系列开源软件产品(以下简称WaitShop)任何版本，WaitShop作者拥有本授权协议的最终解释权。</h3>
                        <h3 class="mt16">二、协议许可的权利</h3>
                        <p class="mt6">1．不能私自去除官方版权，商业使用需要取得商业授权方可使用，可以去除官方版权，否则会被视为盗版行为并承担相应法律责任。</p>
                        <p class="mt6">2．请尊重WaitShop开源团队劳动成果，严禁使用本系统转手、转卖、倒卖或二次开发后转手、转卖、倒卖等商业行为。</p>
                        <p class="mt6">3．任何企业和个人不允许对WaitShop程序代码以任何形式任何目的再发布。</p>
                        <p class="mt6">4．你可以在协议规定的约束和限制范围内修改WaitShop系列开源软件产品或界面风格以适应你的网站要求。</p>
                        <p class="mt6">5．你拥有使用本软件构建的系统全部内容所有权，并独立承担与这些内容的相关法律义务。</p>
                        <p class="mt6">6．获得商业授权之后，你可以将本软件应用于商业用途，同时依据所购买的授权类型中确定的技术支持内容。</p>
                        <h3 class="mt6">三、协议规定的约束和限制</h3>
                        <p class="mt6">1．不能私自去除官方版权，商业使用需要取得商业授权方可使用，可以去除官方版权，否则会被视为盗版行为并承担相应法律责任。</p>
                        <p class="mt6">2．未经官方许可，不得对本软件或与之关联的商业授权进行出租、出售、抵押或发放子许可证。</p>
                        <p class="mt6">3．未经官方许可，禁止在WaitShop开源商城的整体或任何部分基础上以发展任何派生版本、修改版本或第三方版本用于重新分发。</p>
                        <p class="mt6">4．如果你未能遵守本协议的条款，你的授权将被终止，所被许可的权利将被收回，并承担相应法律责任。</p>
                        <h3 class="mt6">四、有限担保和免责声明</h3>
                        <p class="mt6">1．本软件及所附带的文件是作为不提供任何明确的或隐含的赔偿或担保的形式提供的。</p>
                        <p class="mt6">2．用户出于自愿而使用本软件，你必须了解使用本软件的风险，在尚未购买产品技术服务之前，我们不承诺对免费用户提供任何形式的技术支持、使用担保，也不承担任何因使用本软件而产生问题的相关责任。</p>
                        <p class="mt6">3．电子文本形式的授权协议如同双方书面签署的协议一样，具有完全的和等同的法律效力。你一旦开始确认本协议并安装本软件，即被视为完全理解并接受本协议的各项条款，在享有上述条款授予的权力的同时，受到相关的约束和限制。协议许可范围以外的行为，将直接违反本授权协议并构成侵权，我们有权随时终止授权，责令停止损害，并保留追究相关责任的权力。</p>
                        <p class="mt6">4．如果本软件带有其它软件的整合API示范例子包，这些文件版权不属于本软件官方，并且这些文件是没经过授权发布的，请参考相关软件的使用许可合法的使用。</p>
                        <p class="mt6">5．本程本身不是百分百的完善，如因使用过程中发现漏洞导致的损失，我们概不承担任何责任，请自行测试好再进行上线。</p>
                        <br><br>
                        <p class="mt6">gitee:   https://gitee.com/wafts/WaitShop</p>
                        <p class="mt6">github:  https://github.com/topwait/waitshop</p>
                        <p class="mt6">官方网站: https://www.waitshop.cn</p>
                        <p class="mt6">-----------------------------------------------------</p>
                        <p class="mt6">邮箱: 2474369941@qq.com</p>
                        <p class="mt6">作者: WaitShop开源团队</p>
                    </div>
                    <div class="footer">
                        <button class="layui-btn" onclick="goNext(<?php echo $nextStep ?>)">我已阅读并同意</button>
                    </div>
                </div>
            <?php } ?>

            <?php if ($step == '2') { ?>
                <!-- 【2】环境监测 -->
                <div class="step-2">
                     <div class="environment">
                        <table class="layui-table" lay-skin="line">
                            <colgroup>
                                <col width="210">
                                <col width="210">
                                <col>
                            </colgroup>
                            <thead>
                                 <tr>
                                    <th>环境检测</th>
                                    <th>推荐配置</th>
                                    <th>当前状态</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>操作系统</td>
                                    <td>WINNT</td>
                                    <td><?php echo PHP_OS; ?></td>
                                </tr>
                                <tr>
                                    <td>服务器环境</td>
                                    <td>nginx/1.15.11</td>
                                    <td><?php echo $_SERVER['SERVER_SOFTWARE']; ?></td>
                                </tr>
                                <tr>
                                    <td>磁盘空间</td>
                                    <td>>10G</td>
                                    <td><?php echo $proof->freeDiskSpace() ?></td>
                                </tr>
                                <tr>
                                    <td>上传限制</td>
                                    <td>>2M</td>
                                    <?php if (ini_get('file_uploads')): ?>
                                        <td><?php echo ini_get('upload_max_filesize'); ?></td>
                                    <?php else: ?>
                                        <td>禁止上传</td>
                                    <?php endif; ?>
                                </tr>
                                <tr>
                                    <td>PHP版本</td>
                                    <td>>7.2.0</td>
                                    <td>
                                        <?php echo $proof->successOrFail($proof->checkPHP()) ?>
                                        <?php echo PHP_VERSION; ?>
                                    </td>
                                </tr>
                                <tr>
                                    <td>GD库</td>
                                    <td>必须扩展</td>
                                    <td><?php echo $proof->successOrFail($proof->checkGd2()) ?></td>
                                </tr>
                                <tr>
                                    <td>DOM</td>
                                    <td>必须扩展</td>
                                    <td><?php echo $proof->successOrFail($proof->checkDom()) ?></td>
                                </tr>
                                <tr>
                                    <td>pdo</td>
                                    <td>必须扩展</td>
                                    <td><?php echo $proof->successOrFail($proof->checkPDO()) ?></td>
                                </tr>
                                <tr>
                                    <td>pdo_mysql</td>
                                    <td>必须扩展</td>
                                    <td><?php echo $proof->successOrFail($proof->checkPDOMySQL()) ?></td>
                                </tr>
                                <tr>
                                    <td>curl_init</td>
                                    <td>必须扩展</td>
                                    <td><?php echo $proof->successOrFail($proof->checkCurl()) ?></td>
                                </tr>
                                <tr>
                                    <td>openssl</td>
                                    <td>必须扩展</td>
                                    <td><?php echo $proof->successOrFail($proof->checkOpenssl()) ?></td>
                                </tr>
                                <tr>
                                    <td>fileinfo</td>
                                    <td>必须扩展</td>
                                    <td><?php echo $proof->successOrFail($proof->checkFileInfo()) ?></td>
                                </tr>
                            </tbody>
                        </table>

                        <table class="layui-table" lay-skin="line">
                            <colgroup>
                                <col width="210">
                                <col width="210">
                                <col width="210">
                                <col>
                            </colgroup>
                            <thead>
                                 <tr>
                                    <th>目录权限检查</th>
                                    <th>推荐配置</th>
                                    <th>写入</th>
                                    <th>读取</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>/</td>
                                    <td>读写</td>
                                    <td><?php echo $proof->successOrFail($proof->checkDirWrite()) ?></td>
                                    <td><?php echo $proof->successOrFail($proof->checkDirRead()) ?></td>
                                </tr>
                                <tr>
                                    <td>/runtime</td>
                                    <td>读写</td>
                                    <td><?php echo $proof->successOrFail($proof->checkDirWrite('runtime')) ?></td>
                                    <td><?php echo $proof->successOrFail($proof->checkDirRead('runtime')) ?></td>
                                </tr>

                                <tr>
                                    <td>/public/uploads</td>
                                    <td>读写</td>
                                    <td><?php echo $proof->successOrFail($proof->checkDirWrite('public/uploads')) ?></td>
                                    <td><?php echo $proof->successOrFail($proof->checkDirRead('public/uploads')) ?></td>
                                </tr>
                            </tbody>
                        </table>
                        <div class="footer">
                            <?php if ($proof->checkAllowNext()): ?>
                                <button type="button" class="layui-btn layui-btn-primary" onclick="goNext(<?php echo $nextStep-1 ?>)">返回</button>
                                <button type="button" class="layui-btn" onclick="goNext(<?php echo $nextStep ?>)">下一步</button>
                            <?php else: ?>
                                <button type="button" class="layui-btn layui-btn-primary" onclick="goLoad()">重新检测</button>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php } ?>

            <?php if ($step == '3') { ?>
                <!-- 【3】参数配置 -->
                <div class="step-3">
                    <div class="section">
                        <div class="section-header">数据库设定</div>
                        <div class="section-content">
                            <div class="layui-form-item">
                                <label class="layui-form-label">数据库主机：</label>
                                <div class="layui-input-inline">
                                    <input type="text" name="host" value="localhost" lay-verType="tips" lay-verify="required" autocomplete="off" class="layui-input">
                                </div>
                            </div>
                            <div class="layui-form-item">
                                <label class="layui-form-label">数据库用户：</label>
                                <div class="layui-input-inline">
                                    <input type="text" name="username" value="root" lay-verType="tips" lay-verify="required" autocomplete="off" class="layui-input">
                                </div>
                            </div>
                            <div class="layui-form-item">
                                <label class="layui-form-label">数据库密码：</label>
                                <div class="layui-input-inline">
                                    <input type="text" name="password" lay-verType="tips" lay-verify="required" autocomplete="off" class="layui-input">
                                </div>
                            </div>
                            <div class="layui-form-item">
                                <label class="layui-form-label">数据库端口：</label>
                                <div class="layui-input-inline">
                                    <input type="text" name="port" value="3306" lay-verType="tips" lay-verify="required|number" autocomplete="off" class="layui-input">
                                </div>
                            </div>
                            <div class="layui-form-item">
                                <label class="layui-form-label">数据库名称：</label>
                                <div class="layui-input-inline">
                                    <input type="text" name="db" value="waitshop" lay-verType="tips" lay-verify="required" autocomplete="off" class="layui-input">
                                </div>
                            </div>
                            <div class="layui-form-item">
                                <label class="layui-form-label">数据表前缀：</label>
                                <div class="layui-input-inline">
                                    <input type="text" name="prefix" value="wait_" lay-verType="tips" lay-verify="required" autocomplete="off" class="layui-input">
                                </div>
                            </div>
                            <div class="layui-form-item">
                                <label class="layui-form-label">选项：</label>
                                <div class="layui-input-inline">
                                    <input type="checkbox"  name="clear" title="清空已有数据库" lay-skin="primary">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="section">
                        <div class="section-header">管理员初始密码</div>
                        <div class="section-content">
                            <div class="layui-form-item">
                                <label class="layui-form-label">用户名称：</label>
                                <div class="layui-input-inline">
                                    <input type="text" name="admin_user" lay-verType="tips" lay-verify="required" autocomplete="off" class="layui-input">
                                </div>
                            </div>
                            <div class="layui-form-item">
                                <label class="layui-form-label">登录密码：</label>
                                <div class="layui-input-inline">
                                    <input type="password" name="admin_pwd" lay-verType="tips" lay-verify="required" autocomplete="off" class="layui-input">
                                </div>
                            </div>
                            <div class="layui-form-item">
                                <label class="layui-form-label">确认密码：</label>
                                <div class="layui-input-inline">
                                    <input type="password" name="admin_pwd_confirm" lay-verType="tips" lay-verify="required" autocomplete="off" class="layui-input">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="footer">
                        <a class="layui-btn layui-btn-primary" onclick="goNext(<?php echo $nextStep-1 ?>)">返回</a>
                        <a class="layui-btn" lay-submit lay-filter="addForm" onclick="goNext(<?php echo $nextStep ?>)">安装</a>
                    </div>
                </div>
            <?php } ?>

            <?php if ($step == '4') { ?>
                <!-- 【4】安装程序 -->
                <div class="step-4">
                    <div class="section">
                        <div class="section-header">正在安装中...</div>
                        <div class="section-record" id="sqlBox"></div>
                    </div>
                    <div class="footer">
                        <button type="button" class="layui-btn" disabled="disabled" style="background-color: #eee;">
                            <i class="layui-icon layui-icon-loading layui-anim layui-anim-rotate layui-anim-loop"></i>
                            正在安装
                        </button>
                    </div>
                </div>
            <?php } ?>

            <?php if ($step == '5') { ?>
                <!-- 【5】安装结果 -->
                <div class="step-5">
                    <div class="section" style="margin-bottom: 0;">
                        <div class="section-header">安装结果</div>
                        <div class="section-fixed">
                            <div class="success-install">
                                <img class="icon" src="./static/icon/success.png" alt="icon">
                                <h1>安装WaitShop</h1>
                                <div class="tips">安装成功!</div>
                                <div class="warning">
                                    温馨提示：为了您的站点安全，安装完成后请删除网站根目录下的“install”文件夹，
                                    防止重复安装导致数据丢失，最后的最后祝您生活愉快、生意兴隆、财源滚滚！
                                </div>
                                <div class="footer">
                                    <a href="/admin" class="layui-btn">进入管理后台</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php } ?>
        </form>
    </div>
    <?php if (count($successTables) > 0): ?>
        <script>
            let successTables = eval(<?=json_encode($successTables) ?>);
        </script>
    <?php endif; ?>
</body>
</html>