<?php
require __DIR__.'/../config.php';

// 判断GIF图片是否为动态
function isAnimatedGif($filename) {
    $fp = fopen($filename, 'rb');
    $filecontent = fread($fp, filesize($filename));
    fclose($fp);
    return strpos($filecontent, chr(0x21) . chr(0xff) . chr(0x0b) . 'NETSCAPE2.0') === FALSE ? 0 : 1;
}

// 校验登录
function checkLogin() {
    global $config;
    if (!empty( $_POST['password'] ) ) {
        if ( $_POST['password'] == $config['password'] ) {
            $psw = $_POST['password'];
            setcookie('admin',$psw);
            echo '<code>登录成功</code>';
        }else{
            echo '<code>密码错误</code>';
            exit( include __DIR__ . '/login.php' );
        }
    } elseif (!empty( $_COOKIE['admin'] ) ) {
        if ( $_COOKIE['admin'] == $config['password'] ) {

        }
    } else {
        echo '<code>请登录</code>';
        header('loction:login.php');
        exit(include __DIR__.'/login.php');
    }
}

 // 仅允许登录后上传
 function mustLogin(){
    global $config;
    if ($config['mustLogin']){
        checkLogin();
    }
}

// 检查配置文件中目录是否存在是否可写并创建相应目录

function config_path(){
	global $config;
	$img_path = $config['path'].date('Y').'/'.date('m').'/'.date('d');
	
	if (!is_dir($img_path)){
		@mkdir($img_path,0755,true);
	}
	
	if (!is_writable($img_path)){
		@chmod($img_path,0755);
	}
	
	return $img_path.'/';
}

// 图片重命名规则
function imgName(){
    return base_convert(date('His').mt_rand(1024,10240),10,36);
}

 // 设置广告
 function showAD($where) {
    global $config;
    switch ($where){
        case 'top':
            if ($config['ad_top']){
                include (APP_ROOT.'/public/static/ad_top.html');
            }
            break;
        case 'bot':
            if ($config['ad_bot']){
                include (APP_ROOT.'/public/static/ad_bot.html');
            }
            break;
        default:
            echo '广告函数出错';
            break;
    }
}

// 设置一键CDN
function static_cdn(){
    global $config;
    if ($config['static_cdn']){
        // 开启CDN
        return '
        <link href="https://cdn.bootcss.com/zui/1.8.1/css/zui.min.css" rel="stylesheet">
        <link href="https://cdn.bootcss.com/zui/1.8.1/lib/uploader/zui.uploader.min.css" rel="stylesheet">
        
        <script src="https://cdn.bootcss.com/jquery/3.3.1/jquery.min.js?v3.3.1"></script>
        <script src="https://cdn.bootcss.com/zui/1.8.1/js/zui.min.js?v1.8.1"></script>
        <script src="https://cdn.bootcss.com/zui/1.8.1/lib/uploader/zui.uploader.min.js?v1.8.1"></script>
        <script src="https://cdn.jsdelivr.net/gh/icret/easyImages@1.5.3/static/qrcode.min.js?v1"></script>
        <script src="https://cdn.bootcss.com/clipboard.js/2.0.4/clipboard.min.js?v2.0.4"></script>
        ';

    }else{
        // 本地文件
        return '
         <link href="../public/static/zui/css/zui.min.css?v1.8.1" rel="stylesheet">
        <link href="../public/static/zui/lib/uploader/zui.uploader.min.css?v1.8.1" rel="stylesheet">
        
        <script src="../public/static/jquery.min.js?v3.3.1"></script>
        <script src="../public/static/zui/js/zui.min.js?v1.8.1"></script>
        <script src="../public/static/zui/lib/uploader/zui.uploader.min.js?v1.8.1"></script>
        <script src="../public/static/qrcode.min.js?v1.0"></script>
        <script src="../public/static/clipboard.min.js?v2.0.4"></script>
        ';
    }
}

// 开启管理
function tinyfilemanager(){
    global $config;
    if(!$config['tinyfilemanager']){
        header('Location: '.$config['domain'].'?not_open_manager');
        exit;
    }
}


// 获取允许上传的扩展名
function getExtensions(){
    global $config;
    $mime = '';
    for ($i=0;$i<count($config['extensions']);$i++)
    {
        $mime.=$config['extensions'][$i].',';
    }
    return rtrim($mime,',');
}

//$qqgroup = ' <a target="_blank" href="//shang.qq.com/wpa/qunwpa?idkey=3feb4e8be8f1839f71e53bf2e876de36afc6889b2630c33c877d8df5a5583a6f"><img border="0" src="//pub.idqqimg.com/wpa/images/group.png" alt="EasyImage 简单图床" title="EasyImage 简单图床"></a>';