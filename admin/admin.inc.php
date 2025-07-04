<?php
/*
 * 简单图床设置页面
 * 2022-1-24 05:57:35
 */

require_once __DIR__ . '/../app/header.php';
require_once APP_ROOT . '/config/api_key.php';
require_once APP_ROOT . '/config/config.guest.php';

// 是否关闭图床设置
if ($config['show_admin_inc'] === 0) {
    echo '<h2 class="alert alert-danger">已关闭图床设置~~ <br><small>再次开启请修改<code>/config/config.php</code>文件的代码: <code>\'show_admin_inc\' => 1,</code></small></h2>';
    exit(require_once APP_ROOT  . '/app/footer.php');
}

// 检查登录
if (!is_who_login('admin')) {
    echo '
  <script> new $.zui.Messager("请使用管理员账户登录! ", {
	type: "danger", // 定义颜色主题 
	icon: "exclamation-sign" // 定义消息图标
  }).show();</script>';
    header("refresh:2;url=" . $config['domain'] . "/admin/index.php");
    require_once APP_ROOT . '/app/footer.php';
    exit;
}

// 定义文件位置 
$config_file = APP_ROOT . '/config/config.php'; // config.php
$api_key_file = APP_ROOT . '/config/api_key.php'; // api_key.php
$guest_config_file = APP_ROOT . '/config/config.guest.php'; // config.guest.php

// 修改config配置
if (isset($_POST['update'])) {
    $postArr = $_POST;

    if (isset($postArr['user'])) {
        if ($postArr['user'] == $guestConfig[$postArr['user']])
            echo '
            <script>
            new $.zui.Messager("管理员账号不能与上传者账号相同!", {
                type: "danger", // 定义颜色主题 
                icon: "exclamation-sign" // 定义消息图标
            }).show();
            </script>
            ';
        exit(header("refresh:1;"));
    }

    $new_config = array_replace($config, $postArr);
    cache_write($config_file, $new_config);
    echo '
  <script>
  new $.zui.Messager("保存成功", {
    type: "primary", // 定义颜色主题 
    icon: "ok-sign" // 定义消息图标
  }).show();
  </script>
  ';
    header("refresh:1;");
}

/**
 * 添加token
 * $_POST['add_token'] 生成的Token
 * $_POST['add_token_id'] Token的ID
 * $_POST['add_token_expired'] 过期时间
 */
if (isset($_POST['add_token_id'])) {
    $postArr = array(
        $_POST['add_token'] => array(
            'id' => $_POST['add_token_id'],
            'expired' => $_POST['add_token_expired'] * 86400 + time(),
            'add_time' => time()
        )
    );
    $new_config = array_replace($tokenList, $postArr);
    cache_write($api_key_file, $new_config, 'tokenList');
    echo '
  <script>
  new $.zui.Messager("API Token 添加成功!", {
    type: "primary", // 定义颜色主题 
    icon: "ok-sign" // 定义消息图标
  }).show();
  </script>
  ';
    header("refresh:1;");
}

// 禁用Token 
if (isset($_GET['stop_token'])) {
    $stop_token = $_GET['stop_token'];
    $postArr = array(
        $stop_token => array(
            'id' => 0,
            'expired' => time(),
            'add_time' => $tokenList[$stop_token]['add_time']
        )
    );
    $new_config = array_replace($tokenList, $postArr);
    cache_write($api_key_file, $new_config, 'tokenList');
    echo '
        <script>
        new $.zui.Messager("禁用 API Token 成功!", {
            type: "primary", // 定义颜色主题 
            icon: "ok-sign" // 定义消息图标
        }).show();
        </script>
  ';
    header("refresh:1;url=/admin/admin.inc.php");
}

// 删除Token
if (isset($_GET['delete_token'])) {
    unset($tokenList[$_GET['delete_token']]);
    cache_write($api_key_file, $tokenList, 'tokenList');
    echo '
  <script>
  new $.zui.Messager("删除 API Token 成功!", {
    type: "primary", // 定义颜色主题 
    icon: "ok-sign" // 定义消息图标
  }).show();
  </script>
  ';
    header("refresh:1;url=/admin/admin.inc.php");
}

// 禁用用户
if (isset($_GET['stop_guest'])) {
    $stop_guest = $_GET['stop_guest'];
    $postArr = array(
        $stop_guest => array(
            'password' => $guestConfig[$stop_guest]['password'],
            'expired' => time(),
            'add_time' => $guestConfig[$stop_guest]['add_time']
        )
    );
    $new_config = array_replace($guestConfig, $postArr);
    cache_write($guest_config_file, $new_config, 'guestConfig');
    echo '
        <script>
        new $.zui.Messager("禁用上传用户成功!", {
            type: "primary", // 定义颜色主题 
            icon: "ok-sign" // 定义消息图标
        }).show();
        </script>
  ';
    header("refresh:1;url=/admin/admin.inc.php");
}


// 删除用户
if (isset($_GET['delete_guest'])) {
    unset($guestConfig[$_GET['delete_guest']]);
    cache_write($guest_config_file, $guestConfig, 'guestConfig');
    echo '
  <script>
  new $.zui.Messager("删除上传用户成功!", {
    type: "primary", // 定义颜色主题 
    icon: "ok-sign" // 定义消息图标
  }).show();
  </script>
  ';
    header("refresh:1;url=/admin/admin.inc.php");
}

//  添加管理员修改config.php
if (isset($_POST['admin_form'])) {
    $postArr = $_POST;
    if (isset($guestConfig[$postArr['user']])) {
        echo '
        <script>
            new $.zui.Messager("管理员账号不能与上传者账号相同!", {
                type: "danger", // 定义颜色主题 
                icon: "exclamation-sign" // 定义消息图标
            }).show();
        </script>
        ';
        exit(header("refresh:3;"));
    }
    $postArr = array('user' => $postArr['user'], 'password' => $postArr['password']);

    $new_config = array_replace($config, $postArr);
    cache_write($config_file, $new_config);
    echo '
    <script>
    new $.zui.Messager("保存成功", {
        type: "primary", // 定义颜色主题 
        icon: "ok-sign" // 定义消息图标
    }).show();
    </script>
  ';
    header("refresh:1;");
}

// 添加上传账号 修改config.guest.php
if (isset($_POST['uploader_form'])) {
    // 禁止与管理员登录名相同
    if ($_POST['uploader_user'] == $config['user']) {
        echo '
        <script>
        new $.zui.Messager("上传用户账号不能与管理员账号相同!", {
            type: "danger", // 定义颜色主题 
            icon: "exclamation-sign" // 定义消息图标
        }).show();
        </script>
        ';
        exit(header("refresh:3;"));
    }
    // 写入上传者用户数据
    $postArr = array(
        $_POST['uploader_user'] => array(
            'password' => $_POST['uploader_password'],
            'expired' => $_POST['uploader_time'] * 86400 + time(),
            'add_time' => time()
        )
    );
    $new_config = array_replace($guestConfig, $postArr);
    cache_write($guest_config_file, $new_config, 'guestConfig');
    echo '
    <script>
    new $.zui.Messager("上传用户添加成功!", {
        type: "primary", // 定义颜色主题 
        icon: "ok-sign" // 定义消息图标
    }).show();
    </script>
    ';
    header("refresh:1;");
}

// 删除非空目录
if (isset($_REQUEST['delDir'])) {
    $delDir = APP_ROOT . $config['path'] . $_REQUEST['delDir'];
    if (deldir($delDir)) {
        echo '
		<script> new $.zui.Messager("删除成功! ", {
			type: "success", // 定义颜色主题 
			icon: "ok-sign" // 定义消息图标
		}).show();</script>';
    } else {
        echo '
		<script> new $.zui.Messager("删除失败! ", {
			type: "danger", // 定义颜色主题 
			icon: "exclamation-sign" // 定义消息图标
		}).show();</script>';
    }
    // header("refresh:1;"); // 1s后刷新当前页面
    header("refresh:1;url=/admin/admin.inc.php");
}

