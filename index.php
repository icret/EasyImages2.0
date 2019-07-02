<?php
include './libs/header.php';
mustLogin();
if (@$_GET['e']==1){
  setcookie('admin',null);
  echo '<code>退出成功</code>';
  header('Refresh:2;libs/login.php');
}
?>
<div class="container">
  <div class="col-md-12">
      <?php if(!empty($config['tips'])){echo '<ul style="list-style: none;margin:1px 1px 30px 1px;"><li class="icon icon-dot-circle text-muted"> '.$config['tips'].'</li></ul>';} ?>
      <div id='upShowID' class="uploader col-md-10 col-md-offset-1" data-ride="uploader" data-url="file.php">
        <div class="uploader-message text-center">
          <div class="content"></div>
          <button type="button" class="close">×</button>
        </div>
        <div class="uploader-files file-list file-list-lg" data-drag-placeholder="Ctrl+V粘贴/选择文件/将图片直接拖拽至此处"></div>
        <div class="uploader-actions">
          <div class="uploader-status pull-right text-muted"></div>
          <button type="button" class="btn btn-link uploader-btn-browse">
            <i class="icon icon-plus"></i>选择文件</button>
          <button type="button" class="btn btn-link uploader-btn-start">
            <i class="icon icon-cloud-upload"></i>开始上传</button>
            <button type="button" class="btn btn-link uploader-btn-stop">
            <i class="icon icon-pause"></i>暂停上传</button>
        </div>
      </div>
      <div class="col-md-8 col-md-offset-2">
        <ul class="nav nav-tabs">
          <li class="active">
            <a href="#" data-target="#tab2Content1" data-toggle="tab">直链</a></li>
          <li>
            <a href="#" data-target="#tab2Content2" data-toggle="tab">论坛代码</a></li>
          <li>
            <a href="#" data-target="#tab2Content3" data-toggle="tab">MarkDown</a></li>
          <li>
            <a href="#" data-target="#tab2Content4" data-toggle="tab">HTML</a></li>
        </ul>
        <div class="tab-content" align="right">
          <div class="tab-pane fade active in" id="tab2Content1">
            <textarea class="form-control" style="text-align: center;min-height: 100px;" id="links" readonly></textarea>
            <button id="btnLinks" class="btn copyBtn1" data-loading-text="已经复制链接..." style="margin-top:10px;"><i
                class="icon icon-copy"></i> 复制</button>
          </div>
          <div class="tab-pane fade" id="tab2Content2">
            <textarea class="form-control" style="text-align: center;min-height: 100px;" id="bbscode"
              readonly></textarea>
            <button id="btnBbscode" class="btn copyBtn2" data-loading-text="已经复制链接..." style="margin-top:10px;"><i
                class="icon icon-copy"></i> 复制</button>
          </div>
          <div class="tab-pane fade" id="tab2Content3">
            <textarea class="form-control" style="text-align: center;min-height: 100px;" id="markdown"
              readonly></textarea>
            <button id="btnMarkDown" class="btn copyBtn3" data-loading-text="已经复制链接..." style="margin-top:10px;"><i
                class="icon icon-copy"></i> 复制</button>
          </div>
          <div class="tab-pane fade" id="tab2Content4">
            <textarea class="form-control" style="text-align: center;min-height: 100px;" id="html" readonly></textarea>
            <button id="btnHtml" class="btn copyBtn4" data-loading-text="已经复制链接..." style="margin-top:10px;"><i
                class="icon icon-copy"></i> 复制</button>
          </div>
        </div>
      </div>
  </div>
</div>
    <script src="public/static/paste.js"></script>
    <script src="public/static/copy_btn.js"></script>
    <script>
        $('#upShowID').uploader({
            // 当选择文件后立即自动进行上传操作
            autoUpload: false,
            // 文件上传提交地址
            url: 'file.php',
            // 最大支持的上传文件
            max_file_size: <?php echo $config['maxSize']; ?> ,
            // 是否分片上传 0为不分片 经测试分片容易使图片上传失败
            chunk_size: 0,
            //点击文件列表上传文件
            browseByClickList: true,
            // 上传格式过滤
            filters: { // 只允许上传图片或图标（.ico）
                mime_types: [{
                    title: '图片',
                    extensions: '<?php echo getExtensions();?>'
                }, {
                    title: '图标',
                    extensions: 'ico'
                }],
                prevent_duplicates: true
            },
            // 限制文件上传数目
            limitFilesCount: <?php echo $config['maxUploadFiles']; ?> ,
            // 自动上传失败的文件
            autoResetFails: true,

            responseHandler: function (responseObject, file) {
                // 当服务器返回的文本内容包含 `'success'` 文件上传成功
                if (responseObject.response.indexOf('success')) {
                    console.log(responseObject.response);
                    var obj = JSON.parse(responseObject.response); //由JSON字符串转换为JSON对象
                    var links = document.getElementById("links");
                    links.innerHTML += obj.url + "\n";

                    var bbscode = document.getElementById("bbscode");
                    bbscode.innerHTML += "[img]" + obj.url + "[/img]\n";

                    var markdown = document.getElementById("markdown");
                    markdown.innerHTML += "![](" + obj.url + ")\n";

                    var html = document.getElementById("html");
                    html.innerHTML += "&lt;img src=\"" + obj.url + "\" /&#62;\n";
                } else {
                    return '上传失败。服务器返回了一个错误：' + responseObject.response;
                }
            }
        });
    </script>
<?php 
include APP_ROOT.'/libs/footer.php';