<?php
require_once __DIR__ . '/function.php';
// 跳转安装
if (!is_file(APP_ROOT . '/install/install.lock') and is_file(APP_ROOT . '/install/install.php')) {
    exit('<script type="text/javascript">window.location.href="' . get_whole_url('/') . '/install/index.php"</script>');
}
/**
 * 检测弹窗内容
 */

if (file_exists(APP_ROOT . '/admin/EasyIamge.lock')) return; // 查询锁定弹窗文件是否存在
file_put_contents(APP_ROOT . '/admin/EasyIamge.lock', '安装环境检测锁定文件,如需再次展示请删除此文件!', FILE_APPEND | LOCK_EX);
?>
<div class="modal fade" id="myModal-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">
                    <i class="icon icon-heart"> </i><a href="https://blog.png.cm/902.html" target="_blank">简单图床-EasyImage2.0</a> 安装环境检测
                </h4>
            </div>
            <div class="modal-body">
                <h4>说明:</h4>
                <h5>1. 建议使用<span style="color:red">PHP7.0</span>及以上版本;</h5>
                <h5>2. 上传失败大部分是由于<span style="color:red">upload_max_filesize、post_max_size、文件权限</span>设置不正确;</h5>
                <h5>3. 本程序主要用到<span style="color:red">Fileinfo、GD、Openssl</span>扩展,如果缺失会导致无法访问管理面板以及上传/删除图片。</h5>
                <hr />
                <h4>EasyImage2.0 基础检测:</h4>
                <p>当前PHP版本:<sapn style="color:green"><?php echo phpversion() ?></sapn>
                </p>
                <p>PHP最大上传: <sapn style="color:green"><?php echo ini_get('upload_max_filesize'); ?></sapn>
                </p>
                <p>POST最大上传: <sapn style="color:green"><?php echo ini_get('post_max_size'); ?></sapn>
                </p>
                <?php
                // 扩展检测 取消检测imagick扩展
                $expand = array('fileinfo', 'gd', 'openssl');
                foreach ($expand as $val) {
                    if (extension_loaded($val)) {
                        echo '
                            <p style="color:green">' . strtoupper($val) . " - 已安装</p>";
                    } else {
                        echo "<script language='javascript'>alert('$val - 未安装')</script>";
                        echo '<p style="color:red">' . strtoupper($val) . " - 未安装</p>";
                    }
                }
                // 文件权限检测
                $quanxian = substr(base_convert(fileperms(APP_ROOT . "/application/upload.php"), 10, 8), 3);
                if (IS_WIN) {
                    echo '
                    <p style="color:green">upload.php 文件可执行</p>
                    <p style="color:green">' . $config['path'] . ' 目录可读写</p>
                    ';
                }
                if (!IS_WIN) {
                    if ($quanxian !== '755' and !is_writable(APP_ROOT . $config['path'])) {
                        echo '
                        <p style="color:red">upload.php 文件不可执行</font>>
                        <p style="color:red">' . $config['path'] . ' 目录可读写</font>>
                        ';
                    } else {
                        echo '
                        <p style="color:green">upload.php 文件可执行</p>
                        <p style="color:green">' . $config['path'] . ' 目录可读写</p>
                        ';
                    }
                }
                ?>
            </div>
            <div class="modal-footer" style="text-align:left">
                <p class="text-primary">安装环境检测弹窗仅在第一次访问主页时展示,弹出后会在<code>config</code>目录下生成<code>EasyIamge.lock</code>文件,如需再次弹出请删除<code>EasyIamge.lock</code>文件。</p>
                <p class="text-primary">刷新或<kbd>ESC</kbd>关闭安装环境检测弹窗。</p>
            </div>
        </div>
    </div>
</div>
<script>
    $("#myModal-1").modal({
        keyboard: true,
        moveable: true,
        backdrop: "static", //点击空白处不关闭对话框
        show: true
    })
    alert("初次打开会检测环境配置,请仔细看!!");
</script>