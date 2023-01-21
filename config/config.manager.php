<?php

/*---------------tinyfilemanager管理配置（默认已经配置好了 你也可以自定义）-------------------*/

require __DIR__ . '/config.php';

/* Default Configuration
 * 默认配置
 */
// $CONFIG = '{"lang":"zh-CN","error_reporting":false,"show_hidden":false,"hide_Cols":false,"calc_folder":false}';

/*
 * Auth with login/password
 * set true/false to enable/disable it
 * Is independent from IP white- and blacklisting
 * 开启登录
 */

// Auth with login/password
// set true/false to enable/disable it
// Is independent from IP white- and blacklisting
$use_auth = false;

// Login user name and password
// Users: array('Username' => 'Password', 'Username2' => 'Password2', ...)
// Generate secure password hash - https://tinyfilemanager.github.io/docs/pwd.html
// 登录和管理密码 - Admin管理密码请在图床配置中修改
$auth_users = array(
    'admin' => password_hash($config['password'], PASSWORD_DEFAULT), // 登录密码
    'user' => '$2y$10$iPtSuvQnv0FnqdWdQsuWMOGxlul/VQzcKl3q1K7VU/QTw102IU5yi' //密码：CQ4CdBGjGJnA 
    // 先写一个密码然后获取密码Hash填上去- https://tinyfilemanager.github.io/docs/pwd.html
);

// Readonly users
// e.g. array('users', 'guest', ...)
// 只读的用户
$readonly_users = array(
    'user'
);

// Enable highlight.js (https://highlightjs.org/) on view's page
$use_highlightjs = true;

// highlight.js style
// for dark theme use 'ir-black'
// 主题 白天 vs/ 黑夜 ir-black
$highlightjs_style = 'vs';

// Enable ace.js (https://ace.c9.io/) on view's page
$edit_files = true;

// Default timezone for date() and time()
// Doc - http://php.net/manual/en/timezones.php
// 时区
$default_timezone = 'Asia/Shanghai'; // UTC

// Root path for file manager
// use absolute path of directory i.e: '/var/www/folder' or $_SERVER['DOCUMENT_ROOT'].'/folder'
// 管理的目录
$root_path = $_SERVER['DOCUMENT_ROOT'] . $config['path'];

// Root url for links in file manager.Relative to $http_host. Variants: '', 'path/to/subfolder'
// Will not working if $root_path will be outside of server document root
// 文件的路径
$root_url = $config['path'];

// Server hostname. Can set manually if wrong
$http_host = $_SERVER['HTTP_HOST'];
// $http_host = $config['imgurl'];


// user specific directories
// array('Username' => 'Directory path', 'Username2' => 'Directory path', ...)
// 用户路径
$directories_users = array();

// input encoding for iconv
$iconv_input_encoding = 'UTF-8';

// date() format for file modification date
// Doc - https://www.php.net/manual/en/datetime.format.php
$datetime_format = 'Y.m.d H:i:s';

// Allowed file extensions for create and rename files
// e.g. 'txt,html,css,js'
// 允许创建的文件格式
$allowed_file_extensions = '';

// Allowed file extensions for upload files
// e.g. 'gif,png,jpg,html,txt'
// 允许上传的文件格式
$allowed_upload_extensions = '';

// Favicon path. This can be either a full url to an .PNG image, or a path based on the document root.
// full path, e.g http://example.com/favicon.png
// local path, e.g images/icons/favicon.png
// Favicon图标路径
$favicon_path = $config['domain'] . '/favicon.ico';

// Files and folders to excluded from listing
// e.g. array('myfile.html', 'personal-folder', '*.php', ...)
// 不显示的文件类型或文件夹
$exclude_items = array('');

// Online office Docs Viewer
// Availabe rules are 'google', 'microsoft' or false
// google => View documents using Google Docs Viewer
// microsoft => View documents using Microsoft Web Apps Viewer
// false => disable online doc viewer
// 文档查看引擎 'google', 'microsoft' or false
$online_viewer = 'microsoft';

// Sticky Nav bar
// true => enable sticky header
// false => disable sticky header
// 启用导航栏?
$sticky_navbar = false;


// max upload file size
// 文件最大上传大小
$max_upload_size_bytes = 5000;

// Possible rules are 'OFF', 'AND' or 'OR'
// OFF => Don't check connection IP, defaults to OFF
// AND => Connection must be on the whitelist, and not on the blacklist
// OR => Connection must be on the whitelist, or not on the blacklist
// 开启登录IP管理
// OFF 关闭  AND 需在白名单内   OR 必须是白名单内或者不是黑名单内
$ip_ruleset = 'OFF';

// Should users be notified of their block?
// 告诉用户当前IP不可访问?
$ip_silent = true;

// IP-addresses, both ipv4 and ipv6
// 登录白名单
$ip_whitelist = array(
    '127.0.0.1',    // local ipv4
    '::1'           // local ipv6
);

// IP-addresses, both ipv4 and ipv6
// 登录黑名单
$ip_blacklist = array(
    '0.0.0.0',      // non-routable meta ipv4
    '::'            // non-routable meta ipv6
);
