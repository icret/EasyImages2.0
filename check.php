<!DOCTYPE html>
<html lang="zh_CN">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="ie=edge">
  <title>简单图床-EasyImage2.0安装环境检测</title>
  <style>
    a:link {
      color: green;
    }

    a:visited {
      color: black;
    }

    a:hover {
      color: red;
    }

    a:active {
      color: blanchedalmond
    }
  </style>
</head>

<body>
  <h3><a href="https://www.545141.com/846.html" target="_blank">简单图床-EasyImage</a></h3>
  <hr />
  <h4>说明：</h4>
  <h5>1. 建议使用<font color="red">PHP7.0</font>及以上版本；</h5>
  <h5>2. 大部分上传失败是由于<font color="red">upload_max_filesize、post_max_size、文件权限</font>设置不正确；</h5>
  <h5>3. 本程序用到 <font color="red">Fileinfo、iconv、zip、mbstring、openssl</font> 扩展,如果缺失会导致无法访问管理面板以及上传/删除图片。</h5>
  <h5>4. <font color="red">zip</font>扩展不是必须的，但会影响tinyfilemanager文件压缩(不会在首页中检测)。</h5>
  <h5>5. 上传后必须修改config.php的位置：<font color="red">domain</font>当前网站域名，<font color="red">imgurl</font>当前图片域名，<font color="red">password</font>登录管理密码！</h5>
  <h5>6. 使用完本工具后建议删除！避免泄露服务器信息</h5>
  <hr />
  <h4>EasyImage2.0 基础检测：</h4>
  <?php
  echo '当前PHP版本：<font color="green">' . phpversion() . '</font>';
  $quanxian =  substr(base_convert(fileperms("file.php"), 10, 8), 3);
  if (!is_executable('file.php') || $quanxian != '777') {
    echo '<br/><font color="red">上传文件权限错误（当前权限：' . $quanxian . '），<b>windows可以无视</b>，linux使用 chmod -R 0777 /mulu/* 赋予权限</font>';
  } else {

    echo '<br/><font color="green">当前文件可执行</font>';
  }
  echo '<br /><font color="green">upload_max_filesize</font> PHP上传最大值：' . ini_get('upload_max_filesize');
  echo '<br /><font color="green">post_max_size</font> PHP POST上传最大值：' . ini_get('post_max_size') . '<br />';

  $expand = array('fileinfo', 'iconv', 'gd', 'zip', 'mbstring', 'openssl',);

  foreach ($expand as $val) {
    if (extension_loaded($val)) {
      echo '<font color="green">' . $val . "</font>- 已安装<br />";
    } else {
      echo "
    <script language='javascript'>alert('$val - 未安装')</script>
    ";
      echo '<font color="red">' .  $val . "- 未安装</font><br />";
    }
  }

  echo '<hr/>以下是当前PHP所有已安装扩展：<br/>';
  foreach (get_loaded_extensions() as $val) {
    echo '<font color="green">' . $val . '</font>，';
  }
  ?>
</body>
<footer style="text-align: center;">
  <hr>
  <p>请勿用于违反中国政策的网站</p>
  <p>Copyright © 2018-2021 <a href="https://img.545141.com/" target="_blank">EasyImage</a> Power By <a href="https://www.545141.com/902.html" target="_blank">Icret</a> All Rights Reserved</p>
</footer>

</html>