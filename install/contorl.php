<?php
require_once __DIR__ . '/../application/function.php';

if (file_exists(APP_ROOT . '/install/install.lock')) {
  exit(header("Location:/../index.php"));
}

if (isset($_POST['password'])) {
  if ($_POST['password'] == $_POST['repassword']) {

    $config['password'] = md5($_POST['password']);
    $config['user'] = $_POST['user'];
  } else {

    exit('<script>window.alert("两次密码不一致请重新输入!");location.href="./index.php";</script>');
  }
}

if (isset($_POST['domain'])) {
  $config['domain'] = $_POST['domain'];
}

if (isset($_POST['imgurl'])) {
  $config['imgurl'] = $_POST['imgurl'];
}

$config_file = APP_ROOT . '/config/config.php';
cache_write($config_file, $config);

file_put_contents(APP_ROOT . '/install/install.lock', '安装程序锁定文件。'); // 创建安装程序锁

// 删除安装目录
if (isset($_POST['del_install'])) {
  if ($_POST['del_install'] == "del") {
    deldir(APP_ROOT . "/install");
  }
}

// 删除多余文件.whitesource
if (isset($_POST['del_extra_files'])) {
  if ($_POST['del_extra_files'] == "del") {
    @unlink(APP_ROOT . '/LICENSE');
    @unlink(APP_ROOT . '/README.md');
    @deldir(APP_ROOT . "/admin/logs");
    @deldir(APP_ROOT . "/SECURITY.md");
    @unlink(APP_ROOT . '/.whitesource');
    @unlink(APP_ROOT . '/CODE_OF_CONDUCT.md');
    @unlink(APP_ROOT . '/config/EasyIamge.lock');
    @deldir(APP_ROOT . "/.github");
    @deldir(APP_ROOT . "/.git");
  }
}

// 跳转主页
echo '
<script>  
window.alert("安装成功,即将为您跳转到登陆界面!");
location.href="../admin/index.php";  
</script>  
';
