<?php
require_once __DIR__ . '/header.php';
if (!$config['showSwitch'] and !is_online()) {
  echo '<div class="alert alert-info">管理员关闭了预览哦~~</div>';
} else {
  $path = isset($_GET['date']) ? $_GET['date'] : date('Y/m/d/');
  $path = preg_replace("/^d{4}-d{2}-d{2} d{2}:d{2}:d{2}$/s", "", trim($path)); // 过滤非日期，删除空格

  $keyNum = isset($_GET['num']) ? $_GET['num'] : $config['listNumber'];
  $keyNum = preg_replace("/[^0-9]/", "", trim($keyNum));                       // 过滤非数字，删除空格
  
  $fileArr = getFile(APP_ROOT . config_path($path));                           // 统计当日上传数量

  if ($fileArr[0]) {
    echo '<div class="cards-condensed listNum">';
    foreach ($fileArr as $key => $value) {
      if ($key < $keyNum) {
        $imgUrl = $config['imgurl'] . config_path($path) . $value;
        // 会导致速度变慢
        // $re_img = str_replace($config['imgurl'], '', $imgUrl); // 图片相对路径 /i/2021/11/03/hg82t4.jpg
        // <p>' . @getimagesize($imgUrl)[0] . 'x' . @getimagesize($imgUrl)[1] . 'px ' . getDistUsed(filesize(APP_ROOT . $re_img)) . '</p>
        if (is_online()) {
          echo '
        <div class="col-md-4 col-sm-6 col-lg-3">
          <div class="card">
              <img data-toggle="lightbox" data-image="' . back_cache_images($imgUrl) . '" src="../public/images/loading.svg" alt="简单图床-EasyImage">              
                <div class="caption" style="color:#145ccd " >
                <label>选择<input type="checkbox"  style="zoom:120%;" id="url" name="checkbox" value="' . $imgUrl . '"></label>
                <a href="' . $imgUrl . '" target="_blank"><i class="icon icon-picture" title="打开原图"></i></a>
                <a href="' . $config['domain'] . '/application/del.php?url=' . $imgUrl . '" target="_blank" title="删除文件"><i class="icon icon-trash"></i></a>         
                <a data-clipboard-demo="" data-clipboard-action="copy" data-clipboard-text="' . $imgUrl . '" title="复制文件"><i class="icon icon-copy"></i></a>
            </div>            
          </div>
        </div>
				';
        } else {
          echo '
          <div class="col-md-4 col-sm-6 col-lg-3">
          <div class="card">
                <img data-toggle="lightbox" data-image="' . back_cache_images($imgUrl) . '" src="../public/images/loading.svg" alt="简单图床-EasyImage">        
                <div class="caption" style="color:#145ccd " >
                <a href="' . $imgUrl . '" target="_blank"><i class="icon icon-picture" title="打开原图"></i></a>
                <a href="' . $config['domain'] . '/application/del.php?url=' . $imgUrl . '" target="_blank" title="删除文件"><i class="icon icon-trash"></i></a>         
                <a data-clipboard-demo="" data-clipboard-action="copy" data-clipboard-text="' . $imgUrl . '" title="复制文件"><i class="icon icon-copy"></i></a>
            </div>            
          </div>
        </div>
				';
        }
      }
    }
    echo '</div>';
  } else {

    echo '<div class="alert alert-danger">今天还没有上传的图片哟~~ <br />快来上传第一张吧~！</div>';
  }
}
/*
$yesterday = date("Y/m/d/", strtotime("-1 day"));
// 昨日日期
$todayUpload = getFileNumber(APP_ROOT . config_path());
// 今日上传数量
$yesterdayUpload = getFileNumber(APP_ROOT . $config['path'] . $yesterday);
// 昨日上传数量

$spaceUsed = getDistUsed(disk_total_space(__DIR__) - disk_free_space(__DIR__));
// 占用空间
*/
// 当前日期全部上传
$allUploud = isset($_GET['date']) ? $_GET['date'] : date('Y/m/d/');
$allUploud = getFileNumber(APP_ROOT . $config['path'] . $allUploud);
@$httpUrl = array('date' => $path, 'num' => getFileNumber(APP_ROOT . config_path($path)));
?>
<style>
  /** 返回顶部*/
  * {
    list-style: none;
    border: 0;
  }

  #rocket-to-top div {
    left: 0;
    margin: 0;
    overflow: hidden;
    padding: 0;
    position: absolute;
    top: 0;
    width: 149px;
  }

  #rocket-to-top .level-2 {
    background: url("../public/images/rocket_button_up.png") no-repeat scroll -149px 0 transparent;
    display: none;
    height: 250px;
    opacity: 0;
    z-index: 1;
  }

  #rocket-to-top .level-3 {
    background: none repeat scroll 0 0 transparent;
    cursor: pointer;
    display: block;
    height: 150px;
    z-index: 2;
  }

  #rocket-to-top {
    background: url("../public/images/rocket_button_up.png") no-repeat scroll 0 0 transparent;
    cursor: default;
    display: block;
    height: 250px;
    margin: -125px 0 0;
    overflow: hidden;
    padding: 0;
    position: fixed;
    right: 0;
    top: 80%;
    width: 149px;
    z-index: 11;
  }
