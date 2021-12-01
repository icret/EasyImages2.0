<?php
require_once __DIR__.'/../config/base.php';
require_once APP_ROOT . '/config/config.php';

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
			header("refresh:1;"); // 1s后刷新当前页面
		} else {	// 密码错误
			echo '
			<script> new $.zui.Messager("密码错误", {type: "danger" // 定义颜色主题 
			}).show();</script>';
			//exit(include __DIR__ . '/login.php');
			exit(header("refresh:1;"));
		}
	} elseif (isset($_COOKIE['admin'])) {	// cookie正确
		if ($_COOKIE['admin'] == $md5Pwd) {
		} else {	// cookie错误
			echo '
			<script> new $.zui.Messager("密码已更改，请重新登录", {type: "special" // 定义颜色主题 
			}).show();</script>';
			//header('loction:login.php');
			exit(include __DIR__ . '/login.php');
		}
	} else {	// 无登录无cookie
		echo '
			<script> new $.zui.Messager("请登录后再上传！", {type: "danger" // 定义颜色主题 
			}).show();</script>';
		//header('loction:login.php');
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
function config_path($path = null)
{
	global $config;
	// php5.6 兼容写法：
	$path = isset($path) ? $path : date('Y/m/d/');
	// php7.0 $path = $path ?? date('Y/m/d/');
	$img_path = $config['path'] . $path;

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
	global $config;
	$style = $config['imgName'];

	function create_guid()	// guid生成函数
	{
		if (function_exists('com_create_guid') === true) {
			return trim(com_create_guid(), '{}');
		}

		return strtolower(sprintf('%04X%04X-%04X-%04X-%04X-%04X%04X%04X', mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(16384, 20479), mt_rand(32768, 49151), mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(0, 65535)));
	}

	switch ($style) {
		case "date":	// 以上传时间 例：192704
			return date("His");
			break;
		case "unix":	// 以Unix时间 例：1635074840
			return time();
			break;
		case "uniqid":	// 基于以微秒计的当前时间 例：6175436c73418
			return uniqid(true);
			break;
		case "guid":	// 全球唯一标识符 例：6EDAD0CC-AB0C-4F61-BCCA-05FAD65BF0FA
			return create_guid();
			break;
		case "md5":	// md5加密时间 例：3888aa69eb321a2b61fcc63520bf6c82
			return md5(microtime());
			break;
		case "sha1":	// sha1加密微秒 例：654faac01499e0cb5fb0e9d78b21e234c63d842a
			return sha1(microtime());
			break;
		default:
			return base_convert(date('His') . mt_rand(1024, 10240), 10, 36);	// 将上传时间+随机数转换为36进制 例：vx77yu
	}
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
		echo $config['static_cdn_url'];
	} else {
		echo $config['domain'];
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
/*
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
*/
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
 * @param $dir 传入一个路径如：/apps/web
 * @return int 返回文件数量
 */
function getFileNumber($dir)
{
	$num = 0;
	$arr = glob($dir);
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


function getdirnum($file)
{
	$dirn = 0; //目录数
	$filen = 0; //文件数
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
	$arr = $_SERVER['SCRIPT_NAME'];
	if (strpos($arr, $url)) {
		return 'active';
	}else {
		return '';
	}
}

/**
 * 加密/解密图片路径
 * @param string $data 要加密的内容
 * @param int $mode =1或0  1解密 0加密
 * 
 */
function urlHash($data, $mode)
{
	global $config;
	$key =  $config['password'];
	$iv = 'sciCuBC7orQtDhTO';
	if ($mode) {
		$decode =  openssl_decrypt(base64_decode($data), "AES-128-CBC", $key, 0, $iv);
		return $decode;
	} else {
		$encode = base64_encode(openssl_encrypt($data, "AES-128-CBC", $key, 0, $iv));
		return $encode;
	}
}

// 删除指定文件
function getDel($url, $type)
{
	global $config;
	// url本地化
	$url = htmlspecialchars(str_replace($config['imgurl'], '', $url));   // 过滤html 获取url path
	$url = urldecode(trim($url));

	if ($type == 'url') {
		$url = $_SERVER['DOCUMENT_ROOT'] . $url;
	}
	if ($type == 'hash') {
		$url = APP_ROOT . $url;
	}


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
 * 检测是否更改默认域名
 * 
 * 检测是否修改默认密码
 */
function checkEnv($mode)
{
	// 初始化安装
	if (!file_exists(APP_ROOT . '/install/install.lock') and file_exists(APP_ROOT . '/install/install.php')) {
		echo '<script type="text/javascript">window.location.href="' . get_whole_url('/') . '/install/index.php"</script>';
		exit;
	}

	if($mode){
		require_once __DIR__.'/check.php';
	}

}


// 前端改变图片长宽
function imgRatio()
{
	global $config;
	if ($config['imgRatio']) {
		$image_x =  $config['image_x'];
		$image_y =  $config['image_y'];
		echo '
		resize:{
			width: ' . $image_x . ',
			height: ' . $image_y . ',
			preserve_headers: false,	// 是否保留图片的元数据
		},
		';
	} else {
		return null;
	}
}

/**
 * 定时获取GitHub 最新版本
 */

function getVersion()
{
	global $config;

	if ($config['checkEnv']) {
		require_once APP_ROOT . '/application/class.version.php';
		// 获取版本地址
		$url = "https://api.github.com/repositories/188228357/releases/latest";
		$getVersion = new getVerson($url);

		$now = date('dH'); // 当前日期时间
		$get_ver_day = array('1006', '2501');   // 检测日期的时间

		foreach ($get_ver_day as $day) {
			if (empty($getVersion->readJson())) { // 不存在就下载
				$getVersion->downJson();
			} else if ($day == $now) { // 是否在需要更新的日期
				$getVersion->downJson();
				/*
			} elseif ($config['version'] == $getVersion->readJson()) { // 版本相同不提示
				return null;
			*/
			} else { // 返回版本
				return $getVersion->readJson();
			}
		}
	} else {
		return null;
	}
}

// 删除非空目录
function deldir($dir)
{
	if (file_exists($dir)) {
		$files = scandir($dir);
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

// curl访问网站并返回解码过的json信息
function get_json($img)
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
	curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/86.0.4240.198 Safari/537.36');
	$output = curl_exec($ch);
	curl_close($ch);
	$output = json_decode($output, true);
	return $output;
}
// 检查图片是否违规
function checkImg($imageUrl)
{
	global $config;

	$response = get_json($imageUrl);
	if ($response['rating_index'] == 3 or $response['predictions']['adult'] > $config['checkImg_value']) { //  (1 = everyone, 2 = teen, 3 = adult)
		//$old_path = APP_ROOT . parse_url($imageUrl)['path'];    		// 提交网址中的文件路径 /i/2021/10/29/p8vypd.png
		$old_path = APP_ROOT . str_replace($config['imgurl'], '', $imageUrl);            // 提交网址中的文件路径 /i/2021/10/29/p8vypd.png
		$name =  date('Y_m_d') . '_' . basename($imageUrl);    			// 文件名 2021_10_30_p8vypd.png
		$new_path = APP_ROOT . $config['path'] . 'suspic/' . $name;     // 新路径含文件名
		$cache_dir = APP_ROOT . $config['path'] . 'suspic/'; 			// suspic路径

		if (is_dir($cache_dir)) {										// 创建suspic目录并移动
			rename($old_path, $new_path);
		} else {
			mkdir($cache_dir, 0777, true);
			rename($old_path, $new_path);
		}
	}
}

// 还原被审查的图片
function re_checkImg($name)
{
	global $config;

	$fileToPath = str_replace('_', '/', $name); 					// 将图片名称还原为带路径的名称，eg:2021_11_03_pbmn1a.jpg =>2021/11/03/pbmn1a.jpg
	$now_path_file = APP_ROOT . $config['path'] . 'suspic/' . $name; // 当前图片绝对位置 */i/suspic/2021_10_30_p8vypd.png
	$to_file = APP_ROOT . $config['path'] . $fileToPath; 	 		// 要还原图片的绝对位置 */i/2021/10/30/p8vypd.png
	rename($now_path_file, $to_file);
}
// 创建缩略图
function creat_cache_images($imgName)
{
	require_once __DIR__ . '/class.thumb.php';
	global $config;

	$old_img_path = APP_ROOT . config_path() . $imgName; 											       // 获取要创建缩略图文件的绝对路径
	$cache_path = APP_ROOT . $config['path'] . 'thumb/';											       // cache目录的绝对路径

    if(!is_dir($cache_path)){                                                                              // 创建cache目录
        mkdir($cache_path, 0777, true);
    }
	if (!isAnimatedGif($old_img_path)) {															       // 仅针对非gif创建图片缩略图
			$new_imgName = APP_ROOT . $config['path'] . 'thumb/' . date('Y_m_d') . '_' . $imgName; 	// 缩略图缓存的绝对路径
			Thumb::out($old_img_path, $new_imgName, 300, 300);									// 保存缩略图
	}
}

// 根据请求网址路径返回缩略图网址
function back_cache_images($url)
{
	global $config;
	$cache_image_file = str_replace($config['imgurl'], '', $url);

	if (isAnimatedGif(APP_ROOT . $cache_image_file)) { 									// 仅读取非gif的缩略图
		return $url; 																	// 如果是gif则直接返回url
	} else {
		$cache_image_file = str_replace($config['path'], '', $cache_image_file); 		// 将网址中的/i/去除
		$cache_image_file = str_replace('/', '_', $cache_image_file);					// 将文件的/转换为_
		$isFile = APP_ROOT . $config['path'] . 'thumb/' . $cache_image_file; 			// 缓存文件的绝对路径
		if (file_exists($isFile)) { 													// 缓存文件是否存在
			return $config['imgurl'] .  $config['path'] . 'thumb/' . $cache_image_file; // 存在则返回缓存文件
		} else {
			return $url;																// 不存在直接返回url
		}
	}
}

/**
 * 获取当前页面完整URL地址
 * 返回 http://localhost/ww/index.php
 * https://www.php.cn/php-weizijiaocheng-28181.html
 * $search 返回指定搜索文字之前的内容(不含搜索文字)
 */

function get_whole_url($search = null)
{
	$sys_protocal = isset($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] == '443' ? 'https://' : 'http://';
	$php_self = $_SERVER['SCRIPT_NAME'] ? $_SERVER['SCRIPT_NAME'] : $_SERVER['SCRIPT_NAME'];
	$path_info = isset($_SERVER['PATH_INFO']) ? $_SERVER['PATH_INFO'] : '';
	$relate_url = isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : $php_self . (isset($_SERVER['QUERY_STRING']) ? '?' . $_SERVER['QUERY_STRING'] : $path_info);
	$whole_domain =  $sys_protocal . (isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : '') . $relate_url;
	if ($search) {
		// 返回指定符号之前
		return substr($whole_domain, 0, strrpos($whole_domain, $search));
	} else {
		return $whole_domain;
	}
}

//写入
function cache_write($filename, $values, $var = 'config', $format = false)
{
	$cachefile = $filename;
	$cachetext = "<?php\r\n" . '$' . $var . '=' . arrayeval($values, $format) . ";";
	return writefile($cachefile, $cachetext);
}

//数组转换成字串 
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
		$val = !is_array($val) && (!preg_match('/^\-?\d+$/', $val) || strlen($val) > 12) ? '\'' . addcslashes($val, '\'\\') . '\'' : $val;
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

//写入文件
function writefile($filename, $writetext, $openmod = 'w')
{
	if (false !== $fp = fopen($filename, $openmod)) {
		flock($fp, 2);
		fwrite($fp, $writetext);
		fclose($fp);
		return true;
	} else {
		return false;
	}
}
