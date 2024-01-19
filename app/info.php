<?php
include_once __DIR__ . "/header.php";

if (!$config['show_exif_info'] && !is_who_login('admin')) exit(header('Location: ' . $config['domain'] . '?exif#closed'));

// 获取图片地址
if (isset($_GET['img'])) {
    // 过滤特殊符号
    $getIMG = strip_tags($_GET['img']);
} elseif (isset($_GET['history'])) {
    // 过滤特殊符号
    if ($config['hide_path']) {
        $getIMG = $config['path'] . ltrim(strip_tags($_GET['history']), '/');
    } else {
        $getIMG = strip_tags($_GET['history']);
    }
} else {
    // 未获取到图片地址
    $getIMG = "/public/images/404.png";
}


// 开启隐藏上传目录
if ($config['hide_path']) {
    $img_url = rand_imgurl() . str_replace($config['path'], '/', $getIMG);

    // 获取当前图片日志文件
    $logs = str_replace('/', '-', substr(parse_url($img_url, PHP_URL_PATH), 1, 7));
} else {
    // 关闭隐藏上传目录
    $img_url = rand_imgurl() . $getIMG;

    // 获取当前图片日志文件
    $logs = str_replace('/', '-', substr(str_replace($config['path'], '', parse_url($img_url, PHP_URL_PATH)), 0, 7));
}

// 导入日志文件
$logsName = basename($img_url);
if (is_file(APP_ROOT . '/admin/logs/upload/' . $logs . '.php')) {
    include APP_ROOT . '/admin/logs/upload/' . $logs . '.php';
} else {
    $logs = array($logsName => array('source' => '请在图床安全中开启上传日志!', 'date' => '请在图床安全中开启上传日志!', 'ip' => '0.0.0.0', 'port' => '0', 'user_agent' => '请在图床安全中开启上传日志!', 'path' => '请在图床安全中开启上传日志!', 'size' => '请在图床安全中开启上传日志!', 'md5' => '请在图床安全中开启上传日志!', 'checkImg' => '请在图床安全中开启上传日志!', 'from' => '请在图床安全中开启上传日志!'));
}
if (empty($logs[$logsName])) {
    $logs = array($logsName => array('source' => '日志不存在', 'date' => '日志不存在', 'ip' => '0.0.0.0', 'port' => '0', 'user_agent' => '日志不存在', 'path' => '日志不存在', 'size' => '日志不存在', 'md5' => '日志不存在', 'checkImg' => '日志不存在', 'from' => '日志不存在'));
}
// 图片真实路径
$imgABPath = APP_ROOT . $getIMG;
// 图片是否存在
if (!is_file($imgABPath)) {
    $imgABPath = APP_ROOT . "/public/images/404.png";
    $img_url = $config['domain'] . "/public/images/404.png";
}