</style>
<script src="<?php static_cdn(); ?>/public/static/lazyload.js"></script>
<link href="<?php static_cdn(); ?>/public/static/zui/lib/datetimepicker/datetimepicker.min.css" rel="stylesheet">
<script src="<?php static_cdn(); ?>/public/static/zui/lib/datetimepicker/datetimepicker.min.js"></script>

<div class="col-md-12">
  <div class="col-md-8">
    <a href="list.php?<?php echo http_build_query($httpUrl); ?>"><span class="label label-info  label-outline"> 当前<?php echo $allUploud; ?>张</span></a>
    <a href="list.php"><span class="label label-success label-outline">今日<?php echo read_total_json('todayUpload'); ?>张</span></a><a href="list.php?date=<?php echo date("Y/m/d/", strtotime("-1 day")) ?>"><span class="label label-danger  label-outline">昨日<?php echo read_total_json('yestUpload'); ?>张</span></a>
    <?php for ($x = 2; $x <= 5; $x++) {
      /** 倒推日期显示上传图片 */ echo '<a href="list.php?date=' . date('Y/m/d/', strtotime("-{$x} day")) . '"> <span class="label label-danger  label-outline"> ' . date('m月d日', strtotime("-{$x} day")) . '</a>';
    }
    if (is_online()) {
      echo '
      <div class="btn-group" style="padding:5px">
      <button class="btn btn-mini" type="button" onclick="opcheckboxed(\'checkbox\', \'checkall\')">全选</button>
      <button class="btn btn-mini" type="button" onclick="opcheckboxed(\'checkbox\', \'reversecheck\')">反选</button>
      <button class="btn btn-mini btn-primary" type="button" onclick="opcheckboxed(\'checkbox\', \'uncheckall\')">取消</button>
      <button class="btn btn-mini btn-danger" type="button" onclick="fun()">删除</button>
    </div>
    ';
    }

    ?>
  </div>
  <div class="col-md-4">
    <form class="form-inline" action="list.php" method="get">
      <div class="form-group">
        <!--<label for="exampleInputInviteCode3">按日期</label>-->
        <input type="text" class="form-control form-date" value="<?php echo date('Y/m/d/'); ?>" name="date" readonly="">
      </div>
      <button type="submit" class="btn btn-primary">按日期</button>
    </form>
  </div>
</div>
<!-- 返回顶部 -->
<div style="display: none;" id="rocket-to-top">
  <div style="opacity:0;display: block;" class="level-2"></div>
  <div class="level-3"></div>
