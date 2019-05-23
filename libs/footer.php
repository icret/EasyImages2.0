<?php
echo '    
    <script type="text/javascript">
  // jsqrcode获取当前网址并赋值给id=text的value
  document.getElementById("text").value = window.location.href;

  var qrcode = new QRCode(document.getElementById("qrcode"), {
    width: 150,
    height: 150
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
  </script>
  <script src="./public/static/hm.js"></script>
  <footer class="text-muted small col-md-12" style="text-align: center">
   '.showAD('bot').'
  <hr />
      Copyright © 2018-'. date('Y').' EasyImage Powered By <code><a href="https://www.545141.com/easyimage.html" target="_blank">icret</a></code> Verson: '.$config['Version'].@$qqgroup.'
  </footer>
</body>
</html>
    ';