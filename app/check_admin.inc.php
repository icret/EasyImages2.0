<?php
/*
// 检查当前PHP版本是否大于7.0
if (PHP_VERSION < 7) {
    echo ' 
    new $.zui.Messager("当前PHP版本<7.0, 部分功能受限!",{
        type: "primary", // 定义颜色主题 
        time:3000
    }).show();
    ';
}
*/

// 扩展检测
$expand = array('fileinfo', 'iconv', 'gd', 'mbstring', 'openssl',);
foreach ($expand as $val) {
    if (!extension_loaded($val)) {
        echo '
        new $.zui.Messager("扩展:' . $val . '- 未安装,可能导致图片上传失败! 请尽快修复。",{
			type: "black", // 定义颜色主题
			icon: "exclamation-sign", // 定义消息图标
            time:3500
        }).show();
    ';
    }
}

// 检测是否修改默认密码
if ($config['password'] === '7676aaafb027c825bd9abab78b234070e702752f625b752e55e55b48e607e358') {
    echo '
    new $.zui.Messager("请修改默认密码,否则会有泄露风险! ",{
        type: "warning", // 定义颜色主题 
        time:4000
    }).show();
    ';
}

// 检测是否局域网访问
if (is_local($config['domain']) || is_local($config['imgurl'])) {
    echo '
    new $.zui.Messager("当前使用局域网,可能会导致外网访问异常!",{
        type: "black", // 定义颜色主题 
        time:4500
    }).show();
    ';
}

// 检测是否存在.user.ini
if (file_exists(APP_ROOT . '/.user.ini')) {
    echo '
        new $.zui.Messager("请关闭防跨目录读写或删除.user.ini文件",{
            type: "danger", // 定义颜色主题 
            time:5000
        }).show();
';
}

// 检测是否存在 IP数据库文件 ip2region.xdb
if (!file_exists(__DIR__ . '/ip2region/ip2region.xdb')) {
    echo '
        new $.zui.Messager("IP 数据库不存在, 请在系统信息中查看 Ip2region",{
            type: "danger", // 定义颜色主题 
            time:5500
        }).show();
    ';
}

// 检查当前版本与GitHub版本
if (getVersion() !== APP_VERSION) {
    echo '
    new $.zui.Messager("当前版本与GitHub不一致,请检查当前是否最新版本!",{
        type: "danger", // 定义颜色主题 
        time:6000
    }).show();
';
}

// 检测是否开启登录上传
if ($config['mustLogin']) {
    echo '
    $.zui.browser.tip("请注意: 当前已开启登录上传,游客不能上传图片!");
    ';
}

// 检测水印图片是否存在
if (!is_file(APP_ROOT . $config['waterImg'])) {
    echo '
    new $.zui.Messager("水印图片不存在,请检测路径或者文件是否存在!",{
        type: "danger", // 定义颜色主题 
        time:6500
    }).show();
    ';
}

// 检测水印字体是否存在
if (!is_file(APP_ROOT . $config['textFont'])) {
    echo '
    new $.zui.Messager("水印字体不存在,请检测路径或者文件是否存在!",{
        type: "danger", // 定义颜色主题 
        time:6500
    }).show();
    ';
}

// 检测监黄接口是否可以访问
if ($config['checkImg'] !== 0) {

    if ($config['checkImg'] == 1) {

        if (!@IP_URL_Ping('api.moderatecontent.com', 80, 1)) {
            echo '
                new $.zui.Messager("moderatecontent 鉴黄接口无法ping通! ",{
                    type: "warning", // 定义颜色主题 
                    time:7000
                }).show();
            ';
        }
    }

    if ($config['checkImg'] == 2) {

        $ip = parse_url($config['nsfwjs_url'])['host'];
        $port = parse_url($config['nsfwjs_url'])['port'];

        if (filter_var($ip, FILTER_VALIDATE_IP)) {
            if (!@IP_URL_Ping($ip, $port, 1)) {
                echo '
                    new $.zui.Messager("' . $ip . $port . ' 鉴黄接口无法ping通! ",{
                        type: "warning", // 定义颜色主题 
                        time:7000
                    }).show();
                ';
            }
        } else {
            if (!@IP_URL_Ping($ip, 80, 1)) {
                echo '
                    new $.zui.Messager("' . $ip . ' 鉴黄接口无法ping通! ",{
                        type: "warning", // 定义颜色主题 
                        time:7000
                    }).show();
                ';
            }
        }
    }
}

if (!function_exists('fastcgi_finish_request')) {
    echo '
        new $.zui.Messager("开启 fastcgi_finish_request 处理数据会更快喔!",{
            type: "primary", // 定义颜色主题 
            time:7000
        }).show();
    ';
}
