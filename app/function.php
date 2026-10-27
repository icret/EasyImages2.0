<?php
require_once __DIR__ . '/base.php';
require_once __DIR__ . '/WaterMask.php';
require_once APP_ROOT . '/config/config.guest.php';

// 安全：登录态改由服务端 Session 承载，鉴权 Cookie 不再携带任何凭据
_session_boot();

/**
 * 判断GIF图片是否为动态
 * @param $filename string 文件
 * @return int 是|否
 */
function isGifAnimated($filename)
{
    $filecontent = @file_get_contents($filename);
    if ($filecontent === false) {
        return 0;
    }
    return strpos($filecontent, chr(0x21) . chr(0xff) . chr(0x0b) . 'NETSCAPE2.0') === FALSE ? 0 : 1;
}

/**
 * 判断webp是否为动态图片
 * @param string $src 图像文件
 * @return bool 是|否
 */
function isWebpAnimated($src)
{
    $webpFile = @file_get_contents($src);
    if ($webpFile === false) {
        return false;
    }
    return strpos($webpFile, "ANMF") !== FALSE;

    /* 2023-01-24 判断webp是否为动画
    $result = false;
    $fh = fopen($src, "rb");
    fseek($fh, 12);
    if (fread($fh, 4) === 'VP8X') {
        fseek($fh, 16);
        $myByte = fread($fh, 1);
        $result = ((ord($myByte) >> 1) & 1) ? true : false;
    }
    fclose($fh);
    return $result;
    */
}

/**
 * 判断webp或gif动图是否为动态图片
 * @param $src 图片的绝对路径
 * @return bool 是|否
 */
function is_Gif_Webp_Animated($src)
{
    // 无扩展名时 pathinfo 不返回 extension 键，用 PATHINFO 模式取空串安全判定
    $ext = strtolower(pathinfo((string) $src, PATHINFO_EXTENSION));

    if ($ext === 'webp') {
        return isWebpAnimated($src);
    }
    return (bool) isGifAnimated($src);
}


/**
 * 2023-01-06 校验登录
 * @param $user String 登录用户名
 * @param $password 登录密码
 * 返回参数解析 code=>状态码 200成功，400失败; 登录用户级别level => 0无状态, 1管理员, 2上传者, messege => 提示信息
 */

/**
 * 安全：建立/复用服务端登录会话
 * 默认仅当浏览器已持有会话Cookie时才启动（不给匿名访客创建会话）；$create=true 强制创建（登录/验证码）
 * @param bool $create 是否强制建立会话
 */
