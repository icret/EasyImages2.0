<?php if ($config['ad_bot']) echo $config['ad_bot_info']; // 底部广告
?>
<footer class="text-muted small col-md-12" style="text-align: center;">
  <hr>
  <!-- 对话框HTML -->
  <div class="modal fade" id="myModal">
    <div class="modal-dialog modal-sm">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal">
            <span aria-hidden="true">x</span>
            <span class="sr-only">关闭</span></button>
          <p class="modal-title icon icon-mobile" style="text-align: center">扫描二维码使用手机上传</p>
        </div>
        <div class="modal-body">
          <p id="qrcode"></p>
        </div>
      </div>
    </div>
  </div>
  <?php /** 页脚信息 */ if (!empty($config['footer'])) echo $config['footer']; ?>
  <p>
    © 2018-<?php echo date("Y"); ?>
    <a href="https://png.cm/" target="_blank"> EasyImage</a>
    <a href="https://github.com/icret/EasyImages2.0" target="_blank" rel="nofollow"><?php echo $config['version']; ?></a> By
    <a href="https://blog.png.cm" target="_blank">Icret</a>
    <a href="/admin/terms.php" target="_blank"> DMCA</a>
    <!-- 二维码对话框触发按钮 -->
    <a href="#" data-position="center" data-moveable="inside" data-moveable="true" data-toggle="modal" data-target="#myModal" title="使用手机扫描二维码访问"><i class="icon icon-qrcode hidden-xs inline-block"></i></a>
    <?php

    // 登录与退出    
    if (is_who_login('admin') || is_who_login('guest')) {
      echo '<a href="' . $config['domain'] . '/admin/index.php?login=logout" title="退出账号"><i class="icon icon-signout"></i></a>';
    } else {
      echo '<a href="' . $config['domain'] . '/admin/index.php" title="账号登录"><i class="icon icon-user"></i></a>';
    }
    ?>
  </p>
</footer>

<?php /** 环境检测 */ if ($config['checkEnv']) require_once APP_ROOT . '/application/check.php'; ?>

<script>
  // js 获取当前网址二维码
  var qrcode = new QRCode(document.getElementById("qrcode"), {
    text: window.location.href,
    width: 265,
    height: 256,
    colorDark: "#353535",
    colorLight: "#F1F1F1",
    correctLevel: QRCode.CorrectLevel.H
  });

  // NProgress
  NProgress.start();
  NProgress.done();

  // console
  console.log("%cEasyImage2.0", "background: rgba(252,234,187,1);background: -moz-linear-gradient(left, rgba(252,234,187,1) 0%, rgba(175,250,77,1) 12%, rgba(0,247,49,1) 28%, rgba(0,210,247,1) 39%,rgba(0,189,247,1) 51%, rgba(133,108,217,1) 64%, rgba(177,0,247,1) 78%, rgba(247,0,189,1) 87%, rgba(245,22,52,1) 100%);background: -webkit-gradient(left top, right top, color-stop(0%, rgba(252,234,187,1)), color-stop(12%, rgba(175,250,77,1)), color-stop(28%, rgba(0,247,49,1)), color-stop(39%, rgba(0,210,247,1)), color-stop(51%, rgba(0,189,247,1)), color-stop(64%, rgba(133,108,217,1)), color-stop(78%, rgba(177,0,247,1)), color-stop(87%, rgba(247,0,189,1)), color-stop(100%, rgba(245,22,52,1)));background: -webkit-linear-gradient(left, rgba(252,234,187,1) 0%, rgba(175,250,77,1) 12%, rgba(0,247,49,1) 28%, rgba(0,210,247,1) 39%, rgba(0,189,247,1) 51%, rgba(133,108,217,1) 64%, rgba(177,0,247,1) 78%, rgba(247,0,189,1) 87%, rgba(245,22,52,1) 100%);background: -o-linear-gradient(left, rgba(252,234,187,1) 0%, rgba(175,250,77,1) 12%, rgba(0,247,49,1) 28%, rgba(0,210,247,1) 39%, rgba(0,189,247,1) 51%, rgba(133,108,217,1) 64%, rgba(177,0,247,1) 78%, rgba(247,0,189,1) 87%, rgba(245,22,52,1) 100%);background: -ms-linear-gradient(left, rgba(252,234,187,1) 0%, rgba(175,250,77,1) 12%, rgba(0,247,49,1) 28%, rgba(0,210,247,1) 39%, rgba(0,189,247,1) 51%, rgba(133,108,217,1) 64%, rgba(177,0,247,1) 78%, rgba(247,0,189,1) 87%, rgba(245,22,52,1) 100%);background: linear-gradient(to right, rgba(252,234,187,1) 0%, rgba(175,250,77,1) 12%, rgba(0,247,49,1) 28%, rgba(0,210,247,1) 39%, rgba(0,189,247,1) 51%, rgba(133,108,217,1) 64%, rgba(177,0,247,1) 78%, rgba(247,0,189,1) 87%, rgba(245,22,52,1) 100%);filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#fceabb', endColorstr='#f51634', GradientType=1 );font-size:2.34em;font-weight:bold")
  console.log('%c图床演示网站: https://png.cm \n请为本人博客 https://blog.png.cm/ 加上链接, 谢谢尊重!\n作为开发者你可以对相应的后台功能进行扩展(增删改相应代码), 但请保留代码中相关来源信息(例如: 本人博客, 邮箱等);\n本程序由 Icret 独自开发并完全开源, 碰到收费发布的请不要轻易付款; 本人仅为程序开源创作, 如非法网站使用与本人无关, 请勿用于非法用途.%c ', 'color: #eaad1a; padding:5px 0; border:1px solid #448ef6; font-size:12px;', '');
</script>
</body>

</html>