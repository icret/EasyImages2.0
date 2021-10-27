<!-- 对话框HTML -->
<div class="modal fade" id="myModal">
  <div class="modal-dialog modal-sm">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">
          <span aria-hidden="true">×</span>
          <span class="sr-only">关闭</span></button>
        <h4 class="modal-title icon icon-mobile" style="text-align: center">扫描二维码使用手机上传</h4>
      </div>
      <div class="modal-body" align="center">
        <input id="text" type="hidden" value="" />
        <p id="qrcode"></p>
      </div>
      <div class="modal-footer">
        <a class="btn btn-danger btn-sm" href="" target="_blank">访问</a>
        <button type="button" class="btn btn-primary btn-sm" data-dismiss="modal">关闭</button>
      </div>
    </div>
  </div>
</div>
<script>
  // js二维码 获取当前网址并赋值给id=text的value
  document.getElementById("text").value = window.location.href;
  var qrcode = new QRCode(document.getElementById("qrcode"), {
    width: 200,
    height: 200,
  });

  function makeCode() {
    var elText = document.getElementById("text");
    if (!elText.value) {
      alert("Input a text");
      elText.focus();
      return;
    }
    qrcode.makeCode(elText.value);
  }
  makeCode();
  $("#text").on("blur",
    function() {
      makeCode();
    }).on("keydown",
    function(e) {
      if (e.keyCode == 13) {
        makeCode();
      }
    });

  // NProgress
  NProgress.start();
  NProgress.done();
</script>
<footer class="text-muted small col-md-12" style="text-align: center;margin-bottom: 10px"><?php if($config['ad_bot']){echo $config['ad_bot_info'];} ?>
  <p><?php echo $config['customize']; ?></p>
  <hr>
  <p><a href="/libs/terms.php" target="_blank">请勿上传违反中国政策的图片</a><i class="icon icon-smile"></i></p>
  <div>
    <!-- 对话框触发按钮 -->
    <a href="#" data-position="center" data-moveable="inside" data-moveable="true" data-toggle="modal" data-target="#myModal">
      <i class="icon icon-qrcode"></i>二维码 </a>
    <a href="<?php echo $config['domain']; ?>/api/apiTest/" target="_blank"><i class="icon icon-key"></i>API </a>
    <?php
    if (is_online()) {
      echo '
      <a href='.$config['domain'].'/libs/list.php?date='.date('Y/m/d/').' target="_blank"><i class="icon icon-desktop"></i>管理 </a>
      <a href="' . $config['domain'] . '/api/api-web.php" target="_blank"><i class="icon icon-rocket"></i>快捷操作 </a>
      <a href="' . $config['domain'] . '/libs/logout.php" ><i class="icon icon-signout"></i>退出 </a>
      ';
    } else {
      echo '<a href="' . $config['domain'] . '/libs/login.php" ><i class="icon icon-user"></i>登录 </a>';
    } ?>
  </div>
  <?php echo 'Copyright © 2018-' . date('Y'); ?>
  <a href="https://img.545141.com/" target="_blank">EasyImage</a> By
  <a href="https://www.545141.com/902.html" target="_blank">Icret</a> Version:<a href="https://github.com/icret/EasyImages2.0" target="_blank"><?php echo $config['version']; ?></a>
  <a href="/libs/terms.php" target="_blank">DMCA</a>
</footer>
</body>

</html>