function _session_boot($create = false)
{
    // API 等无状态入口定义该常量后跳过会话
    if (defined('EASYIMAGE_NO_SESSION') || session_status() === PHP_SESSION_ACTIVE || headers_sent()) {
        return;
    }
    $secure = (!empty($_SERVER['HTTPS']) && strtolower($_SERVER['HTTPS']) !== 'off')
        || (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https')
        || (isset($_SERVER['SERVER_PORT']) && (int) $_SERVER['SERVER_PORT'] === 443);
    // 保留原 14 天免登录体验：Cookie 与 GC 有效期对齐，默认 gc_maxlifetime(1440s) 会让管理员约 24 分钟就掉线
    $lifetime = 3600 * 24 * 14;
    session_set_cookie_params(array(
        'lifetime' => $lifetime,
        'path'     => '/',
        'secure'   => $secure,
        'httponly' => true,
        'samesite' => 'Lax',
    ));
    ini_set('session.gc_maxlifetime', (string) $lifetime);
    ini_set('session.use_strict_mode', '1');
    ini_set('session.use_only_cookies', '1');
    if ($create || isset($_COOKIE[session_name()])) {
        @session_start();
    }
}

/**
 * 安全：CSRF 令牌（服务端会话承载，表单与状态变更链接共用）
 * @return string 32 字节十六进制令牌
 */
function csrf_token()
{
    if (session_status() !== PHP_SESSION_ACTIVE) {
        _session_boot(true);
    }
    // 输出已发送/会话未能建立时返回空令牌，由调用方按未登录处理，避免读写未初始化的 $_SESSION
    if (session_status() !== PHP_SESSION_ACTIVE) {
        return '';
    }
    if (empty($_SESSION['csrf_token']) || !is_string($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

/**
 * 安全：校验 CSRF 令牌（恒定时间比较）
 * @param mixed $token 请求携带的令牌
 * @return bool
 */
function csrf_verify($token)
{
    if (!is_string($token) || $token === '') {
        return false;
    }
    $expected = isset($_SESSION['csrf_token']) ? $_SESSION['csrf_token'] : '';
    return is_string($expected) && $expected !== '' && hash_equals($expected, $token);
}

/**
 * 安全：SVG 上传安全校验
 * SVG 可按同源脚本执行，除 <script> 外还需拒绝事件属性、外部引用与 foreignObject 等载体。
 * @param string $file SVG 文件绝对路径
 * @return bool 安全|不安全
 */
function is_safe_svg($file)
{
    if (!is_file($file) || !is_readable($file)) {
        return false;
    }
    $svg = @file_get_contents($file);
    if ($svg === false || $svg === '') {
        return false;
    }
    $patterns = array(
        '/<\s*script\b/i',
        '/<\s*foreignObject\b/i',
        '/<\s*iframe\b/i',
        '/<\s*embed\b/i',
        '/<\s*object\b/i',
        '/<\s*use\b/i',
        '/<\s*image\b/i',
        '/\bon[a-z]+\s*=/i',
        '/javascript\s*:/i',
        '/\bdata\s*:\s*text\/html/i',
        '/<\?php/i',
        '/<!ENTITY/i',
        '/xlink\s*:\s*href\s*=/i',
        '/\bhref\s*=/i',
    );
    foreach ($patterns as $pattern) {
        if (preg_match($pattern, $svg)) {
            return false;
        }
    }
    return true;
}

/**
 * 安全：建立登录态并轮换会话ID（防会话固定），凭据不进浏览器
 * @param string $user 用户名
 * @param int $level 级别 1管理员 2上传者
 * @param string $messege 提示信息
 * @return string json code/level/messege
 */
function _login_session($user, $level, $messege)
{
    _session_boot(true);
    // 安全：会话未能建立（如 output_buffering=Off 且已有输出导致 headers_sent）时，不得谎报登录成功
    if (session_status() !== PHP_SESSION_ACTIVE) {
        return json_encode(array('code' => 400, 'level' => 0, 'messege' => '会话建立失败，请检查 PHP 输出缓冲/Session 配置后重试'));
    }
    // 输出已发送时无法再写 Cookie，跳过轮换以避免 Warning（登录态仍以当前会话写入）
    if (!headers_sent()) {
        session_regenerate_id(true);
    }
    $_SESSION['easyimage_auth'] = array('user' => $user, 'level' => $level, 'time' => time());
    // 安全：主动失效历史携带凭据的 auth cookie（若存在）
    if (isset($_COOKIE['auth'])) {
        if (!headers_sent()) {
            setcookie('auth', '', time() - 42000, '/');
        }
        unset($_COOKIE['auth']);
    }
    return json_encode(array('code' => 200, 'level' => $level, 'messege' => $messege));
}

/**
 * 安全：登出，销毁服务端登录态并使会话失效
 */
function _logout()
{
    if (session_status() === PHP_SESSION_ACTIVE) {
        unset($_SESSION['easyimage_auth']);
        $_SESSION = array();
        if (ini_get('session.use_cookies') && !headers_sent()) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000, $params['path'], $params['domain'], $params['secure'], $params['httponly']);
        }
        session_destroy();
    }
    // 安全：失效历史携带凭据的 auth cookie（若存在）
    if (isset($_COOKIE['auth'])) {
        if (!headers_sent()) {
            setcookie('auth', '', time() - 42000, '/');
        }
        unset($_COOKIE['auth']);
    }
}

/**
 * 安全：当前登录用户名（来自服务端会话），未登录返回 null
 * @return string|null
 */
function login_username()
{
    $auth = $_SESSION['easyimage_auth'] ?? null;
    return (is_array($auth) && isset($auth['user'])) ? $auth['user'] : null;
}

/**
 * 2023-01-06 校验登录
 * @param $user String 登录用户名
 * @param $password 登录密码
 * 返回参数解析 code=>状态码 200成功，400失败; 登录用户级别level => 0无状态, 1管理员, 2上传者, messege => 提示信息
 */
function _login($user = null, $password = null)
{
    global $config;
    global $guestConfig;

    // 会话验证（无凭据入参）
    if ($user === null and $password === null) {
        _session_boot();
        $auth = $_SESSION['easyimage_auth'] ?? null;
        if (!is_array($auth) || !isset($auth['user'], $auth['level'])) {
            return json_encode(array('code' => 400, 'level' => 0, 'messege' => '请登录'));
        }
        // 管理员：会话与配置身份一致
        if ($auth['level'] === 1 && $auth['user'] === $config['user']) {
            return json_encode(array('code' => 200, 'level' => 1, 'messege' => '尊敬的管理员'));
        }
        // 上传者：账户仍存在且未过期
        if ($auth['level'] === 2 && array_key_exists($auth['user'], $guestConfig)) {
            if (($guestConfig[$auth['user']]['expired'] ?? 0) < time()) {
                _logout();
                return json_encode(array('code' => 400, 'level' => 0, 'messege' => $auth['user'] . '账号已过期'));
            }
            return json_encode(array('code' => 200, 'level' => 2, 'messege' => $auth['user'] . '用户已登录'));
        }
        // 会话与配置不一致（账号被删除/改名/降权），使会话失效
        _logout();
        return json_encode(array('code' => 400, 'level' => 0, 'messege' => '登录已过期,请重新登录'));
    }

    // 前端登录验证（strip_tags 不接受 null，PHP 8.1 起会 deprecate；数组型入参直接拒绝）
    if (!is_string($user) || !is_string($password)) {
        return json_encode(array('code' => 400, 'level' => 0, 'messege' => '账号或密码格式错误'));
    }
    $user = strip_tags($user);
    $password = strip_tags($password);
    $configuredUser = (string) ($config['user'] ?? '');
    $configuredPassword = (string) ($config['password'] ?? '');
    // 是否管理员（恒定时间比较，避免长度/前缀时间侧信道）
    if ($configuredUser !== '' && $configuredPassword !== ''
        && hash_equals($configuredUser, $user) && hash_equals($configuredPassword, $password)) {
        return _login_session($user, 1, '管理员登录成功');
    }
    // 是否上传者
    if (array_key_exists($user, $guestConfig)) {
        $guestPassword = (string) ($guestConfig[$user]['password'] ?? '');
        if ($guestPassword !== '' && hash_equals($guestPassword, $password)) {
            // 上传者账号过期
            if (($guestConfig[$user]['expired'] ?? 0) < time()) {
                return json_encode(array('code' => 400, 'level' => 0, 'messege' => $user . '账号已过期'));
            }
            return _login_session($user, 2, $user . '用户登录成功');
        }
    }
    // 账号存在但密码错误 / 账号不存在
    if (($configuredUser !== '' && hash_equals($configuredUser, $user)) || array_key_exists($user, $guestConfig)) {
        return json_encode(array('code' => 400, 'level' => 0, 'messege' => '密码错误'));
    }
    return json_encode(array('code' => 400, 'level' => 0, 'messege' => '账号不存在'));
}

/**
 * 校验登录状态码（保留旧返回码语义，改由服务端会话推导）
 * @return int 201未登录 203无效 204管理员 205上传者 206过期
 */
function checkLogin()
{
    global $config;
    global $guestConfig;

    _session_boot();
    $auth = $_SESSION['easyimage_auth'] ?? null;
    if (!is_array($auth) || !isset($auth['user'], $auth['level'])) {
        return 201;
    }
    if ($auth['level'] === 1 && $auth['user'] === $config['user']) {
        return 204;
    }
    if ($auth['level'] === 2 && array_key_exists($auth['user'], $guestConfig)) {
        if (($guestConfig[$auth['user']]['expired'] ?? 0) < time()) {
            return 206;
        }
        return 205;
    }
    return 203;
}

/**
 * 2023-01-06 仅允许登录上传
 */
function mustLogin()
{
    global $config;
    if (!empty($config['mustLogin'])) {
        $status = _login();
        $status = json_decode($status, true);

        if ($status['code'] === 200) {
            echo '
            <script> 
                new $.zui.Messager("' . $status["messege"] . '", {
                type: "success", // 定义颜色主题 
                icon: "linux", // 定义消息图标
                placement:"bottom-right" // 消息位置
                }).show();
            </script>';
        }

        if ($status['code'] === 400) {
            echo '
            <script>
                new $.zui.Messager("' . $status["messege"] . '", {
                type: "danger", // 定义颜色主题 
                icon: "bullhorn" // 定义消息图标
            }).show();
            </script>';
            header("refresh:2;url=" . $config['domain'] . "/admin/index.php");
        }
    }
}

/**
 * 检查配置文件中目录是否存在是否可写并创建相应目录
 * @param null $path 要创建的路径
 * @return string
 */
function config_path($path = null)
{
    global $config;

    if ($path === null) {
        $path = date($config['storage_path'] ?? 'Y/m/d/');
    }
    // 2023-01-06弃用 php5.6 兼容写法：
    // $path = isset($path) ? $path : date('Y/m/d/');
    // php7.0 $path = $path ?? date('Y/m/d/');
    // 原 empty($path) 会把 '0' 这类合法目录名当成空值重新生成

    // 统一分隔符并保证站点相对路径以 / 开头（config.path/admin_path 末尾或开头缺少 / 时也能正确拼接 APP_ROOT）
    // 相对路径的落盘与权限操作必须锚定 APP_ROOT，否则在 cwd 不是项目根（CLI、部分 FCGI 环境）时会创建到错误位置
    $img_path = '/' . ltrim(str_replace('\\', '/', ($config['path'] ?? '') . $path), '/');
    $absolute = APP_ROOT . $img_path;

    if (!is_dir($absolute)) {
        @mkdir($absolute, 0755, true);
    }

    if (!is_writable($absolute)) {
        @chmod($absolute, 0755);
    }

    return $img_path;
}

/**
 * 图片路径收敛，防止路径穿越（CWE-22）
 * 拒绝含 ../ 的入参，用 realpath() 归一化后强制校验结果落在图片存储目录内。
 * 删除、压缩、下载等写/取回操作一律限定在存储目录；仅显式传入 $allow_site_root
 * 的只读展示端点（缩略图、详情页）在管理员登录时才额外放开站点根目录。
 * @param string $input 请求参数中的图片路径，如 /i/2024/01/01/x.png 或 2024/01/01/x.png
 * @param string $error 引用返回失败原因：invalid 参数非法、traversal 穿越、outside 越出允许目录、missing 不存在
 * @param string $type 校验对象：file 文件（默认）、dir 目录
 * @param bool $allow_site_root 仅只读展示端点置 true：管理员登录时放开站点根；写/取回端点保持默认 false
 * @return string|false 合法返回以 / 开头的站点相对路径（可拼接 APP_ROOT），非法返回 false
 */
function resolve_image_path($input, &$error = null, $type = 'file', $allow_site_root = false)
{
    global $config;

    $error = null;

    // 解码并统一分隔符，去掉域名、协议、查询串与锚点
    $clean = str_replace('\\', '/', rawurldecode((string) $input));
    if (strpos($clean, "\0") !== false) {
        $error = 'invalid';
        return false;
    }
    $urlPath = parse_url($clean, PHP_URL_PATH);
    if (!is_string($urlPath) || $urlPath === '') {
        $error = 'invalid';
        return false;
    }

    // 折叠重复斜杠并去除 ./ 段
    $path = '/' . ltrim(preg_replace('#/+#', '/', $urlPath), '/');
    $path = str_replace('/./', '/', $path);

    // 拒绝父目录穿越
    if (preg_match('#(^|/)\.\.(/|$)#', $path)) {
        $error = 'traversal';
        return false;
    }

    // 补齐存储目录前缀（隐藏存储路径时历史记录的路径不含 /i/）
    $storage = '/' . trim($config['path'], '/') . '/';
    // 站点根放行只对显式 opt-in 的只读端点、且当前为管理员时生效；写/取回端点恒限定在存储目录
    $isAdmin = $allow_site_root && is_who_login('admin');

    // 允许的根目录：图片存储目录，管理员追加站点根目录
    $allowedBases = array(array(realpath(APP_ROOT . rtrim($storage, '/')), $storage));
    if ($isAdmin) {
        $allowedBases[] = array(realpath(APP_ROOT), '/');
    }

    // 待校验路径：普通访问一律先映射到存储目录内，管理员再保留原路径
    $candidates = array(strncmp($path, $storage, strlen($storage)) === 0 ? $path : $storage . ltrim($path, '/'));
    if ($isAdmin) {
        $candidates[] = $path;
    }

    // 逐个用 realpath() 归一化（符号链接也在此步解析），并校验是否落在允许目录内
    $checkType = $type === 'dir' ? 'is_dir' : 'is_file';
    $error = 'missing';
    foreach ($candidates as $candidate) {
        $real = realpath(APP_ROOT . $candidate);
        if ($real === false || !$checkType($real)) continue;
        foreach ($allowedBases as $allowedBase) {
            list($base, $prefix) = $allowedBase;
            if ($base === false) continue;
            $baseLen = strlen($base);
            if (strncmp($real, $base, $baseLen) !== 0) continue;
            if (isset($real[$baseLen]) && $real[$baseLen] !== '/' && $real[$baseLen] !== '\\') continue;
            $error = null;
            return rtrim($prefix, '/') . '/' . trim(str_replace('\\', '/', substr($real, $baseLen)), '/');
        }
        // 保留首个有意义的失败原因，避免后续候选把 outside 覆盖回 missing
        if ($error === 'missing') $error = 'outside';
    }

    return false;
}

/**
 * 判断已存在的路径（绝对或相对 APP_ROOT）归一化后是否落在图片存储目录内
 * 用作删除、压缩等写操作前的边界校验
 * @param string $path 待校验路径
 * @return bool 在存储目录内|不在
 */
function is_within_storage($path)
{
    global $config;

    $base = realpath(APP_ROOT . '/' . trim($config['path'], '/') . '/');
    $real = realpath($path);
    if ($base === false || $real === false) {
        return false;
    }

    $baseLen = strlen($base);
    if (strncmp($real, $base, $baseLen) !== 0) {
        return false;
    }

    return !isset($real[$baseLen]) || $real[$baseLen] === '/' || $real[$baseLen] === '\\';
}

/**
 * 图片命名规则
 * @param null $source 源文件名称
 * @return false|int|string|null
 */
function easyimage_create_guid() // guid生成函数
{
    if (function_exists('com_create_guid') === true) {
        return trim(com_create_guid(), '{}');
    }

    return sprintf('%04X%04X-%04X-%04X-%04X-%04X%04X%04X', mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(16384, 20479), mt_rand(32768, 49151), mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(0, 65535));
}

/** uuid 生成 */
function easyimage_uuid()
{
    $chars = md5(uniqid(mt_rand(), true));
    return substr($chars, 0, 8) . '-' . substr($chars, 8, 4) . '-' . substr($chars, 12, 4) . '-' . substr($chars, 16, 4) . '-' . substr($chars, 20, 12);
}

function imgName($source = null)
{
    global $config;

    switch ($config['imgName'] ?? 'default') {

        case "default":
            return base_convert(date('His') . mt_rand(1001, 9999), 10, 36); // 将上传时间+随机数转换为36进制 例：vx77yu
            break;
        case "source":
            // 以上传文件名称 例：微信图片_20211228214754
            // 先取单层文件名并去除控制字符；最终仍由 Upload 类的 sanitize() 统一清洗
            $source = basename(str_replace('\\', '/', (string) $source));
            $source = preg_replace('/[\x00-\x1F\x7F]/u', '', $source);
            return $source;
            break;
        case "date":
            // 以上传时间 例：192704
            return date("His");
            break;
        case "unix":
            // 以Unix时间 例：1635074840
            return time();
            break;
        case "uniqid":
            // 基于以微秒计的当前时间 例：6175436c73418
            return uniqid(true);
            break;
        case "guid":
            // 全球唯一标识符 例：6EDAD0CC-AB0C-4F61-BCCA-05FAD65BF0FA
            return easyimage_create_guid();
            break;
        case "md5":
            // md5加密时间 例：3888aa69eb321a2b61fcc63520bf6c82
            return md5(microtime());
            break;
        case "sha1":
            // sha1加密微秒 例：654faac01499e0cb5fb0e9d78b21e234c63d842a
            return sha1(microtime());
            break;
        case "crc32":
            // crc32加密微秒 例：2495551279
            return crc32(microtime());
            break;
        case "snowflake":
            require_once __DIR__ . '/class.snowflake.php';
            return class_exists('SnowFlake') ? SnowFlake::createOnlyId() : uniqid(true); // 分布式id
            break;
        case "uuid":
            return easyimage_uuid(); // uuid
            break;
        default:
            return base_convert(date('His') . mt_rand(1001, 9999), 10, 36); // 将上传时间+随机数转换为36进制 例：vx77yu
    }
}

/**
 * 静态文件CDN
 */
function static_cdn()
{
    global $config;
    if (!empty($config['static_cdn'])) {
        echo $config['static_cdn_url'] ?? '';
    } else {
        echo $config['domain'] ?? '';
    }
}

/**
 * 获取允许上传的扩展名
 */
function getExtensions()
{
    global $config;
    // 原实现循环拼接的是整个数组（Array to string conversion），结果永远是 "Array,Array,..."
    return $config['extensions'] ?? '';
}

/**
 * 获取目录大小 如果目录文件较多将很费时
 * @param $path string 路径
 * @return int
 */
function getDirectorySize($path)
{
    $bytestotal = 0;
    $path = realpath($path);
    // realpath 失败已返回 false，成功则必然存在，原先的 file_exists() 重复 stat 一次
    if ($path !== false && $path !== '') {
        foreach (new RecursiveIteratorIterator(new RecursiveDirectoryIterator($path, FilesystemIterator::SKIP_DOTS)) as $object) {
            $bytestotal += $object->getSize();
        }
    }
    return $bytestotal;
}

/**
 * 获取指定文件夹文件数量
 * @param $dir 传入一个路径如：/apps/web
 * @return int 返回文件数量
 */
function getFileNumber($dir)
{
    // 目录不存在时直接返回 0，避免 glob()/迭代器在 PHP 8 下告警
    if (!is_dir($dir)) {
        return 0;
    }
    $num = 0;
    try {
        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($dir, FilesystemIterator::SKIP_DOTS)
        );
        foreach ($iterator as $file) {
            if ($file->isFile()) {
                $num++;
            }
        }
    } catch (Throwable $e) {
        return 0;
    }
    return $num;
}

/**
 * 缓存化的文件总数（头部导航每个页面都会展示，避免每次请求全量递归扫描）
 * @return int
 */
function easyimage_cached_file_count()
{
    // total_files.php 必须在全局作用域引入（在函数内 require 会把其 $totalJsonName 等变量限制在函数作用域，
    // 导致 creat_json() 里的 global $totalJsonName 取到空值）
    if (!function_exists('read_total_json')) {
        return 0;
    }
    $count = read_total_json('filenum');
    return is_numeric($count) ? (int) $count : 0;
}

/**
 * 图片展示页面
 * getDirList()取文件夹列表，getFile()取对应文件夹下面的文件列表，二者的区别在于有没有“.”后缀的文件，其他都一样
 * 获取文件目录列表，该方法返回数组
 * @param $dir string 路径
 * @return array
 * @example getDirList("./dir")
 */
function getDirList($dir)
{
    // 目录不存在时早返回，避免 scandir 触发 PHP 8 Warning（旧 readdir 循环对非法句柄静默）
    $dir = $dir ?: '.';
    if (!is_dir($dir)) {
        return array();
    }
    // 仅保留无扩展名（不含“.”）的非隐藏项，与旧 readdir 循环等价，
    // 旧写法 `!strpos($file, '.')` 依赖“位置 0 为假”的魔术判断
    $list = preg_grep('/^\./', scandir($dir) ?: array(), PREG_GREP_INVERT);
    return array_values(array_filter($list, function ($file) {
        return strpos($file, '.') === false;
    }));
}

/**
 * 获取文件列表
 * @param $dir string 目录
 * @return array
 */
function getFile($dir)
{
    if (!is_dir($dir)) {
        return array();
    }
    $list = preg_grep('/^\./', scandir($dir) ?: array(), PREG_GREP_INVERT);
    $fileArray = array_values(array_filter($list, function ($file) {
        return strpos($file, '.') !== false;
    }));
    // 保留旧逻辑的单次最多 1000 项上限
    return array_slice($fileArray, 0, 1000);
}

/**
 * 获取文件夹文件列表或数量
 * @param string $dir_fileName_suffix 获取文件列表：目录+文件名*:全匹配+文件后缀 *: 全匹配 {jpg,png,gif}:匹配指定格式
 *                                    递归文件数量：目录
 * @example get_file_by_glob(__DIR__ . '/i/cache/*.*', $type = 'list'); // 获取目录文件列表
 * @example get_file_by_glob(__DIR__ . '/i/', $type = 'number');        // 递归获取文件夹数量
 * @param string $type list|number 返回列表还是数量
 * @return array|int 返回数组|数量
 */
function get_file_by_glob($dir_fileName_suffix, $type = 'list')
{
    global $config;

    // 请求内缓存：同一页面会多次统计同一目录（头部、列表、管理页），避免重复递归扫描
    static $cache = array();
    $cacheKey = $type . '|' . $dir_fileName_suffix;
    if (array_key_exists($cacheKey, $cache)) {
        return $cache[$cacheKey];
    }
    $res = $type === 'number' ? 0 : array();

    // 获取所有文件
    if ($type == 'list') {
        $flag = defined('GLOB_BRACE') ? GLOB_BRACE : 0;
        $glob = glob($dir_fileName_suffix, $flag);
        // $res 必须先初始化：旧写法在“匹配项全为目录”时读未定义变量（PHP 8 warning，PHP 9 fatal）
        $res = array();

        if ($glob) {
            foreach ($glob as $v) {
                if (is_file($v)) $res[] = basename($v);
            }
            // 排序
            if ($res && !empty($config['showSort'])) {
                $res = array_reverse($res);
            }
        }
    }

    if ($type == 'number') {
        $res = 0;
        $glob = glob($dir_fileName_suffix); //把该路径下所有的文件存到一个数组里面;
        if ($glob) {
            foreach ($glob as $v) {
                if (is_file($v)) {
                    $res++;
                } else {
                    $res += get_file_by_glob($v . "/*", 'number');
                }
            }
        }
    }
    $cache[$cacheKey] = $res;
    return $res;
}

/**
 * 递归统计指定目录下的目录数与文件数
 * 旧实现将 $dirn/$filen 建在局部作用域且递归不回传，任何时候都统计不出结果且无返回值
 * @param $file string 目录
 * @return array array('dir' => 目录数, 'file' => 文件数)
 */
function getdirnum($file)
{
    $dirn = 0;
    $filen = 0;
    $files = is_dir($file) ? scandir($file) : array();
    foreach ((array) $files as $filename) {
        if ($filename === '.' || $filename === '..') {
            continue;
        }
        if (is_dir($file . '/' . $filename)) {
            $dirn++;
            $sub = getdirnum($file . '/' . $filename);
            $dirn += $sub['dir'];
            $filen += $sub['file'];
        } else {
            $filen++;
        }
    }
    return array('dir' => $dirn, 'file' => $filen);
}

/**
 * 把文件或目录的大小转化为容易读的方式
 * disk_free_space - 磁盘可用空间(比如填写D盘某文件夹，则会现在D盘剩余空间）
 * disk_total_space — 磁盘总空间(比如填写D盘某文件夹，则会现在D盘总空间）
 * @param $number
 * @return string
 */
function getDistUsed($number)
{
    // 1024 的幕即原 pow(2, 10/20/30)，整数字面量避免浮点幂运算
    $units = array(array(1024 ** 3, 'GB'), array(1024 ** 2, 'MB'), array(1024, 'KB'));
    foreach ($units as $unit) {
        if ($number > $unit[0]) {
            return round($number / $unit[0], 2) . $unit[1];
        }
    }
    return $number . 'bytes';
}

/**
 * 图片路径能力令牌（源图保护链接 / 用户删除链接）
 * 采用 AES-256-GCM 认证加密：密钥由 HKDF-SHA256 从配置密钥派生（不再截断为 crc32 的 32 位），
 * 每次加密使用随机 IV，密文自带完整性标签，令牌不可伪造、不可篡改。
 * hide 与 del 使用不同主密钥与派生上下文做域隔离，互不通用。
 * 不向后兼容：旧版 base64(AES-128-XTS, crc32) 令牌一律解密失败并回落为无效。
 * @param string $data    mode=0 时待加密的站点相对路径；mode=1 时待解密的 base64url 令牌
 * @param int    $mode    1 解密 0 加密
 * @param string $context 用途域：'hide' 从 hide_key 派生，'del'(默认) 从 password 派生
 * @return string|false   加密返回 base64url 令牌；解密失败(篡改/密钥不符/格式错)返回 false
 */
function urlHash($data, $mode, $context = 'del')
{
    global $config;

    // 主密钥按用途分域：删除令牌绑定管理员口令，源图保护令牌绑定 hide_key
    $master = $context === 'hide'
        ? ($config['hide_key'] ?? '')
        : ($config['password'] ?? '');

    // HKDF 派生 256-bit 密钥，info 绑定用途做域隔离
    $key = hash_hkdf('sha256', $master, 32, 'EasyImages2.0:urlHash:' . $context);

    $cipher = 'aes-256-gcm';
    $tagLen = 16;
    $ivLen = 12;

    if ($mode) {
        // 解密：令牌 = base64url( iv | tag | ciphertext )
        $raw = base64_decode(strtr((string) $data, '-_', '+/'), true);
        if ($raw === false || strlen($raw) < $ivLen + $tagLen + 1) {
            return false;
        }
        $iv = substr($raw, 0, $ivLen);
        $tag = substr($raw, $ivLen, $tagLen);
        $ct = substr($raw, $ivLen + $tagLen);
        // GCM 标签验证不通过（篡改或密钥不符）时 openssl_decrypt 返回 false
        return openssl_decrypt($ct, $cipher, $key, OPENSSL_RAW_DATA, $iv, $tag);
    }

    // 加密：随机 IV，输出 base64url(iv|tag|ct)
    $iv = random_bytes($ivLen);
    $ct = openssl_encrypt($data, $cipher, $key, OPENSSL_RAW_DATA, $iv, $tag, '', $tagLen);
    if ($ct === false) {
        return false;
    }
    return rtrim(strtr(base64_encode($iv . $tag . $ct), '+/', '-_'), '=');
}

/**
 * 删除指定文件
 * @param $url string 文件
 * @param $type string 模式
 */
function getDel($url, $type)
{
    global $config;
    // url本地化（(string) 防止入参为数组/arrayeval 写入的非常规类型时 strpos() 报 TypeError）
    $url = htmlspecialchars(str_replace($config['domain'] ?? '', '', (string) $url)); // 过滤html 获取url path
    $url = urldecode(trim($url));

    if ($type == 'url') {
        $url = APP_ROOT . $url;
    }
    if ($type == 'hash') {
        $url = APP_ROOT . $url;
    }

    // 文件是否存在 限制只能删除存储目录内的文件
    if (is_file($url) && is_within_storage($url)) {
        // 执行删除
        if (@unlink($url)) {
            return TRUE;
        } else {
            return FALSE;
        }
    }
}


/**
 * 删除指定文件
 * @param $url string 文件
 * @param $type string 模式
 */
function easyimage_delete($url, $type)
{
    global $config;
    // url本地化
    $url = htmlspecialchars(str_replace($config['domain'] ?? '', '', (string) $url)); // 过滤html 获取url path
    $url = urldecode(trim($url));

    if ($type == 'url') {
        $url = APP_ROOT . $url;
    }
    if ($type == 'hash') {
        $url = APP_ROOT . $url;
    }

    // 文件是否存在 限制只能删除存储目录内的文件
    if (is_file($url) && is_within_storage($url)) {
        // 执行删除
        $result = @unlink($url);
        // 清除查询（旧写法写在 return 之后，永远不会执行）
        clearstatcache();
        return $result ? TRUE : FALSE;
    }
    return FALSE;
}

/**
 * 判断是否此用户登录
 * @param string $user 判断登录者权限 当$user=null 时检查是否登录, 不区分身份
 * @return bool 是|否
 */
function is_who_login($user)
{
    // 将状态转码
    $status = (array) json_decode(_login(), true);
    // 查询是否登录
    if ($user === 'status') if (($status['level'] ?? 0) > 0) return true;
    // 是否管理员登录
    if ($user === 'admin') if (($status['level'] ?? 0) == 1) return true;
    // 是否上传者登录
    if ($user === 'guest') if (($status['level'] ?? 0) == 2) return true;

    return false;
}

/**
 * 检查PHP缺少简单图床必备的扩展
 * 需检测的扩展：'fileinfo', 'iconv', 'gd', 'mbstring', 'openssl','zip',
 * zip 扩展不是必须的，但会影响tinyfilemanager文件压缩(本次不检测)。
 *
 * 检测是否更改默认域名
 *
 * 检测是否修改默认密码
 * @param $mode bool 是否开启检测
 */
function checkEnv($mode)
{
    // 初始化安装
    if (!is_file(APP_ROOT . '/config/install.lock') and is_file(APP_ROOT . '/install/install.php')) {
        echo '<script type="text/javascript">window.location.href="' . get_whole_url('/') . '/install/index.php"</script>';
    }

    if ($mode) {
        require_once __DIR__ . '/check.php';
    }
}

/**
 * 前端改变图片长宽
 * @return string 裁剪参数
 */
function imgRatio()
{
    global $config;
    if (!empty($config['imgRatio'])) {
        $imgRatio_crop = (($config['imgRatio_crop'] ?? null) === 1) ? 'true' : 'false';
        $imgRatio_preserve_headers = (($config['imgRatio_preserve_headers'] ?? null) === 1) ? 'true' : 'false';

        $image_x = (($config['image_x'] ?? 0) != 0) ? "width:" . $config['image_x'] . ',' : null;
        $image_y = (($config['image_y'] ?? 0) != 0) ? "height:" . $config['image_y'] . ',' : null;

        return '
		resize:{
			' . $image_x . '
			' . $image_y . '
			crop: ' . $imgRatio_crop . ',
			quality:' . ($config['imgRatio_quality'] ?? '') . ',
			preserve_headers: ' . $imgRatio_preserve_headers . ',
		}';
    } else {
        return "file_data_name:'file'";
    }
}

/**
 * 定时获取GitHub 最新版本
 * @return mixed|null 读取版本信息
 */
function getVersion($name = 'tag_name')
{
    global $config;

    // 请求内缓存：管理页会多次读取同一字段，避免重复读盘/重复请求 GitHub
    static $cache = array();
    if (array_key_exists($name, $cache)) {
        return $cache[$name];
    }

    if (!empty($config['checkEnv'])) {
        require_once __DIR__ . '/class.version.php';
        $url = "https://api.github.com/repositories/188228357/releases/latest"; // 获取版本地址
        $getVersion = new getVersion($url);
        try {
            // 旧写法 !empty() 与 return 各调一次 readJson（重复读盘解析）
            $version = $getVersion->readJson($name);
            $result = !empty($version)
                ? $version
                : '存在版本文件, 但是内容为空,请等待1小时候后再次更新版本号!';
        } catch (Throwable $e) {
            // readJson 内部已尝试过 downJson，失败后直接返回提示，避免重复网络请求拖慢后台
            $result = '获取版本文件失败,请检查curl或者网络 错误信息: ' . $e->getMessage();
        }
    } else {
        $result = '已关闭环境自检, 当前版本:' . APP_VERSION;
    }

    $cache[$name] = $result;
    return $result;
}

/**
 * 删除非空目录
 * @param $dir string 要删除的目录
 * @return bool true|false
 */
function deldir($dir)
{
    $dir = (string) $dir;
    if (file_exists($dir)) {
        $files = (array) scandir($dir);
        foreach ($files as $file) {
            if ($file != '.' && $file != '..') {
                $path = $dir . '/' . $file;
                if (is_dir($path)) {
                    deldir($path);
                } else {
                    unlink($path);
                }
            }
        }
        rmdir($dir);
        return true;
    } else {
        return false;
    }
}

/**
 * 图片监黄curl 访问网站并返回解码过的json信息
 * @param $img string 图片url
 * @param null $url 访问的网址
 * @return mixed
 */
function moderatecontent_json($img, $url = null)
{
    global $config;

    if (empty($config['moderatecontent_key'])) {
        exit;
    }

    $url = 'https://api.moderatecontent.com/moderate/?key=' . $config['moderatecontent_key'] . '&url=' . $img;
    $headerArray = array("Content-type:application/json;", "Accept:application/json");
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headerArray);
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);
    curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/86.0.4240.198 Safari/537.36');
    $output = curl_exec($ch);
    if (PHP_VERSION_ID < 80500) {
        curl_close($ch);
    }
    $output = json_decode($output, true);
    return $output;
}

