<?php
require_once 'application/header.php';
/** 顶部广告 */
if ($config['ad_top']) echo $config['ad_top_info'];
/** 检查登陆 */
mustLogin();
?>
<div class="col-md-12">
  <!-- 公告 -->
  <div class="marquee">
    <div class="wrap">
      <div id="marquee2">
        <?php if (!empty($config['tips'])) echo $config['tips']; ?>
      </div>
    </div>
  </div>
  <div id='upShowID' class="uploader col-md-12 clo-xs-12" data-ride="uploader" data-url="/application/upload.php">
    <div class="uploader-message text-center">
      <div class="content"></div>
      <button type="button" class="close">x</button>
    </div>
    <div class="uploader-files file-list file-list-lg file-rename-by-click" data-drag-placeholder="选择文件/Ctrl+V粘贴/拖拽至此处" style="min-height: 188px; border-style: dashed;"></div>
    <div class="uploader-actions">
      <div class="uploader-status pull-right text-muted"></div>
      <button type="button" class="btn btn-link uploader-btn-browse"><i class="icon icon-plus"></i> 选择文件</button>
      <button type="button" class="btn btn-link uploader-btn-start"><i class="icon icon-cloud-upload"></i> 开始上传</button>
      <button type="button" class="btn btn-link uploader-btn-stop"><i class="icon icon-pause"></i> 暂停上传</button>
    </div>
  </div>
  <div class="col-md-12 clo-xs-12">
    <ul class="nav nav-tabs">
      <li <?php if ($config['upload_first_show'] == 1) echo 'class="active"'; ?>>
        <a href="#" data-target="#tab2Content1" data-toggle="tab"><i class="icon icon-link"></i> 直链</a>
      </li>
      <li <?php if ($config['upload_first_show'] == 2) echo 'class="active"'; ?>>
        <a href="#" data-target="#tab2Content2" data-toggle="tab"><i class="icon icon-chat"></i> 论坛代码</a>
      </li>
      <li <?php if ($config['upload_first_show'] == 3) echo 'class="active"'; ?>>
        <a href="#" data-target="#tab2Content3" data-toggle="tab"><i class="icon icon-code"></i> MarkDown</a>
      </li>
      <li <?php if ($config['upload_first_show'] == 4) echo 'class="active"'; ?>>
        <a href="#" data-target="#tab2Content4" data-toggle="tab"><i class="icon icon-html5"></i> HTML</a>
      </li>
      <li <?php if ($config['upload_first_show'] == 5) echo 'class="active"'; ?>>
        <a href="#" data-target="#tab2Content5" data-toggle="tab"><i class="icon icon-trash"></i> 删除</a>
      </li>
    </ul>
    <div class="tab-content" style="text-align:right;">
      <div class="tab-pane fade <?php if ($config['upload_first_show'] == 1) echo 'active in';  ?>" id="tab2Content1">
        <textarea class="form-control" rows="5" id="links" readonly></textarea>
        <button class="btn btn-primary" style="margin-top:10px;" onclick="location.reload()"><i class="icon icon-undo"></i> 刷新</button>
        <button id="btnLinks" class="btn btn-primary copyBtn1" data-loading-text="已经复制链接..." style="margin-top:10px;"><i class="icon icon-copy"></i> 复制</button>
      </div>
      <div class="tab-pane fade <?php if ($config['upload_first_show'] == 2) echo 'active in'; ?>" id="tab2Content2">
        <textarea class="form-control" rows="5" id="bbscode" readonly></textarea>
        <button class="btn btn-primary" style="margin-top:10px;" onclick="location.reload()"><i class="icon icon-undo"></i> 刷新</button>
        <button id="btnBbscode" class="btn btn-primary copyBtn2" data-loading-text="已经复制链接..." style="margin-top:10px;"><i class="icon icon-copy"></i> 复制</button>
      </div>
      <div class="tab-pane fade <?php if ($config['upload_first_show'] == 3) echo 'active in'; ?>" id="tab2Content3">
        <textarea class="form-control" rows="5" id="markdown" readonly></textarea>
        <button class="btn btn-primary" style="margin-top:10px;" onclick="location.reload()"><i class="icon icon-undo"></i> 刷新</button>
        <button id="btnMarkDown" class="btn btn-primary copyBtn3" data-loading-text="已经复制链接..." style="margin-top:10px;"><i class="icon icon-copy"></i> 复制</button>
      </div>
      <div class="tab-pane fade <?php if ($config['upload_first_show'] == 4) echo 'active in';  ?>" id="tab2Content4">
        <textarea class="form-control" rows="5" id="html" readonly></textarea>
        <button class="btn btn-primary" style="margin-top:10px;" onclick="location.reload()"><i class="icon icon-undo"></i> 刷新</button>
        <button id="btnHtml" class="btn btn-primary copyBtn4" data-loading-text="已经复制链接..." style="margin-top:10px;"><i class="icon icon-copy"></i> 复制</button>
      </div>
      <div class="tab-pane fade <?php if ($config['upload_first_show'] == 5) echo 'active in';  ?>" id="tab2Content5">
        <textarea class="form-control" rows="5" id="del" readonly></textarea>
        <button class="btn btn-primary" style="margin-top:10px;" onclick="location.reload()"><i class="icon icon-undo"></i> 刷新</button>
        <button id="btndel" class="btn btn-primary copyBtn5" data-loading-text="已经复制链接..." style="margin-top:10px;"><i class="icon icon-copy"></i> 复制</button>
      </div>
    </div>
  </div>
