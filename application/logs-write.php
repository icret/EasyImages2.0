<?php
require_once __DIR__ . '/function.php';

/**
 * 获得用户的真实IP地址
 * <br />来源：ecshop
 * <br />$_SERVER和getenv的区别，getenv不支持IIS的isapi方式运行的php
 * @access  public
 * @return  string
 */
function real_ip()
{
    static $realip = NULL;
    if ($realip !== NULL) {
        return $realip;
    }
    if (isset($_SERVER)) {
        if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $arr = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
            /* 取X-Forwarded-For中第一个非unknown的有效IP字符串 */
            foreach ($arr as $ip) {
                $ip = trim($ip);

                if ($ip != 'unknown') {
                    $realip = $ip;

                    break;
                }
            }
        } elseif (isset($_SERVER['HTTP_CLIENT_IP'])) {
            $realip = $_SERVER['HTTP_CLIENT_IP'];
        } else {
            if (isset($_SERVER['REMOTE_ADDR'])) {
                $realip = $_SERVER['REMOTE_ADDR'];
            } else {
                $realip = '0.0.0.0';
            }
        }
    } else {
        if (getenv('HTTP_X_FORWARDED_FOR')) {
            $realip = getenv('HTTP_X_FORWARDED_FOR');
        } elseif (getenv('HTTP_CLIENT_IP')) {
            $realip = getenv('HTTP_CLIENT_IP');
        } else {
            $realip = getenv('REMOTE_ADDR');
        }
    }
    // 使用正则验证IP地址的有效性，防止伪造IP地址进行SQL注入攻击
    preg_match("/[\d\.]{7,15}/", $realip, $onlineip);
    $realip = !empty($onlineip[0]) ? $onlineip[0] : '0.0.0.0';
    return $realip;
}

/**
 * 写日志
 * 日志格式：图片名称->上传时间（Asia/Shanghai）->IP地址->浏览器信息->文件相对路径->图片的MD5
 */
function write_log($file, $imgMD5)
{
    $name = trim(basename($file), " \t\n\r\0\x0B");  // 图片名称
    $log = array($name => array(
        'date' => date('Y-m-d H:i:s'),               // 上传日期
        'ip' => real_ip(),                           // 上传ip
        'user_agent' => $_SERVER['HTTP_USER_AGENT'], //浏览器信息
        'path' => $file,                             // 文件相对路径
        'md5' => $imgMD5,                       // 文件缓存相对位置
    ));

    $logFileName = APP_ROOT . '/admin/logs/upload/' . date('Y-m') . '.php';

    // 创建日志文件夹
    if(!is_dir(APP_ROOT.'/admin/logs/upload/')){
        mkdir(APP_ROOT.'/admin/logs/upload',0755,true);
    }

    // 写入禁止浏览器直接访问
    if (filesize($logFileName)==0){
        $php_code = '<?php /** {图片名称{date:上传日期(Asia/Shanghai),ip:上传者IP,user_agent:上传者浏览器信息,path:图片相对路径,md5:图片的MD5}} */ exit;?>';
        file_put_contents($logFileName, $php_code);
    }

    $log = json_encode($log, true);
    file_put_contents($logFileName, PHP_EOL . $log, FILE_APPEND | LOCK_EX);
}
/*
for ($i = 0; $i < 100000; $i++) {
    write_log('/i/2021/11/13/12der8s.jpg', '/i/cache/2021_11_13_12der8s.jpg');
}
*/