/**
 * 使用curl方式实现get或post请求
 * @param $url 请求的url地址
 * @param $data 发送的post数据 如果为空则为get方式请求
 * return 请求后获取到的数据
 */

function nsfwjs_json($url, $data = '')
{
    global $config;

    if (empty($config['nsfwjs_url'])) {
        exit;
    }

    $ch = curl_init();
    $params = array(); // $params 旧写法未初始化，PHP 8 下首次下标写入会报 warning
    $params[CURLOPT_URL] = $config['nsfwjs_url'] . $url; //请求url地址
    $params[CURLOPT_HEADER] = false; //是否返回响应头信息
    $params[CURLOPT_RETURNTRANSFER] = true; //是否将结果返回
    $params[CURLOPT_FOLLOWLOCATION] = true; //是否重定向
    $params[CURLOPT_TIMEOUT] = 30; //超时时间
    if (!empty($data)) {
        $params[CURLOPT_POST] = true;
        $params[CURLOPT_POSTFIELDS] = $data;
    }
    $params[CURLOPT_SSL_VERIFYPEER] = false; //请求https时设置,还有其他解决方案
    $params[CURLOPT_SSL_VERIFYHOST] = false; //请求https时,其他方案查看其他博文
    curl_setopt_array($ch, $params); //传入curl参数
    $content = curl_exec($ch); //执行
    if (PHP_VERSION_ID < 80500) {
        curl_close($ch); //关闭连接
    }
    $content = json_decode($content, true);
    return $content;
}

