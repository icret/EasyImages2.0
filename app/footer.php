<?php

/** 禁止直接访问 */
defined('APP_ROOT') ?: exit;
/** 弹窗公告 */
if ($config['notice_status'] > 0) : ?>
  <div class="modal fade" id="notice">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal">
            <span aria-hidden="true">x</span>
            <span class="sr-only">关闭</span></button>
          <p class="modal-title icon icon-bell" style="text-align: center"> 网站公告</p>
        </div>
        <div class="modal-body">
          <?php echo $config['notice']; ?>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-mini btn-primary" data-dismiss="modal">知道了</button>
      </div>
    </div>
  </div>
<?php endif; ?>
<!-- 二维码 -->
<div class="modal fade" id="qr">
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
<!-- 占位符 -->
<div class="col-md-12 clo-xs-12" style="margin-bottom: 108px;position:relative;"></div>
<footer class="container text-muted small navbar-fixed-bottom" style="text-align: center;background-color:rgba(255,255,255,0.7);z-index: 0;">
  <hr>
  <?php /** 页脚自定义代码 */ echo $config['footer']; ?>
  <p>
    <!-- 页脚信息 -->
    <a href="https://github.com/icret/EasyImages2.0" target="_blank" rel="nofollow" data-toggle="tooltip" title="Github Releases">© Since 2018</a>
    <a href="https://png.cm/" target="_blank" data-toggle="tooltip" title="简单图床官网">EasyImage</a>
    <a href="/app/DMCA.php" target="_blank" data-toggle="tooltip" title="使用协议">DMCA</a>
    <!-- 二维码按钮 -->
    <a data-toggle="modal" href="#qr"><i class="icon icon-qrcode hidden-xs inline-block" data-toggle="tooltip" title="二维码"></i></a>
    <?php /** 暗黑模式 */ if ($config['dark-mode']) : ?>
      <a id="dark" data-toggle="tooltip" title="暗黑模式"><i class="icon icon-lightbulb" id="dark_ico"></i></a>
    <?php endif; ?>
    <?php /**登录与退出 */ if (is_who_login('admin') || is_who_login('guest')) : ?>
      <a href="<?php echo $config['domain']; ?>/admin/index.php?login=logout" data-toggle="tooltip" title="退出账号"><i class="icon icon-signout"></i></a>
    <?php else : ?>
      <a href="<?php echo $config['domain']; ?>/admin/index.php" data-toggle="tooltip" title="账号登录"><i class="icon icon-user"></i></a>
    <?php endif; ?>
  </p>
