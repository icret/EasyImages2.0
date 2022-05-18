<?php
include_once __DIR__ . "/header.php";

if (isset($_POST['md5'])) {
    $value = md5($_POST['md5']);
} else {
    $value = null;
}

?>
<div class="row">
    <div class="col-md-12">
        <p class="text-primary">忘记账号可以打开<code>/config/config.php</code>文件找到user对应的键值->填入</p>
        <p class="text-success">忘记密码请将密码转换成MD5小写(<a href="<?php echo $config['domain'] . '/application/md5.php'; ?>" target="_blank" class="text-purple">转换网址</a>)->打开<code>/config/config.php</code>文件->找到password对应的键值->填入</p>
        <h4 class="text-danger">更改后会立即生效并重新登录,请务必牢记账号和密码! </h4>
    </div>
    <div class="col-md-12">
        <form action="<?php echo $_SERVER['SCRIPT_NAME']; ?>" method="post" class="form-horizontal">
            <div class="form-group">
                <label for="md5" class="col-sm-2">要加密的密码</label>
                <div class="col-md-6 col-sm-10">
                    <input type="text" class="form-control" id="md5" name="md5" value="<?php echo $value; ?>" required placeholder="eg: EasyImage2.0" onkeyup="this.value=this.value.trim()">
                </div>
            </div>
            <div class="form-group">
                <div class="col-sm-offset-2 col-sm-10">
                    <button type="submit" class="btn btn-primary">获取MD5</button>
                </div>
            </div>
        </form>
    </div>
</div>
<script>
    // 更改网页标题
    document.title = "更改密码 密码MD5加密- <?php echo $config['title']; ?>"
</script>
<?php

include_once __DIR__ . "/footer.php";