// 图片尺寸
$imgSize = filesize($imgABPath);
// 上传时间
$upTime = filemtime($imgABPath);
// 广告
if ($config['ad_top']) echo $config['ad_top_info'];
?>
<div class="col-md-12" style="margin-bottom:10px;">
    <div class="col-md-6">
        <img src="<?php echo $img_url; ?>" class="img-rounded" height="436px" width="540px" data-toggle="lightbox" id="img1" data-caption="<?php echo pathinfo($img_url, PATHINFO_FILENAME); ?>的详细信息" alt="<?php echo $img_url; ?>" />
    </div>
    <div class="col-md-6 table-responsive table-condensed">
        <table class="table table-hover table-striped table-bordered text-nowrap">
            <tbody>
                <tr>
                    <td>图片名称</td>
                    <td> <?php echo basename($getIMG); ?></td>
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
                <!-- <tr>
                    <td>使用设备</td>
                    <td id="makeAndModel"></td>
                </tr> -->
                <tr>
                    <td>上传时间</td>
                    <td><?php echo $logs[$logsName]['date']; ?></td>
                </tr>
                <?php if (is_who_login('admin')) : ?>
                    <tr class="text-primary">
                        <td>原始名称</td>
                        <td><?php echo htmlspecialchars($logs[$logsName]['source']); ?></td>
                    </tr>
                    <tr class="text-primary">
                        <td>原始大小</td>
                        <td><?php echo $logs[$logsName]['size']; ?></td>
                    </tr>
                    <tr class="text-primary">
                        <td>上传者IP</td>
                        <td><?php echo $logs[$logsName]['ip'] . ':' . $logs[$logsName]['port']; ?></td>
                    </tr>
                    <tr class="text-primary">
                        <td>上传地址</td>
                        <td><?php echo Ip2Region($logs[$logsName]['ip']); ?></td>
                    </tr>
                    <tr class="text-primary">
                        <td>监黄状态</td>
                        <td><?php echo strstr('OFF', $logs[$logsName]['checkImg']) ? '未开启' : '已通过'; ?></td>
                    </tr>
                    <tr class="text-primary">
                        <td>上传方式</td>
                        <td><?php echo is_numeric($logs[$logsName]['from']) ?  '通过API | Token ID: ' . $logs[$logsName]['from']  : "通过网页"; ?></td>
                    </tr>
                    <tr class="text-primary">
                        <td>文件路径</td>
                        <td><?php echo $logs[$logsName]['path']; ?></td>
                    </tr>
                    <tr class="text-primary">
                        <td>文件MD5</td>
                        <td><?php echo $logs[$logsName]['md5']; ?></td>
                    </tr>
                <?php endif; ?>
                <tr>
                    <td>文件操作</td>
                    <td>
                        <a class="btn btn-mini btn-primary" href="<?php echo  $img_url; ?>" target="_blank"><i class="icon icon-picture"> 查看</i></a>
                        <!-- <a class="btn btn-mini btn-primary" data-toggle="collapse" data-target="#collapseExample"><i class="icon icon-caret-down"> Exif</i></a> -->
                        <a class="btn btn-mini btn-primary" href="" onclick="window.location.replace;"><i class="icon icon-spin icon-refresh"></i> 刷新</a>
                        <a class="btn btn-mini btn-primary" href="/app/down.php?dw=<?php echo  $getIMG; ?>" target="_blank"><i class="icon icon-cloud-download"> 下载</i></a>
                        <?php if (!empty($config['report']) && !is_who_login('admin')) : ?>
                            <a class="btn btn-mini btn-warning" href="<?php echo $config['report'] . '?Website1=' . $img_url; ?>" target="_blank"><i class="icon icon-question-sign"> 举报</i></a>
                        <?php endif; ?>
                        <?php if (is_who_login('admin')) : ?>
                            <a class="btn btn-mini btn-warning" href="#" onclick="ajax_post('<?php echo $getIMG; ?>','recycle')"><i class="icon icon-undo"> 回收</i></a>
                            <a class="btn btn-mini btn-warning" href="#" onclick="ajax_post('<?php echo $getIMG; ?>')"><i class="icon icon-trash"> 删除</i></a>
                        <?php endif; ?>
                    </td>
                </tr>
            </tbody>
        </table>
        <div class="collapse" id="collapseExample">
            <pre style="background-color: rgba(0, 0, 0, 0);" id="allMetaDataSpan"></pre>
        </div>
        <h4 class="with-padding hl-gray"><i class="icon icon-info-sign"> 此图片来自网友上传, 不代表<a href="/admin/terms.php" target="_blank">本站立场</a>, 若有侵权, 请举报或联系管理员!</i></h4>
    </div>
</div>
<div class="col-md-12" style="padding-bottom: 10px;">
    <div class="col-md-6" style="padding-bottom: 10px;">
        <div class="input-group">
            <span class="input-group-addon"><i class="icon icon-link"></i> 直 链&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span>
            <input type="text" class="form-control" id="links" value="<?php echo $img_url; ?>">
            <span class="input-group-btn"><button class="btn btn-default btnLinks" onclick="uploadCopy('links','.btnLinks')" type="button">复制</button></span>
        </div>
    </div>
    <div class="col-md-6" style="padding-bottom: 10px;">
        <div class="input-group">
            <span class="input-group-addon"><i class="icon icon-chat"></i> 论坛代码&nbsp;&nbsp;&nbsp;</span>
            <input type="text" class="form-control" id="bbscode" value="[img]<?php echo $img_url; ?>[/img]">
            <span class="input-group-btn"><button class="btn btn-default btnBbscode" onclick="uploadCopy('bbscode','.btnBbscode')" type="button">复制</button></span>
        </div>
    </div>
