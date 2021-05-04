<?php
require_once __DIR__ . '/../config/config.php';

// 判断GIF图片是否为动态
function isAnimatedGif($filename)
{
	$fp = fopen($filename, 'rb');
	$filecontent = fread($fp, filesize($filename));
	fclose($fp);
	return strpos($filecontent, chr(0x21) . chr(0xff) . chr(0x0b) . 'NETSCAPE2.0') === FALSE ? 0 : 1;
}

// 校验登录
function checkLogin()
{
	global $config;
	$md5Pwd = md5($config['password']);
	if (isset($_POST['password'])) {	// 获取登录密码
		$postPW = $_POST['password'];
		if ($md5Pwd == $postPW) {	// 登录密码正确
			setcookie('admin', $postPW, time() + 3600 * 24 * 14, '/');
			echo '
			<script> new $.zui.Messager("登录成功", {type: "success" // 定义颜色主题 
			}).show();</script>';
			//header("refresh:1;"); // 1s后刷新当前页面
		} else {	// 密码错误
			echo '
			<script> new $.zui.Messager("密码错误", {type: "danger" // 定义颜色主题 
			}).show();</script>';
			exit(include __DIR__ . '/login.php');
		}
	} elseif (isset($_COOKIE['admin'])) {	// cookie正确
		if ($_COOKIE['admin'] == $md5Pwd) {
		} else {	// cookie错误
			echo '
			<script> new $.zui.Messager("密码已更改，请重新登录", {type: "special" // 定义颜色主题 
			}).show();</script>';
			header('loction:login.php');
			exit(include __DIR__ . '/login.php');
		}
	} else {	// 无登录无cookie
		echo '
			<script> new $.zui.Messager("请登录后再上传！", {type: "danger" // 定义颜色主题 
			}).show();</script>';
		header('loction:login.php');
		exit(include __DIR__ . '/login.php');
	}
}

// 仅允许登录后上传
function mustLogin()
{
	global $config;
	if ($config['mustLogin']) {
		checkLogin();
	}
}

// 检查配置文件中目录是否存在是否可写并创建相应目录
function config_path()
{
	global $config;
	$img_path = $config['path'] . date('Y/m/d/');

	if (!is_dir($img_path)) {
		@mkdir($img_path, 0755, true);
	}

	if (!is_writable($img_path)) {
		@chmod($img_path, 0755);
	}

	return $img_path;
}

// 图片命名规则
function imgName()
{
	return base_convert(date('His') . mt_rand(1024, 10240), 10, 36);
}

// 设置广告
function showAD($where)
{
	global $config;
	switch ($where) {
		case 'top':
			if ($config['ad_top']) {
				include(__DIR__ . '/../public/ad/top.html');
			}
			break;
		case 'bot':
			if ($config['ad_bot']) {
				include(__DIR__ . '/../public/ad/bottom.html');
			}
			break;
		default:
			echo '广告函数出错';
			break;
	}
}

// 静态文件CDN
function static_cdn()
{
	global $config;
	if ($config['static_cdn']) {
		// 开启CDN
		return '<link href="//cdn.staticfile.org/zui/1.9.2/css/zui.min.css?v1.9.2" rel="stylesheet">
    <link href="//cdn.staticfile.org/zui/1.9.2/lib/uploader/zui.uploader.min.css?v1.9.2" rel="stylesheet">
    <link href="//cdn.bootcdn.net/ajax/libs/nprogress/0.2.0/nprogress.min.css" rel="stylesheet">
    <script src="//cdn.staticfile.org/jquery/3.6.0/jquery.min.js?v3.6.0"></script>
    <script src="//cdn.staticfile.org/zui/1.9.2/js/zui.min.js?v1.9.2"></script>
    <script src="//cdn.staticfile.org/zui/1.9.2/lib/uploader/zui.uploader.min.js?v1.9.2"></script>
    <script src="//cdn.staticfile.org/qrcodejs/1.0.0/qrcode.min.js"></script>
    <script src="//cdn.staticfile.org/clipboard.js/2.0.8/clipboard.min.js?v2.0.8"></script>
	<script src="//cdn.bootcdn.net/ajax/libs/nprogress/0.2.0/nprogress.min.js"></script>
	<script src="//cdn.jsdelivr.net/gh/icret/EasyImages2.0@2.1.0/public/static/hm.js"></script>	
        ';
	} else {
		// 本地文件
		return '<link href="../public/static/zui/css/zui.min.css?v1.9.2" rel="stylesheet">
    <link href="../public/static/zui/lib/uploader/zui.uploader.min.css?v1.9.2" rel="stylesheet">
	<link href="../public/static/nprogress.min.css?v0.2.0" rel="stylesheet">
    <script src="../public/static/zui/lib/jquery/jquery-3.4.1.min.js?v3.4.1"></script>
    <script src="../public/static/zui/js/zui.min.js?v1.9.2"></script>
    <script src="../public/static/zui/lib/uploader/zui.uploader.min.js?v1.9.2"></script>
    <script src="../public/static/qrcode.min.js?v2.0"></script>	
	<script src="../public/static/hm.js"></script>
    <script src="../public/static/zui/lib/clipboard/clipboard.min.js?vv1.5.5"></script>
	<script src="../public/static/nprogress.min.js"></script>
    ';
	}
}

