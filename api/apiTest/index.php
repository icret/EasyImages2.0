<?php
echo '<title> - EasyImage2.0</title>';
require_once '../../application/function.php';
require_once APP_ROOT . '/application/header.php';
require_once APP_ROOT . '/config/api_key.php';

// 如果关闭Api上传并且没有登录的情况下关闭测试接口
if (!$config['apiStatus'] and !is_online()) {
    exit('<script>
        new $.zui.Messager("Api关闭，请登录", {type: "danger" // 定义颜色主题 
        }).show();
        // 延时2s跳转			
        window.setTimeout("window.location=\'/../application/login.php \'",2000);
        </script>');
}

?>
<div class="container">
</div class="row">
<div class="col-md-12">
    <h4>测试Token：<code><?php echo $tokenList['1']; ?></code></h4>
    <form action="../index.php" method="post" enctype="multipart/form-data" class="form-inline" target="_blank">
        <div class="form-group">
            <input type="file" name="image" accept="image/*" class="form-control" />
        </div>
        <div class="form-group">
            <input type="text" name="token" placeholder="请输入Token" class="form-control" />
        </div>
        <button type="submit" class="btn btn-primary">上传</button>
    </form>
</div>
</div>
</div>

<script>
    document.title = "API图片上传测试 - <?php echo $config['title']; ?>";
</script>
<?php require_once APP_ROOT . '/application/footer.php';
