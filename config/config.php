<?php

/**
 * EasyImage2.0 - 简单图床配置

 * @author  icret
 * @email   lemonim@qq.com
 * @Github  https://github.com/icret/easyImages2.0
 * QQ Group 623688684
 * @Last    2021-5-25 21:12:34

 * 上传后请打开check.php先检查服务器配置，更改密码等操作
 * 安装完毕后请删除README.md,check.php,LICENSE等非必要文件

 * 敬请注意：本程序为开源程序，你可以使用本程序在任何的商业、非商业项目或者网站中。但请你务必保留代码中相关信息（页面logo和页面上必要的链接可以清除）
 * 本人仅为程序开源创作，如非法网站与本人无关，请勿用于非法用途
 * 请为本人博客（www.545141.com）加上网址链接，谢谢支持。作为开发者你可以对相应的后台功能进行扩展（增删改相应代码）,但请保留代码中相关来源信息（例如：本人博客，邮箱等）
 * 如果因安装问题或其他问题可以给我发邮件。
 * 
 * 配置分为三大区块 -  1.基础配置 2.图床配置 3.tinyfilemanager管理配置
 */


/*---------------基础配置-------------------*/

// 设置html为utf8
@header('Content-Type:text/html;charset=utf-8');
//将时区设置为中国·上海
@ini_set('date.timezone', 'Asia/Shanghai');
@date_default_timezone_set('Asia/Shanghai');
// 修改内存限制 根据服务器配置选择，低于128M容易出现上传失败，你懂得图片挺占用内存的
@ini_set('memory_limit', '512M');
// 定义根目录
@define('APP_ROOT', str_replace('\\', '/', realpath(dirname(__FILE__) . '/../')));


/*---------------图床配置-------------------*/