</div>
<link href="<?php static_cdn(); ?>/public/static/marquee/marquee.css" rel="stylesheet">
<link href="<?php static_cdn(); ?>/public/static/zui/lib/uploader/zui.uploader.min.css" rel="stylesheet">
<script src="<?php static_cdn(); ?>/public/static/zui/lib/uploader/zui.uploader.min.js"></script>
<script src="<?php static_cdn(); ?>/public/static/marquee/marquee.min.js"></script>
<script src="<?php static_cdn(); ?>/public/static/EasyImage.js"></script>
<script>
  // 公告
  (function() {
    new Marquee({
      // 要滚动的元素
      elem: document.getElementById("marquee2"),
      // 每次滚动的步长(px)，默认0
      step: 30,
      // 滚动效果执行时间(ms)，默认400
      stepInterval: 400,
      // 每次滚动间隔时间(ms)，默认3000
      interval: 3000,
      // 滚动方向，up、down、left、right，默认为"left" 当前只支持上下
      dir: 'up',
      // 是否自动滚动，默认为true
      autoPlay: true,
      // 是否在鼠标滑过低级元素时暂停滚动，默认为true
      hoverPause: true
    });
  })();

  // 上传控制
  $('#upShowID').uploader({
    // 自动上传
    autoUpload: false,
    // 文件上传提交地址
    url: './application/upload.php',
    // 最大支持的上传文件
    max_file_size: <?php echo $config['maxSize']; ?>,
    // 分片上传 0为不分片 分片容易使图片上传失败
    chunk_size: 0,
    // 点击文件列表上传文件
    browseByClickList: true,
    // flash 上传组件地址
    flash_swf_url: '<?php static_cdn(); ?>/public/static/zui/lib/uploader/Moxie.swf',
    // silverlight 上传组件地址
    flash_swf_url: '<?php static_cdn(); ?>/public/static/zui/lib/uploader/Moxie.xap',
    // 预览图尺寸
    previewImageSize: {
      'width': 80,
      'height': 80
    },
    // 上传格式过滤
    filters: { // 只允许上传图片或图标（.ico）
      mime_types: [{
          title: '图片',
          extensions: '<?php echo $config['extensions']; ?>'
        },
        {
          title: '图标',
          extensions: 'ico'
        }
      ],
      prevent_duplicates: true
    },
    // 限制文件上传数目
    limitFilesCount: <?php echo $config['maxUploadFiles']; ?>,
    // 重置上传失败的文件
    autoResetFails: true,
    <?php echo imgRatio(); ?>,
    responseHandler: function(responseObject, file) {
      var obj = JSON.parse(responseObject.response); //由JSON字符串转换为JSON对象
      console.log(obj); // 输出log
      console.log(file); // 输出log
      if (obj.result === 'success') {
        document.getElementById("links").innerHTML += obj.url + "\r\n";
        document.getElementById("bbscode").innerHTML += "[img]" + obj.url + "[/img]\r\n";
        document.getElementById("markdown").innerHTML += "![" + obj.srcName + "](" + obj.url + ")\r\n";
        document.getElementById("html").innerHTML += '<img src="' + obj.url + '" alt="' + obj.srcName + '" />\r\n';
        document.getElementById("del").innerHTML += obj.del + "\r\n";
      } else {
        return '上传失败,错误信息:' + obj.message;
      }
    }
  });
</script>
<?php
/** 环境检测 */
if ($config['checkEnv']) require_once APP_ROOT . '/application/check.php';
/** 底部广告 */
if ($config['ad_bot']) echo $config['ad_bot_info'];
/** 引入底部 */
require_once APP_ROOT . '/application/footer.php';
