<?php
require_once __DIR__ . '/app/header.php';
/** 顶部广告 */
if ($config['ad_top']) echo $config['ad_top_info'];
/** 检查登陆 */
mustLogin();
?>
<div class="col-md-12">
  <!-- 公告 -->
  <?php if (!empty($config['tips'])) : ?>
    <div class="marquee">
      <div class="wrap">
        <div id="marquee2">
          <?php echo $config['tips']; ?>
        </div>
      </div>
    </div>
  <?php endif; ?>
  <div id='upShowID' class="uploader col-md-12 clo-xs-12" data-ride="uploader" data-url="/app/upload.php">
    <div class="uploader-message text-center">
      <div class="content"></div>
      <button type="button" class="close">x</button>
    </div>
    <div class="uploader-files file-list file-list-lg file-rename-by-click" data-drag-placeholder="选择文件/Ctrl+V粘贴/拖拽至此处" style="min-height: 188px; border-style: dashed;"></div>
    <div class="uploader-actions">
      <button type="button" class="btn btn-link uploader-btn-browse"><i class="icon icon-plus"></i> 选择文件</button>
      <button type="button" class="btn btn-link uploader-btn-start"><i class="icon icon-cloud-upload"></i> 开始上传</button>
      <button type="button" class="btn btn-link uploader-btn-stop"><i class="icon icon-pause"></i> 暂停上传</button>
      <div class="uploader-status pull-right text-muted hidden-xs"></div>
      <div class="uploader-status pull-right text-muted col-xs-12 text-ellipsis visible-xs"></div>
    </div>
  </div>
  <div class="col-md-12 clo-xs-12">
    <ul class="nav nav-tabs">
      <li <?php if ($config['upload_first_show'] == 1) echo 'class="active"'; ?> data-toggle="tooltip" title="图片直链">
        <a href="#" data-target="#tab2Content1" data-toggle="tab"><i class="icon icon-picture"></i></a>
      </li>
      <li <?php if ($config['upload_first_show'] == 2) echo 'class="active"'; ?> data-toggle="tooltip" title="论坛代码">
        <a href="#" data-target="#tab2Content2" data-toggle="tab"><i class="icon icon-chat"></i></a>
      </li>
      <li <?php if ($config['upload_first_show'] == 3) echo 'class="active"'; ?> data-toggle="tooltip" title="Markdown">
        <a href="#" data-target="#tab2Content3" data-toggle="tab"><i class="icon icon-code"></i></a>
      </li>
      <li <?php if ($config['upload_first_show'] == 4) echo 'class="active"'; ?> data-toggle="tooltip" title="HTML链接">
        <a href="#" data-target="#tab2Content4" data-toggle="tab"><i class="icon icon-html5"></i></a>
      </li>
      <li <?php if ($config['upload_first_show'] == 5) echo 'class="active"'; ?> data-toggle="tooltip" title="缩略图">
        <a href="#" data-target="#tab2Content5" data-toggle="tab"><i class="icon icon-camera"></i></a>
      </li>
      <li <?php if ($config['upload_first_show'] == 6) echo 'class="active"';
          if ($config['show_user_hash_del'] == 0) echo 'style="display:none;"' ?> data-toggle="tooltip" title="删除链接">
        <a href="#" data-target="#tab2Content6" data-toggle="tab"><i class="icon icon-trash"></i></a>
      </li>
    </ul>
    <div class="tab-content" style="text-align:right;">
      <div class="tab-pane fade <?php if ($config['upload_first_show'] == 1) echo 'active in';  ?>" id="tab2Content1">
        <textarea class="form-control" rows="5" id="links" readonly></textarea>
        <button class="btn btn-primary" data-toggle="tooltip" data-original-title="刷新" style="margin-top:5px;" onclick="location.reload()"><i class="icon icon-refresh"></i></button>
        <button class="btn btn-primary btnLinks" onclick="uploadCopy('links','.btnLinks')" data-toggle="tooltip" data-original-title="复制" data-loading-text="已复制" style="margin-top:5px;"><i class="icon icon-copy"></i></button>
      </div>
      <div class="tab-pane fade <?php if ($config['upload_first_show'] == 2) echo 'active in'; ?>" id="tab2Content2">
        <textarea class="form-control" rows="5" id="bbscode" readonly></textarea>
        <button class="btn btn-primary" data-toggle="tooltip" data-original-title="刷新" style="margin-top:5px;" onclick="location.reload()"><i class="icon icon-refresh"></i></button>
        <button class="btn btn-primary btnBbscode" onclick="uploadCopy('bbscode','.btnBbscode')" data-toggle="tooltip" data-original-title="复制" data-loading-text="已复制" style="margin-top:5px;"><i class="icon icon-copy"></i></button>
      </div>
      <div class="tab-pane fade <?php if ($config['upload_first_show'] == 3) echo 'active in'; ?>" id="tab2Content3">
        <textarea class="form-control" rows="5" id="markdown" readonly></textarea>
        <button class="btn btn-primary" data-toggle="tooltip" data-original-title="刷新" style="margin-top:5px;" onclick="location.reload()"><i class="icon icon-refresh"></i></button>
        <button class="btn btn-primary btnMarkDown" onclick="uploadCopy('markdown','.btnMarkDown')" data-toggle="tooltip" data-original-title="复制" data-loading-text="已复制" style="margin-top:5px;"><i class="icon icon-copy"></i></button>
      </div>
      <div class="tab-pane fade <?php if ($config['upload_first_show'] == 4) echo 'active in';  ?>" id="tab2Content4">
        <textarea class="form-control" rows="5" id="html" readonly></textarea>
        <button class="btn btn-primary" data-toggle="tooltip" data-original-title="刷新" style="margin-top:5px;" onclick="location.reload()"><i class="icon icon-refresh"></i></button>
        <button class="btn btn-primary btnHtml" onclick="uploadCopy('html','.btnHtml')" data-toggle="tooltip" data-original-title="复制" data-loading-text="已复制" style="margin-top:5px;"><i class="icon icon-copy"></i></button>
      </div>
      <div class="tab-pane fade <?php if ($config['upload_first_show'] == 5) echo 'active in';  ?>" id="tab2Content5">
        <textarea class="form-control" rows="5" id="thumb" readonly></textarea>
        <button class="btn btn-primary" data-toggle="tooltip" data-original-title="刷新" style="margin-top:5px;" onclick="location.reload()"><i class="icon icon-refresh"></i></button>
        <button class="btn btn-primary btnThumb" onclick="uploadCopy('thumb','.btnThumb')" data-toggle="tooltip" data-original-title="复制" data-loading-text="已复制" style="margin-top:5px;"><i class="icon icon-copy"></i></button>
      </div>
      <div class="tab-pane fade <?php if ($config['upload_first_show'] == 6) echo 'active in';  ?>" id="tab2Content6">
        <textarea class="form-control" rows="5" id="del" readonly></textarea>
        <button class="btn btn-primary" data-toggle="tooltip" data-original-title="刷新" style="margin-top:5px;" onclick="location.reload()"><i class="icon icon-refresh"></i></button>
        <button class="btn btn-primary btnDel" onclick="uploadCopy('del','.btnDel')" data-toggle="tooltip" data-original-title="复制" data-loading-text="已复制" style="margin-top:5px;"><i class="icon icon-copy"></i></button>
      </div>
    </div>
  </div>
