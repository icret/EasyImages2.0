<?php
include_once __DIR__ . "/header.php";

if (!$config['show_exif_info']) exit(header('Location: ' . $config['domain'] . '?exif#closed'));

// 获取图片地址
if (isset($_GET['img'])) {
    // 过滤特殊符号
    $getIMG = strip_tags($_GET['img']);
    $del_url = $config['domain'] . $getIMG;
} else {
    // 未获取到图片地址
    $getIMG = "/public/images/404.png";
    $del_url = "#";
}

// 开启隐藏上传目录
if ($config['hide_path']) {
    $img_url = rand_imgurl() . str_replace($config['path'], '/', $getIMG);
} else {
    // 关闭隐藏上传目录
    $img_url =   rand_imgurl() . $getIMG;
}

// 图片真实路径
$imgABPath = APP_ROOT . $getIMG;

// 图片是否存在
if (!file_exists($imgABPath)) {
    $imgABPath = APP_ROOT . "/public/images/404.png";
    $img_url = rand_imgurl() . "/public/images/404.png";
}

// 图片尺寸
$imgSize = filesize($imgABPath);
// 上传时间
$upTime = filemtime($imgABPath);
// 广告
if ($config['ad_top']) echo $config['ad_top_info'];

?>
<div class="col-md-12">
    <div class="col-md-6" style="text-align: center;">
        <img data-toggle="lightbox" src="<?php echo $img_url; ?>" data-image="<?php echo $img_url; ?>" id="img1" class="img-rounded" height="234px" data-caption="<?php echo pathinfo($img_url, PATHINFO_FILENAME); ?>的详细信息" alt="<?php echo $img_url; ?>" />
    </div>
    <div class="col-md-6 table-responsive table-condensed">
        <table class="table table-hover table-striped table-bordered">
            <tbody>
                <tr>
                    <td>图片名称</td>
                    <td> <?php echo pathinfo($getIMG, PATHINFO_FILENAME); ?></td>
                </tr>
                <tr>
                    <td>图片大小</td>
                    <td><?php echo getDistUsed($imgSize); ?></td>
                </tr>
                <tr>
                    <td>图片类型</td>
                    <td>image/<?php echo pathinfo($getIMG, PATHINFO_EXTENSION); ?></td>
                </tr>
                <tr>
                    <td>图片宽高</td>
                    <td><span id="wh"></span>px</td>
                </tr>
                <tr>
                    <td>上传时间</td>
                    <td><?php echo date("Y-m-d H:i:s", $upTime); ?></td>
                </tr>
                <tr>
                    <td>文件操作</td>
                    <td>
                        <a class="btn btn-mini btn-primary" href="<?php echo  $img_url; ?>" target="_blank"><i class="icon icon-picture"> 查看</i></a>
                        <a class="btn btn-mini btn-primary" href="/application/down.php?dw=<?php echo  $getIMG; ?>" target="_blank"><i class="icon icon-cloud-download"> 下载</i></a>
                        <?php if (!empty($config['report'])) : ?>
                            <a class="btn btn-mini btn-warning" href="<?php echo $config['report'] . '?Website1=' . $img_url; ?>" target="_blank"><i class="icon icon-question-sign"> 举报</i></a>
                        <?php endif; ?>
                        <?php if (is_who_login('admin')) : ?>
                            <a class="btn btn-mini btn-warning" href="/application/del.php?recycle_url=<?php echo $getIMG; ?>" target="_blank"><i class="icon icon-undo"> 回收</i></a>
                            <a class="btn btn-mini btn-danger" href="/application/del.php?url=<?php echo $del_url; ?>" target="_blank"><i class="icon icon-trash"> 删除</i></a>
                        <?php endif; ?>
                    </td>
                </tr>
            </tbody>
        </table>
        <h4 class="with-padding hl-gray"><i class="icon icon-info-sign"> 此图片来自网友上传, 不代表<a href="/admin/terms.php" target="_blank">本站立场</a>, 若有侵权, 请举报或联系管理员!</i></h4>
        <!--
        <h4>图片名称: < ?php echo pathinfo($getIMG, PATHINFO_FILENAME); ?></h4>
        <h4>图片大小: < ?php echo getDistUsed($imgSize); ?></h4>
        <h4>图片类型: image/< ?php echo pathinfo($getIMG, PATHINFO_EXTENSION); ?></h4>
        <h4>图片宽高: <span id="wh"></span>px</h4>
        <h4>上传时间: < ?php echo date("Y-m-d H:i:s", $upTime); ?></h4>
        <h4>文件操作：
            <a class="btn btn-mini btn-primary" href="< ?php echo  $img_url; ?>" target="_blank"><i class="icon icon-picture"> 查看</i></a>
            < ?php if (is_who_login('admin')) : ?>
                <a class="btn btn-mini btn-primary" href="/application/del.php?recycle_url=< ?php echo $getIMG; ?>" target="_blank"><i class="icon icon-undo"> 回收</i></a>
                <a class="btn btn-mini btn-primary" href="/application/del.php?url=< ?php echo $del_url; ?>" target="_blank"><i class="icon icon-trash"> 删除</i></a>
            < ?php endif; ?>
        </h4>
        <h4 class="with-padding hl-gray"><i class="icon icon-info-sign"> 此图片来自网友上传, 不代表<a href="/admin/terms.php" target="_blank">本站立场</a>, 若有侵权, 请联系管理员删除!</i></h4>
            -->
        <!-- 读取Exif信息
        <h4>使用设备: <span id="makeAndModel"></span></h4>
        <div class="col-md-12">
            <p>
                <button type="button" class="btn btn-primary btn-sm" data-toggle="collapse" data-target="#collapseExample">Exif 信息 <i class="icon icon-caret-down"></i></button>
                <a class="btn btn-primary btn-sm" href="<php echo  $img_url; ?>" target="_blank">查看图片 <i class="icon icon-picture"></i></a>
                <php if (is_who_login('admin')) : ?>
                    <a class="btn btn-primary btn-sm" href="/application/del.php?url=<php echo $del_url; ?>" target="_blank">删除图片 <i class="icon icon-trash"></i></a>
                <php endif; ?>
            </p>
            <div class="collapse" id="collapseExample">
                <pre id="allMetaDataSpan"></pre>
            </div>
        </div>
        -->
    </div>