auto_delete(); //定时删除
?>
<div class="row">
    <?php if (!empty($config['set_notice'])) echo $config['set_notice']; ?>
    <div class="col-md-2 col-xs-4">
        <ul class="nav nav-tabs nav-stacked">
            <li><a data-tab href="#Content1">网站设置</a></li>
            <li><a data-tab href="#Content9">界面设置</a></li>
            <li><a data-tab href="#Content2">上传设置</a></li>
            <li><a data-tab href="#Content12">水印设置</a></li>
            <li><a data-tab href="#Content15">远程附件</a></li>
            <li><a data-tab href="#Content5">API 设置</a></li>
            <li><a data-tab href="#Content13">上传压缩</a></li>
            <li><a data-tab href="#Content4">压缩图片</a></li>
            <li><a data-tab href="#Content11">图片回收<span class="label label-badge label-success"><?php echo get_file_by_glob(APP_ROOT . $config['path'] . 'recycle', 'number'); ?></span></a></li>
            <li><a data-tab href="#Content7">可疑图片<span class="label label-badge label-success"><?php echo get_file_by_glob(APP_ROOT . $config['path'] . 'suspic', 'number'); ?></span></a></li>
            <li><a data-tab href="#Content3">广告设置</a></li>
            <li><a data-tab href="#Content14">文件管理</a></li>
            <li><a data-tab href="#Content6">图床安全</a></li>
            <li><a data-tab href="#Content10">账号密码</a></li>
            <li><a data-tab href="#Content8">系统信息</a></li>
        </ul>
    </div>
    <div class="tab-content col-md-10 col-xs-8">
        <div class="tab-pane fade" id="Content1">
            <form action="<?php echo $_SERVER['SCRIPT_NAME']; ?>" method="post">
                <div class="form-group">
                    <label>网站域名 | 末尾不加'/'</label>
                    <input type="url" class="form-control" name="domain" required="required" value="<?php echo $config['domain']; ?>" onkeyup="this.value=this.value.replace(/\s/g,'')">
                </div>
                <div class="form-group">
                    <label data-toggle="tooltip" title="支持多个域名并随机选择<br/>* 只有一个域名请与上边一致<br />* 多个域名请以英文 , 分割 <br />* 最后一个域名不要加,">图片域名 | 末尾不加'/'</label>
                    <input type="text" class="form-control" name="imgurl" required="required" value="<?php echo $config['imgurl']; ?>" placeholder="末尾不加/" onkeyup="this.value=this.value.replace(/\s/g,'')" title="网站域名与图片链接域名可以不同,比如A域名上传,可以返回B域名图片链接,A、B需绑定到同一空间下">
                </div>
                <div class="form-group">
                    <label>网站标题</label>
                    <input type="text" class="form-control" name="title" required="required" value="<?php echo $config['title']; ?>" onkeyup="this.value=this.value.trim()">
                </div>
                <div class="form-group">
                    <label>网站关键字</label>
                    <input type="text" class="form-control" name="keywords" required="required" value="<?php echo $config['keywords']; ?>" onkeyup="this.value=this.value.replace(/\s/g,'')">
                </div>
                <div class="form-group">
                    <label>网站描述</label>
                    <textarea class="form-control" rows="2" name="description" required="required" onkeyup="this.value=this.value.replace(/\s/g,'')"><?php echo $config['description']; ?></textarea>
                </div>
                <div class="form-group">
                    <label data-toggle="tooltip" title="不同公告以a标签换行分割">滚动公告 | 支持html</label>
                    <textarea class="form-control" rows="2" name="tips"><?php echo $config['tips']; ?></textarea>
                </div>
                <div class="form-group">
                    <label data-toggle="tooltip" title="需要在图床安全中开启弹窗公告">弹窗公告 | 支持html</label>
                    <textarea class="form-control" rows="2" name="notice" placeholder="弹窗公告会在首次访问网站时弹出,关闭浏览器再次访问弹出"><?php echo $config['notice']; ?></textarea>
                </div>
                <div class="form-group">
                    <label data-toggle="tooltip" title="HTML / 统计代码 / JS / CSS">页首代码 | 需闭合标签</label>
                    <textarea class="form-control" rows="2" name="customize"><?php echo $config['customize']; ?></textarea>
                </div>
                <div class="form-group">
                    <label data-toggle="tooltip" title="HTML / 统计代码 / JS / CSS">页脚代码 | 需闭合标签</label>
                    <textarea class="form-control" rows="2" name="footer"><?php echo $config['footer']; ?></textarea>
                </div>
                <div class="form-group">
                    <label data-toggle="tooltip" title="管理页面顶部提示信息, 可用于一些备注">管理提示 | 支持html</label>
                    <textarea class="form-control" rows="2" name="set_notice" placeholder="管理设置页面的提示信息"><?php echo $config['set_notice']; ?></textarea>
                </div>
                <div class="form-group">
                    <label data-toggle="tooltip" title="服务条款 / 隐私政策 / DMCA">使用条款| 支持HTML</label>
                    <textarea class="form-control" rows="2" name="terms"><?php echo $config['terms']; ?></textarea>
                </div>
                <div class="form-group">
                    <input type="hidden" class="form-control" name="update" value="<?php echo date("Y-m-d H:i:s"); ?>" placeholder="隐藏的保存">
                </div>
                <button type="submit" class="btn btn-primary">保存</button>
            </form>
        </div>
        <div class="tab-pane fade" id="Content2">
            <form action="<?php echo $_SERVER['SCRIPT_NAME']; ?>" method="post">
                <div class="form-group col-md-12">
                    <div class="form-group col-md-1">
                        <label data-toggle="tooltip" title="前后需加'/'">存储目录</label>
                        <input type="text" class="form-control" name="path" required="required" value="<?php echo $config['path']; ?>" onkeyup="this.value=this.value.replace(/\s/g,'')" title="可根据Apache/Nginx配置安全,参考: https://blog.png.cm/981.html 或 README.md">
                    </div>
                    <div class="form-group col-md-2">
                        <label for="storage_path" data-toggle="tooltip" title="图片的分类目录<br>更改会导致广场无法使用<br>非必要不要修改,末尾需要加'/' <br />PHP date() 函数参考">存储分类路径 <a href="https://www.runoob.com/php/php-date.html" target="_blank"><i class="icon icon-external-link"></i></a></label>
                        <input type="text" class="form-control" name="storage_path" required="required" value="<?php echo $config['storage_path']; ?>" onkeyup="this.value=this.value.replace(/\s/g,'')" title="参考PHP date() 函数">
                    </div>
                    <!-- <div class="form-group">
                    <label data-toggle="tooltip" title="不懂就不要改本图床仅针对图片上传,如果想上传其他类型文件请更改此出,不同mime请以英文,分割">允许的MIME类型</label>
                    <input type="text" class="form-control" name="mime" required="required" value="php echo $config['mime'];" onkeyup="this.value=this.value.replace(/\s/g,'')">
                    </div> -->
                    <div class="form-group col-md-3">
                        <label data-toggle="tooltip" title="请以英文 , 分割 最后一个不加 , <br/>非图片格式需在图床安全中关闭图床模式">允许的扩展名 | 非图片格式?</label>
                        <input type="text" class="form-control" name="extensions" required="required" value="<?php echo $config['extensions']; ?>" onkeyup="this.value=this.value.replace(/\s/g,'')">
                    </div>
                    <div class="form-group col-md-2">
                        <label>上传文件的命名方式</label>
                        <select class="chosen-select form-control" name="imgName">
                            <option value="default" <?php if ($config['imgName'] == 'default') echo 'selected'; ?>>默认 - 36进制时间+随机数 >> vx77yu</option>
                            <option value="date" <?php if ($config['imgName'] == 'date') echo 'selected'; ?>>时间 >> 192704</option>
                            <option value="unix" <?php if ($config['imgName'] == 'unix') echo 'selected'; ?>>Unix >> 1635074840</option>
                            <option value="crc32" <?php if ($config['imgName'] == 'crc32') echo 'selected'; ?>>CRC32 >> 2495551279</option>
                            <option value="uniqid" <?php if ($config['imgName'] == 'uniqid') echo 'selected'; ?>>微秒 >> 6175436c73418</option>
                            <option value="snowflake" <?php if ($config['imgName'] == 'snowflake') echo 'selected'; ?>>雪花 >> 5357520647037653166</option>
                            <option value="source" <?php if ($config['imgName'] == 'source') echo 'selected'; ?>>源名 >> 微信图片_20211228214754</option>
                            <option value="md5" <?php if ($config['imgName'] == 'md5') echo 'selected'; ?>>MD5 >> 3888aa69eb321a2b61fcc63520bf6c82</option>
                            <option value="sha1" <?php if ($config['imgName'] == 'sha1') echo 'selected'; ?>>SHA1 >> 654faac01499e0cb5fb0e9d78b21e234c63d842a</option>
                            <option value="uuid" <?php if ($config['imgName'] == 'uuid') echo 'selected'; ?>>通用唯一识别码 >> 668ab647-c874-51e8-cc98-ac5c24a472b0</option>
                            <option value="guid" <?php if ($config['imgName'] == 'guid') echo 'selected'; ?>>全局唯一标识符 >> 6EDAD0CC-AB0C-4F61-BCCA-05FAD65BF0FA</option>
                        </select>
                    </div>
                    <div class="form-group col-md-2">
                        <label data-toggle="tooltip" title="支持图片转换格式后压缩,压缩率与 上传压缩->后端压缩率关联<br />不建议同时启用后端压缩,避免重复压缩导致图片变大"> * 将上传图片转换格式</label>
                        <select class="chosen-select form-control" name="imgConvert">
                            <option value="" <?php if (empty($config['imgConvert'])) echo 'selected'; ?>>不转换</option>
                            <option value="webp" <?php if ($config['imgConvert'] == 'webp') echo 'selected'; ?>>WEBP</option>
                            <option value="png" <?php if ($config['imgConvert'] == 'png') echo 'selected'; ?>>PNG</option>
                            <option value="jpeg" <?php if ($config['imgConvert'] == 'jpeg') echo 'selected'; ?>>JPG</option>
                            <option value="gif" <?php if ($config['imgConvert'] == 'gif') echo 'selected'; ?>>GIF</option>
                            <option value="bmp" <?php if ($config['imgConvert'] == 'bmp') echo 'selected'; ?>>BMP</option>
                        </select>
                    </div>
                    <div class="form-group col-md-2">
                        <label for="chunks" data-toggle="tooltip" title="开启分片后粘贴上传失效<br/>可能使部分文件上传失败<br/>建议最小1Mb(1048576字节)">分片上传 | 设置<code>0</code>关闭</label>
                        <div class="input-group">
                            <input type="number" min="0" class="form-control" id="chunks" name="chunks" required="required" value="<?php echo !empty($config['chunks']) ? $config['chunks'] : 0; ?>" onkeyup="this.value=this.value.replace(/\s/g,'')">
                            <span class="input-group-addon">byte</span>
                        </div>
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="form-group col-md-6">
                        <label>单次最多上传 | 当前: </label><label id="maxUploadFiles"><?php echo $config['maxUploadFiles']; ?></label><label>张</label>
                        <input type="range" class="form-control" name="maxUploadFiles" value="<?php echo $config['maxUploadFiles']; ?>" min="1" max="200" step="1" onchange="document.getElementById('maxUploadFiles').innerHTML=value">
                    </div>
                    <div class="form-group col-md-6">
                        <label>最大上传宽度 | 当前: </label><label id="maxWidth"><?php echo $config['maxWidth']; ?></label><label>px</label>
                        <input type="range" class="form-control" name="maxWidth" value="<?php echo $config['maxWidth']; ?>" min="1024" max="51200" step="1024" onchange="document.getElementById('maxWidth').innerHTML=value">
                    </div>
                    <div class="form-group col-md-6">
                        <label>最大上传高度 | 当前: </label><label id="maxHeight"><?php echo $config['maxHeight']; ?></label><label>px</label>
                        <input type="range" class="form-control" name="maxHeight" value="<?php echo $config['maxHeight']; ?>" min="1024" max="51200" step="1024" onchange="document.getElementById('maxHeight').innerHTML=value">
                    </div>
                    <div class="form-group col-md-6">
                        <label>单文件最大上传(1-100MB) | 当前: </label><label id="maxSize"><?php echo $config['maxSize'] / 1024 / 1024; ?></label><label>MB</label>
                        <input type="range" class="form-control" name="maxSize" value="<?php echo $config['maxSize']; ?>" min="1048576" max="104857600" step="1048576" onchange="document.getElementById('maxSize').innerHTML=value/1024/1024">
                    </div>
                    <div class="form-group col-md-6">
                        <label>最小上传宽度 | 当前: </label><label id="minWidth"><?php echo $config['minWidth']; ?></label><label>px</label>
                        <input type="range" class="form-control" name="minWidth" value="<?php echo $config['minWidth']; ?>" min="5" max="1024" step="10" onchange="document.getElementById('minWidth').innerHTML=value">
                    </div>
                    <div class="form-group col-md-6">
                        <label>最小上传高度 | 当前: </label><label id="minHeight"><?php echo $config['minHeight']; ?></label><label>px</label>
                        <input type="range" class="form-control" name="minHeight" value="<?php echo $config['minHeight']; ?>" min="5" max="1024" step="10" onchange="document.getElementById('minHeight').innerHTML=value">
                    </div>
                    <div class="form-group col-md-6">
                        <input type="hidden" class="form-control" name="update" value="<?php echo date("Y-m-d H:i:s"); ?>" placeholder="隐藏的保存">
                    </div>
                </div>
                <button type="submit" class="btn btn-primary">保存</button>
            </form>
        </div>
        <div class="tab-pane fade" id="Content3">
            <form action="<?php echo $_SERVER['SCRIPT_NAME']; ?>" method="post">
                <div class="form-group">
                    <div class="switch switch-inline">
                        <input type="hidden" name="ad_top" value="0">
                        <input type="checkbox" name="ad_top" value="1" <?php if ($config['ad_top']) echo 'checked="checked"'; ?>>
                        <label style="font-weight: bold">顶部广告</label>
                    </div>
                </div>
                <div class="form-group">
                    <label>顶部广告内容 | 仅支持html代码</label>
                    <textarea class="form-control" rows="8" name="ad_top_info"><?php echo $config['ad_top_info']; ?></textarea>
                </div>
                <div class="form-group">
                    <div class="switch switch-inline">
                        <input type="hidden" name="ad_bot" value="0">
                        <input type="checkbox" name="ad_bot" value="1" <?php if ($config['ad_bot']) echo 'checked="checked"'; ?>>
                        <label style="font-weight: bold">底部广告</label>
                    </div>
                </div>
                <div class="form-group">
                    <label>底部广告内容 | 仅支持html代码</label>
                    <textarea class="form-control" rows="8" name="ad_bot_info"><?php echo $config['ad_bot_info']; ?></textarea>
                </div>
                <div class="form-group">
                    <input type="hidden" class="form-control" name="update" value="<?php echo date("Y-m-d H:i:s"); ?>" placeholder="隐藏的保存">
                </div>
                <button type="submit" class="btn btn-primary">保存</button>
            </form>
        </div>
        <div class="tab-pane fade " id="Content4">
            <form action="../app/compressing.php" method="post" target="_blank">
                <h5 class="header-dividing">压缩文件夹</h5>
                <div class="col-md-12">
                    <div class="form-group col-md-4">
                        <input type="text" class="form-control form-date" placeholder="" name="folder" value="<?php echo date('Y/m/d/'); ?>" readonly="">
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="radio-primary col-md-2">
                        <input type="radio" name="type" value="TinyPng" id="TinyPng"><label for="TinyPng" data-toggle="tooltip" title="需要申请key,填入API设置的TinyPng Key中"> 使用TinyPng</label>
                    </div>
                    <div class="radio-primary col-md-2">
                        <input type="radio" name="type" value="Imgcompress" id="Imgcompress" checked="checked"><label for="Imgcompress" data-toggle="tooltip" title="压缩效率受后端压缩图片压缩率控制"> 使用本地PHP</label>
                    </div>
                </div>
                <div class="col-md-12">
                    <label>* 已开启上传压缩的不需重复压缩! </label><br />
                    <label>* 如果页面长时间没有响应,表示正面正在压缩! </label><br />
                    <label>* 两种压缩均为不可逆,并且非常占用硬件资源. </label><br />
                    <button type="submit" class="btn btn-mini btn-success">开始压缩</button>
                </div>
            </form>
        </div>
        <div class="tab-pane fade" id="Content5">
            <h5 class="header-dividing">外部KEY</h5>
            <form class="form-condensed" action="<?php echo $_SERVER['SCRIPT_NAME']; ?>" method="post" style="margin-bottom: 10px;">
                <div class="form-group col-md-4">
                    <label for="TinyPng" data-toggle="tooltip" title="TinyPng 申请网址">TinyPng Key <a href="https://tinypng.com/developers" target="_blank"><i class="icon icon-external-link"></i></a></label>
                    <input type="text" class="form-control" id="TinyPng" name="TinyPng_key" value="<?php echo $config['TinyPng_key']; ?>" placeholder="填入压缩图片Key" data-toggle="tooltip" title="开启后会受服务器到https://tinypng.com 速度影响,国内不建议开启!" onkeyup="this.value=this.value.replace(/\s/g,'')">
                    <input type="hidden" class="form-control" name="update" value="<?php echo date("Y-m-d H:i:s"); ?>" placeholder="隐藏的保存">
                </div>
                <div class="form-group col-md-4">
                    <label for="moderatecontent_key" data-toggle="tooltip" title="moderatecontent 申请网址">Moderate Key <a href="https://client.moderatecontent.com" target="_blank"><i class="icon icon-external-link"></i></a></label>
                    <input type="text" class="form-control" name="moderatecontent_key" id="moderatecontent_key" value="<?php echo $config['moderatecontent_key']; ?>" placeholder="填入图片鉴黄Key" data-toggle="tooltip" title="开启后会受服务器到https://moderatecontent.com 速度影响,国内不建议开启! " onkeyup="this.value=this.value.replace(/\s/g,'')">
                </div>
                <div class="form-group col-md-4">
                    <label for="nsfwjs_url" data-toggle="tooltip" title="nsfwjs 开源地址">nsfwjs url <a href="https://github.com/infinitered/nsfwjs" target="_blank"><i class="icon icon-external-link"></i></a></label>
                    <input type="url" class="form-control" name="nsfwjs_url" id="nsfwjs_url" value="<?php echo $config['nsfwjs_url']; ?>" placeholder="http://ip:3307/nsfw?url=" data-toggle="tooltip" title="自行搭建nsfwjs服务的网站地址" onkeyup="this.value=this.value.replace(/\s/g,'')">
                </div>
                <input type="hidden" class="form-control" name="update" value="<?php echo date("Y-m-d H:i:s"); ?>" placeholder="隐藏的保存">
                <button type="submit" class="btn btn-primary">保存KEY</button>
            </form>
            <h5 class="page-header">Token API 管理: <?php if (!$config['token_path_status']) echo '<small>* 部分按钮需开启Token分离才能激活, 删除后不可恢复</small>'; ?></h5>
            <label class="text-primary">当前Token列表 <small><code>调用地址: <?php echo $config['domain']; ?>/api/index.php</code></small></label>
            <div id="myDataGrid" class="datagrid table-bordered">
                <div class="input-control search-box search-box-circle has-icon-left has-icon-right" id="searchboxExample2" style="margin-bottom: 10px;">
                    <input id="inputSearchExample2" type="search" class="form-control search-input" placeholder="搜索Token">
                    <label for="inputSearchExample2" class="input-control-icon-left search-icon"><i class="icon icon-search"></i></label>
                    <a href="#" class="input-control-icon-right search-clear-btn"><i class="icon icon-remove"></i></a>
                </div>
                <div class="datagrid-container"></div>
            </div>
            <div class="col-md-12">
                <h5 class="page-header">增加Token</h5>
                <div class="col-md-9">
                    <form class="form-inline" action="<?php echo $_SERVER['SCRIPT_NAME']; ?>" method="post" style="margin-top: 10px;">
                        <div class="form-group">
                            <label for="add_modify_token" data-toggle="tooltip" title="当前的Token是实时生成的,如果需要修改只需要复制已存在的Token并修改有效期即可!">增加/修改Token: </label>
                            <input type="text" class="form-control" id="add_modify_token" name="add_token" value="<?php echo privateToken(); ?>">
                        </div>
                        <div class="form-group">
                            <label for="add_modify_token_time" data-toggle="tooltip" title="正整数或负整数<br/>正整数设置有效期<br/>负整数(-1)设置过期">有效期 (天): </label>
                            <input type="number" class="form-control" id="add_modify_token_time" name="add_token_expired" min="-1" value="30" required="required">
                        </div>
                        <input type="hidden" class="form-control" name="add_token_id" value="<?php echo count($tokenList); ?>" placeholder="隐藏的保存">
                        <button type="submit" class="btn btn-primary">添加</button>
                    </form>
                </div>
                <div class="col-md-3">
                    <form class="form-inline" action="<?php echo $_SERVER['SCRIPT_NAME']; ?>" method="post" style="margin-top: 10px;">
                        <div class="switch switch-inline" data-toggle="tooltip" title="上传的图片名称后缀添加Token ID" data-original-title="上传的图片名称后缀添加Token ID">
                            <input type="hidden" name="token_suffix_ID" value="0">
                            <input type="checkbox" name="token_suffix_ID" value="1" <?php if ($config['token_suffix_ID']) echo 'checked="checked"'; ?>>
                            <label style="font-weight: bold">suffix ID</label>
                        </div>
                        <input type="hidden" class="form-control" name="update" value="<?php echo date("Y-m-d H:i:s"); ?>" placeholder="隐藏的保存">
                        <button type="submit" class="btn btn-primary">保存</button>
                    </form>
                </div>
            </div>
        </div>
        <div class="tab-pane fade" id="Content6">
            <div class="col-md-12">
                <div class="col-md-4">
                    <h5 class="header-dividing">上传日志 <small>需要开启上传日志</small></h5>
                    <form class="form-inline" action="../app/viewlog.php" method="post" target="_blank">
                        <div class="form-group">
                            <label for="logDate" class="text-primary">月份: </label>
                            <input type="text" class="form-control logDate" id="logDate" name="logDate" value="<?php echo date('Y-m'); ?>" required="required" readonly>
                            <input type="hidden" class="form-control" name="sign" value="<?php echo md5($config['password'] . date('ymdh')); ?>" placeholder="日志访问秘钥">
                        </div>
                        <button type="submit" class="btn btn-primary">查看</button>
                    </form>
                </div>
                <div class="col-md-3">
                    <h5 class="header-dividing" data-toggle="tooltip" title="仅限存储分类路径为 Y/m/d/ 格式<br/>且每天需要访问一次后台才执行<br/>先重命名要删除文件夹作为备份<br/>超过定时日期的2倍时间后再删除重命名的文件夹<br/>超过定时日期前和开启分离的文件夹不删除">定时删除 <small>设置<code>0</code>关闭</small></h5>
                    <form action="<?php echo $_SERVER['SCRIPT_NAME']; ?>" method="post">
                        <div class="input-group">
                            <span class="input-group-addon">天数</span>
                            <input type="number" name="auto_delete" class="form-control" min="0" placeholder="0" value="<?php echo $config['auto_delete']; ?>" required="required">
                            <span class="input-group-btn"><button class="btn btn-primary">设置</button></span>
                        </div>
                        <input type="hidden" class="form-control" name="update" value="<?php echo date("Y-m-d H:i:s"); ?>" placeholder="隐藏的保存">
                    </form>
                </div>
                <div class="col-md-3">
                    <h5 class="header-dividing">缩略图缓存</h5>
                    <button type="button" class="btn btn-primary" onclick="ajax_post('cache/','delDir')"><i class="icon icon-trash"> <small><?php echo getFileNumber(APP_ROOT . $config['path'] . 'cache/') . ' | ' . getDistUsed(getDirectorySize(APP_ROOT . $config['path'] . 'cache/')); ?></small></i></button>
                </div>
                <div class="col-md-1">
                    <h5 class="header-dividing" data-toggle="tooltip" title="如果开启OPcache 点击重置缓存">OPcache</h5>
                    <button type="button" class="btn btn-primary" onclick="ajax_post('OPcache','OPcache')">重置</button>
                </div>
                <div class="col-md-1">
                    <h5 class="header-dividing" data-toggle="tooltip" title="仅显示当月">登录日志</h5>
                    <button type="button" class="btn btn-primary" data-toggle="modal" data-title="登录日志 - 仅显示当月" data-icon="book" data-moveable="true" data-width="60%" data-type="ajax" data-url="../app/viewlog.php?login_log&sign=<?php echo md5($config['password'] . date('ymdh')); ?>">查看</button>
                </div>
            </div>
            <div class="col-md-12">
                <h5 class="header-dividing"></h5>
                <form action="<?php echo $_SERVER['SCRIPT_NAME']; ?>" method="post">
                    <div class="form-group col-md-12">
                        <div class="form-group col-md-6 col-xs-12">
                            <label data-toggle="tooltip" title="请选择合适的缩略图生成方式">缩略图生成方式</label>
                            <div class="input-group" data-toggle="tooltip" title="原图 | 直接输出上传链接,会导致流量增加<br/>TimThumb (推荐) | 优点: 带缓存周期 | 缺点: cdn无法缓存<br/>上传生成 | 优点: 缩略图直链,缓存不失效 | 低配vps负载较大,影响前端上传速度">
                                <span class="input-group-addon">生成模式</span>
                                <select class="form-control" name="thumbnail">
                                    <option value="0" <?php if ($config['thumbnail'] == 0) echo 'selected'; ?>>原图</option>
                                    <option value="2" <?php if ($config['thumbnail'] == 2) echo 'selected'; ?>>上传生成 | 直链</option>
                                    <option value="1" <?php if ($config['thumbnail'] == 1) echo 'selected'; ?>>TimThumb | 推荐</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group col-md-6 col-xs-12">
                            <label data-toggle="tooltip" title="已生成的缩略图不会失效 需清理缓存">缩略图生成大小</label>
                            <div class="input-group">
                                <span class="input-group-addon">宽</span>
                                <input type="number" name="thumbnail_w" class="form-control" min="5" placeholder="258" value="<?php echo $config['thumbnail_w']; ?>" required="required">
                                <span class="input-group-addon">px</span>
                                <span class="input-group-addon">高</span>
                                <input type="number" name="thumbnail_h" class="form-control" min="5" placeholder="258" value="<?php echo $config['thumbnail_h']; ?>" required="required">
                                <span class="input-group-addon">px</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="col-md-6">
                            <div class="form-group">
                                <div class="switch switch-inline">
                                    <input type="hidden" name="static_cdn" value="0">
                                    <input type="checkbox" name="static_cdn" value="1" <?php if ($config['static_cdn']) echo 'checked="checked"'; ?>>
                                    <label style="font-weight: bold">静态文件CDN地址 | 末尾不加'/'</label>
                                </div>
                                <input type="url" class="form-control" name="static_cdn_url" value="<?php echo $config['static_cdn_url']; ?>" onkeyup="this.value=this.value.replace(/\s/g,'')" data-toggle="tooltip" title="jsdelivr可在后边添加版本号 例:@2.5.6">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="report" data-toggle="tooltip" title="举报地址支持Zoho表单、金数据、表单大师等<br/>(推荐ZOHO)留空则不显示">举报地址 <a href="https://store.zoho.com.cn/referral.do?servicename=ZohoForms&category=ZohoForms&ref=52f8a4e98a7a7d4c2475713784605af0dc842f6cc9732dd77f37b87f2959149e212e550f50a869f70360f15b80a4abc6" target="_blank"><i class="icon icon-external-link"></i></a></label>
                                <input type="text" class="form-control" id="report" name="report" value="<?php echo $config['report']; ?>" placeholder="可以是网址或邮箱地址" onkeyup="this.value=this.value.replace(/\s/g,'')">
                            </div>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="col-md-6">
                            <div class="form-group">
                                <div class="switch switch-inline">
                                    <input type="hidden" name="check_ip" value="0">
                                    <input type="checkbox" name="check_ip" value="1" <?php if ($config['check_ip']) echo 'checked="checked"'; ?>>
                                    <label style="font-weight: bold">黑/白IP名单上传</label>
                                </div>
                                <textarea class="form-control" rows="4" name="check_ip_list" data-toggle="tooltip" title="每个IP以英文,结尾 支持IP段 例:123.23.23.44,193.134.*.*" placeholder="每个IP以英文,结尾 支持IP段 例:192.168.1.13,123.23.23.44,193.134.*.*" onkeyup="this.value=this.value.replace(/\s/g,'')"><?php echo $config['check_ip_list']; ?></textarea>
                                <label class="radio-inline"><input type="radio" name="check_ip_model" value="0" <?php if ($config['check_ip_model'] == 0) echo 'checked'; ?>> 黑名单模式</label>
                                <label class="radio-inline"><input type="radio" name="check_ip_model" value="1" <?php if ($config['check_ip_model'] == 1) echo 'checked'; ?>> 白名单模式</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <div class="switch switch-inline">
                                    <input type="hidden" name="md5_black" value="0">
                                    <input type="checkbox" name="md5_black" value="1" <?php if ($config['md5_black']) echo 'checked="checked"'; ?>>
                                    <label style="font-weight: bold">通过文件MD5禁止上传</label>
                                </div>
                                <textarea class="form-control" rows="4" name="md5_blacklist" data-toggle="tooltip" title="上传前后文件md5均可匹配<br/>不区分大小写, 直接输入即可匹配" placeholder="上传前后文件md5均可匹配, 不区分大小写, 直接输入即可匹配" onkeyup="this.value=this.value.replace(/\s/g,'')"><?php echo $config['md5_blacklist']; ?></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <h5 class="header-dividing">高级设置 <?php if ($config['domain'] == $config['imgurl']) echo '<small> 网站域名与图片域名相同,锁定隐藏' . $config['path'] . '目录开关</small>'; ?></h5>
                        <div class="col-md-2">
                            <div class="switch switch-inline" data-toggle="tooltip" title="开启仅登陆上传">
                                <input type="hidden" name="mustLogin" value="0">
                                <input type="checkbox" name="mustLogin" value="1" <?php if ($config['mustLogin']) echo 'checked="checked"'; ?>>
                                <label style="font-weight: bold">登录上传</label>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="switch switch-inline" data-toggle="tooltip" title="登录验证码建议开启,有效防止因撞库导致账户密码被破解!">
                                <input type="hidden" name="captcha" value="0">
                                <input type="checkbox" name="captcha" value="1" <?php if ($config['captcha']) echo 'checked'; ?>>
                                <label style="font-weight: bold">验证码</label>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="switch switch-inline" data-toggle="tooltip" title="开启API上传接口">
                                <input type="hidden" name="apiStatus" value="0">
                                <input type="checkbox" name="apiStatus" value="1" <?php if ($config['apiStatus']) echo 'checked="checked"'; ?>>
                                <label style="font-weight: bold">API 上传</label>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="switch switch-inline" data-toggle="tooltip" title="以上传用户的Token ID创建目录">
                                <input type="hidden" name="token_path_status" value="0">
                                <input type="checkbox" name="token_path_status" value="1" <?php if ($config['token_path_status']) echo 'checked="checked"'; ?>>
                                <label style="font-weight: bold">API 分离</label>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="switch switch-inline" data-toggle="tooltip" title="以登陆账号名称创建上传目录">
                                <input type="hidden" name="guest_path_status" value="0">
                                <input type="checkbox" name="guest_path_status" value="1" <?php if ($config['guest_path_status']) echo 'checked="checked"'; ?>>
                                <label style="font-weight: bold">用户分离</label>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="switch switch-inline" data-toggle="tooltip" title="管理员独立上传目录<br />* 自定义目录暂未提供接口,如需修改请修改config.php中的admin_path">
                                <input type="hidden" name="admin_path_status" value="0">
                                <input type="checkbox" name="admin_path_status" value="1" <?php if ($config['admin_path_status']) echo 'checked="checked"'; ?>>
                                <label style="font-weight: bold">管理分离</label>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="switch switch-inline" data-toggle="tooltip" title="* 如果不懂就关闭<br /> * 1. 图片链接会隐藏<?php echo $config['path']; ?>目录<br />* 2. 网站与图片域名不能相同<br />* 3. 图片域名需绑定到<?php echo $config['path']; ?>目录">
                                <input type="hidden" name="hide_path" value="0">
                                <input type="checkbox" name="hide_path" value="1" <?php if ($config['hide_path']) echo 'checked="checked"'; ?> <?php if ($config['domain'] == $config['imgurl']) echo 'disabled'; ?>>
                                <label style="font-weight: bold">隐藏<?php echo $config['path']; ?>目录</label>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="switch switch-inline" data-toggle="tooltip" title="隐藏图片直链 | * 注意: key值与账号密码->源图保护Key绑定,更改源图保护Key后所有链接失效">
                                <input type="hidden" name="hide" value="0">
                                <input type="checkbox" name="hide" value="1" <?php if ($config['hide']) echo 'checked="checked"'; ?>>
                                <label style="font-weight: bold">源图保护</label>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="switch switch-inline" data-toggle="tooltip" title="网址设置->弹窗公告修改内容<br />重开浏览器访问网站会再次展示公告弹窗">
                                <input type="hidden" name="notice_status" value="0">
                                <input type="checkbox" name="notice_status" value="1" <?php if ($config['notice_status']) echo 'checked="checked"'; ?>>
                                <label style="font-weight: bold">显示公告</label>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="switch switch-inline" data-toggle="tooltip" title="上传后显示删除链接<br/>删除链接是经过加密的">
                                <input type="hidden" name="show_user_hash_del" value="0">
                                <input type="checkbox" name="show_user_hash_del" value="1" <?php if ($config['show_user_hash_del']) echo 'checked="checked"'; ?>>
                                <label style="font-weight: bold">显示删除</label>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="switch switch-inline" data-toggle="tooltip" title="图片过多时可能会影响统计时间">
                                <input type="hidden" name="chart_on" value="0">
                                <input type="checkbox" name="chart_on" value="1" <?php if ($config['chart_on']) echo 'checked="checked"'; ?>>
                                <label style="font-weight: bold">显示统计</label>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="switch switch-inline" data-toggle="tooltip" title="关闭后将不能进入当前设置<br/>再次开启需修改config.php: <code>'show_admin_inc'=>1,</code>">
                                <input type="hidden" name="show_admin_inc" value="0">
                                <input type="checkbox" name="show_admin_inc" value="1" <?php if ($config['show_admin_inc']) echo 'checked="checked"'; ?>>
                                <label style="font-weight: bold">开启设置</label>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="switch switch-inline" data-toggle="tooltip" title="广场图片以上传时间倒序 | 正序">
                                <input type="hidden" name="showSort" value="0">
                                <input type="checkbox" name="showSort" value="1" <?php if ($config['showSort']) echo 'checked="checked"'; ?>>
                                <label style="font-weight: bold">广场排序</label>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="switch switch-inline" data-toggle="tooltip" title="使用加密链接删除的文件移动至图片回收">
                                <input type="hidden" name="image_recycl" value="0">
                                <input type="checkbox" name="image_recycl" value="1" <?php if ($config['image_recycl']) echo 'checked="checked"'; ?>>
                                <label style="font-weight: bold">文件回收</label>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <!-- <input type="text" name="admin_path" class="form-control input-sm" value="echo $config['admin_path']" placeholder="请自定义管理的上传目录"> -->
                            <div class="switch switch-inline" data-toggle="tooltip" title="开启文件管理">
                                <input type="hidden" name="file_manage" value="0">
                                <input type="checkbox" name="file_manage" value="1" <?php if ($config['file_manage']) echo 'checked="checked"'; ?>>
                                <label style="font-weight: bold">文件管理</label>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="switch switch-inline" data-toggle="tooltip" title="危险：关闭后除图片外不验证文件是否正常，代表可以上传任意指定格式!">
                                <input type="hidden" name="allowed" value="0">
                                <input type="checkbox" name="allowed" value="1" <?php if ($config['allowed']) echo 'checked="checked"'; ?>>
                                <label style="font-weight: bold">图床模式</label>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="switch switch-inline" data-toggle="tooltip" title="上传日志每月保存一个文件<br/>经测试二十万条数据并不影响速度!">
                                <input type="hidden" name="upload_logs" value="0">
                                <input type="checkbox" name="upload_logs" value="1" <?php if ($config['upload_logs']) echo 'checked="checked"'; ?>>
                                <label style="font-weight: bold">上传日志</label>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="switch switch-inline" data-toggle="tooltip" title="检测 PHP扩展 | 安全设置 | 鉴黄 | 版本 | 文件路径 | DEBUG">
                                <input type="hidden" name="checkEnv" value="0">
                                <input type="checkbox" name="checkEnv" value="1" <?php if ($config['checkEnv']) echo 'checked="checked"'; ?>>
                                <label style="font-weight: bold">环境自检</label>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="panel">
                            <div class="panel-body">
                                <div class="form-group">
                                    <div class="switch switch-inline" data-toggle="tooltip" title="通过指定参数查询图床的开放数据 | 与缓存周期同步 | 使用方法见使用手册->公共查询">
                                        <input type="hidden" name="public" value="0">
                                        <input type="checkbox" name="public" value="1" <?php if ($config['public']) echo 'checked'; ?>>
                                        <label style="font-weight: bold">开放数据 <i class="icon icon-hand-down"></i></label>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="checkbox-inline" data-toggle="tooltip" title="<?php echo $config['domain']; ?>/api/public.php?show=time">
                                        <input type="checkbox" name="public_list[]" value="time" id="time" <?php if (in_array('time', $config['public_list']))  echo 'checked'; ?>><label for="time">统计时间</label>
                                    </label>
                                    <label class="checkbox-inline" data-toggle="tooltip" title="开启分离后仅统计游客上传<br />public.php?show=today">
                                        <input type="checkbox" name="public_list[]" value="today" id="today" <?php if (in_array('today', $config['public_list']))  echo 'checked'; ?>><label for="today">今日</label>
                                    </label>
                                    <label class="checkbox-inline" data-toggle="tooltip" title="开启分离后仅统计游客上传<br />public.php?show=yesterday">
                                        <input type="checkbox" name="public_list[]" value="yesterday" id="yesterday" <?php if (in_array('yesterday', $config['public_list']))  echo 'checked'; ?>><label for="yesterday">昨日</label>
                                    </label>
                                    <label class="checkbox-inline" data-toggle="tooltip" title="public.php?show=total_space">
                                        <input type="checkbox" name="public_list[]" value="total_space" id="total_space" <?php if (in_array('total_space', $config['public_list']))  echo 'checked'; ?>><label for="total_space">总空间</label>
                                    </label>
                                    <label class="checkbox-inline" data-toggle="tooltip" title="public.php?show=used_space">
                                        <input type="checkbox" name="public_list[]" value="used_space" id="used_space" <?php if (in_array('used_space', $config['public_list']))  echo 'checked'; ?>><label for="used_space">已用</label>
                                    </label>
                                    <label class="checkbox-inline" data-toggle="tooltip" title="public.php?show=free_space">
                                        <input type="checkbox" name="public_list[]" value="free_space" id="free_space" <?php if (in_array('free_space', $config['public_list']))  echo 'checked'; ?>><label for="free_space">剩余</label>
                                    </label>
                                    <label class="checkbox-inline" data-toggle="tooltip" title="public.php?show=image_used">
                                        <input type="checkbox" name="public_list[]" value="image_used" id="image_used" <?php if (in_array('image_used', $config['public_list']))  echo 'checked'; ?>><label for="image_used">图片占用</label>
                                    </label>
                                    <label class="checkbox-inline" data-toggle="tooltip" title="public.php?show=file">
                                        <input type="checkbox" name="public_list[]" value="file" id="file" <?php if (in_array('file', $config['public_list']))  echo 'checked'; ?>><label for="file">文件数量</label>
                                    </label>
                                    <label class="checkbox-inline" data-toggle="tooltip" title="public.php?show=dir">
                                        <input type="checkbox" name="public_list[]" value="dir" id="dir" <?php if (in_array('dir', $config['public_list']))  echo 'checked'; ?>><label for="dir">文件夹数量</label>
                                    </label>
                                    <label class="checkbox-inline" data-toggle="tooltip" title="开启分离后仅统计游客上传<br />public.php?show=month">
                                        <input type="checkbox" name="public_list[]" value="month" id="month" <?php if (in_array('month', $config['public_list']))  echo 'checked'; ?>><label for="month">最近30日上传</label>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>缓存周期 | 当前: </label>
                                <label id="cache_freq"><?php echo $config['cache_freq']; ?></label><label>小时</label>
                                <input type="range" class="form-control" name="cache_freq" value="<?php echo $config['cache_freq']; ?>" min="1" step="1" max="24" onchange="document.getElementById('cache_freq').innerHTML=value">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>图片违规判断率 | 当前: </label>
                                <label id="checkImg_value"><?php echo $config['checkImg_value']; ?></label><label>%</label>
                                <input type="range" class="form-control" name="checkImg_value" value="<?php echo $config['checkImg_value']; ?>" min="1" max="100" step="1" onchange="document.getElementById('checkImg_value').innerHTML=value">
                            </div>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="col-md-4">
                            <div class="form-group">
                                <div class="input-group">
                                    <span class="input-group-addon" data-toggle="tooltip" title="点击查看所支持的时区列表">自定义时区 <a href="https://www.php.net/manual/zh/timezones.php" target="_blank"><i class="icon icon-external-link"></i></a>
                                    </span>
                                    <input class="form-control" type="text" name="timezone" value="<?php echo isset($config['timezone']) ? $config['timezone'] : 'Asia/Shanghai'; ?>" data-toggle="tooltip" title="默认时区(中国·上海) Asia/Shanghai" required="required" onkeyup="this.value=this.value.replace(/\s/g,'')">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <div class="input-group" data-toggle="tooltip" title="请输入限制数量 | 0 为不限制<br /> 开启监黄,水印等受网络波动和机器性能,执行完毕前限制不生效!">
                                    <span class="input-group-addon">游客上传限制</span>
                                    <input class="form-control" type="number" name="ip_upload_counts" min="0" value="<?php echo $config['ip_upload_counts']; ?>" required="required">
                                    <span class="input-group-addon">张</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <div class="input-group" data-toggle="tooltip" title="使用nsfwjs方式需要自行搭建或使用开源接口 据说准确率能达到93%">
                                    <span class="input-group-addon">图片鉴黄</span>
                                    <select class="form-control" name="checkImg">
                                        <option value="0" <?php if ($config['checkImg'] == 0) echo 'selected'; ?>>关闭</option>
                                        <option value="1" <?php if ($config['checkImg'] == 1) echo 'selected'; ?>>moderatecontent | API 设置中填入Moderate Key</option>
                                        <option value="2" <?php if ($config['checkImg'] == 2) echo 'selected'; ?> title="">nsfwjs | API 设置中填入nsfwjs url</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <input type="hidden" class="form-control" name="update" value="<?php echo date("Y-m-d H:i:s"); ?>" placeholder="隐藏的保存">
                    </div>
                    <button type="submit" class="btn btn-primary">保存</button>
                </form>
            </div>
        </div>
        <div class="tab-pane fade" id="Content7">
            <h5 class="header-dividing">可疑图片<small> 鉴黄查到的可疑图片</small></h5>
            <p>key申请地址: <a href="https://client.moderatecontent.com/" target="_blank">https://client.moderatecontent.com/</a></p>
            <p>获得key后打开->API 设置->Moderate Key->填入key</p>
            <p>为了访问速度,仅显示最近10张图片;鉴黄需要在图床安全->图片鉴黄中开启</p>
            <div class="table-responsive table-condensed">
                <table class="table table-hover table-striped table-bordered">
                    <thead>
                        <tr>
                            <th>序号</th>
                            <th>缩略图</th>
                            <th>文件名</th>
                            <th>文件大小</th>
                            <th>文件管理</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        // 获取被隔离的文件
                        $cache_dir = APP_ROOT . $config['path'] . 'suspic/';                             // cache目录
                        $cache_file = get_file_by_glob($cache_dir . '*.*');                              // 获取所有文件
                        $cache_num = count($cache_file);                                                 // 统计目录文件个数
                        for ($i = 0; $i < $cache_num and $i < 11; $i++) :                                // 循环输出文件
                            $file_cache_path = APP_ROOT . $config['path'] . 'suspic/' . $cache_file[$i]; // 绝对路径
                            $file_path = $config['path'] . 'suspic/' . $cache_file[$i];                  // 相对路径
                            $file_size = getDistUsed(filesize($file_cache_path));                        // 大小
                            $filen_name = $cache_file[$i];                                               // 名称
                            $url = $config['domain'] . $file_path;                                       // 网络连接
                            $unlink_img = $config['domain'] . '/app/del.php?url=' . $file_path;          // 删除连接
                        ?>
                            <tr>
                                <td><?php echo $i; ?></td>
                                <td><a href="<?php echo $url; ?>" data-toggle="lightbox" data-group="suspic-image-group"><img src="<?php echo get_online_thumbnail($file_path); ?>" class="img-rounded" width="100px"></a></td>
                                <td><?php echo $filen_name; ?></td>
                                <td><?php echo $file_size; ?></td>
                                <td>
                                    <a class="btn btn-mini" href="<?php echo $url; ?>" target="_blank">查看</a>
                                    <a class="btn btn-mini" href="/app/info.php?img=<?php echo $file_path; ?>" target="_blank">信息</a>
                                    <a class="btn btn-mini btn-success" href="#" onclick="ajax_post('<?php echo $filen_name; ?>','suspic_reimg')">恢复</a>
                                    <a class="btn btn-mini btn-danger" href="#" onclick="ajax_post('<?php echo $file_path; ?>','delete')">删除</a>
                                </td>
                            </tr>
                        <?php endfor; ?>
                    </tbody>
                </table>
            </div>
            <a class="btn btn-mini btn-primary" href="filer.php?path=<?php echo $config['path']; ?>suspic" target="_blank">管理可疑</a>
            <button class="btn btn-mini btn-danger" onclick="ajax_post('/suspic/','delDir')"><?php echo $cache_num; ?>张 | 删除全部</button>
        </div>
        <div class=" tab-pane fade" id="Content8">
            <div class="alert alert-primary">
                <h5>系统信息</h5>
                <hr />
                <p class="text-ellipsis">服务系统: <?PHP echo php_uname('s'); ?> <small class="text-muted text-ellipsis"><?PHP echo php_uname(); ?></small></p>
                <p class="text-ellipsis">Web服务: <?PHP echo $_SERVER['SERVER_SOFTWARE']; ?></p>
                <p class="text-ellipsis">服务器IP: <?PHP echo $_SERVER["SERVER_ADDR"] . ip2region($_SERVER["SERVER_ADDR"]) ?></p>
                <p class="text-ellipsis">图床时间: <?PHP echo date("Y-m-d H:i:s"); ?></p>
                <p class="text-ellipsis"><span class="label label-dot label-success" data-toggle="tooltip" title="当前页面占用内存"></span> 页面占用: <?php echo getDistUsed(memory_get_usage()); ?></p>
                <p class="text-ellipsis"><span class="label label-dot label-danger" data-toggle="tooltip" title="本页面占用内存最高峰"></span> 占用峰值: <?php echo getDistUsed(memory_get_peak_usage()); ?></p>
                <p class="text-ellipsis">占用磁盘: <?php echo getDistUsed(disk_total_space(__DIR__) - disk_free_space(__DIR__)) ?></p>
                <p class="text-ellipsis">剩余磁盘: <?php echo getDistUsed(disk_free_space(__DIR__)); ?></p>
                <h5>PHP信息</h5>
                <hr />
                <p class="text-ellipsis">PHP版本: <?php echo phpversion(); ?></p>
                <p class="text-ellipsis">运行模式: <?PHP echo php_sapi_name(); ?></p>
                <p class="text-ellipsis">上传限制: <?PHP echo get_cfg_var("upload_max_filesize"); ?></p>
                <p class="text-ellipsis">运行时间限制: <?PHP echo get_cfg_var("max_execution_time") . "s"; ?></p>
                <p class="text-ellipsis">最大占用内存: <?PHP echo get_cfg_var("memory_limit"); ?></p>
                <p class="text-ellipsis">POST上传限制: <?php echo ini_get('post_max_size'); ?></p>
                <p class="text-ellipsis">GD版本: <?php echo (gd_info()["GD Version"]); ?></p>
                <h5>我的信息</h5>
                <hr />
                <p class="text-ellipsis">IP: <?php echo real_ip() . ip2region(real_ip()); ?></p>

                <p class="text-ellipsis">配置文件更新日期：<?php echo date('Y-m-d H:i:s', filemtime($config_file)); ?></p>
                <p class="text-ellipsis">Browser: <?php echo $_SERVER['HTTP_USER_AGENT']; ?></p>
                <h5>图床信息</h5>
                <hr />
                <h6>API 插件</h6>
                <a href="https://microsoftedge.microsoft.com/addons/detail/%E7%AE%80%E5%8D%95%E5%9B%BE%E5%BA%8A-edge-version/hdafcoenpmebcjjcccojdlhfnndelefk" target="_blank"><span class="label label-badge label-primary" data-toggle="tooltip" title="浏览器插件">Edge</span></a>
                <a href="https://github.com/icret/EasyImage-Browser-Extension" target="_blank"><span class="label label-badge label-primary" data-toggle="tooltip" title="浏览器插件">Chrome</span></a>
                <a href="https://icret.github.io/EasyImages2.0/#/./使用ShareX上传" target="_blank"><span class="label label-badge label-primary" data-toggle="tooltip" title="使用ShareX上传">ShareX</span></a>
                <a href="https://icret.github.io/EasyImages2.0/#/./使用PicGo上传" target="_blank"><span class="label label-badge label-primary" data-toggle="tooltip" title="使用PicGo上传">PicGo</span></a>
                <h6>开发支持</h6>
                <p>
                    <a href="https://png.cm/?demo" target="_blank"><span class="label label-badge label-primary" data-toggle="tooltip" title="演示地址"><i class="icon icon-external-link"></i> Demo</span></a>
                    <a href="<?php echo is_file(APP_ROOT . '/docs/index.html') ?  '../docs' : 'https://icret.github.io/EasyImages2.0/#/'; ?>" target="_blank"><span class="label label-badge label-primary" data-toggle="tooltip" title="使用手册"><i class="icon icon-book"></i> Manual</span></a>
                    <a href="https://t.me/Easy_Image" target="_blank"><span class="label label-badge label-primary" data-toggle="tooltip" title="EasyImage Telegram Group"><i class="icon icon-comments-alt"></i> Telegram</span></a>
                    <a href="https://github.com/icret/EasyImages2.0/discussions" target="_blank"><span class="label label-badge label-primary" data-toggle="tooltip" title="GitHub 社区"><i class="icon icon-comments-alt"></i> Discuss</span></a>
                    <span data-toggle="tooltip" title="当前版本"><button type="button" class="btn btn-sm btn-primary label label-badge label-primary" data-toggle="modal" data-moveable="true" ata-size="sm" data-scroll-inside="true" data-icon="heart" data-title="当前版本更新日志" data-custom="<pre style='background-color: rgba(0, 0, 0, 0);border-color:rgba(0, 0, 0, 0);'><?php echo getVersion('name') . '<br/><br/>' . getVersion('body'); ?></pre>"><i class="icon icon-github"></i> <?php echo APP_VERSION; ?></button></span>
                    <span data-toggle="tooltip" title="您的赞美是我开发的动力!"><button type="button" class="btn btn-sm btn-danger label label-badge label-danger" data-toggle="modal" data-moveable="true" data-size="sm" data-icon="heart" data-title="您的赞美是我开发的动力!" data-custom="<ul class='nav nav-tabs'><li class='active'><a data-tab href='#wechat'><i class='icon icon-wechat' style='color:#329d38;'> 微信</i></a></li><li><a data-tab href='#alipay'><i class='icon icon-zhifubao-square' style='color:#1970fc;'> 支付宝</i></a></li></ul><div class='tab-content'><div class='tab-pane active' id='wechat'><img src='../public/images/wechat.jpg'></div><div class='tab-pane' id='alipay'><img src='../public/images/alipay.jpg'></div></div>"><i class="icon icon-yen"></i><small>打赏开发者</small></button></span>
                </p>
                <h6>程序依赖</h6>
                <p>
                    <a href="https://www.openzui.com/" target="_blank"><span class="label label-badge label-primary" data-toggle="tooltip" title="前端框架">ZUI</span></a>
                    <a href="https://github.com/verot/class.upload.php" target="_blank"><span class="label label-badge label-primary" data-toggle="tooltip" title="图像处理类">Verot</span></a>
                    <a href="https://tinyfilemanager.github.io/" target="_blank"><span class="label label-badge label-primary" data-toggle="tooltip" title="文件管理">Tinyfilemanager</span></a>
                    <a href="#"><span class="label label-badge label-primary" data-toggle="tooltip" title="文件管理">Web-indexr</span></a>
                    <a href="#Ip2Region" data-toggle="collapse" target="_blank"><span class="label label-badge label-primary" data-toggle="tooltip" title="IP地址数据库">Ip2Region<i class="icon icon-angle-down"></i></span></a>
                    <?php if (empty($config['TinyPng_key'])) : ?>
                        <span class="label label-badge label-warning" data-toggle="tooltip" title="压缩图片 TinyPng<br/>未填写">TinyPng</span>
                    <?php else : ?>
                        <span class="label label-badge label-primary" data-toggle="tooltip" title="压缩图片 TinyPng<br/>已填写">TinyPng</span>
                    <?php endif; ?>
                    <?php if (empty($config['moderatecontent_key'])) : ?>
                        <span class="label label-badge label-warning" data-toggle="tooltip" title="监黄 moderatecontent<br/>未填写">Moderatecontent</span>
                    <?php else : ?>
                        <span class="label label-badge label-primary" data-toggle="tooltip" title="监黄 moderatecontent<br/>已填写">Moderatecontent</span>
                    <?php endif; ?>
                    <a href="https://github.com/icret/EasyImages2.0/blob/master/LICENSE" target="_blank"><span class="label label-badge" data-toggle="tooltip" title="开源许可证">GPL-2.0</span></a>
                </p>
                <p class="text-muted"><i class="icon icon-certificate"> EasyImage2.0简单图床构建于众多优秀的开源项目之上, 非常感谢这些项目!</i></p>
            </div>
            <div class="collapse" id="Ip2Region">
                <div class="bg-primary with-padding">
                    <h4>Ip2region - IP地址数据库 <a href="https://raw.githubusercontent.com/lionsoul2014/ip2region/master/data/ip2region.xdb" target="_blank" class="label label-badge">下载 | 更新</a></h4>
                    <hr />
                    <p>
                        Ip2region (2.0 - xdb) 是一个离线 IP 数据管理框架和定位库,支持亿级别的数据段,10微秒级别的查询性能,提供了许多主流编程语言的 xdb 数据管理引擎的实现。
                        每个 ip 数据段的 region 信息都固定了格式：国家|区域|省份|城市|ISP, 只有中国的数据绝大部分精确到了城市, 其他国家部分数据只能定位到国家, 后前的选项全部是0。
                    </p>
                    <h6>* 下载 <a href="https://raw.githubusercontent.com/lionsoul2014/ip2region/master/data/ip2region.xdb" target="_blank">ip2region.xdb</a> IP数据库上传到 <small style="color: black;">/app/ip2region/</small> 文件夹, 如遇到下载失败可访问开源地址下载: [ <a href="https://github.com/lionsoul2014/ip2region" target="_blank"><small style="color: black;">Github</small></a> | <a href="https://gitee.com/lionsoul/ip2region" target="_blank"><small style="color: black;">Gitee</small></a> ] 更新方法与此相同。</h6>
                </div>
            </div>
            <?php if (getVersion() !== APP_VERSION) : ?>
                <div class="bg-warning with-padding hidden-xs">
                    <h5 class="header-dividing"><span class="label label-success">New</span> <?php echo getVersion('name'); ?></h5>
                    <pre style="background-color: rgba(0, 0, 0, 0);border-color:rgba(0, 0, 0, 0);">更新内容: <br /><?php echo getVersion('body'); ?></pre>
                    <h6>* 更新日期: <?php echo getVersion('created_at'); ?> 下载新版本上传至网站升级 <a href="<?php echo is_file(APP_ROOT . '/docs/index.html') ?  '../docs' : 'https://icret.github.io/EasyImages2.0/#/'; ?>" target="_blank" data-toggle="tooltip" title="升级方法"><i class="icon icon-question"></i></a> 然后点击更新版本号。</h6>
                    <div class="btn-group">
                        <a class="btn btn-mini btn-primary" href="https://github.com/icret/EasyImages2.0/releases" target="_blank" data-toggle="tooltip" title="Releases Info">Github</a>
                        <a class="btn btn-mini btn-primary" href="<?php echo getVersion('zipball_url'); ?>" target="_blank" data-toggle="tooltip" title="① 下载后上传至网站更新">下载新版本</a>
                        <a class="btn btn-mini btn-danger" href="#" id="post" onclick="ajax_post('/admin/logs/version/version.json','del_version_file')" data-toggle="tooltip" title="② 升级后获取新的版本信息">更新版本号</a>
                    </div>
                </div>
            <?php endif; ?>
        </div>
        <div class="tab-pane fade" id="Content9">
            <form action="<?php echo $_SERVER['SCRIPT_NAME']; ?>" method="post">
                <div class="form-group col-md-12">
                    <h5>上传后首选显示</h5>
                    <label class="radio-inline">
                        <input type="radio" name="upload_first_show" value="1" data-toggle="tooltip" title="图片直链" <?php if ($config['upload_first_show'] == 1) echo 'checked'; ?>>
                        <i class="icon icon-picture"></i>
                    </label>
                    <label class="radio-inline">
                        <input type="radio" name="upload_first_show" value="2" data-toggle="tooltip" title="论坛代码" <?php if ($config['upload_first_show'] == 2) echo 'checked'; ?>>
                        <i class="icon icon-chat"></i>
                    </label>
                    <label class="radio-inline">
                        <input type="radio" name="upload_first_show" value="3" data-toggle="tooltip" title="Markdown" <?php if ($config['upload_first_show'] == 3) echo 'checked'; ?>>
                        <i class="icon icon-code"></i>
                    </label>
                    <label class="radio-inline">
                        <input type="radio" name="upload_first_show" value="4" data-toggle="tooltip" title="HTML" <?php if ($config['upload_first_show'] == 4) echo 'checked'; ?>>
                        <i class="icon icon-html5"></i>
                    </label>
                    <label class="radio-inline">
                        <input type="radio" name="upload_first_show" value="5" data-toggle="tooltip" title="缩略图" <?php if ($config['upload_first_show'] == 5) echo 'checked'; ?>>
                        <i class="icon icon-camera"></i>
                    </label>
                    <label class="radio-inline" data-toggle="tooltip" title="删除链接">
                        <input <?php if ($config['show_user_hash_del'] == 0) echo 'disabled'; ?> type="radio" name="upload_first_show" value="6" <?php if ($config['upload_first_show'] == 6) echo 'checked'; ?>>
                        <i class="icon icon-trash"></i>
                    </label>
                </div>
                <div class="form-group col-md-12">
                    <label data-toggle="tooltip" title="选择网站对外展示的一些功能和页面">对外功能页面展示</label><br />
                    <div class="switch switch-inline" data-toggle="tooltip" title="暗黑模式切换">
                        <input type="hidden" name="dark-mode" value="0">
                        <input type="checkbox" name="dark-mode" value="1" <?php if ($config['dark-mode']) echo 'checked="checked"'; ?>>
                        <label style="font-weight: bold">暗黑</label>
                    </div>
                    <div class="switch switch-inline" data-toggle="tooltip" title=" 关闭后非登录状态不显示广场图片">
                        <input type="hidden" name="showSwitch" value="0">
                        <input type="checkbox" name="showSwitch" value="1" <?php if ($config['showSwitch']) echo 'checked="checked"'; ?>>
                        <label style="font-weight: bold">广场</label>
                    </div>
                    <div class="switch switch-inline" data-toggle="tooltip" title="广场图片详细信息按钮">
                        <input type="hidden" name="show_exif_info" value="0">
                        <input type="checkbox" name="show_exif_info" value="1" <?php if ($config['show_exif_info']) echo 'checked="checked"'; ?>>
                        <label style="font-weight: bold">详情页</label>
                    </div>
                    <div class="switch switch-inline" data-toggle="tooltip" title="非上传记录|清空缓存|浏览器版本低不显示">
                        <input type="hidden" name="history" value="0">
                        <input type="checkbox" name="history" value="1" <?php if ($config['history']) echo 'checked="checked"'; ?>>
                        <label style="font-weight: bold">历史记录</label>
                    </div>
                    <div class="switch switch-inline" data-toggle="tooltip" title="图片详细信息显示随机图片">
                        <input type="hidden" name="info_rand_pic" value="0">
                        <input type="checkbox" name="info_rand_pic" value="1" <?php if ($config['info_rand_pic']) echo 'checked="checked"'; ?>>
                        <label style="font-weight: bold">随机图片</label>
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="form-group col-md-2">
                        <label data-toggle="tooltip" title="暂支持中文简繁体转换">界面语言</label>
                        <select class="chosen-select form-control" name="language">
                            <option value="0" <?php if ($config['language'] == '0') echo 'selected'; ?>>简体中文</option>
                            <option value="1" <?php if ($config['language'] == '1') echo 'selected'; ?>>繁體中文</option>
                        </select>
                    </div>
                    <div class="form-group col-md-2">
                        <label data-toggle="tooltip" title="配色样式文件夹位置: /public/static/zui/theme/">网站配色</label>
                        <select class="chosen-select form-control" name="theme">
                            <option value="default" style="background: #3280fc;" <?php if ($config['theme'] == 'default') echo 'selected'; ?>>默认配色</option>
                            <option value="red" style="background: #d9534f;" <?php if ($config['theme'] == 'red') echo 'selected'; ?>>红色</option>
                            <option value="green" style="background: #4caf50;" <?php if ($config['theme'] == 'green') echo 'selected'; ?>>绿色</option>
                            <option value="blue" style="background: #039be5;" <?php if ($config['theme'] == 'blue') echo 'selected'; ?>>蓝色</option>
                            <option value="bluegrey" style="background: #607D8B;" <?php if ($config['theme'] == 'bluegrey') echo 'selected'; ?>>蓝灰</option>
                            <option value="indigo" style="background: #3F51B5;" <?php if ($config['theme'] == 'indigo') echo 'selected'; ?>>靛青</option>
                            <option value="brown" style="background: #8D6E63;" <?php if ($config['theme'] == 'brown') echo 'selected'; ?>>棕色</option>
                            <option value="yellow" style="background: #d0884d;" <?php if ($config['theme'] == 'yellow') echo 'selected'; ?>>黄色</option>
                            <option value="purple" style="background: #8666b8;" <?php if ($config['theme'] == 'purple') echo 'selected'; ?>>紫色</option>
                            <option value="black" style="background: #333;" <?php if ($config['theme'] == 'black') echo 'selected'; ?>>黑色</option>
                            <option value="grey" style="background: grey;" <?php if ($config['theme'] == 'grey') echo 'selected'; ?>>纪念灰</option>
                        </select>
                    </div>
                    <div class="form-group col-md-2">
                        <label for="login_bg" data-toggle="tooltip" title="图片地址可以是相对路径或网址">登录背景</label>
                        <input type="text" class="form-control" id="login_bg" name="login_bg" value="<?php if ($config['login_bg']) echo $config['login_bg']; ?>" required="required" placeholder="图片地址可以是相对路径或网址" onkeyup="this.value=this.value.replace(/\s/g,'')">
                    </div>
                    <div class="form-group col-md-2">
                        <label data-toggle="tooltip" title="默认进度条颜色 RGB|HSL|HEX">默认进度条颜色</label>
                        <input type="color" class="form-control" name="NProgress_default" value="<?php echo $config['NProgress_default']; ?>">
                    </div>
                    <div class="form-group col-md-2">
                        <label data-toggle="tooltip" title="上传过程中进度条颜色 RGB|HSL|HEX">上传过程进度条颜色</label>
                        <input type="color" class="form-control" name="NProgress_Progress" value="<?php echo $config['NProgress_Progress']; ?>">
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="col-md-9">
                        <label data-toggle="tooltip" title="可在网址后填写参数更改预览数量 eg: /list.php?num=3">广场默认浏览数量 | 当前: </label>
                        <label id="listNumber"><?php echo $config['listNumber']; ?>张</label>
                        <input type="range" class="form-control" name="listNumber" value="<?php echo $config['listNumber']; ?>" min="10" max="100" step="10" onchange="document.getElementById('listNumber').innerHTML=value">
                    </div>
                    <div class="col-md-3">
                        <label id="listDate" data-toggle="tooltip" title="广场往日浏览限制<br />有助于防爬虫抓取<br />建议不超10天,否则页面导致排版混乱">广场浏览历史限制 | 当前: <?php echo $config['listDate']; ?>天</label>
                        <input type="number" class="form-control" id="listDate" name="listDate" value="<?php if ($config['listDate']) echo $config['listDate']; ?>" min="1" max="100" required="required" placeholder="有助于防爬虫抓取 建议不超10天" onkeyup="this.value=this.value.replace(/\s/g,'')">
                    </div>
                </div>
                <input type="hidden" class="form-control" name="update" value="<?php echo date("Y-m-d H:i:s"); ?>" placeholder="隐藏的保存">
                <button type="submit" class="btn btn-primary">保存</button>
            </form>
        </div>
        <div class="tab-pane fade" id="Content10">
            <!-- 管理员账号 start-->
            <form action="<?php echo $_SERVER['SCRIPT_NAME']; ?>" method="post" onsubmit="return md5_post()">
                <h5 class="header-dividing">管理员账号<small> 不更改账号或者密码就不要保存</small></h5>
                <div class="col-md-12">
                    <div class="form-group col-md-4">
                        <div class="input-control has-icon-left" data-toggle="tooltip" title="上传者账号只能上传不能操作其他项目">
                            <input type="text" name="user" id="account" class="form-control" value="<?php echo $config['user']; ?>" required="required" placeholder="更改管理账号" onkeyup="this.value=this.value.replace(/\s/g,'')">
                            <label for="account" class="input-control-icon-left"><i class="icon icon-user "></i></label>
                        </div>
                    </div>
                    <div class="form-group col-md-4">
                        <div class="input-control has-icon-left">
                            <input type="text" name="password" id="password" class="form-control" value="" required="required" placeholder="更改管理密码" onkeyup="this.value=this.value.replace(/\s/g,'')">
                            <input type="hidden" name="password" id="md5_password">
                            <label for="password" class="input-control-icon-left"><i class="icon icon-key"></i></label>
                        </div>
                    </div>
                    <div class="form-group col-md-4">
                        <input type="hidden" name="admin_form" value="" placeholder="隐藏的保存">
                        <button type="submit" class="btn btn-primary">更改管理员 账号|密码</button>
                    </div>
                </div>
            </form>
            <div class="alert alert-primary with-icon col-xs-8">
                <i class="icon-info-sign"></i>
                <div class="content">
                    <ul>
                        <li>管理员账号不能与上传者账号相同</li>
                        <li>直接输入账号和密码即可完成修改</li>
                        <li>更改后会立即生效并重新登录,请务必牢记账号和密码! </li>
                        <li>如果忘记账号可以打开-><code>/config/config.php</code>文件->找到<code data-toggle="tooltip" title="'user'=><strong>admin</strong>'">user</code>对应的键值->填入</li>
                        <li>如果忘记密码请将密码->转换成SHA256-><a href="<?php echo $config['domain'] . '/app/reset_password.php'; ?>" target="_blank" class="text-purple">转换网址</a>->打开<code>/config/config.php</code>文件->找到<code data-toggle="tooltip" title="'password'=>'<strong>e6e0612609</strong>'">password</code>对应的键值->填入</li>
                    </ul>
                </div>
            </div>
            <!-- 上传用户管理 start-->
            <form action="<?php echo $_SERVER['SCRIPT_NAME']; ?>" method="post" onsubmit="return uploader_md5_post()">
                <h5 class="header-dividing">上传者账号<small> 账户只能用于上传</small></h5>
                <div class="col-md-12">
                    <div class="form-group col-md-3">
                        <div class="input-control has-icon-left" data-toggle="tooltip" title="上传者账号只能上传不能操作其他项目">
                            <input type="text" name="uploader_user" id="account" class="form-control" value="" required="required" autocomplete="off" placeholder="添加上传者账号" onkeyup="this.value=this.value.replace(/\s/g,'')">
                            <label for="account" class="input-control-icon-left"><i class="icon icon-user "></i></label>
                        </div>
                    </div>
                    <div class="form-group col-md-3">
                        <div class="input-control has-icon-left">
                            <input type="text" name="uploader_password" id="uploader_password" class="form-control" value="" required="required" autocomplete="off" placeholder="添加/更改 上传者密码" onkeyup="this.value=this.value.replace(/\s/g,'')">
                            <input type="hidden" name="uploader_password" id="uploader_md5_password">
                            <label for="password" class="input-control-icon-left"><i class="icon icon-key"></i></label>
                        </div>
                    </div>
                    <div class="form-group col-md-3">
                        <div class="input-group">
                            <span class="input-group-addon">有效期</span>
                            <input type="number" class="form-control" name="uploader_time" value="30" id="uploader_time" placeholder="有效期 单位: 天" required="required">
                            <span class="input-group-addon">天</span>
                        </div>
                    </div>
                    <div class="form-group col-md-3">
                        <input type="hidden" name="uploader_form" value="" placeholder="隐藏的保存">
                        <button type="submit" class="btn btn-danger">添加|更改 上传者 账号|密码</button>
                    </div>
                </div>
            </form>
            <div class="alert alert-primary with-icon col-xs-8">
                <i class="icon-info-sign"></i>
                <div class="content">
                    <ul>
                        <li>上传用户账号不能与管理员账号相同</li>
                        <li>上传用户的配置文件在<code>config.guest.php</code></li>
                        <li>开启登录上传后,可以添加一些只能上传的账号</li>
                        <li>更改后会立即生效并重新登录,请将账号和密码发给使用者</li>
                        <li>如果忘记密码请填入账号并填写新的密码即可更正密码 | <b class="text-success">与更改管理 账号/密码不同!</b></li>
                    </ul>
                </div>
            </div>
            <h5>* 开启用户分离后删除上传按钮激活, 删除后不可恢复</h5>
            <div id="guest" class="datagrid table-bordered">
                <div class="input-control search-box search-box-circle has-icon-left has-icon-right" id="searchboxExample2" style="margin-bottom: 10px;">
                    <input id="inputSearchExample2" type="search" class="form-control search-input" placeholder="上传用户搜索...">
                    <label for="inputSearchExample2" class="input-control-icon-left search-icon"><i class="icon icon-search"></i></label>
                    <a href="#" class="input-control-icon-right search-clear-btn"><i class="icon icon-remove"></i></a>
                </div>
                <div class="datagrid-container"></div>
            </div>
            <!-- 源图加密Key start-->
            <form action="<?php echo $_SERVER['SCRIPT_NAME']; ?>" method="post">
                <h5 class="header-dividing">源图保护Key<small> * 设定后请勿更改,否则所有加密链接失效</small></h5>
                <div class="col-md-3">
                    <div class="input-control has-icon-left" style="margin-top: 5px;">
                        <input type="text" class="form-control" name="hide_key" required="required" value="<?php echo $config['hide_key']; ?>" onkeyup="this.value=this.value.trim()">
                        <label for="password" class="input-control-icon-left"><i class="icon icon-key"></i></label>
                    </div>
                </div>
                <div class="form-group col-md-9" style="margin-top: 5px;">
                    <input type="hidden" class="form-control" name="update" value="<?php echo date("Y-m-d H:i:s"); ?>" placeholder="隐藏的保存">
                    <button type="submit" class="btn btn-danger">保存源图加密Key</button>
                </div>
            </form>
        </div>
        <div class="tab-pane fade" id="Content11">
            <h5 class="header-dividing">图片回收<small> 显示通过加密删除文件</small></h5>
            <p>为了访问速度,仅显示最近10张图片; 图片回收需要在图床安全->开启图片回收</p>
            <div class="table-responsive table-condensed">
                <table class="table table-hover table-striped table-bordered">
                    <thead>
                        <tr>
                            <th>序号</th>
                            <th>缩略图</th>
                            <th>文件名</th>
                            <th>文件大小</th>
                            <th>文件管理</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        // 获取被隔离的文件
                        $cache_dir = APP_ROOT . $config['path'] . 'recycle/';                             // cache目录
                        $cache_file = get_file_by_glob($cache_dir . '*.*');                               // 获取所有文件
                        $cache_num = count($cache_file);                                                  // 统计目录文件个数
                        for ($i = 0; $i < $cache_num and $i < 11; $i++) :                                 // 循环输出文件
                            $file_cache_path = APP_ROOT . $config['path'] . 'recycle/' . $cache_file[$i]; // 绝对路径
                            $file_path = $config['path'] . 'recycle/' . $cache_file[$i];                  // 相对路径
                            $file_size = getDistUsed(filesize($file_cache_path));                         // 大小
                            $filen_name = $cache_file[$i];                                                // 名称
                            $url = $config['domain'] . $file_path;                                        // 网络连接
                            // $unlink_img = $config['domain'] . '/app/del.php?url=' . $file_path;        // 删除连接
                        ?>
                            <tr>
                                <td><?php echo $i; ?></td>
                                <td><a href="<?php echo $url; ?>" data-toggle="lightbox" data-group="recycle-image-group"><img src="<?php echo get_online_thumbnail($file_path); ?>" class="img-rounded" width="100px"></a></td>
                                <td><?php echo $filen_name; ?></td>
                                <td><?php echo $file_size; ?></td>
                                <td>
                                    <a class="btn btn-mini" href="<?php echo $url; ?>" target="_blank">查看</a>
                                    <a class="btn btn-mini" href="/app/info.php?img=<?php echo $file_path; ?>" target="_blank">信息</a>
                                    <a class="btn btn-mini btn-success" href="#" onclick="ajax_post('<?php echo $filen_name; ?>','recycle_reimg')">恢复</a>
                                    <a class="btn btn-mini btn-danger" href="#" onclick="ajax_post('<?php echo $file_path; ?>','delete')">删除</a>
                                </td>
                            </tr>
                        <?php endfor; ?>
                    </tbody>
                </table>
            </div>
            <a class="btn btn-mini btn-primary" href="filer.php?path=<?php echo $config['path']; ?>recycle" target="_blank">管理回收</a>
            <button class="btn btn-mini btn-danger" onclick="ajax_post('/recycle/','delDir')"><?php echo $cache_num; ?>张 | 删除全部</button>
        </div>
        <div class="tab-pane fade" id="Content12">
            <form action="<?php echo $_SERVER['SCRIPT_NAME']; ?>" method="post">
                <div class="form-group col-md-6">
                    <label>水印方式</label>
                    <select class="chosen-select form-control" name="watermark">
                        <option value="0" <?php if (!$config['watermark']) echo 'selected'; ?>>关闭水印</option>
                        <option value="1" <?php if ($config['watermark'] == 1) echo 'selected'; ?>>文字水印</option>
                        <option value="2" <?php if ($config['watermark'] == 2) echo 'selected'; ?>>图片水印</option>
                    </select>
                </div>
                <div class="form-group col-md-6">
                    <label data-toggle="tooltip" title="不开启水印方式不生效">水印位置</label>
                    <select class="chosen-select form-control" name="waterPosition">
                        <option value="0" <?php if (!$config['waterPosition']) echo 'selected'; ?>>随机位置</option>
                        <option value="1" <?php if ($config['waterPosition'] == 1) echo 'selected'; ?>>顶部居左</option>
                        <option value="2" <?php if ($config['waterPosition'] == 2) echo 'selected'; ?>>顶部居中</option>
                        <option value="3" <?php if ($config['waterPosition'] == 3) echo 'selected'; ?>>顶部居右</option>
                        <option value="4" <?php if ($config['waterPosition'] == 4) echo 'selected'; ?>>左边居中</option>
                        <option value="5" <?php if ($config['waterPosition'] == 5) echo 'selected'; ?>>图片中心</option>
                        <option value="6" <?php if ($config['waterPosition'] == 6) echo 'selected'; ?>>右边居中</option>
                        <option value="7" <?php if ($config['waterPosition'] == 7) echo 'selected'; ?>>底部居左</option>
                        <option value="8" <?php if ($config['waterPosition'] == 8) echo 'selected'; ?>>底部居中</option>
                        <option value="9" <?php if ($config['waterPosition'] == 9) echo 'selected'; ?>>底部居右</option>
                    </select>
                </div>
                <div class="form-group col-md-6">
                    <label data-toggle="tooltip" title="支持GIF,JPG,BMP,PNG和PNG alpha">图片水印路径</label>
                    <input type="text" class="form-control" name="waterImg" required="required" value="<?php echo $config['waterImg']; ?>" onkeyup="this.value=this.value.replace(/\s/g,'')">
                </div>
                <div class="form-group col-md-6">
                    <label data-toggle="tooltip" title="水印中含有中文的,请选用符合GB/2312的字体">文字水印 | 验证码的字体路径</label>
                    <input type="text" class="form-control" name="textFont" required="required" value="<?php echo $config['textFont']; ?>" onkeyup="this.value=this.value.replace(/\s/g,'')">
                </div>
                <div class="form-group col-md-3">
                    <label>文字水印</label>
                    <input type="text" class="form-control" name="waterText" required="required" value="<?php echo $config['waterText']; ?>" onkeyup="this.value=this.value.trim()">
                </div>
                <div class="form-group col-md-3">
                    <label data-toggle="tooltip" title="格式RGBA 末尾为透明度0-127 0为不透明,仅支持文字水印">文字水印颜色</label>
                    <input type="text" name="textColor" class="form-control" value="" data-jscolor="{preset:'myPreset'}" readonly required="required">
                </div>
                <div class="form-group col-md-6">
                    <label>文字水印大小 | 当前: </label><label id="textSize"><?php echo $config['textSize']; ?></label><label>px</label>
                    <input type="range" class="form-control" name="textSize" value="<?php echo $config['textSize']; ?>" min="10" max="300" step="5" onchange="document.getElementById('textSize').innerHTML=value">
                </div>
                <div class="form-group">
                    <input type="hidden" class="form-control" name="update" value="<?php echo date("Y-m-d H:i:s"); ?>" placeholder="隐藏的保存">
                </div>
                <button type="submit" class="btn btn-primary">保存</button>
            </form>
        </div>
        <div class="tab-pane fade" id="Content13">
            <form action="<?php echo $_SERVER['SCRIPT_NAME']; ?>" method="post">
                <h5 class="header-dividing">前端裁剪/压缩 <small>优点:服务器无压力 缺点:PC配置低的会导致浏览器卡顿,偶现丢失方向信息,仅支持JPG</small></h5>
                <div class="col-md-12">
                    <div class="form-group col-md-2">
                        <div class="switch switch-inline" data-toggle="tooltip" title="控制以下五项 不开启不生效">
                            <input type="hidden" name="imgRatio" value="0">
                            <input type="checkbox" name="imgRatio" value="1" <?php if ($config['imgRatio']) echo 'checked="checked"'; ?>>
                            <label style="font-weight: bold">前端修改图片</label>
                        </div>
                    </div>
                    <div class="form-group col-md-2">
                        <div class="switch switch-inline">
                            <input type="hidden" name="imgRatio_crop" value="0">
                            <input type="checkbox" name="imgRatio_crop" value="1" <?php if ($config['imgRatio_crop']) echo 'checked="checked"'; ?>>
                            <label style="font-weight: bold">上传前裁剪</label>
                        </div>
                    </div>
                    <div class="form-group col-md-3">
                        <div class="switch switch-inline">
                            <input type="hidden" name="imgRatio_preserve_headers" value="0">
                            <input type="checkbox" name="imgRatio_preserve_headers" value="1" <?php if ($config['imgRatio_preserve_headers']) echo 'checked="checked"'; ?>>
                            <label style="font-weight: bold">保留图片原始数据</label>
                        </div>
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="form-group col-md-4">
                        <label>压缩后的宽度 | 当前: </label><label id="image_x"><?php echo $config['image_x']; ?></label><label>px (0为不生效)</label>
                        <input type="range" class="form-control" name="image_x" value="<?php echo $config['image_x']; ?>" min="0" max="4096" step="100" onchange="document.getElementById('image_x').innerHTML=value">
                    </div>
                    <div class="form-group col-md-4">
                        <label>压缩后的高度 | 当前: </label><label id="image_y"><?php echo $config['image_y']; ?></label><label>px (0为不生效)</label>
                        <input type="range" class="form-control" name="image_y" value="<?php echo $config['image_y']; ?>" min="0" max="4096" step="100" onchange="document.getElementById('image_y').innerHTML=value">
                    </div>
                    <div class="form-group col-md-4">
                        <label>前端压缩率(仅支持JPG) | 当前: </label><label id="imgRatio_quality"><?php echo $config['imgRatio_quality']; ?></label><label>%</label>
                        <input type="range" class="form-control" name="imgRatio_quality" value="<?php echo $config['imgRatio_quality']; ?>" min="10" max="100" step="5" onchange="document.getElementById('imgRatio_quality').innerHTML=value">
                    </div>
                </div>


                <h5 class="header-dividing">后端压缩 <small data-toggle="tooltip" title=" 有一定概率改变图片方向,有可能使图片变大(特别是小图片) !<br />开启转换图片格式后不建议开启此选项,可能会导致图片变大!">优点:避免用户端欺骗,效果更好 缺点:增加服务器压力</small></h5>
                <div class="form-group">
                    <div class="switch switch-inline">
                        <input type="hidden" name="compress" value="0">
                        <input type="checkbox" name="compress" value="1" <?php if ($config['compress']) echo 'checked="checked"'; ?>>
                        <label style="font-weight: bold">后端压缩上传图片 | 更多图片格式的支持</label>
                    </div>
                </div>
                <div class="form-group">
                    <label>后端压缩率 | 当前: </label><label id="compress_ratio"><?php echo $config['compress_ratio']; ?></label><label>%</label>
                    <input type="range" class="form-control" name="compress_ratio" value="<?php echo $config['compress_ratio']; ?>" min="1" max="100" step="1" onchange="document.getElementById('compress_ratio').innerHTML=value">
                </div>
                <div class="form-group">
                    <input type="hidden" class="form-control" name="update" value="<?php echo date("Y-m-d H:i:s"); ?>" placeholder="隐藏的保存">
                </div>
                <button type="submit" class="btn btn-primary">保存</button>
            </form>
        </div>
        <div class="tab-pane fade" id="Content14">
            <h5 class="header-dividing">文件管理 <small>由作者定制,非必要请勿替换</small></h5>
            <a class="btn btn-mini btn-primary" href="/admin/manager.php?p=<?php echo date('Y/m/d'); ?> " target="_blank" data-toggle="tooltip" title="使用Tinyfilemanager管理文件"><i class="icon icon-folder-open"> 文件管理</i></a>
            <a class="btn btn-mini btn-primary" href="filer.php?path=<?php echo $config['path'] . date('Y/m/d'); ?> " target="_blank" data-toggle="tooltip" title="使用web-indexr管理文件"><i class="icon icon-folder-close"> 文件管理</i></a>
            <h5 class="header-dividing">删除文件 <small>* 删除后不可恢复</small></h5>
            <form class="form-inline" method="POST" style="margin-bottom: 5px;">
                <p id="delimgurl"></p>
                <div class="form-group">
                    <label for="delSingle" class="text-warning">删除单张图片文件: </label>
                    <input type="url" name="url_admin_inc" class="form-control input-sm" id="delSingle" required="required" placeholder="请输入图片链接">
                </div>
                <button type="submit" class="btn btn-sm btn-warning" onclick="ajax_post($('#delSingle').val(),'delete')">删除</button>
            </form>
            <form class="form-inline" method="POST">
                <div class="form-group">
                    <label for="delDir" class="text-danger">删除指定日期文件: </label>
                    <input type="text" class="form-control form-date input-sm" id="dateDir" name="dateDir" value="<?php echo date('Y/m/d/'); ?>" readonly>
                </div>
                <button type="submit" class="btn btn-sm btn-danger" onclick="ajax_post($('#dateDir').val(),'delDir')">删除</button>
            </form>
        </div>
        <div class="tab-pane fade" id="Content15">
            <div class="alert alert-primary with-icon">
                <i class="icon-info-sign"></i>
                <div class="content">
                    <ol>
                        <li>慎用，正在实验中</li>
                        <li>已回收的图片无法再还原到FTP</li>
                        <li>效果: 开启后上传|删除等受网络影响</li>
                        <li>流程: 客户端->图床服务器->远程附件</li>
                        <li>网络影响: 当前服务器->远程附件服务器速度</li>
                    </ol>
                </div>
            </div>
            <h5 class="header-dividing">远程附件 <small> 将文件以FTP方式上传到第三方</small></h5>
            <form action="<?php echo $_SERVER['SCRIPT_NAME']; ?>" method="post">
                <div class="col-md-12">
                    <div class="form-group col-md-2">
                        <div class="switch switch-inline">
                            <input type="hidden" name="ftp_status" value="0">
                            <input type="checkbox" name="ftp_status" value="1" <?php if ($config['ftp_status']) echo 'checked="checked"'; ?>>
                            <label style="font-weight: bold">启用远程附件</label>
                        </div>
                    </div>
                    <div class="form-group col-md-2">
                        <div class="switch switch-inline" data-toggle="tooltip" title="一般情况下被动模式即可<br/>如果存在上传失败可尝试开启">
                            <input type="hidden" name="ftp_pasv" value="0">
                            <input type="checkbox" name="ftp_pasv" value="1" <?php if ($config['ftp_pasv']) echo 'checked="checked"'; ?>>
                            <label style="font-weight: bold">主动模式</label>
                        </div>
                    </div>
                    <div class="form-group col-md-2">
                        <div class="switch switch-inline" data-toggle="tooltip" title="注意: FTP服务器需开启SSL">
                            <input type="hidden" name="ftp_ssl" value="0">
                            <input type="checkbox" name="ftp_ssl" value="1" <?php if ($config['ftp_ssl']) echo 'checked="checked"'; ?>>
                            <label style="font-weight: bold">启用SSL</label>
                        </div>
                    </div>
                    <div class="form-group col-md-2">
                        <div class="switch switch-inline" data-toggle="tooltip" title="FTP上传完毕删除本地文件<br/>非自用不建议开启此选项<br/>没有SQL无法管理远程文件">
                            <input type="hidden" name="ftp_complete_del_local" value="0">
                            <input type="checkbox" name="ftp_complete_del_local" value="1" <?php if ($config['ftp_complete_del_local']) echo 'checked="checked"'; ?>>
                            <label style="font-weight: bold">完成删除</label>
                        </div>
                    </div>
                    <div class="form-group col-md-2">
                        <div class="switch switch-inline" data-toggle="tooltip" title="删除本地文件时同步删除远程附件">
                            <input type="hidden" name="ftp_delloc_sync" value="0">
                            <input type="checkbox" name="ftp_delloc_sync" value="1" <?php if ($config['ftp_delloc_sync']) echo 'checked="checked"'; ?>>
                            <label style="font-weight: bold">同步删除</label>
                        </div>
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="form-group col-md-3">
                        <label data-toggle="tooltip" title="域名/IP地址, 可以先测试FTP软件是否能正常登录">FTP 服务器地址</label>
                        <input type="text" class="form-control" name="ftp_host" value="<?php echo $config['ftp_host']; ?>" onkeyup="this.value=this.value.replace(/\s/g,'')" placeholder="FTP 服务器地址">
                    </div>
                    <div class="form-group col-md-1">
                        <label data-toggle="tooltip" title="FTP 链接端口, 默认:21">端口</label>
                        <input type="number" class="form-control" name="ftp_port" required="required" value="<?php echo $config['ftp_port']; ?>" onkeyup="this.value=this.value.replace(/\s/g,'')">
                    </div>
                    <div class="form-group col-md-1">
                        <label data-toggle="tooltip" title="FTP 传输超时时间<br/> 单位:秒, 0为服务器默认时间">超时时间</label>
                        <input type="number" class="form-control" name="ftp_time" required="required" value="<?php echo $config['ftp_time']; ?>" onkeyup="this.value=this.value.replace(/\s/g,'')">
                    </div>
                    <div class="form-group col-md-2">
                        <label data-toggle="tooltip" title="FTP 账号,该账户需要有以下权限: 读取/写入/删除文件、创建/继承目录">账号</label>
                        <input type="text" class="form-control" name="ftp_user" value="<?php echo $config['ftp_user']; ?>" onkeyup="this.value=this.value.replace(/\s/g,'')" placeholder="FTP 账号">
                    </div>
                    <div class="form-group col-md-2">
                        <label data-toggle="tooltip" title="FTP 密码">密码</label>
                        <input type="password" class="form-control" name="ftp_pass" value="<?php echo $config['ftp_pass']; ?>" onkeyup="this.value=this.value.replace(/\s/g,'')" placeholder="FTP 密码">
                    </div>
                    <div class="form-group col-md-3">
                        <label data-toggle="tooltip" title="网址与图片域名一致, 末尾不加'/'">远程图片域名</label>
                        <input type="url" class="form-control" name="imgurl" value="<?php echo $config['imgurl']; ?>" required="required" readonly="readonly" onkeyup="this.value=this.value.replace(/\s/g,'')">
                    </div>
                </div>
                <div class="form-group">
                    <input type="hidden" class="form-control" name="update" value="<?php echo date("Y-m-d H:i:s"); ?>" placeholder="隐藏的保存">
                </div>
                <button type="submit" class="btn btn-primary">保存</button>
            </form>
        </div>
    </div>
