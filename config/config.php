<?php

/**
 * EasyImage2.0 - 简单图床配置

 * @author  icret
 * @email   lemonim@qq.com
 * @Github  https://github.com/icret/easyImages2.0
 * QQ Group 623688684
 * @Last    2021-5-25 21:12:34

 * 上传服务器后第一次打开会检查运行环境，请根据提示操作；
 * 检查环境仅会在第一开始开始出现，并在config目录下生成EasyImage.lock文件，如需再次查看请删除此文件。

 * 敬请注意：本程序为开源程序，你可以使用本程序在任何的商业、非商业项目或者网站中。但请你务必保留代码中相关信息（页面logo和页面上必要的链接可以清除）
 * 本人仅为程序开源创作，如非法网站与本人无关，请勿用于非法用途
 * 请为本人博客（www.545141.com）加上网址链接，谢谢支持。作为开发者你可以对相应的后台功能进行扩展（增删改相应代码）,但请保留代码中相关来源信息（例如：本人博客，邮箱等）
 * 如果因安装问题或其他问题可以给我发邮件。
 * 
 * 配置分为两大区块 -  1.基础配置 2.图床配置
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
  // 网站公告 为空则不显示
  'tips' => '本站仅做演示用,不定时清理图片，单文件≤5M，每次上传≤30张',
  /**
   * 网站域名与图片链接域名可以不同，比如A域名上传，可以返回B域名图片链接，A、B需绑定到同一空间下
   * 如果不变的话，下边2个填写成一样的！
   */
  // 网站域名,末尾不加"/" 
  'domain' => 'http://localhost',
  // 图片链接域名,末尾不加"/"
  'imgurl' => 'http://localhost',
  // 登录上传和后台管理密码,管理用户名为：admin
  'password' => 'admin@123',
  // 是否开启登录上传 开启:true 关闭:false
  'mustLogin' => false,
  // 是否开启API上传 开启:true 关闭:false
  'apiStatus' => false,
  /**
   * 存储路径 前后要加"/" 
   * 可根据Apache/Nginx配置安全，参考：https://www.545141.com/981.html 或 README.md
   */
  'path' => '/i/',
  /** 文件的命名方式 更改后不影响之前上传的
   * date     以上传时间 例：192704
   * unix     以Unix时间 例：1635074840
   * uniqid   基于以微秒计的当前时间 例：6175436c73418
   * guid     全球唯一标识符 例：6EDAD0CC-AB0C-4F61-BCCA-05FAD65BF0FA
   * md5      md5加密时间 例：3888aa69eb321a2b61fcc63520bf6c82
   * sha1     sha1加密微秒 例：654faac01499e0cb5fb0e9d78b21e234c63d842a
   * default  将上传时间+随机数转换为36进制 例：vx77yu
   */
  'imgName'  =>  'default',
  // 最大上传限制 默认为5M 请使用工具转换Mb http://www.bejson.com/convert/filesize/
  'maxSize' => 5242880,
  // 每次最多上传图片数
  'maxUploadFiles' => 30,
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
  /* 轻微有损压缩图片 开启:true 关闭:false  
   * 此压缩有可能使图片变大！特别是小图片 也有一定概率改变图片方向
   * 开启后会增加服务器负担  
   */
  'compress' => false,
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
  // 静态文件CDN加速网址 末尾不加 /
  'static_cdn_url'  => '//cdn.jsdelivr.net/gh/icret/EasyImages2.0',
  // 开启顶部广告 开启:true 关闭:false 如果想添加或修改广告请到
  'ad_top' => false,
  // 顶部广告内容 支持html
  'ad_top_info'  => '
  <div id="ad" class="col-md-12" align="center" style="padding:5px;">
    <!--广告 按照这个范例替换相应链接，如果想多几个广告，就多复制几个-->
    <a href="https://app.cloudcone.com/?ref=3521" target="_blank"><img src="/public/images/ad.jpg" /></a>
  </div>
  ',
  // 开启底部广告 开启:true 关闭:false 如果想添加或修改广告请到
  'ad_bot' => false,
  // 底部广告内容 支持html
  'ad_bot_info'  => '
  <div id="ad" class="col-md-12" align="center" style="padding:5px;">
      <!--广告 按照这个范例替换相应链接，如果想多几个广告，就多复制几个-->
      <a href="https://app.cloudcone.com/?ref=3521" target="_blank"><img src="/public/images/ad.jpg" /></a>
  </div>
  ',
  // 开启游客预览（广场）开启:true 关闭:false
  'showSwitch' => true,
  // 默认预览数量，可在网址后填写参数实时更改预览数量 如：https://img.545141.com/libs/list.php?num=3
  'listNumber' => 20,
  // 上传框底部自定义信息，仅支持html格式 可以放置统计代码 下面是举例：
  'customize' => '
    <!-- 统计代码-->
    <script>
    var _hmt = _hmt || [];
    (function() {
      var hm = document.createElement("script");
      hm.src = "https://hm.baidu.com/hm.js?5320b69f4f1caa9328dfada73c8e6a75";
      var s = document.getElementsByTagName("script")[0]; 
      s.parentNode.insertBefore(hm, s);
    })();
    </script>

    <!-- 自定义js举例：非img.545141.com跳转 
    <img style="display:none" src=" " onerror=\'this.onerror=null;var currentDomain="img."+"545141." + "com"; var str1=currentDomain; str2="docu"+"ment.loca"+"tion.host"; str3=eval(str2) ;if( str1!=str3 ){ do_action = "loca" + "tion." + "href = loca" + "tion.href" + ".rep" + "lace(docu" +"ment"+".loca"+"tion.ho"+"st," + "currentDomain" + ")";eval(do_action) }\' />		
    -->
    <!--自定义代码举例：打赏、QQ邮箱、QQ群 可删除
    <iframe src="https://img.545141.com/sponsor/index.html" style="overflow-x:hidden;overflow-y:hidden; border:0xp none #fff; min-height:240px; width:100%;"  frameborder="0" scrolling="no"></iframe>
    <a target="_blank" href="https://mail.qq.com/cgi-bin/qm_share?t=qm_mailme&amp;email=cR0UHB4fGBwxAABfEh4c">
      <i class="icon icon-envelope-alt">联系邮箱 </i></span>
    </a> 
    <a target="_blank" href="//shang.qq.com/wpa/qunwpa?idkey=3feb4e8be8f1839f71e53bf2e876de36afc6889b2630c33c877d8df5a5583a6f">
        <i class="icon icon-qq">加入QQ群</i></span>
    </a>
    <a target="_blank" href="/master.zip"><i class="icon icon-download-alt">下载源码</i></a>
    --> 
    ',
  // PHP插件检测-安全设置检测-版本检测 开启:true 关闭:false
  'checkEnv' => true,
  /* 图片监黄 开启:true 关闭:false 
   * 从 https://moderatecontent.com/ 获取key并填入/config/api_key.php的图片检查key
   * 开启后会受服务器到https://moderatecontent.com/ 速度影响，国内不建议开启！
   */
  'checkImg' => true,
  // 设置是不良图片概率,概率越大准确率越高，
  'checkImg_value' => 50,
  // 当前版本
  'version' => '2.3.1'
);
