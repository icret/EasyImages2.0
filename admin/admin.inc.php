<?php
/*
 * 登录页面
 */

require_once __DIR__ . '/../application/function.php';
require_once APP_ROOT . '/application/header.php';

if (!is_online()) {
  echo '
  <script> new $.zui.Messager("请登录后再修改！", {type: "danger" // 定义颜色主题 
  }).show();</script>';
  exit(require_once APP_ROOT . '/application/login.php');
}

if (isset($_POST['form'])) {
  $postArr = $_POST;
  $new_config = array_replace($config, $postArr);
  $config_file = APP_ROOT.'/config/config.php';
  cache_write($config_file,$new_config);
  echo '
  <script>
  new $.zui.Messager("保存成功", {
    type: "success" // 定义颜色主题 
  }).show();
  </script>  
  ';
  header("refresh:1;");
}


?>
<div class="container">
  <div class="row">
    <div class="col-xs-3">
      <ul class="nav nav-tabs nav-stacked">
        <li class="active"><a href="###" data-target="#tab3Content1" data-toggle="tab">网站设置</a></li>
        <li><a href="###" data-target="#tab3Content2" data-toggle="tab">上传设置</a></li>
        <li><a href="###" data-target="#tab3Content3" data-toggle="tab">拓展设置</a></li>
        <li><a href="###" data-target="#tab3Content4" data-toggle="tab">API 设置</a></li>
        <li><a href="###" data-target="#tab3Content5" data-toggle="tab">安全设置</a></li>
      </ul>
    </div>
    <div class="col-xs-9">
      <div class="tab-content col-xs-9">
        <div class="tab-pane fade active in" id="tab3Content1">
          <form action="<?php echo $_SERVER['SCRIPT_NAME']; ?>" method="post">
            <div class="form-group">
              <label>网站域名,末尾不加"/" </label>
              <input type="url" class="form-control" name="domain" required="required" value="<?php echo $config['domain']; ?>" onkeyup="this.value=this.value.replace(/\s/g,'')">
            </div>
            <div class="form-group">
              <label>图片链接域名,末尾不加"/"</label>
              <input type="text" class="form-control" name="imgurl" required="required" value="<?php echo $config['imgurl']; ?>" placeholder="末尾不加/" onkeyup="this.value=this.value.replace(/\s/g,'')" title="网站域名与图片链接域名可以不同，比如A域名上传，可以返回B域名图片链接，A、B需绑定到同一空间下,如果不变的话，下边2个填写成一样的！">
            </div>
            <div class="form-group">
              <label>登录上传和后台管理密码,管理用户名为：admin,更改后会立即生效</label>
              <div class="input-group">
                <input type="password" class="form-control" name="password" required="required" value="<?php echo $config['password']; ?>" id="pwd" onkeyup="this.value=this.value.replace(/\s/g,'')">
                <span class="input-group-btn">
                  <button class="btn btn-default" type="button"><i class="icon icon-eye-close" onclick="showhide()" id="eye"></i></button>
                </span>
              </div>
            </div>
            <div class="form-group">
              <label>网站标题</label>
              <input type="text" class="form-control" name="title" required="required" value="<?php echo $config['title']; ?>" onkeyup="this.value=this.value.trim()">
            </div>
            <div class="form-group">
              <label>网站关键字</label>
              <textarea class="form-control" rows="3" name="keywords" required="required" onkeyup="this.value=this.value.replace(/\s/g,'')"><?php echo $config['keywords']; ?></textarea>
            </div>
            <div class="form-group">
              <label>网站描述</label>
              <textarea class="form-control" rows="3" name="description" required="required" onkeyup="this.value=this.value.replace(/\s/g,'')"><?php echo $config['description']; ?></textarea>
            </div>
            <div class="form-group">
              <label>网站公告</label>
              <textarea class="form-control" rows="3" name="tips" required="required" onkeyup="this.value=this.value.replace(/\s/g,'')"><?php echo $config['tips']; ?></textarea>
            </div>
            <div class="form-group">
              <label>页脚信息</label>
              <textarea class="form-control" rows="3" name="footer" required="required"><?php echo $config['footer']; ?></textarea>
            </div>
            <div class="form-group">
              <div class="switch">
                <input type="hidden" name="static_cdn" value="0">
                <input type="checkbox" name="static_cdn" value="1" <?php if ($config['static_cdn']) {echo 'checked="checked"';} ?>>
                <label style="font-weight: bold">开启静态文件CDN</label>
              </div>
            </div>
            <div class="form-group">
              <label>静态文件CDN加速网址 末尾不加 /</label>
              <input type="url" class="form-control" name="static_cdn_url" value="<?php echo $config['static_cdn_url']; ?>" onkeyup="this.value=this.value.replace(/\s/g,'')">
            </div>
            <div class="form-group">
              <input type="hidden" class="form-control" name="form" value="" placeholder="隐藏的保存">
            </div>
            <button type="submit" class="btn btn-primary">保存</button>
          </form>
        </div>
        <div class="tab-pane fade" id="tab3Content2">
          <form action="<?php echo $_SERVER['SCRIPT_NAME']; ?>" method="post">
            <div class="form-group">
              <div class="switch">
                <input type="hidden" name="mustLogin" value="0">
                <input type="checkbox" name="mustLogin" value="1" <?php if ($config['mustLogin']) {echo 'checked="checked"';} ?>>
                <label style="font-weight: bold">开启登录上传</label>
              </div>
            </div>
            <div class="form-group">
              <div class="switch">
                <input type="hidden" name="apiStatus" value="0">
                <input type="checkbox" name="apiStatus" value="1" <?php if ($config['apiStatus']) {echo 'checked="checked"';} ?>>
                <label style="font-weight: bold">开启API上传</label>
              </div>
            </div>
            <div class="form-group">
              <label>存储路径 例：/i/ 前后需加英文'/'</label>
              <input type="text" class="form-control" name="path" required="required" value="<?php echo $config['path']; ?>" onkeyup="this.value=this.value.replace(/\s/g,'')" title="可根据Apache/Nginx配置安全，参考：https://www.545141.com/981.html 或 README.md">
            </div>
            <div class="form-group">
              <label>文件的命名方式</label>
              <select class="chosen-select form-control" name="imgName">
                <option value="default" <?php if ($config['imgName'] == 'default') {echo 'selected';} ?>>将上传时间+随机数转换为36进制 例：vx77yu</option>
                <option value="date" <?php if ($config['imgName'] == 'date') {echo 'selected';} ?>>以上传时间 例：192704</option>
                <option value="unix" <?php if ($config['imgName'] == 'unix') {echo 'selected';} ?>>以Unix时间 例：1635074840</option>
                <option value="uniqid" <?php if ($config['imgName'] == 'uniqid') {echo 'selected';} ?>>基于以微秒计的当前时间 例：6175436c73418</option>
                <option value="guid" <?php if ($config['imgName'] == 'guid') {echo 'selected';} ?>>全球唯一标识符 例：6EDAD0CC-AB0C-4F61-BCCA-05FAD65BF0FA</option>
                <option value="md5" <?php if ($config['imgName'] == 'md5') {echo 'selected';} ?>>md5加密时间 例：3888aa69eb321a2b61fcc63520bf6c82</option>
                <option value="sha1" <?php if ($config['imgName'] == 'sha1') {echo 'selected';} ?>>sha1加密微秒 例：654faac01499e0cb5fb0e9d78b21e234c63d842a</option>
              </select>
            </div>
            <div class="form-group">
              <label>最大上传限制最小1MB，最大50MB，当前：</label><label id="maxSize"><?php echo $config['maxSize'] / 1024 / 1024; ?></label><label>MB</label>
              <input type="range" class="form-control" name="maxSize" value="<?php echo $config['maxSize']; ?>" min="1048576" max="52428800" step="1048576" onchange="document.getElementById('maxSize').innerHTML=value/1024/1024">
            </div>
            <div class="form-group">
              <label>每次最多上传图片数，当前：</label><label id="maxUploadFiles"><?php echo $config['maxUploadFiles']; ?></label><label>张</label>
              <input type="range" class="form-control" name="maxUploadFiles" value="<?php echo $config['maxUploadFiles']; ?>" min="1" max="100" step="1" onchange="document.getElementById('maxUploadFiles').innerHTML=value">
            </div>
            <div class="form-group">
              <label>上传水印</label>
              <select class="chosen-select form-control" name="watermark">
                <option value="0" <?php if (!$config['watermark']) {echo 'selected';} ?>>关闭水印</option>
                <option value="1" <?php if ($config['watermark'] == 1) {echo 'selected';} ?>>文字水印</option>
                <option value="2" <?php if ($config['watermark'] == 2) {echo 'selected';} ?>>图片水印</option>
              </select>
            </div>
            <div class="form-group">
              <label>水印文字内容</label>
              <input type="text" class="form-control" name="waterText" required="required" value="<?php echo $config['waterText']; ?>" onkeyup="this.value=this.value.trim()">
            </div>
            <div class="form-group">
              <label>水印位置：</label>
              <select class="chosen-select form-control" name="waterPosition">
                <option value="0" <?php if (!$config['waterPosition']) {echo 'selected';} ?>>随机位置</option><option value="1" <?php if ($config['waterPosition'] == 1) {echo 'selected';} ?>>顶部居左</option>
                <option value="2" <?php if ($config['waterPosition'] == 2) {echo 'selected';} ?>>顶部居中</option>
                <option value="3" <?php if ($config['waterPosition'] == 3) {echo 'selected';} ?>>顶部居右</option>
                <option value="4" <?php if ($config['waterPosition'] == 4) {echo 'selected';} ?>>左边居中</option>
                <option value="5" <?php if ($config['waterPosition'] == 5) {echo 'selected';} ?>>图片中心</option>
                <option value="6" <?php if ($config['waterPosition'] == 6) {echo 'selected';} ?>>右边居中</option>
                <option value="7" <?php if ($config['waterPosition'] == 7) {echo 'selected';} ?>>底部居左</option>
                <option value="8" <?php if ($config['waterPosition'] == 8) {echo 'selected';} ?>>底部居中</option>
                <option value="9" <?php if ($config['waterPosition'] == 9) {echo 'selected';} ?>>底部居右</option>
              </select>
            </div>
            <div class="form-group">
              <label>水印文字颜色 rgba 末尾为透明度0-127 0为不透明</label>
              <input type="text" name="textColor" class="form-control" value="" readonly data-jscolor="{value:'rgba(<?php echo $config['textColor']; ?>)', position:'bottom', height:80, backgroundColor:'#333',palette:'rgba(0,0,0,0) #fff #808080 #000 #996e36 #f55525 #ffe438 #88dd20 #22e0cd #269aff #bb1cd4',paletteCols:11, hideOnPaletteClick:true}">
            </div>
            <div class="form-group">
              <label>水印字体大小，当前：</label><label id="textSize"><?php echo $config['textSize']; ?></label><label>像素</label>
              <input type="range" class="form-control" name="textSize" value="<?php echo $config['textSize']; ?>" min="1" max="50" step="1" onchange="document.getElementById('textSize').innerHTML=value">
            </div>
            <div class="form-group">
              <label>字体路径 如果想改变字体，请选择支持中文的 GB/2312 字体</label>
              <input type="text" class="form-control" name="textFont" required="required" value="<?php echo $config['textFont']; ?>" onkeyup="this.value=this.value.replace(/\s/g,'')">
            </div>
            <div class="form-group">
              <label>图片水印路径 支持GIF,JPG,BMP,PNG和PNG alpha</label>
              <input type="text" class="form-control" name="waterImg" required="required" value="<?php echo $config['waterImg']; ?>" onkeyup="this.value=this.value.replace(/\s/g,'')">
            </div>
            <div class="form-group">
              <label>允许上传的图片扩展名,请以英文,分割</label>
              <input type="text" class="form-control" name="extensions" required="required" value="<?php echo $config['extensions']; ?>" onkeyup="this.value=this.value.replace(/\s/g,'')">
            </div>
            <div class="form-group">
              <div class="switch">
                <input type="hidden" name="compress" value="0">
                <input type="checkbox" name="compress" value="1" <?php if ($config['compress']) {echo 'checked="checked"';} ?> title=" 轻微有损压缩图片, 此压缩有可能使图片变大！特别是小图片 也有一定概率改变图片方向">
                <label style="font-weight: bold">开启压缩图片-开启后会增加服务器负担</label>
              </div>
            </div>
            <div class="form-group">
              <label>转换图片为指定格式<?php echo $config['imgConvert']; ?></label>
              <select class="chosen-select form-control" name="imgConvert">
                <option value="">不转换</option>
                <option value="png">png</option>
                <option value="jpeg">jpeg</option>
                <option value="gif">gif</option>
                <option value="bmp">bmp</option>
              </select>
            </div>
            <div class="form-group">
              <label>最大上传宽度</label>
              <input type="range" class="form-control" name="maxWidth" value="<?php echo $config['maxWidth']; ?>" min="1024" max="102400" step="1024" onchange="document.getElementById('maxWidth').innerHTML=value">
              <label id="maxWidth"><?php echo $config['maxWidth']; ?></label><label>像素</label>
            </div>
            <div class="form-group">
              <label>最大上传高度</label>
              <input type="range" class="form-control" name="maxHeight" value="<?php echo $config['maxHeight']; ?>" min="1024" max="102400" step="1024" onchange="document.getElementById('maxHeight').innerHTML=value">
              <label id="maxHeight"><?php echo $config['maxHeight']; ?></label><label>像素</label>
            </div>
            <div class="form-group">
              <label>允许上传的最小宽度</label>
              <input type="range" class="form-control" name="minWidth" value="<?php echo $config['minWidth']; ?>" min="5" max="1024" step="10" onchange="document.getElementById('minWidth').innerHTML=value">
              <label id="minWidth"><?php echo $config['minWidth']; ?></label><label>像素</label>
            </div>
            <div class="form-group">
              <label>允许上传的最小高度</label>
              <input type="range" class="form-control" name="minHeight" value="<?php echo $config['minHeight']; ?>" min="5" max="1024" step="10" onchange="document.getElementById('minHeight').innerHTML=value">
              <label id="minHeight"><?php echo $config['minHeight']; ?></label><label>像素</label>
            </div>
            <div class="form-group">
              <div class="switch">
                <input type="hidden" name="imgRatio" value="0">
                <input type="checkbox" name="imgRatio" value="1" <?php if ($config['imgRatio']) {echo 'checked="checked"';} ?>>
                <label style="font-weight: bold">开启上传后改变图片尺寸（不开启下边不会生效！）</label>
              </div>
            </div>
            <div class="form-group">
              <label>开启上传后改变图片尺寸->更改后的宽度：</label><label id="image_x"><?php echo $config['image_x']; ?></label><label>像素</label>
              <input type="range" class="form-control" name="image_x" value="<?php echo $config['image_x']; ?>" min="100" max="10240" step="100" onchange="document.getElementById('image_x').innerHTML=value">
            </div>
            <div class="form-group">
              <label>开启上传后改变图片尺寸->更改后的高度：</label><label id="image_y"><?php echo $config['image_y']; ?></label><label>像素</label>
              <input type="range" class="form-control" name="image_y" value="<?php echo $config['image_y']; ?>" min="100" max="10240" step="100" onchange="document.getElementById('image_y').innerHTML=value">
            </div>
            <div class="form-group">
              <input type="hidden" class="form-control" name="form" value="" placeholder="隐藏的保存">
            </div>
            <button type="submit" class="btn btn-primary">保存</button>
          </form>
        </div>
        <div class="tab-pane fade" id="tab3Content3">
          <form action="<?php echo $_SERVER['SCRIPT_NAME']; ?>" method="post">
            <div class="form-group">
              <div class="switch">
                <input type="hidden" name="ad_top" value="0">
                <input type="checkbox" name="ad_top" value="1" <?php if ($config['ad_top']) {echo 'checked="checked"';} ?>>
                <label style="font-weight: bold">开启顶部广告</label>
              </div>
            </div>
            <div class="form-group">
              <label>顶部广告内容 支持html代码</label>
              <textarea class="form-control" rows="5" name="ad_top_info"><?php echo $config['ad_top_info']; ?></textarea>
            </div>
            <div class="form-group">
              <div class="switch">
                <input type="hidden" name="ad_bot" value="0">
                <input type="checkbox" name="ad_bot" value="1" <?php if ($config['ad_bot']) {echo 'checked="checked"';} ?>>
                <label style="font-weight: bold">开启底部广告</label>
              </div>
            </div>
            <div class="form-group">
              <label>底部广告内容 支持html代码</label>
              <textarea class="form-control" rows="5" name="ad_bot_info"><?php echo $config['ad_bot_info']; ?></textarea>
            </div>
            <div class="form-group">
              <label>自定义信息，仅支持html代码 可以放置统计代码</label>
              <textarea class="form-control" rows="7" name="customize"><?php echo $config['customize']; ?></textarea>
            </div>
            <div class="form-group">
              <input type="hidden" class="form-control" name="form" value="" placeholder="隐藏的保存">
            </div>
            <button type="submit" class="btn btn-primary">保存</button>
          </form>
        </div>
        <div class="tab-pane fade " id="tab3Content4">
          <form action="<?php echo $_SERVER['SCRIPT_NAME']; ?>" method="post">
            <div class="form-group">
              <label>TinyImag Key 申请地址：<a href="https://tinypng.com/developers" target="_blank">https://tinypng.com/developers</a></label>
              <input type="text" class="form-control input-sm" name="TinyImag_key" value="<?php echo $config['TinyImag_key']; ?>" title="开启后会受服务器到https://tinypng.com 速度影响，国内不建议开启！" onkeyup="this.value=this.value.replace(/\s/g,'')">
            </div>
            <div class="form-group">
              <input type="hidden" class="form-control" name="form" value="" placeholder="隐藏的保存">
            </div>
            <button type="submit" class="btn btn-primary">保存</button>
          </form>
        </div>
        <div class="tab-pane fade" id="tab3Content5">
          <form action="<?php echo $_SERVER['SCRIPT_NAME']; ?>" method="post">
            <div class="form-group">
              <div class="switch">
                <input type="hidden" name="showSwitch" value="0">
                <input type="checkbox" name="showSwitch" value="1" <?php if ($config['showSwitch']) {echo 'checked="checked"';} ?>>
                <label style="font-weight: bold">开启游客预览（广场）</label>
              </div>
            </div>
            <div class="form-group">
              <label>默认预览数量，当前：</label>
              <label id="listNumber"><?php echo $config['listNumber']; ?></label><label>张</label>
              <input type="range" class="form-control" name="listNumber" value="<?php echo $config['listNumber']; ?>" min="10" max="100" step="10" onchange="document.getElementById('listNumber').innerHTML=value" title="可在网址后填写参数实时更改预览数量 如：https://img.545141.com/application/list.php?num=3">
            </div>
            <div class="form-group">
              <div class="switch">
                <input type="hidden" name="checkImg" value="0">
                <input type="checkbox" name="checkImg" value="1" <?php if ($config['checkImg']) {echo 'checked="checked"';} ?> title="开启后会受服务器到https://moderatecontent.com速度影响，国内不建议开启！">
                <label style="font-weight: bold">开启图片监黄</label>
              </div>
            </div>
            <div class="form-group">
              <label>图片监黄key 申请地址：<a href="https://client.moderatecontent.com" target="_blank">https://client.moderatecontent.com</a></label>
              <input type="text" class="form-control input-sm" name="moderatecontent_key" value="<?php echo $config['moderatecontent_key']; ?>" title="开启后会受服务器到https://moderatecontent.com 速度影响，国内不建议开启！" onkeyup="this.value=this.value.replace(/\s/g,'')">
            </div>
            <div class="form-group">
              <label>设置是不良图片概率,概率越大准确率越高，当前：</label>
              <label id="checkImg_value"><?php echo $config['checkImg_value']; ?></label><label>%</label>
              <input type="range" class="form-control" name="checkImg_value" value="<?php echo $config['checkImg_value']; ?>" min="1" max="100" step="1" onchange="document.getElementById('checkImg_value').innerHTML=value">
            </div>
            <div class="form-group">
              <label>缓存有效期，当前：</label>
              <label id="cache_freq"><?php echo $config['cache_freq']; ?></label><label>小时</label>
              <input type="range" class="form-control" name="cache_freq" value="<?php echo $config['cache_freq']; ?>" min="1" step="1"max="24" onchange="document.getElementById('cache_freq').innerHTML=value">
            </div>
            <div class="form-group">
              <div class="switch">
                <input type="hidden" name="checkEnv" value="0">
                <input type="checkbox" name="checkEnv" value="1" <?php if ($config['checkEnv']) {echo 'checked="checked"';} ?>>
                <label style="font-weight: bold">开启PHP插件检测-安全设置检测-版本检测</label>
              </div>
            </div>
            <div class="form-group">
              <div class="switch">
                <input type="hidden" name="upload_logs" value="0">
                <input type="checkbox" name="upload_logs" value="1" <?php if ($config['upload_logs']) {echo 'checked="checked"';} ?> title="日志每月保存一个文件；经过测试每月二十万条数据并不影响速度！">
                <label style="font-weight: bold">开启上传日志</label>
              </div>
            </div>            
            <div class="form-group">
              <p style="font-weight: bold">
              当前版本：<span class="label label-badge label-outline"><?php echo $config['version']; ?></span>
              Github：<a href="https://github.com/icret/EasyImages2.0/releases" target="_blank"><span class="label label-badge label-success label-outline"><?php echo getVersion(); ?></span></a>
              </p>
            </div>
            <div class="form-group">
              <input type="hidden" class="form-control" name="form" value="" placeholder="隐藏的保存">
            </div>
            <button type="submit" class="btn btn-primary">保存</button>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>
</div>

<script type="text/javascript" src="<?php static_cdn(); ?>/public/static/jscolor.js"></script>
<script>
  var eye = document.getElementById("eye");
  var pwd = document.getElementById("pwd");

  function showhide() {
    if (pwd.type == "password") {
      pwd.type = "text";
      eye.className = 'icon icon-eye-open'
    } else {
      pwd.type = "password";
      eye.className = 'icon icon-eye-close'
    }
  }
  // jscolor
  jscolor.presets.default = {
    position: 'right',
    palette: [
      '#000000', '#7d7d7d', '#870014', '#ec1c23', '#ff7e26',
      '#fef100', '#22b14b', '#00a1e7', '#3f47cc', '#a349a4',
      '#ffffff', '#c3c3c3', '#b87957', '#feaec9', '#ffc80d',
      '#eee3af', '#b5e61d', '#99d9ea', '#7092be', '#c8bfe7',
    ],
    //paletteCols: 12,
    //hideOnPaletteClick: true,
  };
  // 更改网页标题
  document.title = "配置中心 - EasyImage2.0 简单图床"
</script>
<?php
require_once APP_ROOT . '/application/footer.php';
