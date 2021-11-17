<?php

/**
 * 删除文件页面
 */
require_once './header.php';
echo '<div class="col-md-4 col-md-offset-4">
	<div id="title" style="margin: 10px;"></div>

        <form class="form-inline" method="get" action="' . $_SERVER['SCRIPT_NAME'] . '" id="form" name="delForm" onSubmit="getStr();">
            <div class="form-group">
                <label for="exampleInputInviteCode3">删除图片-格式：</label>
                <input type="text" class="form-control" id="exampleInputInviteCode3" name="url" placeholder="https://i1.100024.xyz/i/2021/05/04/10fn9ei.jpg">
            </div>
            <button type="submit" class="btn btn-danger">删除</button>
        </form>
	</div>
    ';
if (empty($_REQUEST)) {
    echo '
    <script>
    new $.zui.Messager("没有要删除的图片！", {type: "danger" // 定义颜色主题 
    }).show();
    </script>
    ';
    //header("refresh:3;url=".$config['domain']."");
    
} elseif (isset($_GET['url'])) {
    $img = $_GET['url'];
    echo '
    <div class="col-md-12">
    <a href="' . $img . '" target="_blank"><img src="' . $img  . '" alt="简单图床-EasyImage" class="img-thumbnail"></a>
    </div>';
}

// 解密删除
if (isset($_GET['hash'])) {
    $delHash = $_GET['hash'];
    $delHash = urlHash($delHash, 1);
    getDel($delHash, 'hash');
}

// 检查登录后再处理url删除请求
if (is_online()) {
    if (isset($_GET['url'])) {
        getDel($_GET['url'], 'url');
    }
} else {
    if (isset($_GET['url'])) {
        echo '
			<script>
            new $.zui.Messager("请登录后再删除", {type: "danger" // 定义颜色主题 
            }).show();
            // 延时2s跳转			
            window.setTimeout("window.location=\'/../application/login.php \'",2000);
            </script>
			';
            //header("refresh:2;url=".$config['domain']."/application/login.php");            
    }
}

require_once APP_ROOT . '/application/footer.php';
?>
<script>
    // 修改网页标题
    document.title = "删除图片 - <?php echo $config['title']; ?>";

    var oBtn = document.getElementById('del');
    var oTi = document.getElementById('title');
    if ('oninput' in oBtn) {
        oBtn.addEventListener("input", getWord, false);
    } else {
        oBtn.onpropertychange = getWord;
    }

    function getWord() {
        oTi.innerHTML = '<img src="' + oBtn.value + '" width="200" class="img-rounded" /><br />';
    }
</script>