</div>
<link rel="stylesheet" href="<?php static_cdn(); ?>/public/static/marquee/marquee.css">
<link rel="stylesheet" href="<?php static_cdn(); ?>/public/static/zui/lib/uploader/zui.uploader.min.css">
<script type="application/javascript" src="<?php static_cdn(); ?>/public/static/zui/lib/uploader/zui.uploader.min.js"></script>
<script type="application/javascript" src="<?php static_cdn(); ?>/public/static/marquee/marquee.min.js"></script>
<script type="application/javascript" src="<?php static_cdn(); ?>/public/static/EasyImage.js"></script>
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
    url: './app/upload.php',
    // 最大支持的上传文件
    max_file_size: <?php echo $config['maxSize']; ?>,
    // 分片上传 0为不分片 分片容易使图片上传失败
    chunk_size: <?php echo $config['chunks']; ?>,
    // 点击文件列表上传文件
    browseByClickList: true,
    // flash 上传组件地址
    flash_swf_url: '<?php static_cdn(); ?>/public/static/zui/lib/uploader/Moxie.swf',
    // silverlight 上传组件地址
    flash_swf_url: '<?php static_cdn(); ?>/public/static/zui/lib/uploader/Moxie.xap',
    // sign
    multipart_params: {
      'sign': new Date().getTime() / 1000 | 0,
    },
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
    // 移除文件进行确认
    deleteConfirm: true,
    // 重置上传失败的文件
    autoResetFails: true,
    // 当文件上传进度发送变化时触发，此回调函数会在上传文件的过程中反复触发
    onUploadProgress: function(file) {
      NProgress.configure({
        barColor: '<?php echo $config['NProgress_Progress']; ?>'
      });
      NProgress.set(0)
      NProgress.set(file.percent / 100)
    },
    // 显示上传成功消息
    uploadedMessage: '已上传 {uploaded} 个文件，{failed} 个文件上传失败',
    // 当启用分片上传选项后，每个文件片段上传完成时触发
    onChunkUploaded: function(file, responseObject) {
      NProgress.set(responseObject.offset / responseObject.total);
    },
    <?php echo imgRatio(); ?>,
    responseHandler: function(responseObject, file) {
      var obj = JSON.parse(responseObject.response); //由JSON字符串转换为JSON对象
      console.log(file); // 输出上传log
      console.log(obj); // 输出回传log
      if (obj.code === 200) {
        $("#links").append(obj.url + "\r\n");
        $("#bbscode").append("[img]" + obj.url + "[/img]\r\n");
        $("#markdown").append("![" + obj.srcName + "](" + obj.url + ")\r\n");
        $("#html").append('&lt;img src="' + obj.url + '" alt="' + obj.srcName + '" /&gt;\r\n');
        $("#thumb").append(obj.thumb + "\r\n");
        $("#del").append(obj.del + "\r\n");

        // 上传成功提示
        new $.zui.Messager(obj.srcName + " 上传成功", {
          type: "primary",
          placement: 'bottom-right',
          icon: "check"
        }).show();

        try { // 储存上传历史
          console.log('history localStorage success');
          $.zui.store.set(obj.srcName, obj)
        } catch (err) {
          // 失败提示
          $.zui.messager.show('存储上传记录失败' + err, {
            icon: 'bell',
            time: 4000,
            type: 'danger',
            placement: 'top'
          });
          console.log('localStorage failed:' + err);
        }
      } else {
        // 上传失败提示
        new $.zui.Messager(obj.message, {
          type: "danger",
          placement: 'bottom-right',
          icon: "times"
        }).show();
        return;
      }
    },
  });
</script>
<?php
/** 环境检测 */
require_once APP_ROOT . '/app/check.php';
/** 底部广告 */
if ($config['ad_bot']) echo $config['ad_bot_info'];
/** 引入底部 */
require_once __DIR__ . '/app/footer.php';