$config = array(
  // 网站标题
  'title' => '简单图床 - EasyImage',
  // 网站关键字
  'keywords' => '简单图床,easyimage,无数据库图床,PHP多图长传程序,自适应页面,HTML5,markdown,bbscode,一键复制',
  // 网站描述
  'description' => '简单图床EasyImage是一款支持多文件上传的无数据库图床,可以完美替代PHP多图上传程序，最新html5自适应页面兼容手机电脑，上传后返回图片直链，markdown图片，论坛贴图bbscode链接，简单方便支持一键复制，支持多域名，api上传。',
  // 网站公告
  'tips' => '本站仅做演示用,不定时清理图片，单文件≤5M，每次上传≤30张',
  /**
   * 网站域名与图片链接域名可以不同，比如A域名上传，可以返回B域名图片链接，A、B需绑定到同一空间下
   * 如果不变的话，下边2个填写成一样的！
   */
  // 网站域名,末尾不加"/" 
  'domain' => 'http://192.168.2.100',
  // 图片链接域名,末尾不加"/"
  'imgurl' => 'http://192.168.2.100',
  /**
   * 存储路径 前后要加"/" 
   * 可根据Apache/Nginx配置安全，参考：https://www.545141.com/981.html 或 README.md
   */
  'path' => '/i/',
  // 最大上传限制 默认为5M 请使用工具转换Mb http://www.bejson.com/convert/filesize/
  'maxSize' => 15242880,
  // 每次最多上传图片数
  'maxUploadFiles' => 30,
  // 是否开启登录上传 开启:true 关闭:false
  'mustLogin' => false,
  // 是否开启tinyfilemanager文件管理 开启:true 关闭:false
  'tinyfilemanager' => true,
  // 登录上传和后台管理密码,管理用户名为：admin
  'password' => 'admin@123',
  // 是否开启API上传 开启:true 关闭:false
  'apiStatus' => true,
  // 是否开启水印:0关闭，1文字水印，2图片水印 不能使用动态gif添加水印
  'watermark' => 0,
  // 水印文字内容
  'waterText' => '简单图床 img.545141.com',
  /**
   * 水印位置
   * 0：随机位置，在1-8之间随机选取一个位置
   * 1：顶部居左 2：顶部居中 3：顶部居右 4：左边居中
   * 5：图片中心 6：右边居中  7：底部居左 8：底部居中 9：底部居右
   */
  'waterPosition' => 8,
  // 水印文字颜色 rgba 末尾为透明度0-127 0为不透明
  'textColor' => '47,79,79,0',
  // 水印文字大小
  'textSize' => 16,
  // 字体路径 如果想改变字体，请选择支持中文的 GB/2312 字体
  'textFont' => 'public/static/hkxzy.ttf',
  // 图片水印路径 支持GIF,JPG,BMP,PNG和PNG alpha
  'waterImg' => 'public/images/watermark.png',
  // 允许上传的图片扩展名
  'extensions' => "'bmp,jpg,png,tif,gif,pcx,tga,svg,webp,jpeg,tga,svg,ico'",
  // 轻微有损压缩图片 开启:true 关闭:false  * 此压缩有可能使图片变大！特别是小图片 也有一定概率改变图片方向
  'compress' => true,
  // 转换图片为指定格式 可选：''|'png'|'jpeg'|'gif'|'bmp';默认值：''
  'imgConvert' => '',
  // 最大上传宽度
  'maxWidth' => 10240,
  // 最大上传高度
  'maxHeight' => 10240,
  // 允许上传的最小宽度
  'minWidth' => 5,
  // 允许上传的最小高度
  'minHeight' => 5,
  // 改变图片宽高 宽度和高度请设置 image_x image_y 开启:true 关闭:false 关闭下image_x和image_y设置不生效
  'imgRatio' => false,
  // 缩减的最大高度
  'image_x' => 1000,
  // 缩减的最大宽度
  'image_y' => 800,
  // 开启静态文件CDN 开启:true 关闭:false
  'static_cdn' => false,
  // 开启顶部广告 开启:true 关闭:false 如果想添加或修改广告请到 public/static/ad_top.html
  'ad_top' => false,
  // 开启底部广告 开启:true 关闭:false 如果想添加或修改广告请到 public/static/ad_bot.html
  'ad_bot' => false,
  // 开启游客预览（广场）开启:true 关闭:false
  'showSwitch' => true,
  // 默认预览数量，可在网址后填写参数实时更改预览数量 如：https://img.545141.com/libs/list.php?num=3
  'listNumber' => 20,
  // 上传框底部自定义信息，仅支持html格式 下面是举例：
  'customize' => '
    <!--打赏
    <iframe src="https://img.545141.com/sponsor/index.html" style="overflow-x:hidden;overflow-y:hidden; border:0xp none #fff; min-height:240px; width:100%;"  frameborder="0" scrolling="no"></iframe>
    -->
    <!-- 非img.545141.com跳转
    <img style="display:none" src=" " onerror=\'this.onerror=null;var currentDomain="img."+"545141." + "com"; var str1=currentDomain; str2="docu"+"ment.loca"+"tion.host"; str3=eval(str2) ;if( str1!=str3 ){ do_action = "loca" + "tion." + "href = loca" + "tion.href" + ".rep" + "lace(docu" +"ment"+".loca"+"tion.ho"+"st," + "currentDomain" + ")";eval(do_action) }\' />		
    -->
    <!-- QQ邮箱、QQ群
    <a target="_blank" href="https://mail.qq.com/cgi-bin/qm_share?t=qm_mailme&amp;email=cR0UHB4fGBwxAABfEh4c">
      <i class="icon icon-envelope-alt">联系邮箱 </i></span>
    </a> 
    <a target="_blank" href="//shang.qq.com/wpa/qunwpa?idkey=3feb4e8be8f1839f71e53bf2e876de36afc6889b2630c33c877d8df5a5583a6f">
        <i class="icon icon-qq">加入QQ群</i></span>
    </a>
    --> 
    ',
  // PHP插件检测-安全设置检测-版本检测 开启:true 关闭:false
  'checkEnv' => true,
  // 当前版本
  'version' => '2.2.2',
);


/*---------------tinyfilemanager管理配置（默认已经配置好了 你也可以自定义）-------------------*/

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
$use_auth = true;

/* Login user name and password
 * Users: array('Username' => 'Password', 'Username2' => 'Password2', ...)
 * Generate secure password hash - https://tinyfilemanager.github.io/docs/pwd.html
 * 登录和管理密码 - Admin管理密码请在图床配置中修改
 */

$auth_users = array(
  'admin' => password_hash($config['password'], PASSWORD_DEFAULT), // 登录密码
  'user' => '$2y$10$iPtSuvQnv0FnqdWdQsuWMOGxlul/VQzcKl3q1K7VU/QTw102IU5yi' //密码：CQ4CdBGjGJnA 
  // 先写一个密码然后获取密码Hash填上去- https://tinyfilemanager.github.io/docs/pwd.html
);

/* Readonly users
 * e.g. array('users', 'guest', ...)
 * 只读的用户
 */

