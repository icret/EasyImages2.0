<?php
require_once __DIR__ . '/../app/function.php';

if (file_exists(APP_ROOT . '/config/install.lock')) {
  header("Location: ../index.php");
  exit;
}

// 安全：安装器只允许在“尚未配置”时执行。默认密码仍是出厂哈希才视为未配置，
// 这样即使 install.lock 丢失/被删，已上线站点也不会被任意访客重置管理员口令。
if (!isset($config['password']) || !hash_equals(EASYIMAGE_DEFAULT_PASSWORD_HASH, (string) $config['password'])) {
  exit('<script>window.alert("站点已完成安装，如需重装请手动删除 config/install.lock 并恢复默认密码。");location.href="../index.php";</script>');
}

if (!isset($_POST['password'], $_POST['repassword'], $_POST['user'])) {
  header("Location: index.php");
  exit;
}

$plainPassword = (string) $_POST['password'];
if (!hash_equals($plainPassword, (string) $_POST['repassword'])) {
  exit('<script>window.alert("两次密码不一致请重新输入!");location.href="./index.php";</script>');
}
if (strlen($plainPassword) < 8 || strlen($plainPassword) > 64) {
  exit('<script>window.alert("密码长度应为 8~64 位!");location.href="./index.php";</script>');
}

// 登录端校验的是 SHA256(明文)（前端 SHA256.js 计算后提交），此处必须保持同一算法；
// 原实现写入 password_hash()（bcrypt），导致安装完成后永远无法登录。
$config['password'] = hash('sha256', $plainPassword);
$safeUser = preg_replace('/[^\w\.\-]/', '', (string) $_POST['user']);
$config['user'] = $safeUser !== '' ? $safeUser : 'admin';

if (isset($_POST['domain'])) {
  $config['domain'] = rtrim((string) $_POST['domain'], '/');
}

if (isset($_POST['imgurl'])) {
  $config['imgurl'] = rtrim((string) $_POST['imgurl'], '/');
}

$config_file = APP_ROOT . '/config/config.php';
cache_write($config_file, $config);

// 创建安装程序锁
file_put_contents(APP_ROOT . '/config/install.lock', '安装程序锁定文件。');

// 删除安装目录
if (isset($_POST['del_install'])) {
  if ($_POST['del_install'] === "del") {
    try {
      @deldir(APP_ROOT . "/install");
    } catch (Exception $e) {
      echo $e->getMessage();
    }
  }
}

// 删除多余文件.whitesource
if (isset($_POST['del_extra_files'])) {
  if ($_POST['del_extra_files'] === "del") {
    try {
      @unlink(APP_ROOT . '/LICENSE');
      @unlink(APP_ROOT . '/README.md');
      @deldir(APP_ROOT . "/admin/logs");
      @deldir(APP_ROOT . "/SECURITY.md");
      @unlink(APP_ROOT . '/.whitesource');
      @unlink(APP_ROOT . '/CODE_OF_CONDUCT.md');
      @unlink(APP_ROOT . '/config/EasyIamge.lock');
      @deldir(APP_ROOT . "/.github");
      @deldir(APP_ROOT . "/.git");
      @deldir(APP_ROOT . "/docs");
    } catch (Exception $e) {
      echo $e->getMessage();
    }
  }
}
?>
<!-- 跳转主页 -->

<script>
  window.alert("安装成功,即将为您跳转到登陆界面!");
  location.href = "../admin/index.php";
</script>