<?php
echo '
    <script>
    // js二维码 获取当前网址并赋值给id=text的value
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
            function () {
                makeCode();
            }).on("keydown",
            function (e) {
                if (e.keyCode==13) {
                    makeCode();
                }
            });
        
    // 百度统计
        var _hmt = _hmt || [];
        (function() {
            var hm = document.createElement("script");
            hm.src = "https://hm.baidu.com/hm.js?5320b69f4f1caa9328dfada73c8e6a75";
            var s = document.getElementsByTagName("script")[0];
            s.parentNode.insertBefore(hm, s);
        })();
    </script>
  <footer class="text-muted small col-md-12" style="text-align: center">
   '.showAD('bot').'
  <hr />Copyright © 2018-'. date('Y').' <a href="https://img.545141.com/" target="_blank"> EasyImage</a> Powered By <code><a href="https://www.545141.com/902.html" target="_blank">icret</a></code> Verson: <a href="https://github.com/icret/easyImages2.0" target="_blank"> '.$config['version'].'</a>'.@$qqgroup.'
  </footer>
</body>
</html>
    ';