$readonly_users = array(
  'user'
);

/* set application theme
 * options - 'light' and 'dark'
 * 管理主题 白天 light/ 黑夜 dark
 */
$theme = 'light';

/* Enable highlight.js (https://highlightjs.org/) on view's page
 * 开启可预览代码js highlight.js (https://highlightjs.org/) 
 */
$use_highlightjs = true;

/* highlight.js style
 * for dark theme use 'ir-black'
 * 代码预览样式 黑夜模式请使用 ir-black
 */
$highlightjs_style = 'vs';

/* Enable ace.js (https://ace.c9.io/) on view's page
 * 启用 ace.js (https://ace.c9.io/) 
 */
$edit_files = true;

/* Default timezone for date() and time()
 * Doc - http://php.net/manual/en/timezones.php
 * 时区设置
 */
$default_timezone = 'Asia/Shanghai'; // UTC

/* Root path for file manager
 * use absolute path of directory i.e: '/var/www/folder' or $_SERVER['DOCUMENT_ROOT'].'/folder'
 * 文件的绝对路径 不出错就别动
 */
$root_path = $_SERVER['DOCUMENT_ROOT'];

/* Root url for links in file manager.Relative to $http_host. Variants: '', 'path/to/subfolder'
 * Will not working if $root_path will be outside of server document root
 * 文件夹相对路径
 */
$root_url = '';

// Server hostname. Can set manually if wrong
// 当前域名 不出错就别动
$http_host = $_SERVER['HTTP_HOST'];

/* user specific directories
 *array('Username' => 'Directory path', 'Username2' => 'Directory path', ...)
 *用户路径
 */
$directories_users = array('admin' => ltrim($config['path'], '/'), 'user' => ltrim($config['path']), '/');

/* input encoding for iconv
 * html编码
 */
$iconv_input_encoding = 'UTF-8';

/* date() format for file modification date
 *Doc - https://www.php.net/manual/en/datetime.format.php
 * 时间格式 类似 20210419 22:39:06
 */
$datetime_format = 'd.m.y H:i:s';

/* Allowed file extensions for create and rename files
 * e.g. 'txt,html,css,js'
 * 允许创建的文件格式
 */
$allowed_file_extensions = '';

/* Allowed file extensions for upload files
 * e.g. 'gif,png,jpg,html,txt'
 * 运行创建上传的文件格式
 */
$allowed_upload_extensions = '';

/* Favicon path. This can be either a full url to an .PNG image, or a path based on the document root.
 *full path, e.g http://example.com/favicon.png
 * local path, e.g images/icons/favicon.png
 * Favicon图标路径
 */
$favicon_path = './favicon.ico';

/* Files and folders to excluded from listing
 * e.g. array('myfile.html', 'personal-folder', '*.php', ...)
 * 不显示的文件类型或文件夹
 */
$exclude_items = array('tinyfilemanager.php', 'public/static/translation.json');

/* Online office Docs Viewer
 * Availabe rules are 'google', 'microsoft' or false
 * google => View documents using Google Docs Viewer
 * microsoft => View documents using Microsoft Web Apps Viewer
 * false => disable online doc viewer
 * 文档查看引擎 'google', 'microsoft' or false
 */
$online_viewer = 'google';

/* Sticky Nav bar
 *true => enable sticky header
 * false => disable sticky header
 * 启用导航栏?
 */
$sticky_navbar = true;


/* max upload file size
 * 文件最大上传大小
 */
$max_upload_size_bytes = 5000;

/* Possible rules are 'OFF', 'AND' or 'OR'
 * OFF => Don't check connection IP, defaults to OFF
 * AND => Connection must be on the whitelist, and not on the blacklist
 * OR => Connection must be on the whitelist, or not on the blacklist
 * 开启登录IP管理
 * OFF 关闭  AND 需在白名单内   OR 必须是白名单内或者不是黑名单内
 */
$ip_ruleset = 'OFF';

/* Should users be notified of their block?
 * 告诉用户当前IP不可访问?
 */
$ip_silent = true;

/* IP-addresses, both ipv4 and ipv6
 * 登录白名单
 */
$ip_whitelist = array(
  '127.0.0.1',    // local ipv4
  '::1'           // local ipv6
);

/* IP-addresses, both ipv4 and ipv6
 * 登录黑名单
 */
$ip_blacklist = array(
  '0.0.0.0',      // non-routable meta ipv4
  '::'            // non-routable meta ipv6
);