/**
 * 检查图片是否违规
 * @param $imageUrl 图片链接
 * @param int $type 模式: 1|moderatecontent 2|nsfwjs 3|移入回收站
 * @param string $dir 移入的目录
 * @return bool
 */
function checkImg($imageUrl, $type = 1, $dir = 'suspic/')
{
    global $config;

    // $bad_pic 必须预置：旧写法在“未违规”分支读未定义变量（PHP 8 warning，PHP 9 fatal）
    $bad_pic = false;

    /** # 使用moderatecontent */
    if ($type === 1) {
        $response = (array) moderatecontent_json($imageUrl);
        if (($response['rating_index'] ?? 0) == 3 or ($response['predictions']['adult'] ?? 0) > $config['checkImg_value']) { // (1 = everyone, 2 = teen, 3 = adult)
            $bad_pic = true;
        }
    }

    /** # 使用nsfwjs */
    if ($type === 2) {
        /**
         * probability，概率
         * className，类型
         * 
         * 上传图片后，总共会返回 5 个维度的数值来鉴别该图片的尺度:
         * 
         * 绘画（Drawing）—— 无害的艺术，或艺术绘画；
         * 变态（Hentai）—— 色情艺术，不适合大多数工作环境；
         * 中立（Neutral）—— 一般，无害的内容；
         * 色情（Porn）—— 不雅的内容和行为，通常涉及生殖器；
         * 性感（Sexy）—— 不合时宜的挑衅内容。
         * 
         * 当porn评分超过>=0.6左右,就几乎是一张带有色情性质的图片了。
         */

        $file = (array) nsfwjs_json($imageUrl);

        // 将获取的值删除className后组建数组（五个维度先置 0，接口少返项时不会读未定义键）
        $res = array('Drawing' => 0, 'Hentai' => 0, 'Neutral' => 0, 'Porn' => 0, 'Sexy' => 0);
        // 旧写法 $i <= count($file) 越界一位，且固定读 5 个下标假设接口一定返回 5 项
        foreach ($file as $item) {
            $item = (array) $item;
            $className = $item['className'] ?? null;
            if ($className !== null && array_key_exists($className, $res)) {
                $res[$className] = $item['probability'] ?? 0;
            }
        }

        // 测试数组是否正确
        // foreach ($file as $k => $v) {
        //     foreach ($v as $k1 => $v1) {
        //         echo $k1 . '=>' . $v1 . '<br/>';
        //     }
        // }

        if (
            $res['Sexy'] * 100 > $config['checkImg_value'] ||
            $res['Porn'] * 100 > $config['checkImg_value'] ||
            $res['Hentai'] * 100 > $config['checkImg_value']
        ) {
            $bad_pic = true;
        }
    }

    // 移入回收站
    if ($type === 3) {
        $bad_pic = true;
        $dir = 'recycle/';
    }

    /** # 如果违规则移动图片到违规文件夹 */
    if ($bad_pic === true) {
        // 非法/空 URL 时 parse_url 返回 false 或不含 path，拼接前必须先收改
        $urlPath = parse_url((string) $imageUrl);
        if (!is_array($urlPath) || !isset($urlPath['path'])) {
            return false;
        }
        $old_path = APP_ROOT . $urlPath['path'];            // 提交网址中的文件路径 /i/2021-10-29/p8vypd.png

        // 限制只能处理存储目录内的文件，防止路径穿越移文件
        if (!is_within_storage($old_path)) {
            return false;
        }

        $name = $urlPath['path'];                           // 获得图片的相对地址
        $name = str_replace($config['path'] ?? '', '', $name); // 去除 path目录
        $name = str_replace('/', '_', $name);                  // 文件名 2021_10_30_p8vypd.png
        $new_path = APP_ROOT . $config['path'] . $dir . $name; // 新路径含文件名
        $suspic_dir = APP_ROOT . $config['path'] . $dir;       // suspic路径

        if (!is_dir($suspic_dir)) {                            // 创建suspic目录并移动
            @mkdir($suspic_dir, 0755, true);
        }
        if (is_file($old_path)) {
            rename($old_path, $new_path);

            // FTP
            if (isset($config['ftp_status']) && $config['ftp_status'] === 1) {
                any_upload($urlPath['path'], $config['path'] . $dir . $name, 'rename');
            }

            return true;
        } else {
            return false;
        }
    }
}

