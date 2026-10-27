<?php
include_once __DIR__ . "/header.php";

// 安全：口令转换走 bcrypt(cost 12, 单次约数百 ms CPU)。本页可匿名访问，若匿名即可触发在线散列，
// 会被当作 CPU 放大（DoS）面。故转换功能仅限"已登录管理员"使用；
// 真正被锁在后台之外的用户走静态恢复指引（见下）：用发行包 config.php 覆盖 -> 以出厂默认口令登录 -> 立即在后台改密。
$canConvert = is_who_login('admin');

$value = '';
if ($canConvert && isset($_POST['md5']) && is_string($_POST['md5'])) {
    // 本页入参为明文，显式走明文路径：bcrypt(SHA256(明文))，与登录/安装存储法配对
    $value = easyimage_password_hash_plaintext($_POST['md5']);
}

?>
<div class="row">
    <div class="col-md-12">
        <p class="text-primary">忘记账号可以打开<code>/config/config.php</code>文件找到<code data-toggle="tooltip" title="'user'=><strong>admin</strong>'">user</code>对应的键值->填入</p>
        <?php if ($canConvert) { ?>
        <p class="text-success">在此输入明文口令生成加密串（加盐 bcrypt）->打开<code>/config/config.php</code>文件->找到<code data-toggle="tooltip" title="'password'=>'<strong>$2y$12$...</strong>'">password</code>对应的键值->粘贴填入</p>
        <h4 class="text-danger">更改后会立即生效并重新登录,请务必牢记账号和密码! </h4>
        <?php } else { ?>
        <p class="text-warning">口令转换仅限登录后的管理员使用（防止匿名滥用在线散列）。若已被锁在后台之外，请按下列步骤恢复：<br>1) 用发行包内的 <code>/config/config.php</code> 覆盖当前文件，口令即回到出厂默认；<br>2) 用默认口令 <code>admin@123</code> 登录后台；<br>3) 登录后立即在「管理员设置」改为自定义强口令（8~64 位）。</p>
        <?php } ?>
    </div>
    <?php if ($canConvert) { ?>
    <div class="col-md-12">
        <form action="<?php echo $_SERVER['SCRIPT_NAME']; ?>" method="post" class="form-horizontal">
            <div class="form-group">
                <label for="md5" class="col-sm-2">要加密的密码</label>
                <div class="col-md-6 col-sm-10">
                    <input type="text" class="form-control" id="md5" name="md5" value="<?php echo htmlspecialchars((string) $value, ENT_QUOTES); ?>" required placeholder="eg: EasyImage2.0" onkeyup="this.value=this.value.trim()">
                </div>
            </div>
            <div class="form-group">
                <div class="col-sm-offset-2 col-sm-10">
                    <button type="submit" class="btn btn-primary">获取新的密码</button>
                </div>
            </div>
        </form>
    </div>
    <?php } ?>
</div>
<script>
    // 更改网页标题
    document.title = "获取新的密码 - <?php echo $config['title']; ?>"
</script>
<?php

include_once __DIR__ . "/footer.php";