// 开启tinyfilemanager图片管理
function tinyfilemanager()
{
	global $config;
	if (!$config['tinyfilemanager']) {
		header('Location: ' . $_SERVER["HTTP_REFERER"] . '?manager-closed');
		exit;
	}
}


// 获取允许上传的扩展名
function getExtensions()
{
	global $config;
	$mime = '';
	for ($i = 0; $i < count($config['extensions']); $i++) {
		$mime .= $config['extensions'][$i] . ',';
	}
	return rtrim($mime, ',');
}

// 获取目录大小 如果目录文件较多将很费时
function getDirectorySize($path)
{
	$bytestotal = 0;
	$path = realpath($path);
	if ($path !== false && $path != '' && file_exists($path)) {
		foreach (new RecursiveIteratorIterator(new RecursiveDirectoryIterator($path, FilesystemIterator::SKIP_DOTS)) as $object) {
			$bytestotal += $object->getSize();
		}
	}
	return $bytestotal;
}


/**
 * 获取指定文件夹文件数量
 * @param $url 传入一个路径如：/apps/web
 * @return int 返回文件数量
 */
function getFileNumber($url)
{
	$num = 0;
	$arr = glob($url);
	foreach ($arr as $v) {
		if (is_file($v)) {
			$num++;
		} else {
			$num += getFileNumber($v . "/*");
		}
	}
	return $num;
}


/* 
 * 图片展示页面
 * getDir()取文件夹列表，getFile()取对应文件夹下面的文件列表,二者的区别在于判断有没有“.”后缀的文件，其他都一样
 * 获取文件目录列表,该方法返回数组
 * 调用方法getDir("./dir")……
 */
function getDir($dir)
{
	$dirArray[] = NULL;
	if (false != ($handle = opendir($dir))) {
		$i = 0;
		while (false !== ($file = readdir($handle))) {
			//去掉"“.”、“..”以及带“.xxx”后缀的文件
			if ($file != "." && $file != ".." && !strpos($file, ".")) {
				$dirArray[$i] = $file;
				$i++;
			}
		}
		//关闭句柄
		closedir($handle);
	}
	return $dirArray;
}
// 获取文件列表
function getFile($dir)
{
	$fileArray[] = NULL;
	if (is_dir($dir)) {
		if (false != ($handle = opendir($dir))) {
			$i = 0;
			while (false !== ($file = readdir($handle))) {
				//去掉"“.”、“..”以及带“.xxx”后缀的文件
				if ($file != "." && $file != ".." && strpos($file, ".")) {
					$fileArray[$i] = $file;
					if ($i == 100) {
						break;
					}
					$i++;
				}
			}
			//关闭句柄
			closedir($handle);
		}
	}
	return $fileArray;
}

/* 递归函数实现遍历指定文件下的目录与文件数量
 * 用来统计一个目录下的文件和目录的个数
 * echo "目录数为:{$dirn}<br>";
 * echo "文件数为:{$filen}<br>";
 */
$dirn = 0; //目录数
$filen = 0; //文件数