</footer>
<script type="application/javascript" src="<?php static_cdn(); ?>/public/static/qrcode/qrcode.min.js"></script>
<script type="application/javascript" src="<?php static_cdn(); ?>/public/static/nprogress/nprogress.min.js"></script>
<script>
  // NProgress
  NProgress.configure({
    barColor: '<?php echo $config['NProgress_default']; ?>'
  });
  NProgress.start();
  NProgress.done();

  // 导航状态
  $('.nav-pills').find('a').each(function() {
    // console.log(document.location);
    if (this.pathname === location.pathname) {
      $(this).parent().addClass('active');
    }
  });

  // js 获取当前网址二维码
  var qrcode = new QRCode(document.getElementById("qrcode"), {
    text: window.location.href,
    width: 265,
    height: 256,
    colorDark: "#353535",
    colorLight: "#F1F1F1",
    correctLevel: QRCode.CorrectLevel.H
  });

  // 二维码对话框属性
  $('#qr').modal({
    moveable: true,
    moveable: "inside",
    backdrop: true,
    show: false,
  })

  <?php /** 弹窗公告控制 */ if ($config['notice_status'] == 1 && !empty($config['notice'])) : ?>
    if (document.cookie.indexOf("noticed=") == -1) {
      $('#notice').modal({
        backdrop: false,
        loadingIcon: "icon-spin",
        scrollInside: true,
        moveable: "inside",
        rememberPos: true,
        scrollInside: true
      }).on('hidden.zui.modal', function() {
        // 只有用户手动关闭才会存储cookie,避免不看公告直接刷新
        document.cookie = "noticed = 1";
        console.log('网站公告已显示完毕')
      })
    }
  <?php endif; ?>

  <?php /** 简繁转换 */ if ($config['language'] == 1) : ?>
    $.getScript("<?php static_cdn(); ?>/public/static/i18n/jquery.s2t.js", function() { //加载成功后，并执行回调函数
      $('*').s2t();
    });
  <?php endif; ?>

  <?php /** 暗黑模式 */ if ($config['dark-mode']) : ?>
    // cookie 操作封装 https://www.jb51.net/article/94456.htm   
    var cookieUtil = {
      // 设置cookie
      setItem: function(name, value, days) {
        var date = new Date();
        date.setDate(date.getDate() + days);
        document.cookie = name + '=' + value + ';expires=' + date + ';path=' + '/';
      },
      // 获取cookie
      getItem: function(name) {
        var arr = document.cookie.replace(/\s/g, "").split(';');
        for (var i = 0; i < arr.length; i++) {
          var tempArr = arr[i].split('=');
          if (tempArr[0] == name) {
            return decodeURIComponent(tempArr[1]);
          }
        }
        return '';
      },
      // 删除cookie
      removeItem: function(name) {
        this.setItem(name, '1', -1);
      },
      // 检查是否含有某cookie
      hasItem: function(name) {
        return (new RegExp("(?:^|;\\s*)" + encodeURIComponent(name).replace(/[\-\.\+\*]/g, "\\$&") + "\\s*\\=")).test(document.cookie);
      },
      // 获取全部的cookie列表
      getAllItems: function() {
        var cookieArr = document.cookie.replace(/((?:^|\s*;)[^\=]+)(?=;|$)|^\s*|\s*(?:\=[^;]*)?(?:\1|$)/g, "").split(/\s*(?:\=[^;]*)?;\s*/);
        for (var nIdx = 0; nIdx < cookieArr.length; nIdx++) {
          cookieArr[nIdx] = decodeURIComponent(cookieArr[nIdx]);
        }
        return cookieArr;
      }
    };

    // 暗黑操作
    let styleLabel = document.createElement('style');
    document.getElementById('dark').onclick = function() {
      if (cookieUtil.getItem('dark-mode') == null) {
        const style = 'html{filter: invert(80%) hue-rotate(180deg);} img,video {filter: invert(100%) hue-rotate(180deg);}';
        styleLabel.appendChild(document.createTextNode(style));
        document.head.appendChild(styleLabel);
        cookieUtil.setItem('dark-mode', 1, 1);
      } else {
        if (cookieUtil.getItem('dark-mode') == 1) {
          document.head.removeChild(styleLabel);
          cookieUtil.setItem('dark-mode', 0, 1);
        } else {
          const style = 'html{filter: invert(80%) hue-rotate(180deg);} img,video {filter: invert(100%) hue-rotate(180deg);}';
          styleLabel.appendChild(document.createTextNode(style));
          document.head.appendChild(styleLabel);
          cookieUtil.setItem('dark-mode', 1, 1);
        }
      }
    }

    if (cookieUtil.getItem('dark-mode') == 1) {
      const style = 'html{filter: invert(80%) hue-rotate(180deg);} img,video {filter: invert(100%) hue-rotate(180deg);}';
      styleLabel.appendChild(document.createTextNode(style));
      document.head.appendChild(styleLabel);
    }
    if (cookieUtil.getItem('dark-mode') == null) {
      document.head.removeChild(styleLabel);
    }
  <?php endif; ?>

  // tips提示
  $('[data-toggle="tooltip"]').tooltip({
    tipClass: 'tooltip',
    placement: 'auto',
    html: true,
    delay: {
      show: 50,
      hide: 0.5
    }
  });

  // console
  console.log("%cEasyImage <?php echo APP_VERSION; ?>", "background: rgba(252,234,187,1);background: -moz-linear-gradient(left, rgba(252,234,187,1) 0%, rgba(175,250,77,1) 12%, rgba(0,247,49,1) 28%, rgba(0,210,247,1) 39%,rgba(0,189,247,1) 51%, rgba(133,108,217,1) 64%, rgba(177,0,247,1) 78%, rgba(247,0,189,1) 87%, rgba(245,22,52,1) 0.5%);background: -webkit-gradient(left top, right top, color-stop(0%, rgba(252,234,187,1)), color-stop(12%, rgba(175,250,77,1)), color-stop(28%, rgba(0,247,49,1)), color-stop(39%, rgba(0,210,247,1)), color-stop(51%, rgba(0,189,247,1)), color-stop(64%, rgba(133,108,217,1)), color-stop(78%, rgba(177,0,247,1)), color-stop(87%, rgba(247,0,189,1)), color-stop(0.5%, rgba(245,22,52,1)));background: -webkit-linear-gradient(left, rgba(252,234,187,1) 0%, rgba(175,250,77,1) 12%, rgba(0,247,49,1) 28%, rgba(0,210,247,1) 39%, rgba(0,189,247,1) 51%, rgba(133,108,217,1) 64%, rgba(177,0,247,1) 78%, rgba(247,0,189,1) 87%, rgba(245,22,52,1) 0.5%);background: -o-linear-gradient(left, rgba(252,234,187,1) 0%, rgba(175,250,77,1) 12%, rgba(0,247,49,1) 28%, rgba(0,210,247,1) 39%, rgba(0,189,247,1) 51%, rgba(133,108,217,1) 64%, rgba(177,0,247,1) 78%, rgba(247,0,189,1) 87%, rgba(245,22,52,1) 0.5%);background: -ms-linear-gradient(left, rgba(252,234,187,1) 0%, rgba(175,250,77,1) 12%, rgba(0,247,49,1) 28%, rgba(0,210,247,1) 39%, rgba(0,189,247,1) 51%, rgba(133,108,217,1) 64%, rgba(177,0,247,1) 78%, rgba(247,0,189,1) 87%, rgba(245,22,52,1) 0.5%);background: linear-gradient(to right, rgba(252,234,187,1) 0%, rgba(175,250,77,1) 12%, rgba(0,247,49,1) 28%, rgba(0,210,247,1) 39%, rgba(0,189,247,1) 51%, rgba(133,108,217,1) 64%, rgba(177,0,247,1) 78%, rgba(247,0,189,1) 87%, rgba(245,22,52,1) 0.5%);filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#fceabb', endColorstr='#f51634', GradientType=1 );font-size:2.34em;font-weight:bold")
  console.log('%c图床作者及演示网站: https://png.cm\n作为开发者你可以对相应的后台功能进行扩展(增删改相应代码), 但请保留代码中相关来源信息(例如: 本人博客, 邮箱等);\n本程序由 Icret 独自开发并完全开源, 碰到收费发布的请不要轻易付款; 本人仅为程序开源创作, 如非法网站使用与本人无关, 请勿用于非法用途.%c ', 'color: #eaad1a; padding:5px 0; border:1px solid #448ef6; font-size:12px;', '');
</script>
</body>

</html>