/**
 * 还原被审查的图片
 * @param $name string 要还原的图片
 */
function re_checkImg($name, $dir = 'suspic/')
{
    global $config;

    $name = (string) $name;
    // 待还原的图片名只能是扁平文件名，防止路径穿越
    if ($name === '' || strpos($name, '/') !== false || strpos($name, '\\') !== false || strpos($name, '..') !== false) {
        return false;
    }

    $fileToPath = str_replace('_', '/', $name);                 // 将图片名称还原为带路径的名称，eg:2021_11_03_pbmn1a.jpg =>2021-11-03/pbmn1a.jpg
    $now_path_file = APP_ROOT . $config['path'] . $dir . $name; // 当前图片绝对位置 */i/suspic/2021_10_30_p8vypd.png
    if (is_file($now_path_file)) {
        $to_file = APP_ROOT . $config['path'] . $fileToPath;    // 要还原图片的绝对位置 */i/2021/10/30/p8vypd.png
        rename($now_path_file, $to_file);                       // 移动文件
        // FTP
        if (isset($config['ftp_status']) && $config['ftp_status'] === 1) {
            any_upload($config['path'] . $dir . $name, $config['path'] . $fileToPath, 'rename');
        }
        return true;
    }
}

/**
 * 创建缩略图
 * @param $imgName string 需要创建缩略图的名称
 */
function creat_thumbnail_images($imgName)
{
    require_once __DIR__ . '/class.thumb.php';
    global $config;

    $old_img_path = APP_ROOT . config_path() . $imgName;                                          // 获取要创建缩略图文件的绝对路径
    $cache_path = APP_ROOT . ($config['path'] ?? '') . 'cache/';                                   // cache目录的绝对路径

    if (!is_dir($cache_path)) {                                                                   // 创建cache目录
        @mkdir($cache_path, 0755, true);
    }
    if (!isGifAnimated($old_img_path)) {                                                          // 仅针对非gif创建图片缩略图
        $new_imgName = APP_ROOT . $config['path'] . 'cache/' . date('Y_m_d') . '_' . $imgName;    // 缩略图缓存的绝对路径
        Thumb::out($old_img_path, $new_imgName, $config['thumbnail_w'] ?? 0, $config['thumbnail_h'] ?? 0);  // 保存缩略图
    }
}

/**
 * 根据请求网址路径返回缩略图网址
 * @param $url string 图片链接
 * @return string
 */
function return_thumbnail_images($url)
{
    global $config;
    $cache_image_file = str_replace($config['imgurl'] ?? '', '', (string) $url);

    if (isGifAnimated(APP_ROOT . $cache_image_file)) {                                 // 仅读取非gif的缩略图
        return $url;                                                                   // 如果是gif则直接返回url
    } else {
        $cache_image_file = str_replace($config['path'] ?? '', '', $cache_image_file);   // 将网址中的/i/去除
        $cache_image_file = str_replace('/', '_', $cache_image_file);                  // 将文件的/转换为_
        $isFile = APP_ROOT . $config['path'] . 'cache/' . $cache_image_file;           // 缓存文件的绝对路径
        if (file_exists($isFile)) {                                                    // 缓存文件是否存在
            return $config['imgurl'] . $config['path'] . 'cache/' . $cache_image_file; // 存在则返回缓存文件
        } else {
            return $url;                                                                    // 不存在直接返回url
        }
    }
}

/**
 * 在线输出缩略图
 * @param $imgUrl string 图片链接
 * @return string 缩略图链接
 */
function get_online_thumbnail($imgUrl)
{
    global $config;
    if (!empty($config['thumbnail'])) {
        $imgUrl = str_replace($config['domain'] ?? '', '', (string) $imgUrl);
        return ($config['domain'] ?? '') . '/app/thumb.php?img=' . $imgUrl;
    }

    return $imgUrl;
}

/**
 * 用户浏览广场的时候生成缩略图，由此解决上传生成缩略图时服务器超时响应
 * @param $imgUrl string 源图片网址
 * @return string 缩略图地址
 */
function creat_thumbnail_by_list($imgUrl)
{

    global $config;
    ini_set('max_execution_time', '300'); // 脚本运行的时间（以秒为单位）0不限制

    $extension = strtolower(pathinfo((string) $imgUrl, PATHINFO_EXTENSION));
    // 过滤非指定格式（GD/Thumb 不支持 tif/tga，直接回落到通用文件图标，避免致命错误）
    if (!in_array($extension, array('png', 'gif', 'jpeg', 'jpg', 'webp', 'bmp'), true)) {
        // ico和svg格式直接返回直链
        if ($extension === 'ico' || $extension === 'svg') return $imgUrl;
        // 其他格式直接返回指定图标
        return '../public/images/file.svg';
    }

    switch ($config['thumbnail'] ?? null) {
            // 输出原图
        case 0:
            return $imgUrl;
        // 访问生成
        case 1:
            return get_online_thumbnail($imgUrl);
        // case 2（上传生成）及其余值继续向下走缓存生成分支
    }

    // 将网址图片转换为相对路径（非法/绝对外网 URL 可能取不到 path，此时不生成缩略图）
    $pathName = parse_url((string) $imgUrl, PHP_URL_PATH);
    if (!is_string($pathName) || $pathName === '') {
        return $imgUrl;
    }
    // 图片绝对路径
    $abPathName = APP_ROOT . $pathName;
    // 将网址中的/i/去除
    $pathName = str_replace($config['path'] ?? '', '', $pathName);
    // 将文件的/转换为_
    $thumbnail = str_replace('/', '_', $pathName);

    // 缓存文件是否存在
    if (is_file(APP_ROOT . $config['path'] . 'cache/' . $thumbnail)) {
        // 存在则返回缓存文件
        return $config['domain'] . $config['path'] . 'cache/' . $thumbnail;
    } else {
        // 源文件不存在时不能进入生成分支（旧写法会向 Zebra_Image/Thumb 传入不存在的源路径）
        if (!is_file($abPathName)) {
            return $imgUrl;
        }

        // 如果图像是gif则直接返回网址
        if (isGifAnimated($abPathName)) {
            return $imgUrl;
        }
        // 如果是webp动图则直接返回网址
        if (isWebpAnimated($abPathName)) {
            return $imgUrl;
        }

        /** 创建缓存文件并输出文件链接 */

        // thumbnails目录的绝对路径
        $cache_path = APP_ROOT . $config['path'] . 'cache/';
        // 创建cache目录
        if (!is_dir($cache_path)) {
            mkdir($cache_path, 0755, true);
        }
        // 缩略图缓存的绝对路径 $imgName 是不带/i/的相对路径
        $new_imgName = $cache_path . $thumbnail;
        // 创建并保存缩略图
        if (($config['thumbnail'] ?? 0) == 2) {
            require_once __DIR__ . '/Zebra_Image.php';
            $image = new Zebra_Image();
            $image->source_path = $abPathName;
            $image->target_path = $new_imgName;
            $image->resize($config['thumbnail_w'] ?? 0, $config['thumbnail_h'] ?? 0, ZEBRA_IMAGE_CROP_CENTER);
        } else {
            require_once __DIR__ . '/class.thumb.php';
            Thumb::out($abPathName, $new_imgName, $config['thumbnail_w'] ?? 0, $config['thumbnail_h'] ?? 0);
        }

        // 输出缩略图（返回可访问 URL，而不是服务器绝对路径）
        return $config['domain'] . $config['path'] . 'cache/' . $thumbnail;
    }
}

