<?php
include_once __DIR__ . "/header.php";
?>
<link rel="stylesheet" href="<?php static_cdn(); ?>/public/static/EasyImage.css">
<link rel="stylesheet" href="<?php static_cdn(); ?>/public/static/viewjs/viewer.min.css">
<div class="row">
    <div class="col-md-12">
        <ul id="viewjs">
            <div class="cards listNum">
                <!-- 历史上传列表 -->
            </div>
        </ul>
    </div>
</div>
<div class="col-md-12 history_clear">
</div>
<script type="application/javascript" src="<?php static_cdn(); ?>/public/static/EasyImage.js"></script>
<script type="application/javascript" src="<?php static_cdn(); ?>/public/static/lazyload/lazyload.min.js"></script>
<script type="application/javascript" src="<?php static_cdn(); ?>/public/static/viewjs/viewer.min.js"></script>
<script type="application/javascript" src="<?php static_cdn(); ?>/public/static/zui/lib/clipboard/clipboard.min.js"></script>
<script>
    if ($.zui.store.length() > 1) {
        console.log('saved: ' + $.zui.store.length()) // 获取总数
        $.zui.store.forEach(function(key, value) { // 遍历所有本地存储的条目
            console.log('url list: ' + value['url']) // 获取所有链接
            if (value['url'] !== undefined) {
                let v_url = parseURL(value['url']); // 获取链接路径 console.log(parseURL(value['url']).path);
                $('.listNum').append('<div class="col-md-4 col-sm-6 col-lg-3"><div class="card"><li><img src="../public/images/loading.svg" data-image="' + value['thumb'] + '" data-original="" alt="简单图床-EasyImage"></li><div class="bottom-bar"><a href="' + value['url'] + '" target="_blank"><i class="icon icon-picture" data-toggle="tooltip" title="打开" style="margin-left:10px;"></i></a><a href="#" class="copy" data-clipboard-text="' + value['url'] + '" data-toggle="tooltip" title="复制链接" style="margin-left:10px;"><i class="icon icon-copy"></i></a><a href="info.php?history=' + v_url.path + '" data-toggle="tooltip" title="详细信息" target="_blank" style="margin-left:10px;"><i class="icon icon-info-sign"></i></a><a href="down.php?history=' + v_url.path + '" data-toggle="tooltip" title="下载文件" target="_blank" style="margin-left:10px;"><i class="icon icon-cloud-download"></i></a><a href="#" data-toggle="tooltip" title="删除记录" class="Remove"id="' + value['srcName'] + '" style="margin-left:10px;"><i class="icon icon-remove-sign"></i></a></a><a href="' + value['del'] + '" target="_blank"><i class="icon icon-trash" data-toggle="tooltip" title="删除文件" style="margin-left:10px;"></i></a><a href="#" data-toggle="tooltip" title="源文件名" class="copy text-ellipsis" data-clipboard-text="' + value['srcName'] + '" style="margin-left:10px;">' + value['srcName'] + '</a></div></div></div>')
            }
        })
        $('.history_clear').append('<h3 class="header-dividing" style="text-align: center;" data-toggle="tooltip" title="非上传记录|清空缓存|浏览器版本低不显示<br/>点击清空历史上传记录"><button class="btn btn-mini btn-primary" type="button"><i class="icon icon-eye-open"></i> 历史上传记录</button></h3>');
    } else {
        $('.listNum').append('<h2 class="alert alert-danger">上传历史记录不存在~~ <br /><small>非上传记录 | 清空缓存 | 浏览器版本低不显示~!</small></h2>');
    };

    // 删除指定存储条目
    $('.Remove').on('click', function() {

        let Remove = $('.Remove').attr("id");
        $.zui.store.remove(Remove); // 删除指定存储条目

        new $.zui.Messager('已删除 ' + Remove + ' 上传记录', {
            type: "success", // 定义颜色主题 
            icon: "ok-sign" // 定义消息图标
        }).show();

        setTimeout(location.reload.bind(location), 2000); // 延迟2秒刷新
    })

    // 清空所有本地存储的条目
    $('button').on('click', function() {
        new $.zui.Messager('已清空' + $.zui.store.length() + "条历史记录", {
            type: "success", // 定义颜色主题 
            icon: "ok-sign" // 定义消息图标
        }).show();

        $.zui.store.clear(); // 清空上传记录
        setTimeout(location.reload.bind(location), 2000); // 延迟2秒刷新
    })

    // 复制 文件名/URL
    var clipboard = new Clipboard('.copy');
    clipboard.on('success', function(e) {
        new $.zui.Messager("复制成功", {
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

    // viewjs
    new Viewer(document.getElementById('viewjs'), {
        url: 'data-original',
    });

    //懒加载
    var lazy = new Lazy({
        onload: function(elem) {
            console.log(elem)
        },
        delay: 300
    })

    // 更改网页标题
    document.title = "上传记录 - <?php echo $config['title']; ?>"
</script>
<?php
/** 引入底部 */
require_once __DIR__ . '/footer.php';
