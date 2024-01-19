<?php

/** 禁止直接访问 */
defined('APP_ROOT') ?: exit;

// 跳转安装
if (!is_file(APP_ROOT . '/config/install.lock') and is_file(APP_ROOT . '/install/install.php')) {
    exit('<script type="text/javascript">window.location.href="' . get_whole_url('/') . '/install/index.php"</script>');
}
/** 检测弹窗 */
if (is_file(APP_ROOT . '/config/EasyIamge.lock')) return; // 查询锁定弹窗文件是否存在
file_put_contents(APP_ROOT . '/config/EasyIamge.lock', '安装环境检测锁定文件,如需再次展示请删除此文件!', FILE_APPEND | LOCK_EX);
?>
<div class="modal fade" id="myModal-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">x</span><span class="sr-only">关闭</span></button>
                <h4 class="modal-title">
                    <i class="icon icon-heart"> </i><a href="https://github.com/icret/EasyImages2.0" target="_blank">简单图床-EasyImage2.0</a> 安装环境检测
                </h4>
            </div>
            <div class="modal-body">
                <h4>说明:</h4>
                <h5>1. 建议使用<span class="text-red">PHP7.0</span>及以上版本;</h5>
                <h5>2. 上传失败大部分是由于<span class="text-red">upload_max_filesize、post_max_size、文件权限</span>设置不正确;</h5>
                <h5>3. 主要用到<span class="text-red">Fileinfo、GD、Openssl</span>扩展,如果缺失会导致无法访问管理面板以及上传/删除图片。</h5>
                <hr />
                <h4>EasyImage2.0 基础检测:</h4>
                <p>当前PHP版本:<sapn class="text-green"><?php echo phpversion() ?></sapn>
                </p>
                <p>PHP最大上传: <sapn class="text-green"><?php echo ini_get('upload_max_filesize'); ?></sapn>
                </p>
                <p>POST最大上传: <sapn class="text-green"><?php echo ini_get('post_max_size'); ?></sapn>
                </p>
                <?php
                // 扩展检测 取消检测imagick扩展
                $expand = array('fileinfo', 'openssl', 'gd');
                foreach ($expand as $val) {
                    if (extension_loaded($val)) {
                        echo "<p class='text-green'> $val - 已安装</p>";
                    } else {
                        echo "<script language='javascript'>alert($val - 未安装)</script>";
                        echo "<p class='text-red'> $val- 未安装</p>";
                    }
                }
                // 文件权限检测
                $quanxian = substr(base_convert(fileperms(APP_ROOT . "/app/upload.php"), 10, 8), 3);
                if (IS_WIN) {
                    echo '
                    <p class="text-green">upload.php 文件可执行</p>
                    <p class="text-green">' . $config['path'] . ' 目录可读写</p>
                    ';
                }
                if (!IS_WIN) {
                    if ($quanxian !== '755' and !is_writable(APP_ROOT . $config['path'])) {
                        echo '
                        <p class="text-red">upload.php 文件不可执行</font>>
                        <p class="text-red">' . $config['path'] . ' 目录可读写</font>>
                        ';
                    } else {
                        echo '
                        <p class="text-green">upload.php 文件可执行</p>
                        <p class="text-green">' . $config['path'] . ' 目录可读写</p>
                        ';
                    }
                }

                echo '<p class="text-green">当前支持图片格式:';
                foreach (gd_info() as $k => $v) {
                    $k = str_ireplace(array('Support', 'Create', 'Read', 'Linkage', 'GD Version', 'FreeType', 'Font', 'JIS-mapped', 'Japanese'), '', $k);
                    if ($v) {
                        printf("%s", $k);
                    } else {
                        printf("%s", $k);
                    }
                }
                echo '</p>';
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
    if (confirm("初次打开会检测环境配置,是否需要查看?")) {
        $("#myModal-1").modal({
            keyboard: true,
            moveable: true,
            backdrop: "static", //点击空白处不关闭对话框
            show: true
        })
    } else {
        new $.zui.Messager("取消查看 - 如需再次展示请删除config/EasyIamge.lock文件", {
            type: "info", // 定义颜色主题 
            time: 6000,
            icon: "info-sign" // 定义消息图标
        }).show();
    }
    console.log('EasyIamge.lock 生成完毕!')
</script>