/**
 * 获取当前页面完整URL地址
 * @param null $search string 剪掉末位出现的该子串及其后内容（常用于去掉脚本名/末尾斜杠）
 * @return string 返回读取网址
 */
function get_whole_url($search = null)
{
    $sys_protocal = (($_SERVER['SERVER_PORT'] ?? '') == '443') ? 'https://' : 'http://';
    $php_self = $_SERVER['SCRIPT_NAME'] ?? '';
    $path_info = $_SERVER['PATH_INFO'] ?? '';
    // REQUEST_URI 已含查询串；不存在时用 SCRIPT_NAME + QUERY_STRING(或 PATH_INFO) 拼出
    $relate_url = $_SERVER['REQUEST_URI'] ?? $php_self . (isset($_SERVER['QUERY_STRING']) ? '?' . $_SERVER['QUERY_STRING'] : $path_info);
    // Host 头由客户端控制，过滤非法字符避免 Host Header 注入到跳转/安装链接
    $host = preg_replace('/[^A-Za-z0-9\.\-:\[\]]/', '', (string) ($_SERVER['HTTP_HOST'] ?? ''));
    $whole_url = $sys_protocal . $host . $relate_url;

    if ($search !== null && $search !== '') {
        // 旧写法 if ($search) 在 $search 为 "0" 时不进入分支；strrpos 返回 0 时又会被 ?: 吞掉，
        // 等价重做为：仅当位置 > 0 时截断，找不到则原样返回（与 substr($s,0,false) 一致）
        $pos = strrpos($whole_url, $search);
        if ($pos !== false && $pos > 0) {
            return substr($whole_url, 0, $pos);
        }
    }
    return $whole_url;
}

/**
 * 配置写入
 * @param $filename string 要存储的源文件名称
 * @param $values array 获取到的数组
 * @param string $var 源文件的数组名称
 * @param bool $format 不知道啥作用
 * @return bool
 */
function cache_write($filename, $values, $var = 'config', $format = false)
{
    $cachefile = $filename;
    $cachetext = "<?php\r\n" . '$' . $var . '=' . arrayeval($values, $format) . ";";
    $result = writefile($cachefile, $cachetext);

    // 清除Opcache缓存
    if (function_exists('opcache_reset')) {
        opcache_reset();
    }

    return $result;
}

/**
 * 数组转换成字串
 * @param array $array 要转换的数组
 * @param bool $format 不知道啥作用
 * @param int $level 层级
 * @return string
 */
function arrayeval($array, $format = false, $level = 0)
{
    $space = $line = '';
    if (!$format) {
        for ($i = 0; $i <= $level; $i++) {
            $space .= "\t";
        }
        $line = "\n";
    }
    $evaluate = 'Array' . $line . $space . '(' . $line;
    $comma = $space;
    foreach ($array as $key => $val) {
        $key = is_string($key) ? '\'' . addcslashes($key, '\'\\') . '\'' : $key;
        // 按类型序列化：布尔/null/浮点不再被 preg_match()/addcslashes() 隐式转换（PHP 8.1+ 弃用告警）
        if (is_array($val)) {
            // 由下方递归处理
        } elseif (is_bool($val)) {
            $val = $val ? 'true' : 'false';
        } elseif ($val === null) {
            $val = 'NULL';
        } elseif (is_int($val)) {
            $val = (string) $val;
        } elseif (is_float($val)) {
            $val = var_export($val, true);
        } elseif (is_string($val) && preg_match('/^-?\d+$/', $val) && strlen($val) <= 12) {
            // 保留数字字面量
        } else {
            $val = '\'' . addcslashes((string) $val, '\'\\') . '\'';
        }
        if (is_array($val)) {
            $evaluate .= $comma . $key . '=>' . arrayeval($val, $format, $level + 1);
        } else {
            $evaluate .= $comma . $key . '=>' . $val;
        }
        $comma = ',' . $line . $space;
    }
    $evaluate .= $line . $space . ')';
    return $evaluate;
}

/**
 * 配置写入文件
 * @param $filename string 要写入的文件名
 * @param $writetext array 要写入的文字
 * @param string $openmod 写文件模式（仅保留形参，当前唯一调用点 cache_write() 只用默认 'w'）
 * @return bool
 */
function writefile($filename, $writetext, $openmod = 'w')
{
    // file_put_contents 先加锁再写，修正了旧写法 fopen('w') 先截断后 flock 的竞争窗口
    return @file_put_contents($filename, $writetext, LOCK_EX) !== false;
}

/**
 * 获得用户的真实IP地址
 * 来源：ecshop
 * @return mixed|string string
 */
function real_ip()
{
    static $realip = NULL;
    if ($realip !== NULL) {
        return $realip;
    }

    // 直连对端地址永远是可信来源
    $remote = $_SERVER['REMOTE_ADDR'] ?? (getenv('REMOTE_ADDR') ?: '0.0.0.0');
    $realip = $remote;

    // 仅当直连对端是内网/回环地址（即前方存在可信反向代理）时才采信转发头，
    // 防止互联网客户端直接伪造 X-Forwarded-For 绕过 IP 黑白名单与游客上传限额。
    $remoteIsValid = filter_var($remote, FILTER_VALIDATE_IP) !== false;
    $peerIsPrivate = $remoteIsValid && filter_var(
        $remote,
        FILTER_VALIDATE_IP,
        FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE
    ) === false;
    if ($peerIsPrivate) {
        $candidates = array();
        if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $candidates = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
        } elseif (!empty($_SERVER['HTTP_CLIENT_IP'])) {
            $candidates = array($_SERVER['HTTP_CLIENT_IP']);
        }
        foreach ($candidates as $ip) {
            $ip = trim($ip);
            if (filter_var($ip, FILTER_VALIDATE_IP)) {
                $realip = $ip;
                break;
            }
        }
    }

    // 最终仍不合法时回退为不可路由地址
    if (!filter_var($realip, FILTER_VALIDATE_IP)) {
        $realip = '0.0.0.0';
    }
    return $realip;
}

/**
 * IP黑白名单检测，支持IP段（通配符）检测
 * @param string|null $ipNow 要检测的IP，null 则取 real_ip()
 * @param string|array $ipList 白名单IP或者黑名单IP（逗号分隔）
 * @param bool $model 白名单模式 true / 黑名单模式 false
 * @return bool true: 应拦截（黑名单命中 或 白名单未命中）；false: 放行
 */
function checkIP($ipNow = null, $ipList = null, $model = false)
{
    // 旧写法 `isset($ipNow) ?: real_ip()` 是裸 Elvis，结果是布尔值，调用方传 null 时拿 true 去比对，名单形同虚设
    $ipNow = $ipNow ?? real_ip();
    $ipNow = (string) $ipNow;

    // 将IP文本转换为数组（旧写法把数组入参当错误报出）
    if (is_string($ipList)) {
        $ipList = explode(',', $ipList);
    } elseif (!is_array($ipList)) {
        $ipList = array();
    }

    // 去掉首尾空白与空项，空名单不再生成 `/^()$/` 这种能匹配空串的正则
    $ipList = array_values(array_filter(array_map('trim', $ipList), function ($item) {
        return $item !== '';
    }));
    if ($ipList === array()) {
        return false;
    }

    // 先 preg_quote 再还原通配符：避免名单中的 + ( ) [ ] 等元字符破坏正则或触发 warning
    $ipregexp = implode('|', array_map(function ($item) {
        return str_replace('\*', '\d+', preg_quote($item, '/'));
    }, $ipList));
    // 用 / 作正则定界符（旧写法 "^(...)"$" 首字符即引号被当分隔符，导致 preg_match 报“No ending delimiter”而使名单彻底失效）
    $matched = preg_match('/^(' . $ipregexp . ')$/', $ipNow);

    // 白名单模式：名单内（含通配段）放行，名单外拦截
    if ($model) {
        return $matched ? false : true;
    }
    // 黑名单模式：命中拦截
    return $matched ? true : false;
}

/**
 * 测试IP或者url是否可以连通（TCP 端口探测，非 ICMP ping）
 * @param $host string ip或网址
 * @param $port int 端口
 * @param $timeout float 过期时间
 * @return bool true|false
 */
function IP_URL_Ping($host, $port, $timeout)
{
    $errno = 0;
    $errstr = '';
    // stream_socket_client 与 fsockopen 等价，且支持浮点超时与更多 transport 选项
    $fP = @stream_socket_client('tcp://' . $host . ':' . $port, $errno, $errstr, (float) $timeout);
    if (!$fP) {
        return false;
    }
    fclose($fP);
    return true;
}

/**
 * 生成Token
 * @param int $length Token长度
 * @return string 返回Token
 */
function privateToken($length = 32)
{
    // 旧写法用非 CSPRNG 的 mt_rand 拼字符（chr(65..122) 还会冒出 [\]^_` 等符号）后再 md5，长度参数实际无效
    return substr(bin2hex(random_bytes(32)), 0, max(1, (int) $length));
}

/**
 * 检查Token
 * @param $token 要检查的Token
 * @return string 201 访问成功但是服务端关闭API上传
 * @return string 202 访问成功但是Token错误
 */
function check_api($token)
{
    global $config;
    global $tokenList;

    if (empty($config['apiStatus'])) {
        // 关闭API
        $reJson = array(
            "result" => 'failed',
            'code' => 201,
            'message' => 'API Closed',
        );
        exit(json_encode($reJson, JSON_UNESCAPED_UNICODE));
    }

    // $tokenList 结构是 token => 元数据数组，旧写法 in_array() 拿元数据去全值里找自己，仅靠 PHP7 数组 == 比自身为 true 侥幸成立
    if (!is_array($tokenList) || !array_key_exists($token, $tokenList)) {
        // Token 不存在
        $reJson = array(
            "result" => 'failed',
            'code' => 202,
            'message' => 'Token Error',
        );
        exit(json_encode($reJson, JSON_UNESCAPED_UNICODE));
    }

    if (($tokenList[$token]['expired'] ?? 0) < time()) {
        // Token 过期
        $reJson = array(
            "result" => 'failed',
            'code' => 203,
            'message' => 'Token Expired',
        );
        exit(json_encode($reJson, JSON_UNESCAPED_UNICODE));
    }
}