function getdirnum($file)
{
	global $dirn;
	global $filen;
	$dir = opendir($file);
	while ($filename = readdir($dir)) {
		if ($filename != "." && $filename != "..") {
			$filename = $file . "/" . $filename;
			if (is_dir($filename)) {
				$dirn++;
				getdirnum($filename);
				//递归，就可以查看所有子目录
			} else {
				$filen++;
			}
		}
	}
	closedir($dir);
}

/* 把文件或目录的大小转化为容易读的方式
 * disk_free_space  - 磁盘可用空间(比如填写D盘某文件夹，则会现在D盘剩余空间）
 * disk_total_space — 磁盘总空间(比如填写D盘某文件夹，则会现在D盘总空间）
 */
function getDistUsed($number)
{
	$dw = ''; // 指定文件或目录统计的单位方式
	if ($number > pow(2, 30)) {
		$dw = "GB";
		$number = round($number / pow(2, 30), 2);
	} else if ($number > pow(2, 20)) {
		$dw = "MB";
		$number = round($number / pow(2, 20), 2);
	} else if ($number > pow(2, 10)) {
		$dw = "KB";
		$number = round($number / pow(2, 10), 2);
	} else {
		$dw = "bytes";
	}
	return $number . $dw;
}

// 根据url填写active
function getActive($url)
{
	$arr = $_SERVER['PHP_SELF'];
	if (strpos($arr, $url)) {
		return 'active';
	} elseif (strpos($arr, $url)) {
		return 'active';
	} else {
		return '';
	}
}

/**
 * 加密/解密图片路径
 * @param string $data 要加密的内容
 * @param $mode=1或0  1解密 0加密
 * 
 */
function ulrHash($data, $mode)
{
	global $config;
	$key =  $config['password'];
	if ($mode) {
		$decode =  openssl_decrypt(urldecode($data), "DES-ECB", $key, 0);
		return $decode;
	} else {
		$encode = urlencode(openssl_encrypt($data, "DES-ECB", $key, 0));
		return $encode;
	}
}

// 删除指定文件
function getDel($url)
{
	// url本地化
	$url = htmlspecialchars(parse_url($url)['path']);   // 过滤html 获取url path
	$url = urldecode(trim($url));
	$url = $_SERVER['DOCUMENT_ROOT'] . $url;

	// 文件是否存在
	if (is_file($url)) {
		// 执行删除
		if (@unlink($url)) {
			echo '
			<script>
            new $.zui.Messager("删除成功，请刷新浏览器；如果开启了CDN，请等待缓存失效!", {type: "success" // 定义颜色主题 
            }).show();
			// 延时2s跳转			
            // window.setTimeout("window.location=\'/../ \'",3500);
            </script>
			';
		} else {
			echo '
			<script>
            new $.zui.Messager("删除失败", {type: "black" // 定义颜色主题 
            }).show();
            </script>
			';
		}
	} else {
		echo '
		<script>
		new $.zui.Messager("文件不存在", {type: "danger" // 定义颜色主题 
		}).show();
		</script>
		';
	}
	// 清除查询
	clearstatcache();
}

// 获取登录状态
function is_online()
{
	global $config;
	$md5Pwd = md5($config['password']);
	if (empty($_COOKIE['admin']) || $_COOKIE['admin'] != $md5Pwd) {
		echo false;
	} else {
		return true;
	}
}

/** 
 * 检查PHP缺少简单图床必备的扩展
 * 需检测的扩展：'fileinfo', 'iconv', 'gd', 'mbstring', 'openssl','zip',
 * zip 扩展不是必须的，但会影响tinyfilemanager文件压缩(本次不检测)。
 * 
 * 检测是否修改默认密码
 * 
 * 检测是否更改默认域名
 */
function checkEnv()
{
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
	$url = preg_replace('#^(http(s?))?(://)#', '', 'http://192.168.1.15');
	if (strstr($url, $_SERVER['HTTP_HOST'])) {
		echo '
		<script>
		new $.zui.Messager("请修改默认域名，可能会导致图片访问异常！", {type: "black" // 定义颜色主题 
		}).show();
		</script>
		';
	}
	// 检测是否更改默认密码
	if ($config['password'] === 'admin@123') {
		echo '
		<script>
		new $.zui.Messager("请修改默认密码，否则会有泄露风险！", {type: "warning" // 定义颜色主题 
		}).show();
		</script>
		';
	}
}