</div>
<script>
  // 复制url
  var clipboardDemos = new ClipboardJS('[data-clipboard-demo]');
  clipboardDemos.on('success', function(e) {
    new $.zui.Messager("复制成功！", {
      type: "success" // 定义颜色主题 
    }).show();
  });
  clipboardDemos.on('error', function(e) {
    console.error('Action:', e.action);
    console.error('Trigger:', e.trigger);
    showTooltip(e.trigger, fallbackMessage(e.action));
  });

  // 取消/全选文件
  function opcheckboxed(objName, type) {
    var objNameList = document.getElementsByName(objName);
    if (null != objNameList) {
      for (var i = 0; i < objNameList.length; i++) {
        if (objNameList[i].checked == true) {
          if (type != 'checkall') { // 非全选
            objNameList[i].checked = false;
          }
        } else {
          if (type != 'uncheckall') { // 非取消全选
            objNameList[i].checked = true;
          }
        }
      }
    }
  }
  //获取所有的 checkbox 属性的 input标签
  function fun() {
    confirm('确认要删除？\n* 删除文件夹后将无法恢复！');
    obj = document.getElementsByName("checkbox");
    check_val = [];
    for (k in obj) {
      //判断复选框是否被选中
      if (obj[k].checked)
        //获取被选中的复选框的值
        check_val.push(obj[k].value);
      console.log(check_val);
    }
    $.post("./post_del.php", {
        'del_url_array': check_val
      },
      function(data) {
        if (data.search('success') > 0) {
          new $.zui.Messager("删除成功，请刷新浏览器；如果开启了CDN，请等待缓存失效!", {
            type: "success" // 定义颜色主题 
          }).show();
          // 延时2秒刷新
          window.setTimeout(function() {
            window.location.reload();
          }, 1500)
        } else {
          new $.zui.Messager("文件不存在", {
            type: "danger" // 定义颜色主题 
          }).show();
        }

      });
  }
  // 返回顶部
  $(function() {
    var e = $("#rocket-to-top"),
      t = $(document).scrollTop(),
      n,
      r,
      i = !0;
    $(window).scroll(function() {
        var t = $(document).scrollTop();
        t == 0 ? e.css("background-position") == "0px 0px" ? e.fadeOut("slow") : i && (i = !1, $(".level-2").css("opacity", 1), e.delay(100).animate({
            marginTop: "-1000px"
          },
          "normal",
          function() {
            e.css({
                "margin-top": "-125px",
                display: "none"
              }),
              i = !0
          })) : e.fadeIn("slow")
      }),
      e.hover(function() {
          $(".level-2").stop(!0).animate({
            opacity: 1
          })
        },
        function() {
          $(".level-2").stop(!0).animate({
            opacity: 0
          })
        }),
      $(".level-3").click(function() {
        function t() {
          var t = e.css("background-position");
          if (e.css("display") == "none" || i == 0) {
            clearInterval(n),
              e.css("background-position", "0px 0px");
            return
          }
          switch (t) {
            case "0px 0px":
              e.css("background-position", "-298px 0px");
              break;
            case "-298px 0px":
              e.css("background-position", "-447px 0px");
              break;
            case "-447px 0px":
              e.css("background-position", "-596px 0px");
              break;
            case "-596px 0px":
              e.css("background-position", "-745px 0px");
              break;
            case "-745px 0px":
              e.css("background-position", "-298px 0px");
          }
        }
        if (!i) return;
        n = setInterval(t, 50),
          $("html,body").animate({
              scrollTop: 0
            },
            "slow");
      });
  });
</script>
<script type="text/javascript">
  // 按日期浏览
  $(".form-date").datetimepicker({
    weekStart: 1,
    todayBtn: 1,
    autoclose: 1,
    todayHighlight: 1,
    startView: 2,
    minView: 2,
    forceParse: 0,
    format: "yyyy/mm/dd/"
  });
  // 更改网页标题
  document.title = "图床广场 今日上传<?php echo read_total_json('todayUpload'); ?>张 昨日<?php echo read_total_json('yestUpload'); ?>张 - <?php echo $config['title']; ?> "
  //懒加载
  var lazy = new Lazy({
    onload: function(elem) {
      console.log(elem)
    },
    delay: 300
  })
</script>
<?php require_once APP_ROOT . '/application/footer.php';
