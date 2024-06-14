<?php

/** 广场页面 */
require_once __DIR__ . '/header.php';

/** 顶部广告 */
if ($config['ad_top']) echo $config['ad_top_info'];
?>
<div class="row">
  <div class="col-md-12">
    <?php
    if (!$config['showSwitch'] && !is_who_login('admin')) : ?>
      <div class="alert alert-info">管理员关闭了预览哦~~</div>
      <?php exit(require_once __DIR__ . '/footer.php'); ?>
      <?php else :
      // $path = isset($_GET['date']) ? $_GET['date'] : date('Y/m/d/');                                 // 获取指定目录
      /* 限制GET浏览日期 有助于防止爬虫*/
      $listDate = $config['listDate'];                                                                  // 配置限制日期
      $path =  date('Y/m/d/');                                                                          // 当前日期
      if (isset($_GET['date'])) {
        if ($_GET['date'] < date('Y/m/d/', strtotime("- $listDate day"))) {                             // GET日期小于配置日期时返回当前日期
          $path =  date('Y/m/d/');
          echo '
          <script>
            new $.zui.Messager("已超出浏览页数, 返回今日上传列表", {
            type: "info", // 定义颜色主题 
            icon: "exclamation-sign" // 定义消息图标
            }).show();
          </script>';
        } else {
          $path = $_GET['date'];                                                                        // 如果不小于则返回当前GET日期
        }
      }

      $path = preg_replace("/^d{4}-d{2}-d{2} d{2}:d{2}:d{2}$/s", "", trim($path));                      // 过滤非日期，删除空格
      $keyNum = isset($_GET['num']) ? $_GET['num'] : $config['listNumber'];                             // 获取指定浏览数量
      $keyNum = preg_replace("/[\W]/", "", trim($keyNum));                                              // 过滤非数字，删除空格
      // $fileArr = getFile(APP_ROOT . config_path($path));                                             // 获取当日上传列表
      $fileType = isset($_GET['search']) ? '*.' . preg_replace("/[\W]/", "", $_GET['search'])  : '*.*'; // 按照图片格式
      $fileArr = get_file_by_glob(APP_ROOT . config_path($path) .  $fileType, 'list');                  // 获取当日上传列表
      $allUploud = isset($_GET['date']) ? $_GET['date'] : date('Y/m/d/');
      $allUploud = get_file_by_glob(APP_ROOT . $config['path'] . $allUploud, 'number');                 // 当前日期全部上传
      $httpUrl = array('date' => $path, 'num' => getFileNumber(APP_ROOT . config_path($path)));         // 组合url

      // 隐藏path目录获取图片复制与原图地址
      if ($config['hide_path']) {
        $config_path = str_replace($config['path'], '/', config_path($path));
      } else {
        $config_path = config_path($path);
      }

      if (empty($fileArr[0])) : ?>
        <div class="alert alert-danger">今天还没有上传的图片哟~~ <br />快来上传第一张吧~!</div>
      <?php else : ?>
        <ul id="viewjs">
          <div class="cards listNum">
            <?php foreach ($fileArr as $key => $value) {
              if ($key < $keyNum) {
                $relative_path = config_path($path) . $value;     // 相对路径
                $imgUrl = $config['domain'] . $relative_path;     // 图片地址
                $linkUrl = rand_imgurl() . $config_path . $value; // 图片复制与原图地址
            ?>
                <div class="col-lg-3 col-md-4 col-sm-6 col-xs-12">
                  <div class="card">
                    <li><img src="<?php static_cdn(); ?>/public/images/loading.svg" data-image="<?php echo creat_thumbnail_by_list($imgUrl); ?>" data-original="<?php echo $imgUrl; ?>" alt="简单图床-EasyImage"></li>
                    <div class="bottom-bar">
                      <a href="<?php echo $linkUrl; ?>" target="_blank"><i class="icon icon-picture" data-toggle="tooltip" title="打开" style="margin-left:10px;"></i></a>
                      <a href="#" class="copy" data-clipboard-text="<?php echo $linkUrl; ?>" data-toggle="tooltip" title="复制链接" style="margin-left:10px;"><i class="icon icon-copy"></i></a>
                      <?php if ($config['show_exif_info'] || is_who_login('admin')) : ?>
                        <a href="/app/info.php?img=<?php echo $relative_path; ?>" data-toggle="tooltip" title="详细信息" target="_blank" style="margin-left:10px;"><i class="icon icon-info-sign"></i></a>
                      <?php endif; ?>
                      <a href="/app/down.php?dw=<?php echo $relative_path; ?>" data-toggle="tooltip" title="下载文件" target="_blank" style="margin-left:10px;"><i class="icon icon-cloud-download"></i></a>
                      <?php if (!empty($config['report'])) : ?>
                        <a href="<?php echo $config['report'] . '?Website1=' . $linkUrl; ?>" target="_blank"><i class="icon icon-question-sign" data-toggle="tooltip" title="举报文件" style="margin-left:10px;"></i></a>
                      <?php endif; ?>
                      <?php if (is_who_login('admin')) : ?>
                        <a href="#" onclick="ajax_post('<?php echo $relative_path; ?>','recycle')" data-toggle="tooltip" title="回收文件" style="margin-left:10px;"><i class="icon icon-undo"></i></a>
                        <a href="#" onclick="ajax_post('<?php echo $relative_path; ?>')" data-toggle="tooltip" title="删除文件" style="margin-left:10px;"><i class="icon icon-trash"></i></a>
                        <label class="text-primary"><input type="checkbox" id="url" name="checkbox" value="<?php echo $relative_path; ?>"> 选择</label>
                      <?php endif; ?>
                    </div>
                  </div>
                </div>
            <?php
              }
            }
            ?>
          </div>
        </ul>
    <?php
      endif;
    endif;
    /** 底部广告 */
    if ($config['ad_bot']) echo $config['ad_bot_info']; ?>
  </div>
  <div class="col-md-12" style="margin-bottom: 5em;">
    <hr />
    <div class="col-md-8 col-xs-12" style="padding-bottom:5px">
      <div class="btn-toolbar">
        <div class="btn-group">
          <a class="btn btn-danger btn-mini" href="?<?php echo http_build_query($httpUrl); ?>">当前<?php echo $allUploud; ?></a>
          <a class="btn btn-primary btn-mini" href="list.php">今日<?php echo get_file_by_glob(APP_ROOT . config_path() . '*.*', 'number'); ?></a>
          <a class="btn btn-mini" href="?date=<?php echo date("Y/m/d/", strtotime("-1 day")) ?>">昨日<?php echo get_file_by_glob(APP_ROOT . $config['path'] . date("Y/m/d/", strtotime("-1 day")), 'number'); ?></a>
          <?php
          // 倒推日期显示上传图片 @param $listDate 配置的倒退日期
          for ($x = 2; $x <= $listDate; $x++)
            echo '<a class="btn btn-mini hidden-xs inline-block" href="?date=' . date('Y/m/d/', strtotime("-$x day"))  .  '">' . date('j号', strtotime("-$x day")) . '</a>';
          ?>
        </div>
        <?php if (is_who_login('admin')) : ?>
          <div class="btn-group">
            <a class="btn btn-mini" onclick="opcheckboxed('checkbox', 'checkall')">全选</a>
            <a class="btn btn-mini" onclick="opcheckboxed('checkbox', 'reversecheck')">反选</a>
            <a class="btn btn-mini" onclick="opcheckboxed('checkbox', 'uncheckall')">取消</a>
            <a class="btn btn-mini" onclick="recycle_img()">回收</a>
            <a class="btn btn-mini" onclick="delete_img()">删除</a>
          </div>
        <?php endif; ?>
      </div>
    </div>
    <!-- 按格式 -->
    <!-- <div class="row">
      <div class="col-md-2 col-xs-6">
        <form action="<php echo '?' . http_build_query($httpUrl) . '&'; ?>" method="get">
          <div class="input-group">
            <select name="search" class="form-control input-sm">
              <option value="jpg">JPG</option>
              <option value="png">PNG</option>
              <option value="gif">Gif</option>
              <option value="webp">WEBP</option>
            </select>
            <span class="input-group-btn">
              <input type="submit" value="按格式" class="btn btn-primary input-sm" />
            </span>
          </div>
        </form>
      </div> -->
    <div class="col-md-2 col-xs-7">
      <div class="btn-group">
        <a class="btn btn-mini" href="<?php echo '?' . http_build_query($httpUrl) . '&search=jpg'; ?>">JPG</a>
        <a class="btn btn-mini" href="<?php echo '?' . http_build_query($httpUrl) . '&search=png'; ?>">PNG</a>
        <a class="btn btn-mini" href="<?php echo '?' . http_build_query($httpUrl) . '&search=gif'; ?>">GIF</a>
        <a class="btn btn-mini" href="<?php echo '?' . http_build_query($httpUrl) . '&search=webp'; ?>">Webp</a>
      </div>
    </div>
    <!-- 按日期-->
    <div class="col-md-2 col-xs-5">
      <form action="<?php echo $_SERVER['SCRIPT_NAME']; ?>" method="get">
        <div class="input-group">
          <span class="input-group-addon fix-border fix-padding"></span>
          <input type="text" class="form-control form-date input-sm" name="date" value="<?php echo date('Y/m/d/'); ?>" readonly="readonly">
          <span class="input-group-btn">
            <button type="submit" class="btn btn-primary input-sm">按日期</button>
          </span>
        </div>
      </form>
      <!-- 返回顶部-->
      <div class="btn btn-mini btn-primary btn-back-to-top"><i class="icon icon-arrow-up"></i></div>
    </div>
  </div>
  <link rel="stylesheet" href="<?php static_cdn(); ?>/public/static/EasyImage.css">
  <link rel="stylesheet" href="<?php static_cdn(); ?>/public/static/viewjs/viewer.min.css">
  <link rel="stylesheet" href="<?php static_cdn(); ?>/public/static/zui/lib/bootbox/bootbox.min.css">
  <link rel="stylesheet" href="<?php static_cdn(); ?>/public/static/zui/lib/datetimepicker/datetimepicker.min.css">
  <script type="application/javascript" src="<?php static_cdn(); ?>/public/static/viewjs/viewer.min.js"></script>
  <script type="application/javascript" src="<?php static_cdn(); ?>/public/static/lazyload/lazyload.min.js"></script>
  <script type="application/javascript" src="<?php static_cdn(); ?>/public/static/zui/lib/bootbox/bootbox.min.js"></script>
  <script type="application/javascript" src="<?php static_cdn(); ?>/public/static/zui/lib/clipboard/clipboard.min.js"></script>
  <script type="application/javascript" src="<?php static_cdn(); ?>/public/static/zui/lib/datetimepicker/datetimepicker.min.js"></script>
  <script>
    // viewjs
    new Viewer(document.getElementById('viewjs'), {
      url: 'data-original',
    });

    // POST 删除提交
    function ajax_post(url, mode = 'delete') {

      bootbox.confirm({
        message: "确认执行 " + mode + " 操作?",
        buttons: {
          confirm: {
            label: '确定',
            className: 'btn-success'
          },
          cancel: {
            label: '取消',
            className: 'btn-danger'
          }
        },
        callback: function(result) {
          if (result == true) {
            $.post("del.php", {
                url: url,
                mode: mode
              },
              function(data, status) {
                console.log(data)
                let res = JSON.parse(data);
                new $.zui.Messager(res.msg, {
                  type: res.type,
                  icon: res.icon
                }).show();
                // 延时2秒刷新
                window.setTimeout(function() {
                  window.location.reload();
                }, 2000)
              });
          } else {
            new $.zui.Messager("取消 " + mode, {
              type: "primary", // 定义颜色主题 
              icon: "info-sign" // 定义消息图标
            }).show();
          }
        }
      });
    }

    // 复制url
    var clipboard = new Clipboard('.copy');
    clipboard.on('success', function(e) {
      new $.zui.Messager("复制直链成功", {
        type: "success", // 定义颜色主题 
        icon: "ok-sign" // 定义消息图标
      }).show();

    });
    clipboard.on('error', function(e) {
      document.querySelector('.copy');
      new $.zui.Messager("复制失败", {
        type: "danger", // 定义颜色主题 
        icon: "exclamation-sign" // 定义消息图标
      }).show();
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
    // 回收图片
    function recycle_img() {
      bootbox.confirm({
        message: "确认要放入回收站? <br /> 可在可疑图片中恢复!",
        buttons: {
          confirm: {
            label: '确定',
            className: 'btn-success'
          },
          cancel: {
            label: '取消',
            className: 'btn-danger'
          }
        },
        callback: function(result) {
          if (result == true) {
            obj = document.getElementsByName("checkbox");
            check_val = [];
            for (k in obj) {
              //判断复选框是否被选中
              if (obj[k].checked)
                //获取被选中的复选框的值
                check_val.push(obj[k].value);
              console.log(check_val);
            }
            $.post("del.php", {
              'recycle_url_array': check_val
            }, );
            new $.zui.Messager("放入回收站成功", {
              type: "success", // 定义颜色主题 
              icon: "ok-sign" // 定义消息图标
            }).show();
            // 延时2秒刷新
            window.setTimeout(function() {
              window.location.reload();
            }, 1500)
          } else {
            new $.zui.Messager("取消回收", {
              type: "primary", // 定义颜色主题 
              icon: "info-sign" // 定义消息图标
            }).show();
          }
          console.log('是否回收图片: ' + result);
        }
      });

    }
    // 删除图片
    function delete_img() {
      bootbox.confirm({
        message: "确认要删除?<br />* 删除文件夹后将无法恢复!",
        buttons: {
          confirm: {
            label: '确定',
            className: 'btn-success'
          },
          cancel: {
            label: '取消',
            className: 'btn-danger'
          }
        },
        callback: function(result) {
          if (result == true) {
            obj = document.getElementsByName("checkbox");
            check_val = [];
            for (k in obj) {
              //判断复选框是否被选中
              if (obj[k].checked)
                //获取被选中的复选框的值
                check_val.push(obj[k].value);
              console.log(check_val);
            }
            $.post("del.php", {
                'del_url_array': check_val
              },
              function(data) {
                if (data.search('success') > 0) {
                  new $.zui.Messager("删除成功", {
                    type: "success", // 定义颜色主题 
                    icon: "ok-sign" // 定义消息图标
                  }).show();
                } else {
                  new $.zui.Messager("删除失败 请登录后再删除!", {
                    type: "danger", // 定义颜色主题 
                    icon: "exclamation-sign" // 定义消息图标
                  }).show();
                }
                // 延时2秒刷新
                window.setTimeout(function() {
                  window.location.reload();
                }, 1500)
              });
          } else {
            new $.zui.Messager("取消删除", {
              type: "primary", // 定义颜色主题 
              icon: "info-sign" // 定义消息图标
            }).show();
          }
          console.log('是否删除图片: ' + result);
        }
      });
    }

    //懒加载
    var lazy = new Lazy({
      onload: function(elem) {
        console.log(elem)
      },
      delay: 300,
    })

    // 返回顶部
    var back_to_top_button = jQuery('.btn-back-to-top');
    jQuery(window).scroll(function() {
      if (jQuery(this).scrollTop() > 100 && !back_to_top_button.hasClass('scrolled')) {
        back_to_top_button.addClass('scrolled');

      } else if (jQuery(this).scrollTop() < 100 && back_to_top_button.hasClass('scrolled')) {
        back_to_top_button.removeClass('scrolled');

      }
    });
    // 返回顶部
    back_to_top_button.click(function() {
      jQuery('html, body').animate({
        scrollTop: 0
      }, 800);
      return false;
    });

    // 按日期浏览
    $(".form-date").datetimepicker({
      weekStart: 1,
      todayBtn: 1,
      autoclose: 1,
      todayHighlight: 1,
      startView: 2,
      minView: 2,
      forceParse: 0,
      pickerPosition: "top-right",
      format: "yyyy/mm/dd/",
      endDate: new Date() // 只能选当前日期之前
    });

    // 更改网页标题
    document.title = "图床广场 - 今日上传<?php echo get_file_by_glob(APP_ROOT . config_path(), 'number'); ?>张 昨日<?php echo get_file_by_glob(APP_ROOT . $config['path'] . date("Y/m/d/", strtotime("-1 day")) . '*.*', 'number'); ?>张 - <?php echo $config['title']; ?>"
  </script>
  <?php
  /** 引入底部 */
  require_once __DIR__ . '/footer.php';
