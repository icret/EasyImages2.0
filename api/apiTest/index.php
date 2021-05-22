<?php
echo '<title> - EasyImage2.0</title>';
require_once '../../libs/function.php';
require_once APP_ROOT . '/libs/header.php';
require_once APP_ROOT . '/config/api_key.php';

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
                <button type="submit" class="btn btn-primary">提交</button>
            </form>
        </div>
    </div>
</div>

<script>
    document.title = "API图片上传测试 - <?php echo $config['title']; ?>";
</script>
<?php require_once APP_ROOT . '/libs/footer.php';