</div>
<div class="col-md-12" style="padding-bottom: 10px;">
    <div class="col-md-6" style="padding-bottom: 10px;">
        <div class="input-group">
            <span class="input-group-addon"><i class="icon icon-link"></i> 直 链&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span>
            <input type="text" class="form-control" id="links" onclick="copyText()" value="<?php echo $img_url; ?>">
            <span class="input-group-btn"><button class="btn btn-default copyBtn1" type="button">复制</button></span>
        </div>
    </div>
    <div class="col-md-6" style="padding-bottom: 10px;">
        <div class="input-group">
            <span class="input-group-addon"><i class="icon icon-chat"></i> 论坛代码&nbsp;&nbsp;&nbsp;</span>
            <input type="text" class="form-control" id="bbscode" value="[img]<?php echo $img_url; ?>[/img]">
            <span class="input-group-btn"><button class="btn btn-default copyBtn2" type="button">复制</button></span>
        </div>
    </div>
</div>
<div class="col-md-12" style="padding-bottom: 10px;">
    <div class="col-md-6" style="padding-bottom: 10px;">
        <div class="input-group">
            <span class="input-group-addon"><i class="icon icon-code"></i> MarkDown</span>
            <input type="text" class="form-control" id="markdown" value="![简单图床 - EasyImage](<?php echo $img_url; ?>)">
            <span class="input-group-btn"><button class="btn btn-default copyBtn3" type="button">复制</button></span>
        </div>
    </div>
    <div class="col-md-6" style="padding-bottom: 10px;">
        <div class="input-group">
            <span class="input-group-addon"><i class="icon icon-html5"></i> HTML&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span>
            <input type="text" class="form-control" id="html" value='<img src="<?php echo $img_url; ?>" alt="简单图床 - EasyImage" />'>
            <span class="input-group-btn"><button class="btn btn-default copyBtn4" type="button">复制</button></span>
        </div>
    </div>
</div>

<? /** 底部广告 */ if ($config['ad_bot']) echo $config['ad_bot_info']; ?>

<!-- 随机图片 -->
<?php if ($config['info_rand_pic']) : ?>
    <div class="col-md-12" style="padding-bottom: 10px;">
        <h4 class="header-dividing">当月随机图片：</h4>
        <div class="cards cards-borderless">
            <?php
            $logFile = APP_ROOT . '/admin/logs/upload/' . date('Y-m') . '.php';
            if (is_file($logFile)) {
                include_once $logFile;
                for ($i = 0; $i <= 7; $i++) {
                    $randName = array_rand($logs, 1);
                    // echo  $img_url . $logs[$randName]['path'];
                    echo '
                <div class="col-md-4 col-sm-6 col-lg-3">
                    <a class="card" href="?img=' . $logs[$randName]['path'] . '" target="_blank">
                    <img src="' . $logs[$randName]['path'] . '">
                    <div class="card-content text-muted text-ellipsis">' . $logs[$randName]['source'] . '</div>
                    </a>
                </div>';
                }
            } else {
                echo '<div class="alert alert-danger">本月还没有上传的图片哟~~ <br />快来上传第一张吧~!</div>';
            }
            ?>
        </div>
    </div>