/**
 * 根据URL判断是否本地局域网访问(PHP代码函数)
 * https://blog.csdn.net/monxinmonxin0/article/details/44854383
 * @param string $url 要判断的网址
 * @return bool 是|否
 */
function is_local($url)
{
    if (stristr($url, '://localhost') || stristr($url, '://127.') || stristr($url, '://192.') || stristr($url, '://10.')) return true;

    return false;
}

/**
 * 将图片域名转换为数组并随即输出
 * @param string $text 字符串
 * @return String 随机网址
 */
function rand_imgurl($text = null)
{
    global $config;
    $url = $text ?? ($config['imgurl'] ?? '');
    $url = explode(',', $url);
    return $url[array_rand($url, 1)];
}

/**
 * 获取当前版本号 | 读取字符串
 * @param String $file 文件相对路径
 * @return String 内容信息
 */
function get_current_version($file = '/admin/version.php')
{
    $file = APP_ROOT . (string) $file;

    if (is_file($file)) {
        return file_get_contents($file);
    }

    return 'file does not exist';
}

/**
 * 压缩图片 process 模式
 * @param String $absolutePath 图片绝对路径
 */
function process_compress($absolutePath)
{
    global $config;

    if (!empty($config['compress'])) {
        if (!is_Gif_Webp_Animated($absolutePath)) {
            require_once __DIR__ . '/compress/Imagick/class.Imgcompress.php';
            $percent = ($config['compress_ratio'] ?? 100) / 100; // 压缩率
            $img = new Imgcompress($absolutePath, $percent);
            $img->compressImg($absolutePath);
            // 释放（无活动缓冲层时直接调 ob_flush() 会报 warning）
            while (ob_get_level() > 0) {
                ob_end_flush();
            }
            flush();
        }
    }
}

/**
 * 设置水印 process 模式
 * @param String $source 图片路径
 */
function water($source)
{
    global $config;

    $watermark = $config['watermark'] ?? null;

    // 文字水印
    if ($watermark === 1) {
        // 过滤gif
        if (!is_Gif_Webp_Animated($source)) {
            $arr = [
                # 水印图片路径（如果不存在将会被当成是字符串水印）
                'res' => $config['waterText'] ?? '',
                # 水印显示位置
                'pos' => $config['waterPosition'] ?? 0,
                # 不指定name(会覆盖原图，也就是保存成thumb.jpeg)
                'name' => $source,
                'font' => APP_ROOT . ($config['textFont'] ?? ''),
                'fontSize' => $config['textSize'] ?? 0,
                'color' => str_replace(array('rgba', '(', ')'), '', $config['textColor'] ?? ''),
            ];
            Imgs::setWater($source, $arr);
        }
    }

    // 图片水印
    if ($watermark === 2) {
        // 过滤gif
        if (!is_Gif_Webp_Animated($source)) {
            $arr = [
                #  水印图片路径（如果不存在将会被当成是字符串水印）
                'res' => APP_ROOT . ($config['waterImg'] ?? ''),
                #  水印显示位置
                'pos' => $config['waterPosition'] ?? 0,
                #  不指定name(会覆盖原图，也就是保存成thumb.jpeg)
                'name' => $source,
            ];
            Imgs::setWater($source, $arr);
        }
    }
}
/**
 * 图片违规检查 process
 * @param String $imgurl 图片链接
 */
function process_checkImg($imgurl)
{
    global $config;

    $checkImg = $config['checkImg'] ?? null;

    if ($checkImg == 1) {
        checkImg($imgurl, 1);
    }

    if ($checkImg == 2) {
        checkImg($imgurl, 2);
    }
}

/**
 * 写日志
 * 
 * 格式：
 * {
 *  上传图片名称{
 *      source:源文件名称,
 *      date:上传日期(Asia/Shanghai),
 *      ip:上传者IP,port:IP端口,
 *      user_agent:上传者浏览器信息,
 *      path:文件相对路径,
 *      size:文件大小(格式化),
 *      md5:文件MD5,
 *      checkImg:鉴黄状态,
 *      form:上传方式web/API ID
 *  }
 * }
 * 
 * $filePath 文件相对路径
 * $sourceName 源文件名称
 * $absolutePath 图片的绝对路径
 * $fileSize 图片的大小
 * $form 来源如果是网页上传直接显示网页,如果是API上传则显示ID
 */
function write_upload_logs($filePath, $sourceName, $absolutePath, $fileSize, $from = "web")
{
    global $config;

    if (empty($config['upload_logs'])) {
        return null;
    }

    $checkImg = $config['checkImg'] == true ? "ON" : "OFF";

    // $name = trim(basename($filePath), " \t\n\r\0\x0B"); // 当前图片名称
    $log = array(basename($filePath) => array(             // 以上传图片名称为Array
        // 原始文件名称/UA 均由客户端控制：存储时只做长度与控制字符收敛，展示端统一做 HTML 转义
        'source'     => substr(strip_tags(str_replace(array("\r", "\n"), ' ', (string) $sourceName)), 0, 255),
        'date'       => date('Y-m-d H:i:s'),               // 上传日期
        'ip'         => real_ip(),                         // 上传IP
        'port'       => $_SERVER['REMOTE_PORT'] ?? '',     // IP端口（CLI/局部代理下可能不存在）
        'user_agent' => preg_replace('/[\x00-\x1F\x7F]/', ' ', substr((string) ($_SERVER['HTTP_USER_AGENT'] ?? ''), 0, 500)),
        'path'       => $filePath,                         // 文件相对路径
        'size'       => getDistUsed($fileSize),            // 文件大小(格式化)
        'md5'        => md5_file($absolutePath),           // 文件的md5
        'checkImg'   => $checkImg,                         // 鉴黄状态
        'from'       => $from,                             // 图片上传来源
    ));

    // 创建日志文件夹
    if (!is_dir(APP_ROOT . '/admin/logs/upload/')) {
        mkdir(APP_ROOT . '/admin/logs/upload', 0755, true);
    }

    // logs文件组成
    $logFileName = APP_ROOT . '/admin/logs/upload/' . date('Y-m') . '.php';

    // 创建logs文件
    if (!is_file($logFileName)) {
        file_put_contents($logFileName, '<?php $logs=Array();?>');
    }

    // 引入logs
    include $logFileName;

    // // 写入禁止浏览器直接访问
    // if (filesize($logFileName) == 0) {
    //     $php_exit = '<?php /** {图片名称{source:源文件名称,date:上传日期(Asia/Shanghai),ip:上传者IP,port:IP端口,user_agent:上传者浏览器信息,path:文件相对路径,size:文件大小(格式化),md5:文件MD5,checkImg:鉴黄状态,form:上传方式web/API ID}} */ exit;? >';
    //     file_put_contents($logFileName, $php_exit);
    // }

    // $log = json_encode($log, JSON_UNESCAPED_UNICODE);
    // file_put_contents($logFileName, PHP_EOL . $log, FILE_APPEND | LOCK_EX);

    // $logs 由 include 的日志文件注入，文件被清空/损坏时会是未定义变量
    $log = array_replace((array) ($logs ?? array()), $log);
    cache_write($logFileName, $log, 'logs');
}

/**
 * IP地址查询
 * @param int $ip IP地址
 */
function ip2region($IP)
{
    if (!is_file(__DIR__ . '/ip2region/ip2region.xdb')) return '<a href="https://raw.githubusercontent.com/lionsoul2014/ip2region/v3.17.0/data/ip2region_v4.xdb" target="_blank"><span class="label label-primary" data-toggle="tooltip" title="点击下载 IP数据库 并上传到<br/><code>/app/ip2region/</code>">IP数据库不存在</span></a>';

    try {
        require_once __DIR__ . '/ip2region/Ip2Region.php';
        $ip2region = new Ip2Region();
        /* 显示完整信息 
        $location = $ip2region->memorySearch($IP); 
        $location = $location['region'];        
        $location =  str_replace(array('0', '||'), '', $location);
        */
        return $ip2region->simple($IP); // 显示简易信息
    } catch (Throwable $e) {
        // 旧写法只 catch Exception，引擎级 Error（TypeError 等）会穿透造成白页
        return $e->getMessage();
    }
}

/**
 * 记录同IP每日上传次数 process
 */
function write_ip_upload_count_logs()
{
    global $config;

    if (($config['ip_upload_counts'] ?? 0) > 0) {
        // 上传IP地址        
        $ip = real_ip();
        // 日志目录
        $dir = APP_ROOT . '/admin/logs/ipcounts/';
        // 日志文件
        $file = $dir . date('Y-m-d') . '.php';
        // 创建日志目录
        if (!is_dir($dir)) mkdir($dir, 0777);
        // 创建日志文件
        if (!is_file($file)) file_put_contents($file, '<?php $ipcounts=array(); ?>' . PHP_EOL, FILE_APPEND | LOCK_EX);
        // 引入日志
        require $file;
        // $ipcounts/$info 旧写法未初始化，日志文件被清空时会读未定义变量
        $ipcounts = (array) ($ipcounts ?? array());
        $info = array();
        // 获取存在的IP
        if (isset($ipcounts[$ip])) {
            $info[$ip] = $ipcounts[$ip] + 1;
        } else {
            $info[$ip] = 1;
        }
        // 写入日志
        $info = array_replace($ipcounts, $info);
        cache_write($file, $info, 'ipcounts');
    }
}
/**
 * 限制IP每日上传次数
 * @param Sting $ip IP地址
 */
function get_ip_upload_log_counts($ip)
{
    global $config;
    $file = APP_ROOT . '/admin/logs/ipcounts/' . date('Y-m-d') . '.php';

    if (!is_file($file)) return true;
    require_once $file;

    $ipcounts = (array) ($ipcounts ?? array());
    if (!isset($ipcounts[$ip])) return true;
    if ($ipcounts[$ip] >= ($config['ip_upload_counts'] ?? 0)) return false;

    return true;
}