</div>
<div class="col-md-12" style="padding-bottom: 10px;">
    <div class="col-md-6" style="padding-bottom: 10px;">
        <div class="input-group">
            <span class="input-group-addon"><i class="icon icon-code"></i> MarkDown</span>
            <input type="text" class="form-control" id="markdown" value="![简单图床 - EasyImage](<?php echo $img_url; ?>)">
            <span class="input-group-btn"><button class="btn btn-default btnMarkDown" onclick="uploadCopy('markdown','.btnMarkDown')" type="button">复制</button></span>
        </div>
    </div>
    <div class="col-md-6" style="padding-bottom: 10px;">
        <div class="input-group">
            <span class="input-group-addon"><i class="icon icon-html5"></i> HTML&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span>
            <input type="text" class="form-control" id="html" value='<img src="<?php echo $img_url; ?>" alt="简单图床 - EasyImage" />'>
            <span class="input-group-btn"><button class="btn btn-default btnHtml" onclick="uploadCopy('html','.btnHtml')" type="button">复制</button></span>
        </div>
    </div>
</div>
<?php /** 底部广告 */ if ($config['ad_bot']) echo $config['ad_bot_info']; ?>
<!-- 随机图片 -->
<?php if ($config['info_rand_pic']) : ?>
    <div class="col-md-12" style="padding-bottom: 10px;">
        <h4 class="header-dividing">当月随机图片：</h4>
        <div class="cards cards-borderless">
            <?php if ($logs[$logsName]['port'] != 0) : for ($i = 0; $i <= 7; $i++) : $randName = array_rand($logs, 1) ?>

                    <div class="col-md-3">
                        <a class="card" href="?img=<?php echo $logs[$randName]['path']; ?>" target="_blank">
                            <img src="thumb.php?img=<?php echo $logs[$randName]['path']; ?>" width="100%">
                            <div class="caption"><?php echo  $logs[$randName]['source']; ?></div>
                        </a>
                    </div>
                <?php endfor; ?>
            <?php else : ?>
                <h3 class="alert alert-danger">本月没有上传图片或上传日志不存在~~</h3>
            <?php endif; ?>
        </div>
    </div>
<?php endif; ?>
<script type="application/javascript" src="<?php static_cdn(); ?>/public/static/EasyImage.js"></script>
<script type="application/javascript" src="<?php static_cdn(); ?>/public/static/zui/lib/imgready/imgready.min.js"></script>
<script type="application/javascript" src="<?php static_cdn(); ?>/public/static/zui/lib/clipboard/clipboard.min.js"></script>
<!-- <script type="application/javascript" src="https://fastly.jsdelivr.net/gh/icret/EasyImages2.0@2.6.5/public/static/exif/exif.js"></script> -->
<script>
    // POST 删除提交
    function ajax_post(url, mode = 'delete') {
        $.post("del.php", {
                url: url,
                mode: mode
            },
            function(data, status) {
                console.log(data)
                let res = JSON.parse(data);
                new $.zui.Messager(res.msg, {
                    type: res.type,
                    icon: res.icon
                }).show();
                // 延时2秒刷新
                window.setTimeout(function() {
                    window.location.reload();
                }, 2000)
            });
    }

    // 获取图片长宽
    $.zui.imgReady($('#img1')[0].src, function() {
        $('#wh').text(this.width + "x" + this.height);
    });

    // // Exif信息
    // window.onload = getExif;

    // function getExif() {
    //     var img1 = document.getElementById("img1");
    //     EXIF.getData(img1, function() {
    //         var make = EXIF.getTag(this, "Make");
    //         var model = EXIF.getTag(this, "Model");
    //         var makeAndModel = document.getElementById("makeAndModel");
    //         makeAndModel.innerHTML = `${make} ${model}`;
    //     });
    //     var img2 = document.getElementById("img1");
    //     EXIF.getData(img2, function() {
    //         var allMetaData = EXIF.getAllTags(this);
    //         var allMetaDataSpan = document.getElementById("allMetaDataSpan");
    //         allMetaDataSpan.innerHTML = EXIF.pretty(this);;
    //     });
    // }

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
require_once APP_ROOT . '/app/footer.php';
