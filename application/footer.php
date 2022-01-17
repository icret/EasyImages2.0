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

  // console
  console.log("%cEasyImage 简单图床", "background: rgba(252,234,187,1);background: -moz-linear-gradient(left, rgba(252,234,187,1) 0%, rgba(175,250,77,1) 12%, rgba(0,247,49,1) 28%, rgba(0,210,247,1) 39%,rgba(0,189,247,1) 51%, rgba(133,108,217,1) 64%, rgba(177,0,247,1) 78%, rgba(247,0,189,1) 87%, rgba(245,22,52,1) 100%);background: -webkit-gradient(left top, right top, color-stop(0%, rgba(252,234,187,1)), color-stop(12%, rgba(175,250,77,1)), color-stop(28%, rgba(0,247,49,1)), color-stop(39%, rgba(0,210,247,1)), color-stop(51%, rgba(0,189,247,1)), color-stop(64%, rgba(133,108,217,1)), color-stop(78%, rgba(177,0,247,1)), color-stop(87%, rgba(247,0,189,1)), color-stop(100%, rgba(245,22,52,1)));background: -webkit-linear-gradient(left, rgba(252,234,187,1) 0%, rgba(175,250,77,1) 12%, rgba(0,247,49,1) 28%, rgba(0,210,247,1) 39%, rgba(0,189,247,1) 51%, rgba(133,108,217,1) 64%, rgba(177,0,247,1) 78%, rgba(247,0,189,1) 87%, rgba(245,22,52,1) 100%);background: -o-linear-gradient(left, rgba(252,234,187,1) 0%, rgba(175,250,77,1) 12%, rgba(0,247,49,1) 28%, rgba(0,210,247,1) 39%, rgba(0,189,247,1) 51%, rgba(133,108,217,1) 64%, rgba(177,0,247,1) 78%, rgba(247,0,189,1) 87%, rgba(245,22,52,1) 100%);background: -ms-linear-gradient(left, rgba(252,234,187,1) 0%, rgba(175,250,77,1) 12%, rgba(0,247,49,1) 28%, rgba(0,210,247,1) 39%, rgba(0,189,247,1) 51%, rgba(133,108,217,1) 64%, rgba(177,0,247,1) 78%, rgba(247,0,189,1) 87%, rgba(245,22,52,1) 100%);background: linear-gradient(to right, rgba(252,234,187,1) 0%, rgba(175,250,77,1) 12%, rgba(0,247,49,1) 28%, rgba(0,210,247,1) 39%, rgba(0,189,247,1) 51%, rgba(133,108,217,1) 64%, rgba(177,0,247,1) 78%, rgba(247,0,189,1) 87%, rgba(245,22,52,1) 100%);filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#fceabb', endColorstr='#f51634', GradientType=1 );font-size:2.34em;font-weight:bold")
  console.log('%c图床演示网站： https://img.545141.com/ \n本程序由 Icret 独自开发并完全开源，碰到收费发布的请不要轻易付款；\n本人仅为程序开源创作，如非法网站使用与本人无关，请勿用于非法用途；\n作为开发者你可以对相应的后台功能进行扩展（增删改相应代码）,但请保留代码中相关来源信息（例如：本人博客，邮箱等）。\n请为本人博客 https://www.545141.com/ 加上链接，谢谢尊重！%c ', 'color: #eaad1a; padding:5px 0; border:1px solid #448ef6; font-size:12px;', '');
</script>
<footer class="text-muted small col-md-12" style="text-align: center;margin-bottom: 10px"><?php if ($config['ad_bot']) {echo $config['ad_bot_info'];} ?>
    <?php if($config['customize']){echo $config['customize'];}?>
  <hr>
  <!-- 对话框触发按钮 -->
  <a href="#" data-position="center" data-moveable="inside" data-moveable="true" data-toggle="modal" data-target="#myModal">
    <i class="icon icon-qrcode"></i>二维码 </a>
  <?php
  if (is_online()) {
    echo '<a href="' . $config['domain'] . '/application/logout.php" ><i class="icon icon-signout"></i>退出 </a>';
  } else {
    echo '<a href="' . $config['domain'] . '/application/login.php" ><i class="icon icon-user"></i>登录 </a>';
  }

  if (isset($config['footer'])) {
    echo '<div>' . $config['footer'] . ' 
  Copyright © 2018-' . date('Y') . '
	<a href="https://img.545141.com/" target="_blank"> EasyImage</a> By
	<a href="https://www.545141.com" target="_blank"> Icret</a> Version:
	<a href="https://github.com/icret/EasyImages2.0" target="_blank"> ' . $config['version'] . '</a>
	<a href="/admin/terms.php" target="_blank"> DMCA</a>    
  </div>';
  }
  ?>
</footer>
</body>

</html>