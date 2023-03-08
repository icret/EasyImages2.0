<?php

/**
 * 删除文件页面
 */
require_once __DIR__ . '/header.php';

if (empty($_REQUEST)) {
    echo '
    <script>
        new $.zui.Messager("没有要删除的图片!", {
            type: "danger", // 定义颜色主题 
            icon: "exclamation-sign" // 定义消息图标
        }).show();
    </script>
    ';
}

$img = rand_imgurl() . '/public/images/404.png';
if (isset($_GET['url'])) {
    $img = strip_tags($_GET['url']);
}

// 解密删除
if (isset($_GET['hash'])) {
    $delHash = $_GET['hash'];
    $delHash = urlHash($delHash, 1);

    if ($config['image_recycl']) {
        // 如果开启回收站则进入回收站
        if (checkImg($delHash, 3, 'recycle/') == true) {
            echo '
        <script>
            new $.zui.Messager("删除成功", {
                type: "success", // 定义颜色主题 
                icon: "ok-sign" // 定义消息图标
            }).show();
        </script>
        ';
        } else {
            echo '
			<script>
                new $.zui.Messager("文件不存在!", {
                    type: "danger", // 定义颜色主题
                    icon: "exclamation-sign" // 定义消息图标
                }).show();
            </script>
			';
        }
    } else {
        // 否则直接删除
        getDel($delHash, 'url');
    }
}

// 检查登录后再处理url删除请求
if (is_who_login('admin')) {

    // 广场页面删除
    if (isset($_GET['url'])) {
        getDel($img, 'url');
    }

    // 从管理页面删除
    if (isset($_GET['url_admin_inc'])) {
        $del_url = $_GET['url_admin_inc'];
        if ($config['hide_path']) {
            $del_url = $config['domain'] . $config['path'] . parse_url($del_url)['path'];
        }
        getDel($del_url, 'url');
    }
    // 回收
    if (isset($_GET['recycle_url'])) {
        $recycle_url = $_GET['recycle_url'];
        $recycle_url = parse_url($recycle_url)['path'];
        if (file_exists(APP_ROOT . $recycle_url)) {
            checkImg($recycle_url, 3);
            echo '
			<script>
                new $.zui.Messager("已放入回收站!", {
                    type: "success", // 定义颜色主题
                    icon: "ok" // 定义消息图标
                }).show();
            </script>
			';
        } else {
            echo '
			<script>
                new $.zui.Messager("文件不存在!", {
                    type: "danger", // 定义颜色主题
                    icon: "exclamation-sign" // 定义消息图标
                }).show();
            </script>
			';
        }
    }
} else {
    if (isset($_GET['url'])) {
        echo '
        <script>
            new $.zui.Messager("请使用管理员账号登录再删除!", {
			type: "danger", // 定义颜色主题
			icon: "exclamation-sign" // 定义消息图标
            }).show();
            // 延时2s跳转			
            window.setTimeout("window.location=\'/../admin/index.php \'",3000);
        </script>
		';
    }
}
?>
<div class="col-md-4 col-md-offset-4">
    <a href="<?php echo $img; ?>" target="_blank"><img src="<?php echo $img; ?>" alt="简单图床-EasyImage" class="img-thumbnail"></a>
    <form class="form-inline" method="get" action="<?php $_SERVER['SCRIPT_NAME']; ?>" id="form" name="delForm" onSubmit="getStr();">
        <div class="form-group">
            <label for="exampleInputInviteCode3">删除图片-格式:</label>
            <input type="text" class="form-control" id="exampleInputInviteCode3" name="url" placeholder="https://i1.100024.xyz/i/2021/05/04/10fn9ei.jpg">
        </div>
        <button type="submit" class="btn btn-danger">删除</button>
    </form>
</div>
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
<?php require_once __DIR__ . '/footer.php';
