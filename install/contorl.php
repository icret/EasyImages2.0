<?php
require_once __DIR__ . '/../app/function.php';

if (file_exists(APP_ROOT . '/config/install.lock')) {
  header("Location: ../index.php");
  exit;
}

// 安全：安装器只允许在“尚未配置”时执行。当前口令仍为出厂默认才视为未配置（存储已为 bcrypt，故用 password_verify 比对默认摘要），
// 这样即使 install.lock 丢失/被删，已上线站点也不会被任意访客重置管理员口令。
if (!isset($config['password']) || !password_verify(EASYIMAGE_DEFAULT_PASSWORD_HASH, (string) $config['password'])) {
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
// 安全：不向后兼容——禁止用出厂默认口令完成安装，强制设置自定义密码。
// 按“有效摘要”比对（与输入形态无关）：拦明文 admin@123，也不允许落入与默认凭据等价的存储值
if (hash_equals(EASYIMAGE_DEFAULT_PASSWORD_HASH, hash('sha256', $plainPassword))) {
  exit('<script>window.alert("禁止使用出厂默认密码，请设置自定义密码!");location.href="./index.php";</script>');
}

// 安装器入参恒为明文，显式走明文路径：存 bcrypt(SHA256(明文))，不走“十六进制当摘要”的猜测分支；
// 登录校验走 easyimage_password_verify()→password_verify(摘要, bcrypt)，与存法配对。
$config['password'] = easyimage_password_hash_plaintext($plainPassword);
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