/**
 * 自动删除
 * 开启自动删除后会先重命名文件夹作为备份
 * 自动删除日期超过定时的2倍时间后再彻底删除重命名的文件夹
 */
function auto_delete()
{
    global $config;
    // 旧写法未限定类型，后台写回字符串时 PHP 8 下 -$config['auto_delete'] 会直接抛 TypeError
    $auto_delete = (int) ($config['auto_delete'] ?? 0);
    if ($auto_delete > 0 && ($config['storage_path'] ?? '') == 'Y/m/d/') {
        $path = $config['path'] ?? '';

        /** 重命名要删除的文件夹 */
        $Odir = APP_ROOT . $path . date('Y/m/d', strtotime('-' . $auto_delete . ' day')); // 重命名文件夹路径
        $Rdir = APP_ROOT . $path . date('Y/m/d', strtotime('-' . $auto_delete . ' day')) . '_auto_delete'; // 新命名文件夹路径
        if (is_dir($Odir)) { // 执行重命名
            rename($Odir, $Rdir);
            $log = $Rdir . ' rename succees ' . date('Y-m-d H:i:s');
        } else {
            $log = $Rdir . ' rename failed ' . date('Y-m-d H:i:s');
        }

        /**  以定时的2倍倒叙时间删除已经重命名的文件夹路径 */
        $Ddir = APP_ROOT . $path . date('Y/m/d', strtotime('-' . ($auto_delete * 2) . ' day')) . '_auto_delete'; // 文件夹路径
        if (is_dir($Ddir)) { // 执行删除
            try {
                if (deldir($Ddir)) {
                    $log = $Ddir . ' delete succees ' . date('Y-m-d H:i:s');
                } else {
                    throw new Exception($Ddir . ' delete failed ' . date('Y-m-d H:i:s'));
                }
            } catch (Exception $e) {
                $log = $e->getMessage();
            }
        }

        /** 创建日志文件夹及文件 */
        if (!is_dir(APP_ROOT . '/admin/logs/tasks/')) {
            mkdir(APP_ROOT . '/admin/logs/tasks/', 0755, true);
        }

        if (!is_file(APP_ROOT . '/admin/logs/tasks/auto_delete.php')) {
            file_put_contents(APP_ROOT . '/admin/logs/tasks/auto_delete.php', '<?php /** 自动删除日志 */ return; ?>' . PHP_EOL, FILE_APPEND | LOCK_EX);
        }

        // 写入日志
        file_put_contents(APP_ROOT . '/admin/logs/tasks/auto_delete.php', $log . PHP_EOL, FILE_APPEND | LOCK_EX);
        return true;
    }
    return false;
}

/**
 * 登录日志
 * @param String $user 用户
 * @param String $msg  提示
 */
function write_login_log($user, $msg)
{
    $log_path = APP_ROOT . '/admin/logs/login/';
    $log_file = $log_path . date('/Y-m-') . 'logs.php';

    /** 创建日志文件夹及文件 */
    if (!is_dir($log_path)) mkdir($log_path, 0755, true);
    if (!is_file($log_file)) file_put_contents($log_file, '<?php /** 登录日志 */ exit; ?>' . PHP_EOL, FILE_APPEND | LOCK_EX);

    // 安全：登录日志不再记录任何口令/摘要（登录校验使用 SHA256 摘要，记录摘要等同泄露可重放凭据）
    $user = str_replace(array("\r", "\n"), ' ', strip_tags((string) $user));
    $msg = str_replace(array("\r", "\n"), ' ', strip_tags((string) $msg));
    $log = htmlentities(date('Y-m-d H:i:s') . ' IP: ' . real_ip() . ' 账号: ' . $user . ' 提示: ' . $msg, ENT_QUOTES, 'UTF-8');
    file_put_contents($log_file, $log . PHP_EOL, FILE_APPEND | LOCK_EX);
}

/**
 * curl检查远程链接是否有效
 * @param String $url 
 * @param return boll 
 */
function validUrl($url)
{
    $ch = curl_init();
    $timeout = 10;
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_HEADER, 1); //将文件的信息作为数据流输出
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); //将获取的信息以字符串返回
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout); //设置等待时间
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); //禁止验证对等证书
    $contents = curl_exec($ch);

    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE); //获取请求状态码
    if (PHP_VERSION_ID < 80500) {
        curl_close($ch);
    }
    if ($http_code != '200') {
        return false;
    }
    return true;
}

/**
 * 其他上传
 * 支持: FTP上传
 * @link https://github.com/dg/ftp-php
 * @param String $remoteFile 远程地址
 * @param String $localFile 本地地址
 * @param String $way 使用方式 upload 上传 | delete 删除 
 */
function any_upload($remoteFile = null, $localFile = null, $way = 'upload')
{
    global $config;

    if (empty($config['ftp_status'])) return null;
    require_once __DIR__ . '/Ftp.php';

    // 登录FTP
    // $ftp 先置 null：旧写法登录失败时只 echo 错误后继续往下跑，后面调用 $ftp->xxx() 直接 Undefined variable 致命
    $ftp = null;
    try {
        $ftp = new Ftp;

        if (($config['ftp_ssl'] ?? null) === 1) {
            $ftp->sslConnect($config['ftp_host'] ?? '', $config['ftp_port'] ?? 21, $config['ftp_time'] ?? 30);
        } else {
            $ftp->connect($config['ftp_host'] ?? '', $config['ftp_port'] ?? 21, $config['ftp_time'] ?? 30);
        }

        $ftp->login($config['ftp_user'] ?? '', $config['ftp_pass'] ?? '');
        $ftp->pasv($config['ftp_pasv'] ?? null);
    } catch (Throwable $e) {
        // 旧写法只捕 FtpException，ftp_connect 失败等抛 Warning/TypeError 时无人接管
        echo 'Error: ', $e->getMessage();
        return null;
    }

    // 获取文件相对目录
    $dir = pathinfo((string) $remoteFile, PATHINFO_DIRNAME);
    // 隐藏上传目录
    if (!empty($config['hide_path'])) {
        $dir = str_replace($config['path'] ?? '', '', $dir);
        $remoteFile = str_replace($config['path'] ?? '', '', (string) $remoteFile);
    }

    switch ($way) {
        case 'upload':
            try {
                // 递归创建目录
                if (!$ftp->isDir($dir)) {
                    $ftp->mkDirRecursive($dir);
                }
                // 上传文件 远端->本地 模式: $config['ftp_mode']
                $ftp->put($remoteFile, $localFile, 2);
            } catch (FtpException $e) {
                echo 'Error: ', $e->getMessage();
            }
            break;
        case 'delete':
            if (($config['ftp_delloc_sync'] ?? null) === 0) return true;
            $ftp->tryDelete($remoteFile);
            break;
        case 'list':
            return $ftp->nList($remoteFile);
            break;
        case 'rename':
            try {
                // 获取文件相对目录
                $dir = pathinfo((string) $localFile, PATHINFO_DIRNAME);
                // 隐藏上传目录
                if (!empty($config['hide_path'])) {
                    $dir = str_replace($config['path'] ?? '', '', $dir);
                    $localFile = str_replace($config['path'] ?? '', '', (string) $localFile);
                }
                // 递归创建目录
                if (!$ftp->isDir($dir)) {
                    $ftp->mkDirRecursive($dir);
                }
                // 此处 $remoteFile为源文件名及路径 $localFile为新文件名及路径
                $ftp->rename($remoteFile, $localFile);
            } catch (FtpException $e) {
                echo 'Error: ', $e->getMessage();
            }
            break;
    }
    // 关闭FTP
    $ftp->close();

    // FTP上传后是否删除本地文件
    if (!empty($config['ftp_complete_del_local'])) {
        @unlink($localFile);
    }
}

/**
 * 分片上传
 * @param $target_name 名称
 * @return string 合并后的文件绝对路径
 */
function chunk($target_name)
{
    global $config;
    // 分片名只能为扁平名，旧写法直接拼接 $_POST['name']，存在路径穿越与写非预期位置风险
    if (is_array($target_name)) {
        die('Invalid input');
    }
    $target_name = basename(str_replace('\\', '/', (string) $target_name));
    if ($target_name === '' || $target_name === '.' || $target_name === '..' || strpos($target_name, "\0") !== false) {
        die('Invalid input');
    }
    $path = $config['path'] ?? '';
    // 分片缓存根目录（新装站点可能尚未创建 cache 目录）
    $cache_dir = APP_ROOT . $path . 'cache/';
    // 分片缓存目录
    $temp_dir = $cache_dir . $target_name . '/';
    // 分片合并后的文件
    $target_file = $cache_dir . $target_name;
    // 储存分片
    if (!is_dir($temp_dir)) mkdir($temp_dir, 0755, true);
    // 检查分片参数（$_REQUEST 键可能不存在，先取再判）
    $chunk = $_REQUEST['chunk'] ?? null;
    $chunks = $_REQUEST['chunks'] ?? null;
    if (!is_numeric($chunk) || !is_numeric($chunks) || (int) $chunks < 1) {
        die('Invalid input');
    }
    $chunk = (int) $chunk;
    $chunks = (int) $chunks;
    // 移动缓存分片（分片文件缺失时不再继续，避免后续合并出空文件）
    if (!isset($_FILES['file']['tmp_name']) || !is_uploaded_file($_FILES['file']['tmp_name'])
        || !move_uploaded_file($_FILES['file']['tmp_name'], $temp_dir . $chunk)) {
        die('Invalid input');
    }
    // 合并分片
    if ($chunk === $chunks - 1) { // 最后一个分片
        // 旧写法 fopen($target_name) 用相对路径（落到当前工作目录），且先 mkdir 了同名目录导致 fopen 必败
        if (!is_dir($cache_dir)) mkdir($cache_dir, 0755, true);
        $handle = @fopen($target_file, 'w');
        if ($handle === false) {
            die('Invalid input');
        }
        for ($i = 0; $i < $chunks; $i++) {
            $piece = $temp_dir . $i;
            if (is_file($piece)) {
                fwrite($handle, (string) file_get_contents($piece));
            }
        }
        fclose($handle);
        deldir($temp_dir); // 删除临时目录
    }
    return $target_file;
}
