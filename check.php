<!DOCTYPE html>
<html lang="zh_CN">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="ie=edge">
  <title>检测工具</title>
  <style>
    a:link{color:grey;}
    a:visited{color:black;}
    a:hover{color:red;}
    a:active{color:blanchedalmond}
  </style>
</head>
<body>
  <h3><a href="https://www.545141.com/easyimage.html" target="_blank"/>简单图床-easyimage</a></h3>
  <hr />
  <p>
    本工具用来检测当前服务器是否支持本程序,需要检测的有：<br />
    1.检测 PHP版本 建议使用php5.6及以上<br />
    2.检测 GD库 大多数图像操作都需要GD<br />
    3.检测 PHP允许上传的大小<br />
    4.检测 Fileinfo, iconv ,zip和 mbstring扩展，如果缺失会导致无法访问管理面板以及上传图片<br/>
    <br/>PHP检测不一定准确，请以最下边phpinfo信息为准！可以实用ctrl+f搜索，如果是disable则没有安装!<br/>
    5.修改密码是用于文件管理 请将新密码全部复制并覆盖<code>/public/data/tinyfilemanager.php</code>第28行中替换相应的字符
    <br />默认密码：<code>easyimage</code><br />
    6.使用完本工具后建议删除！避免泄露服务器信息<br />
  </p>
  <form action="<?php echo $_SERVER['PHP_SELF'];?>" method="post">
    <label>请输入新密码:</label>
    <input type="text" name="psw" value="easyimage">
    <input type="submit" value="获取新密码">
  </form>
</body>

</html>
<?php
if(@isset($_POST['psw'])){
  echo '<hr/><br />新加密密码：<code>'.password_hash($_POST['psw'],PASSWORD_DEFAULT).'</code>';
  echo '<br />请将新密码全部复制并覆盖<code>/public/data/tinyfilemanager.php</code>第28行中替换相应的字符';
  echo '<br/>登录密码为：<code>'.$_POST['psw'].'</code>';
}

echo '<hr/>当前PHP版本：'.phpversion();
if(extension_loaded('gd')){
  echo '<br />GD已安装：'.gd_info()['GD Version'];
}else{
  echo '<br/>GD未安装'; 
}
echo '<br />当前upload_max_filesize：'.ini_get('upload_max_filesize'), ",<br/>当前post_max_size：" , ini_get('post_max_size');
// 使用linux系统时需要赋予权限 chmod -R 777 /路径/
if(!is_writable('file.php')){
  echo '当前目录文件不可写，如果是linux请使用 chmod -R 777 /路径/* 赋予权限';
}else{
  echo'<br/>当前目录权限正常';
}

if(!extension_loaded('fileinfo')){
  echo '<br/>>>fileinfo未安装';
}else{
  echo '<br/>fileinfo已安装';
}
if(!extension_loaded('iconv')){
  echo '<br/>>>iconv未安装';
}else{
  echo '<br/>iconv已安装';
}

if(!extension_loaded('zip')){
  echo '<br/>>>zip未安装';
}else{
  echo '<br/>zip已安装';
}

if(!extension_loaded('mbstring')){
  echo '<br/>mbstring未安装';
}else{
  echo '<br/>mbstring已安装';
}

echo '<br/><br/>以下是php所有拓展和版本信息，请使用ctrl+f快速搜索查找问题：<br/>';
phpinfo();
exit;