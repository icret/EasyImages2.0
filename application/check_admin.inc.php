<?php
// 扩展检测
$expand = array('fileinfo', 'iconv', 'gd', 'mbstring', 'openssl',);
foreach ($expand as $val) {
    if (!extension_loaded($val)) {
        echo '
        <script>
        new $.zui.Messager("扩展:' . $val . '- 未安装,可能导致图片上传失败! 请尽快修复。",{
			type: "black", // 定义颜色主题
			icon: "exclamation-sign" // 定义消息图标
        }).show();
        </script>
    ';
    }
}

// 检测是否修改默认密码
if ($config['password'] === 'e6e061838856bf47e1de730719fb2609') {
    echo '
    <script>
    new $.zui.Messager("请修改默认密码,否则会有泄露风险! ",{
        type: "warning", // 定义颜色主题 
        time:6000
    }).show();
    </script>
    ';
}

/*
// 检测是否更改默认域名
if (strstr('localhost|127.0.0.1|192.168.', $_SERVER['HTTP_HOST'])) {
    echo '
    <script>
    new $.zui.Messager("请修改默认域名,可能会导致网站访问异常! ",{
        type: "black" // 定义颜色主题 
    }).show();
    </script>
    ';
}
*/

// 检测是否局域网访问
if (is_local($config['domain'])) {
    echo '
    <script>
    new $.zui.Messager("当前使用局域网,可能会导致外网访问异常!",{
        type: "black" // 定义颜色主题 
    }).show();
    </script>
    ';
}

// 检测是否存在.user.ini
if (file_exists(APP_ROOT . '/.user.ini')) {
    echo '
    <script>
        new $.zui.Messager("请关闭防跨目录读写或删除.user.ini文件",{
            type: "danger", // 定义颜色主题 
            time:7000
        }).show();
    </script>
    ';
}

// 检查当前版本与GitHub版本
if (getVersion() !== get_current_verson()) {
    echo '
    <script>
    new $.zui.Messager("当前版本与GitHub不一致,请检查当前是否最新版本!",{
        type: "danger", // 定义颜色主题 
        time:9000
    }).show();
    </script>
    ';
}

// 检测是否开启登录上传
if ($config['mustLogin']) {
    echo '
    <script>
    $.zui.browser.tip("请注意: 当前已开启登录上传,非登录用户不可上传图片!");
    </script>
    ';
}

// 检测水印图片是否存在
if (!is_file(APP_ROOT . $config['waterImg'])) {
    echo '
    <script>
    new $.zui.Messager("水印图片不存在,请检测路径或者文件是否存在!",{
        type: "danger", // 定义颜色主题 
        time:10000
    }).show();
    </script>
    ';
}

// 检测水印字体是否存在
if (!is_file(APP_ROOT . $config['textFont'])) {
    echo '
    <script>
    new $.zui.Messager("水印字体不存在,请检测路径或者文件是否存在!",{
        type: "danger", // 定义颜色主题 
        time:10000
    }).show();
    </script>
    ';
}

// 检测监黄接口是否可以访问
if ($config['checkImg'] !== 0) {

    if ($config['checkImg'] == 1) {

        if (!@IP_URL_Ping('api.moderatecontent.com', 80, 1)) {
            echo '
            <script>
                new $.zui.Messager("moderatecontent 鉴黄接口无法ping通! ",{
                    type: "warning" // 定义颜色主题 
                }).show();
            </script>
            ';
        }
    }

    if ($config['checkImg'] == 2) {

        $ip = parse_url($config['nsfwjs_url'])['host'];
        $port = parse_url($config['nsfwjs_url'])['port'];

        if (filter_var($ip, FILTER_VALIDATE_IP)) {
            if (!@IP_URL_Ping($ip, $port, 1)) {
                echo '
                <script>
                    new $.zui.Messager("' . $ip . $port . ' 鉴黄接口无法ping通! ",{
                        type: "warning" // 定义颜色主题 
                    }).show();
                </script>
                ';
            }
        } else {
            if (!@IP_URL_Ping($ip, 80, 1)) {
                echo '
                <script>
                    new $.zui.Messager("' . $ip . ' 鉴黄接口无法ping通! ",{
                        type: "warning" // 定义颜色主题 
                    }).show();
                </script>
                ';
            }
        }
    }
}