<?php endif; ?>
<!-- <script src="< php static_cdn(); ?>/public/static/exif/exif.js"></script> -->
<script src="<?php static_cdn(); ?>/public/static/EasyImage.js"></script>
<script src="<?php static_cdn(); ?>/public/static/zui/lib/clipboard/clipboard.min.js"></script>
<script>
    // 获取图片长宽 https://www.cnblogs.com/houxianzhou/p/14807983.html
    var imgReady = (function() {
        var list = [],
            intervalId = null,
            // 用来执行队列
            tick = function() {
                var i = 0;
                for (; i < list.length; i++) {
                    list[i].end ? list.splice(i--, 1) : list[i]();
                };
                !list.length && stop();
            },
            // 停止所有定时器队列
            stop = function() {
                clearInterval(intervalId);
                intervalId = null;
            };
        return function(url, ready, load, error) {
            var onready, width, height, newWidth, newHeight,
                img = new Image();
            img.src = url;
            // 如果图片被缓存，则直接返回缓存数据
            if (img.complete) {
                ready.call(img);
                load && load.call(img);
                return;
            };
            width = img.width;
            height = img.height;
            // 加载错误后的事件
            img.onerror = function() {
                error && error.call(img);
                onready.end = true;
                img = img.onload = img.onerror = null;
            };
            // 图片尺寸就绪
            onready = function() {
                newWidth = img.width;
                newHeight = img.height;
                if (newWidth !== width || newHeight !== height || newWidth * newHeight > 1024) {
                    // 如果图片已经在其他地方加载可使用面积检测
                    ready.call(img);
                    onready.end = true;
                };
            };
            onready();
            // 完全加载完毕的事件
            img.onload = function() {
                // onload在定时器时间差范围内可能比onready快
                // 这里进行检查并保证onready优先执行
                !onready.end && onready();
                load && load.call(img);
                // IE gif动画会循环执行onload，置空onload即可
                img = img.onload = img.onerror = null;
            };
            // 加入队列中定期执行
            if (!onready.end) {
                list.push(onready);
                // 无论何时只允许出现一个定时器，减少浏览器性能损耗
                if (intervalId === null) intervalId = setInterval(tick, 40);
            };
        };
    })();

    imgReady('<?php echo $img_url; ?>', function() {
        // alert('size ready: width=' + this.width + '; height=' + this.height);
        var hw = document.getElementById("wh");
        hw.innerHTML = this.width + "x" + this.height
    });

    // Exif信息
    /*
    window.onload = getExif;

    function getExif() {
        var img1 = document.getElementById("img1");
        EXIF.getData(img1, function() {
            var make = EXIF.getTag(this, "Make");
            var model = EXIF.getTag(this, "Model");
            var makeAndModel = document.getElementById("makeAndModel");
            makeAndModel.innerHTML = `${make} ${model}`;
        });
        var img2 = document.getElementById("img1");
        EXIF.getData(img2, function() {
            var allMetaData = EXIF.getAllTags(this);
            var allMetaDataSpan = document.getElementById("allMetaDataSpan");

            allMetaDataSpan.innerHTML = EXIF.pretty(this);;
        });
    }
    */
    //禁用右键
    document.onkeydown = function() {
        var e = window.event || arguments[0];
        if (e.keyCode == 123) {
            //    alert('禁止F12');
            return false;
        } else if ((e.ctrlKey) && (e.shiftKey) && (e.keyCode == 73)) {
            //    alert('禁止Ctrl+Shift+I');
            return false;
        } else if ((e.ctrlKey) && (e.keyCode == 85)) {
            //    alert('禁止Ctrl+u');
            return false;
        } else if ((e.ctrlKey) && (e.keyCode == 83)) {
            //    alert('禁止Ctrl+s');
            return false;
        }
    }
    // 屏蔽鼠标右键
    document.oncontextmenu = function() {
        new $.zui.Messager("正在查看图片详细信息", {
            type: "success", // 定义颜色主题 
            icon: "exclamation-sign" // 定义消息图标
        }).show();
        return false;
    }

    // 更改网页标题
    document.title = "图片<?php echo basename($getIMG); ?>的详细信息 - <?php echo $config['title']; ?>"
</script>
<?php
/** 引入底部 */
require_once APP_ROOT . '/application/footer.php';
