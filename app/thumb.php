<?php
/*
// +----------------------------------------------------------------------

// | 把大图缩略到缩略图指定的范围内，不留白（原图会剪切掉不符合比例的右边和下边）

// | https://www.php.cn/php-ask-458473.html

// +----------------------------------------------------------------------

require_once __DIR__ . '/function.php';
require_once __DIR__ . '/class.thumb.php';

$src = isset($_GET['img']) ? APP_ROOT . $_GET['img'] : APP_ROOT . '/public/images/404.png'; // 原图路径

if (!file_exists($src)) {
    exit('image does not exist');
}

$w = isset($_GET['width']) ? $_GET['width'] : 258; // 预生成缩略图的宽

$h = isset($_GET['height']) ? $_GET['height'] : 258; // 预生成缩略图的高

Thumb::show($src, $w, $h);
*/

/**
 * 使用新的TimThumb.php生成缩略图
 * TimThumb.php EasyImage修改版 by Icret
 * form https://github.com/podipod/TimThumb
 * 2022-1-30 06:35:08
 *
 * TimThumb参数指南
 * 命令     作用        参数                描述
 * src	    源文件      图像URL			    告诉TimThumb调整哪个图片
 * w	    宽度		宽度调整			调整输出图像的宽度
 * h	    高度		高度调整			调整输出图像的高度
 * q	    质量		0-100               压缩质量，值越大质量越高。不建议高于95
 * a	    对齐	    c, t, l, r, b, tl, tr, bl, br	图像对齐。 c = center, t = top, b = bottom, r = right, l = left。 可以创建对角位置
 * zc	    缩放/裁剪	0、1、2、3	0：     根据传入的值进行缩放（不裁剪）， 1：以最合适的比例裁剪和调整大小（裁剪）， 2：按比例调整大小，并添加边框（裁剪），3：按比例调整大小，不添加边框（裁剪）
 * f	    过滤器      太多了			    可以改变亮度/对比度;甚至模糊图像
 * s	    锐化		锐化				使得按比例缩小图片看起来有点;更清晰
 * cc	    画布颜色	#ffffff         	改变背景颜色。 大多数更改缩放和作物设置时使用,进而可以添加图像边界。
 * ct	    画布透明度	true (1)	        使用透明而忽略背景颜色
 */

require_once __DIR__ . '/function.php';

// 缓存时间
$cache_freq = $config['cache_freq'] * 60 * 60;

// 中文翻译 https://my.oschina.net/whrlmc/blog/81739
define('LOCAL_FILE_BASE_DIRECTORY', APP_ROOT);
define('MEMORY_LIMIT', '256M');
define('DEFAULT_WIDTH', $config['thumbnail_w']);
define('DEFAULT_HEIGHT', $config['thumbnail_h']);
define('FILE_CACHE_PREFIX', 'EasyImage');
define('DEFAULT_ZC', 0);

define('MAX_WIDTH', 10240);
define('MAX_HEIGHT', 10240);
define('FILE_CACHE_DIRECTORY', APP_ROOT . $config['path'] . 'cache/');
define('NOT_FOUND_IMAGE', $config['domain'] . '/public/images/404.png');
define('ERROR_IMAGE', $config['domain'] . '/public/images/404.png');
define('DISPLAY_ERROR_MESSAGES', false);
define('MAX_FILE_SIZE', $config['maxSize']);             // 10 Megs 是 10485760。这是我们将处理的最大内部或外部文件大小。
define('FILE_CACHE_TIME_BETWEEN_CLEANS', $cache_freq);   // 多久清理一次缓存
define('FILE_CACHE_MAX_FILE_AGE', $cache_freq);          // 文件必须从缓存中删除多长时间
define('BROWSER_CACHE_MAX_AGE', $cache_freq);            // 浏览器缓存时间

global $ALLOWED_SITES;
$ALLOWED_SITES = array(
    $config['domain'],
    $config['imgurl'],
);

/**
 * 修复无法生成生成webp动态图片的缩略图bug
 */
if (isset($_GET['img'])) {

    // 引入文件
    require_once __DIR__ . '/TimThumb.php';
    $src = $_GET['img'];

    // 重定向不包含存储路径的缩略图地址
    if (!stristr($src, $config['path'])) {
        $src = $config['path'] . $src;
        header("Location:thumb.php?img=$src");
        exit();
    }

    // 图片绝对路径
    $src = APP_ROOT . $_GET['img'];
    // 获取文件后缀
    $ext =  pathinfo($src)['extension'];
    // 404 文件
    $i404 = APP_ROOT . '/public/images/404.png';

    // 文件不存在
    if (!is_file($src)) {
        // 输出404
        header("Content-type: image/png");
        exit(file_get_contents($i404, true));
    }

    switch ($ext) {
        case 'ico':
            header("Content-type: image/jpeg");
            exit(file_get_contents($src, true));
            break;
        case 'svg':
            header('Content-Type:image/svg+xml');
            exit(file_get_contents($src, true));
            break;
        case 'webp':
            if (isWebpAnimated($src)) {
                // 输出动态的webp
                header("Content-type: image/webp");
                exit(file_get_contents($src, true));
            } else {
                timthumb::start();
            }
            break;
        default:
            timthumb::start();
    }
} else {
    // 输出404
    header("Content-type: image/png");
    exit(file_get_contents($i404, true));
}
