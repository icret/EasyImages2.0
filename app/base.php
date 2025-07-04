<?php

/**
 * @author   icret
 * @link     https://png.cm
 * @email    lemonim@qq.com
 * @project  EasyImage2.0 - 简单图床
 * @Github   https://github.com/icret/easyImages2.0
 * @Telegram https://t.me/Easy_Image
 * QQ Group  954441002
 * @Last     2024-01-20

 * 上传服务器后第一次打开会检查运行环境，请根据提示操作；
 * 检查环境仅会在第一开始开始出现，并在config目录下生成EasyImage.lock文件，如需再次查看请删除此文件。

 * 敬请注意：本程序为开源程序，你可以使用本程序在任何非商业项目或者网站中。但请你务必保留代码中相关信息（页面logo和页面上必要的链接可以更改）
 * 本人仅为程序开源创作，如非法网站与本人无关，请勿用于非法用途
 * 请为本人网站 (https://png.cm) 加上网址链接，谢谢支持。作为开发者你可以对相应的后台功能进行扩展（增删改相应代码）,但请保留代码中相关来源信息（例如：本人博客，邮箱等）
 * 如果因安装问题或其他问题可以给我发邮件。
 */

/*---------------基础配置开始-------------------*/

// 设置html为utf8
header('Content-Type:text/html;charset=utf-8');
// 定义根目录
define('APP_ROOT', str_replace(DIRECTORY_SEPARATOR, '/', realpath(dirname(__FILE__) . '/../')));
// 时区设置 https://www.php.net/manual/zh/timezones.php
require_once APP_ROOT . '/config/config.php';
empty($config['timezone']) ? date_default_timezone_set('Asia/Shanghai') : date_default_timezone_set($config['timezone']);
// 修改内存限制 根据服务器配置选择，低于128M容易出现上传失败，你懂得图片挺占用内存的
ini_set('memory_limit', '512M');
// 判断当前系统是否为windows
define('IS_WIN', strstr(PHP_OS, 'WIN') ? 1 : 0);
// 定义程序版本
define('APP_VERSION', '2.8.7');

/*---------------基础配置结束-------------------*/