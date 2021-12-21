<?php
require_once __DIR__ . '/../application/function.php';

if (file_exists(APP_ROOT . '/install/install.lock')) {
  exit(header("Location:/../index.php"));
}



if (isset($_POST['password'])) {
  if ($_POST['password'] == $_POST['repassword']) {

    $config['password']=$_POST['password'];
   
    
  } else {

    exit('<script>window.alert("两次密码不一致请重新输入！");location.href="./index.php";</script>');
  }
}

if (isset($_POST['domain'])) {
  $config['domain']= $_POST['domain']; 
  
}

if (isset($_POST['imgurl'])) {
  $config['imgurl']= $_POST['imgurl'];  
}

$config_file = APP_ROOT . '/config/config.php';
cache_write($config_file, $config);

file_put_contents(APP_ROOT . '/install/install.lock', '安装程序锁定文件。'); // 创建安装程序锁

// 跳转主页
echo '
<script>  
window.alert("安装成功，即将为您跳转到登陆界面！");
location.href="'.get_whole_url('/install/contorl.php').'/application/login.php'.'";  
</script>  
';
// 删除安装目录
if (isset($_POST['del_install'])) {
  if ($_POST['del_install'] == "del") {
    deldir(APP_ROOT . "/install/");
  }
}

//exit(header("Location:/../application/login.php")); // 跳转主页