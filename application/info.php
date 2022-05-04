<?php
include_once __DIR__ . "/header.php";

if (!$config['show_exif_info']) exit(header('Location: ' . $config['domain'] . '?exif#closed'));

// 获取图片地址
if (isset($_GET['img'])) {
    // 过滤特殊符号
    $getIMG = strip_tags($_GET['img']);
} else {
    // 未获取到图片地址
    $getIMG = rand_imgurl() . "/public/images/404.png";
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
    $getIMG = rand_imgurl() . "/public/images/404.png";
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
        <a href="<?php echo $img_url; ?>" data-toggle="lightbox" data-group="image-group-1"><img src="<?php echo creat_thumbnail_by_list($getIMG); ?>" id="img1" width="350px" height="200px" class="img-rounded" alt=" <?php echo basename($getIMG); ?>"></a>
    </div>
    <div class="col-md-6">
        <h4>图片名称: <?php echo pathinfo($getIMG, PATHINFO_FILENAME); ?></h4>
        <h4>图片类型: <?php echo pathinfo($getIMG, PATHINFO_EXTENSION); ?></h4>
        <h4>图片宽高: <span id="wh"></span>px</h4>
        <h4>图片大小: <?php echo getDistUsed($imgSize); ?></h4>
        <h4>上传时间: <?php echo date("Y-m-d H:i:s", $upTime); ?></h4>
        <h4>使用设备: <span id="makeAndModel"></span></h4>
        <div class="col-md-12">
            <p>
                <button type="button" class="btn btn-primary btn-sm" data-toggle="collapse" data-target="#collapseExample">Exif 信息 <i class="icon icon-caret-down"></i></button>
                <a class="btn btn-primary btn-sm" href="<?php echo  $getIMG; ?>" target="_blank">查看图片 <i class="icon icon-picture"></i></a>
                <a class="btn btn-primary btn-sm" href="/application/del.php?url=<?php echo  $getIMG; ?>" target="_blank">删除图片 <i class="icon icon-trash"></i></a>
            </p>
            <div class="collapse" id="collapseExample">
                <div class="bg-primary">
                    <pre id="allMetaDataSpan"></pre><!-- style="background-color:transparent;"设置透明 -->
                </div>
            </div>
        </div>
    </div>
</div>
<? /** 底部广告 */ if ($config['ad_bot']) echo $config['ad_bot_info']; ?>
<div class="col-md-12" style="margin-top: 10px;">
    <div class="col-md-12" style="padding-bottom: 10px;">
        <div class="col-md-6" style="padding-bottom: 10px;">
            <div class="input-group">
                <span class="input-group-addon"><i class="icon icon-link"></i> 直 连&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span>
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
</div>
<script src="<?php static_cdn(); ?>/public/static/exif/exif.js"></script>
<script src="<?php static_cdn(); ?>/public/static/EasyImage.js"></script>
<script src="<?php static_cdn(); ?>/public/static/zui/lib/clipboard/clipboard.min.js"></script>
<script>
    // 获取图片长宽
    function getImgNaturalDimensions(oImg, callback) {
        var nWidth, nHeight;
        if (!oImg.naturalWidth) { // 现代浏览器

            nWidth = oImg.naturalWidth;
            nHeight = oImg.naturalHeight;
            callback({
                w: nWidth,
                h: nHeight
            });

        } else { // IE6/7/8
            var nImg = new Image();

            nImg.onload = function() {
                var nWidth = nImg.width,
                    nHeight = nImg.height;
                callback({
                    w: nWidth,
                    h: nHeight
                });
            }
            nImg.src = oImg.src;
        }
    }
    var img = document.getElementById("img1");

    getImgNaturalDimensions(img, function(dimensions) {
        var hw = document.getElementById("wh");
        hw.innerHTML = dimensions.w + "x" + dimensions.h
    })

    // Exif信息
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
    // 更改网页标题
    document.title = "图片<?php echo basename($getIMG); ?>的详细信息 - <?php echo $config['title']; ?>"
</script>
<?php
/** 引入底部 */
require_once APP_ROOT . '/application/footer.php';
