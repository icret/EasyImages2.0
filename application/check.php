<?php
require_once __DIR__ . '/function.php';

global $config;

// 扩展检测
$expand = array('fileinfo', 'iconv', 'gd', 'mbstring', 'openssl',);
foreach ($expand as $val) {
    if (!extension_loaded($val)) {
        echo '
        <script>
        new $.zui.Messager("扩展：' . $val . '- 未安装,可能导致图片上传失败！请尽快修复。", {type: "black" // 定义颜色主题 
        }).show();
        </script>
    ';
    }
}
// 检测是否更改默认域名
if (strstr('localhost', $_SERVER['HTTP_HOST'])) {
    echo '
    <script>
    new $.zui.Messager("请修改默认域名，可能会导致图片访问异常！", {type: "black" // 定义颜色主题 
    }).show();
    </script>
    ';
}
// 检测是否修改默认密码
if ($config['password'] === 'admin@123') {
    echo '
    <script>
    new $.zui.Messager("请修改默认密码，否则会有泄露风险！", {type: "warning" // 定义颜色主题 
    }).show();
    </script>
    ';
}
// 上部内容
if (!is_file(APP_ROOT . '/config/EasyIamge.lock')) {
    echo '
    <div class="modal fade" id="myModal-1">
        <div class="modal-dialog">
            <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">
                <i class="icon icon-heart">	</i><a href="https://www.545141.com/846.html" target="_blank">简单图床-EasyImage2.0</a> 安装环境检测</h4>
            </div>
            <div class="modal-body">
                <h4>说明：</h4>
                <h5>1. 建议使用<font color="red">PHP7.0</font>及以上版本；</h5>
                <h5>2. 上传失败大部分是由于<font color="red">upload_max_filesize、post_max_size、文件权限</font>设置不正确；</h5>
                <h5>3. 本程序用到<font color="red">Fileinfo、GD、openssl</font>扩展,如果缺失会导致无法访问管理面板以及上传/删除图片。</h5>
                <h5>4. 上传后必须修改<font color="red">当前网站域名、当前图片域名，登录管理密码！</font></h5>
                <hr />
                <h4>EasyImage2.0 基础检测：</h4>
                当前PHP版本：<font style="color:green">' . phpversion() . '</font><br/>';

    echo '<font color="green">upload_max_filesize</font> - PHP上传最大值：' . ini_get('upload_max_filesize');
    echo '<br /><font color="green">post_max_size</font> - POST上传最大值：' . ini_get('post_max_size') . '<br />';
    // 扩展检测
    $expand = array('fileinfo', 'gd', 'openssl',);
    foreach ($expand as $val) {
        if (extension_loaded($val)) {
            echo '
                <font color="green">' . $val . "</font> - 已安装
                <br />";
        } else {
            echo "
                <script language='javascript'>alert('$val - 未安装')</script>";
            echo '
                <font color="red">' . $val . " - 未安装</font>
                <br />";
        }
    }
    // 文件权限检测
    $quanxian = substr(base_convert(fileperms("file.php"), 10, 8), 3);
    if (IS_WIN) {
        echo '
            <font style="color:green">file.php 文件可执行</font><br/>
            <font style="color:green">/i 目录可读写</font><br/>
            ';
    }
    if (!IS_WIN) {
        if ($quanxian !== '755' and !is_writable(APP_ROOT . '/i/')) {
            echo '
            <p style="color:red">file.php 文件不可执行</font>><br/>
            <p style="color:red">/i 目录可读写</font>><br/>
            ';
        } else {
            echo '
            <font style="color:green">file.php 文件可执行</font><br/>
            <font style="color:green">/i 目录可读写</font><br/>
            ';
        }
    }
    echo '</div>
            <div class="modal-footer">
            <p style="font-weight: bold">安装环境检测弹窗只会第一次打开时展示，会在config目录下自动生成EasyIamge.lock，如需再次展示或更换空间请自行删除EasyIamge.lock！刷新后此提示框消失。</p>
            </div>
        </div>
    </div>
</div>
        <script>$("#myModal-1").modal({
            keyboard: true,
            moveable: true,
            backdrop: "static",//点击空白处不关闭对话框
            show: true
        })
        alert("初次打开会检测环境配置，请仔细看!!");
        </script>
    ';
    file_put_contents(APP_ROOT . '/config/EasyIamge.lock', '安装环境检测锁定文件，如需再次展示请删除此文件！', FILE_APPEND | LOCK_EX);
    clearstatcache();
}
