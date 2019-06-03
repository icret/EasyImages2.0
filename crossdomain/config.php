<?php
/**
 * EasyImage - 简单图床异域存储配置
 *
 * @author icret
 * @email  lemonim@qq.com
 * @Github  https://github.com/icret/easyImages
 * @Review 2019-5-21 13:05:20
 * 上传后请打开check.php先检查服务器配置，更改密码等操作
 */

// 设置html为utf8
header('Content-Type:text/html;charset=utf-8');
//将时区设置为上海时区
ini_set('date.timezone', 'Asia/Shanghai');
// 修改内存限制 根据服务器配置选择，低于128M容易出现上传失败，你懂得图片挺占用内存的
ini_set('memory_limit','512M');
// 定义当前目录
define('APP_ROOT', __DIR__);

$config = array(
    // 网站标题
     'title' => '简单图床 - EasyImage',
    // 网站关键字
     'keywords' => '简单图床,easyimage,无数据库图床',
    // 网站描述
     'description' => '支持多文件上传,远程上传,api上传,简单无数据库,直接返回图片url,markdown,bbscode的一款html5图床程序 。',
    // 网站公告
     'tips' => ' 单个文件限制5M，每次最多上传30张图片。',
    // 当前域名,末尾不加"/" 如果是异域上传请修改为当前异域域名
     'domain' => 'https://img.545141.com/crossdomain',
    // 存储路径 末尾需要加"/"
     'path' => '/public/data/',
    // 最大上传限制 默认为5m 请使用工具转换mb http://www.bejson.com/convert/filesize/
     'maxSize' => 5242880,
    // 是否开启登录上传 开启:true 关闭false
     'mustLogin' => false,
     // 登录密码 此密码非管理密码
     'password'=>'7070',
    // 开启管理 开启后务必修改密码 修改方式请见read.php
     'tinyfilemanager' => true,
    // 是否开启API上传
     'apiStatus' => true,
    // 是否开启异域上传 开启true 关闭 false
    'crossdomain'=>false,
     // 异域上传域名 末尾需要加'/'
     'CDomains'=>'https://img.545141.com/crossdomain/',
    // 是否开启水印:0关闭，1文字水印，2图片水印 动态gif不能添加水印
     'watermark' => 2,
    // 水印文字内容
     'waterText' => 'img.545141.com',
    // 水印位置 T=top，B =bottom，L=left，R=right 'TBLR'中的一个或两个的组合
     'waterPosition' => 'TB',
    // 水印文字方向 h水平 v垂直
     'textDirection' => 'h',
    // 水印文字颜色
     'textColor' => '#778899',
    // 水印文字大小
     'textSize' => 16,
    // 字体路径 如果想改变字体，请选择支持中文的 gb2312
     'textFont' => APP_ROOT.'/public/static/hkxzy.ttf',
    // 水印边距 px
     'textPadding' => 10,
    // 水印透明度
     'textOpacity' => 100,
    // 图片水印路径 支持GIF,JPG,BMP,PNG和PNG alpha
     'waterImg' => 'public/static/watermark.png',
    // 转换图片为指定格式 可选：''|'png'|'jpeg'|'gif'|'bmp';默认值：''
     'imgConvert' => '',
     // 是否通过缩放来压缩，如果要保持源图比例，把参数$percent保持为1，范围 0.1-1
    // 即使原比例压缩，也可大幅度缩小。如果缩小比例，则体积会更小。
    'imgcompress_percent' => 0.9,
    //最大宽度
     'maxWidth' => 10240,
    // 最大高度
     'maxHeight' => 10240,
    // 最小宽度
     'minWidth' => 5,
    // 最小高度
     'minHeight' => 5,
    // 等比例缩小图片 宽度和高度请设置 image_x image_y 开启true，关闭false 关闭下mage_x和image_y设置不生效
     'imgRatio' => false,
    // 缩减的最大高度
     'image_x' => 1024,
    // 缩减的最大宽度
     'image_y' => 1024,
    // 开启静态文件CDN 开启true 关闭false
     'static_cdn' => true,
    // 开启顶部广告 如果想添加或修改广告请到 public/static/ad_top.html
     'ad_top' => false,
    //  开启底部广告 如果想添加或修改广告请到 public/static/ad_bot.html
     'ad_bot' => false,
     'Version' => '2.0.0.9'
);