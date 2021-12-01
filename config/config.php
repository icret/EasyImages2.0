<?php
$config=Array
	(
	'title'=>'简单图床 - EasyImage',
	'keywords'=>'简单图床,easyimage,无数据库图床,PHP多图长传程序,自适应页面,HTML5,markdown,bbscode,一键复制',
	'description'=>'简单图床EasyImage是一款支持多文件上传的无数据库图床,可以完美替代PHP多图上传程序，最新html5自适应页面兼容手机电脑，上传后返回图片直链，markdown图片，论坛贴图bbscode链接，简单方便支持一键复制，支持多域名，api上传。',
	'tips'=>'本站仅做演示用,不定时清理图片，单文件≤5M，每次上传≤30张',
	'domain'=>'http://localhost',
	'imgurl'=>'http://localhost',
	'password'=>'admin@123',
	'mustLogin'=>0,
	'apiStatus'=>0,
	'path'=>'/i/',
	'imgName'=>'default',
	'maxSize'=>5242880,
	'maxUploadFiles'=>30,
	'watermark'=>0,
	'waterText'=>'简单图床 - img.545141.com',
	'waterPosition'=>0,
	'textColor'=>'255,0,0,1',
	'textSize'=>16,
	'textFont'=>'/public/static/hkxzy.ttf',
	'waterImg'=>'/public/images/watermark.png',
	'extensions'=>'bmp,jpg,png,tif,gif,pcx,tga,svg,webp,jpeg,tga,svg,ico',
	'compress'=>0,
	'imgConvert'=>'',
	'maxWidth'=>10240,
	'maxHeight'=>10240,
	'minWidth'=>5,
	'minHeight'=>5,
	'imgRatio'=>0,
	'image_x'=>1000,
	'image_y'=>800,
	'static_cdn'=>0,
	'static_cdn_url'=>'https://cdn.jsdelivr.net/gh/icret/EasyImages2.0',
	'ad_top'=>0,
	'ad_top_info'=>'  <div id="ad" class="col-md-12" align="center" style="padding:5px;">
    <!--广告 按照这个范例替换相应链接，如果想多几个广告，就多复制几个-->
    <a href="https://app.cloudcone.com/?ref=3521" target="_blank"><img src="/public/images/ad.jpg" /></a>
  </div>
  ',
	'ad_bot'=>0,
	'ad_bot_info'=>'  <div id="ad" class="col-md-12" align="center" style="padding:5px;">
      <!--广告 按照这个范例替换相应链接，如果想多几个广告，就多复制几个-->
      <a href="https://app.cloudcone.com/?ref=3521" target="_blank"><img src="/public/images/ad.jpg" /></a>
  </div>
  ',
	'showSwitch'=>1,
	'listNumber'=>20,
	'customize'=>'    <!-- 统计代码-->
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
    <a target="_blank" href="https://mail.qq.com/cgi-bin/qm_share?t=qm_mailme&email=cR0UHB4fGBwxAABfEh4c">
      <i class="icon icon-envelope-alt">联系邮箱 </i>
    </a> 
    <a target="_blank" href="//shang.qq.com/wpa/qunwpa?idkey=3feb4e8be8f1839f71e53bf2e876de36afc6889b2630c33c877d8df5a5583a6f">
        <i class="icon icon-qq">加入QQ群</i>
    </a>
    <a target="_blank" href="/master.zip"><i class="icon icon-download-alt">下载源码</i></a>
 --> ',
	'checkEnv'=>1,
	'checkImg'=>0,
	'checkImg_value'=>50,
	'upload_logs'=>1,
	'cache_freq'=>2,
	'version'=>'2.4.3',
	'form'=>'',
	'TinyImag_key'=>'',
	'moderatecontent_key'=>'',
	'footer'=>'<a href="/admin/terms.php" target="_blank">请勿上传违反中国政策的图片</a>
  <i class="icon icon-smile"></i> <br/>

Copyright © 2018-2021
<a href="https://img.545141.com/" target="_blank"> EasyImage</a> By
<a href="https://www.545141.com/902.html" target="_blank"> Icret</a> Version:
<a href="https://github.com/icret/EasyImages2.0" target="_blank"> 2.4.3</a>
<a href="/admin/terms.php" target="_blank"> DMCA</a>
'
	);