</div>
<link rel="stylesheet" href="<?php static_cdn(); ?>/public/static/zui/lib/datagrid/zui.datagrid.min.css">
<link rel="stylesheet" href="<?php static_cdn(); ?>/public/static/zui/lib/datetimepicker/datetimepicker.min.css">
<script type="application/javascript" src="<?php static_cdn(); ?>/public/static/crypto/SHA256.js"></script>
<script type="application/javascript" src="<?php static_cdn(); ?>/public/static/crypto/SHA256.js"></script>
<script type="application/javascript" src="<?php static_cdn(); ?>/public/static/jscolor/jscolor.min.js"></script>
<script type="application/javascript" src="<?php static_cdn(); ?>/public/static/zui/lib/datagrid/zui.datagrid.min.js"></script>
<script type="application/javascript" src="<?php static_cdn(); ?>/public/static/zui/lib/datetimepicker/datetimepicker.min.js"></script>
<script>
    // POST 更新数据
    function update_post(cid, url = "update.php") {
        $.ajax({
            //几个参数需要注意一下
            type: "POST", //方法类型
            dataType: "json", //预期服务器返回的数据类型
            url: url, //url
            data: $("#" + cid).serialize(),
            success: function(res) {
                console.log(res); //打印服务端返回的数据(调试用)
                // if (res.code === 200) {};
                new $.zui.Messager(res.msg, {
                    type: res.type, // 定义颜色主题 
                    icon: res.icon // 定义消息图标
                }).show();

                // 延时2秒刷新
                window.setTimeout(function() {
                    window.location.reload();
                }, 1500)
            },
            error: function() {
                confirm("保存异常,请使用浏览器F12查看控制台信息");
                console.log(res); //打印服务端返回的数据(调试用)
            }
        });
    }

    // POST提交
    function ajax_post(url, mode) {
        $.post("../app/del.php", {
                url: url,
                mode: mode
            },
            function(data, status) {
                let res = JSON.parse(data);
                new $.zui.Messager(res.msg, {
                    type: res.type,
                    icon: res.icon
                }).show();
                if (res.code === 200) {
                    // 延时2秒刷新
                    window.setTimeout(function() {
                        window.location.reload();
                    }, 2000)
                }
            });
    }

    // 水印字体颜色
    jscolor.presets.myPreset = {
        value: '<?php echo $config['textColor']; ?>',
        format: 'rgba',
        closeText: true,
        width: 201,
        height: 81,
        backgroundColor: '#333',
        palette: [
            '#000000', '#7d7d7d', '#870014', '#ec1c23', '#ff7e26',
            '#fef100', '#22b14b', '#00a1e7', '#3f47cc', '#a349a4',
            '#ffffff', '#c3c3c3', '#b87957', '#feaec9', '#ffc80d',
            '#eee3af', '#b5e61d', '#99d9ea', '#7092be', '#c8bfe7',
        ],
    }

    // 使用本地存储记录当前tab页面
    if ($.zui.store.enable == false) {
        new $.zui.Messager("localStorage 错误,无法存储上传历史记录", {
            type: "special", // 定义颜色主题 
            time: 9000
        }).show();
    }
    $('[data-tab]').on('shown.zui.tab', function(e) {
        var cookie_value = e.delegateTarget.attributes[1].value;
        $.zui.store.pageSet('data-tab-now', cookie_value);
        console.log('当前被激活的标签页', e.target);
        console.log('上一个标签页', e.relatedTarget);
    })
    // cookie有
    if ($.zui.store.pageGet('data-tab-now') != null) {
        $ac = $.zui.store.pageGet('data-tab-now');
        $("a[href = '" + $ac + "']").parent().addClass("active in")
        $($ac).addClass("active in")
    }
    // cookie无
    if ($.zui.store.pageGet('data-tab-now') == null) {
        $("a[href = '#Content1']").parent().addClass("active in")
        $('#Content1').addClass("active in")
    }

    // 账号密码 | 以md5加密方式发送
    function uploader_md5_post() {
        var password = document.getElementById('uploader_password');
        var md5pwd = document.getElementById('uploader_md5_password');
        md5pwd.value = SHA256(password.value);
        //可以校验判断表单内容,true就是通过提交,false,阻止提交
        return true;
    }
    // 账号密码 | 以md5加密方式发送
    function md5_post() {
        var password = document.getElementById('password');
        var md5pwd = document.getElementById('md5_password');
        md5pwd.value = SHA256(password.value);
        //可以校验判断表单内容,true就是通过提交,false,阻止提交
        return true;
    }

    // 动态显示要删除的图片
    var oBtn = document.getElementById('delSingle');
    var oTi = document.getElementById('title');
    if ('oninput' in oBtn) {
        oBtn.addEventListener("input", getWord, false);
    } else {
        oBtn.onpropertychange = getWord;
    }

    function getWord() {
        var delimgurl = document.getElementById("delimgurl");
        delimgurl.innerHTML += '<img src="' + oBtn.value + '" width="200" class="img-rounded" /><br />';
    }
    // 日期选择
    $(".form-date").datetimepicker({
        weekStart: 1,
        todayBtn: 1,
        autoclose: 1,
        todayHighlight: 1,
        startView: 2,
        minView: 2,
        forceParse: 0,
        format: "yyyy/mm/dd/",
        endDate: new Date() // 只能选当前日期之前
    });

    // viewlog日期选择
    $(".logDate").datetimepicker({

        format: 'yyyy-mm',
        weekStart: 1,
        autoclose: true,
        startView: 3,
        minView: 3,
        forceParse: false,
        language: 'zh-CN',
        todayBtn: true,
        todayHighlight: true,
        endDate: new Date() // 只能选当前日期之前
    });

    // Token 数据表格
    $('#myDataGrid').datagrid({
        dataSource: {
            cols: [{
                    name: 'id',
                    label: 'ID',
                    width: 0.05
                },
                {
                    name: 'token',
                    label: 'Token',
                    html: true,
                    width: 0.25
                },
                {
                    name: 'add_time',
                    label: '添加时间',
                    html: true,
                    width: 0.15
                },
                {
                    name: 'expired',
                    label: '有效期至',
                    html: true,
                    width: 0.15
                },
                {
                    name: 'number',
                    label: '上传数量',
                    html: true,
                    width: 0.1
                },
                {
                    name: 'manage',
                    label: '管理',
                    html: true,
                    width: 0.3
                },
            ],
            array: [
                <?php foreach ($tokenList as $key => $value) :
                    $expired = $value['expired'] < time() ? '<p class="text-gray">已过期</p>' : '<p class="text-green">' . date('Y-m-d H:i:s', $value['expired']) . '</p>'; ?> {
                        id: '<?php echo $value['id']; ?>',
                        token: '<input class="form-control input-sm" type="text" value="<?php echo $key; ?>" readonly>',
                        add_time: '<?php echo date('Y-m-d H:i:s', $value['add_time']); ?>',
                        expired: '<?php echo $expired; ?>',
                        number: <?php echo get_file_by_glob(APP_ROOT . $config['path'] . $value['id'], $type = 'number'); ?>,
                        manage: "<a href='/admin/manager.php?p=<?php echo $value['id']; ?>' target='_blank' class='btn btn-mini btn-success <?php if (!$config['token_path_status']) echo 'disabled'; ?>'>文件</a> <a href='admin.inc.php?stop_token=<?php echo $key; ?>' class='btn btn-mini btn-danger'>禁用</a> <a href='admin.inc.php?delete_token=<?php echo $key; ?>' class='btn btn-mini btn-danger'>删除</a> <a href='#' onclick=\"ajax_post('<?php echo $value['id']; ?>','delDir')\" class='btn btn-mini btn-primary <?php if (!$config['token_path_status']) echo 'disabled'; ?>'>删除上传</a>"
                    },
                <?php endforeach; ?>
            ]
        },
        sortable: true,
        storage: true,
        hoverCell: true,
        showRowIndex: false,
        responsive: true,
        height: 200,
        // ... 其他初始化选项
        configs: {
            R1: {
                style: {
                    color: '#00b8d4',
                    backgroundColor: '#e0f7fa'
                }
            },
        }
    });
    // 获取数据表格实例
    var tokenMyDataGrid = $('#myDataGrid').data('zui.datagrid');

    // 按照 `name` 列降序排序
    tokenMyDataGrid.sortBy('expired', 'desc');

    // guest 上传用户数据表格
    $('#guest').datagrid({
        dataSource: {
            height: 800,
            cols: [{
                    label: '账号',
                    name: 'guest',
                    html: true,
                    width: 0.1
                },
                {
                    label: '密码 (SHA256)',
                    name: 'password',
                    html: true,
                    width: 0.2
                },
                {
                    label: '添加时间',
                    name: 'add_time',
                    html: true,
                    width: 0.15

                },
                {
                    label: '有效期至',
                    name: 'expired',
                    html: true,
                    width: 0.15
                },
                {
                    label: '上传数量',
                    name: 'files',
                    html: true,
                    width: 0.1
                },
                {
                    label: '管理账号',
                    name: 'manage',
                    html: true,
                    width: 0.3

                },
            ],
            array: [
                <?php foreach ($guestConfig as $k => $v) :
                    $expired = $v['expired'] < time() ? '<p class="text-gray">已过期</p>' : '<p class="text-green">' . date('Y-m-d H:i:s', $v['expired']) . '</p>'; ?> {
                        guest: '<?php echo $k; ?>',
                        password: '<input class="form-control input-sm" type="text" value="<?php echo $v['password']; ?>" readonly>',
                        add_time: '<?php echo date('Y-m-d H:i:s', $v['add_time']); ?>',
                        expired: '<?php echo $expired; ?>',
                        files: <?php echo get_file_by_glob(APP_ROOT . $config['path'] . $k, $type = 'number'); ?>,
                        manage: "<a href='/admin/manager.php?p=<?php echo $k; ?>' target='_blank' class='btn btn-mini btn-success <?php if (!$config['guest_path_status']) echo 'disabled'; ?>'>文件</a> <a href='admin.inc.php?stop_guest=<?php echo $k; ?>' class='btn btn-mini btn-danger'>禁用</a> <a class='btn btn-mini btn-danger' href='admin.inc.php?delete_guest=<?php echo $k; ?>'>删除</a> <a class='btn btn-mini btn-primary <?php if (!$config['guest_path_status']) echo 'disabled'; ?>' href='#' onclick=\"ajax_post('<?php echo $k; ?>','delDir')\">删除上传</a>",
                    },
                <?php endforeach; ?>
            ]
        },
        sortable: true,
        storage: true,
        hoverCell: true,
        showRowIndex: true,
        responsive: true,
        height: 200,
        // ... 其他初始化选项
        configs: {
            R1: {
                style: {
                    color: '#00b8d4',
                    backgroundColor: '#e0f7fa'
                }
            },
        }
    });

    // 获取数据表格实例
    var guestMyDataGrid = $('#guest').data('zui.datagrid');

    // 按照 `name` 列降序排序
    guestMyDataGrid.sortBy('add_time', 'desc');

    /** 引入设置页面检测文件 */
    <?php if ($config['checkEnv']) require_once __DIR__ . '/inc.check.php'; ?>

    // 更改网页标题
    document.title = "图床设置 - <?php echo $config['title']; ?>"
</script>
<?php
/** 引入底部 */
require_once APP_ROOT . '/app/footer.php';
