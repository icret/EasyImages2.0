<?php

/**
 * 读取上传日志
 */

require_once __DIR__ . '/function.php';

// 非管理员不可访问!
if (!is_who_login('admin')) {
    exit;
}

// 禁止直接访问
if (empty($_POST['pass']) || $_POST['pass'] !== md5($config['password'] . date('YMDH'))) exit('No permission!');

require_once APP_ROOT . '/application/header.php';

if (isset($_POST['logDate'])) {
    $logFile = APP_ROOT . '/admin/logs/upload/' . $_POST['logDate'] . '.php';
} else {
    $logFile = APP_ROOT . '/admin/logs/upload/' . date('Y-m') . '.php';
}

try {
    if (is_file($logFile)) {
        require_once $logFile;
    } else {
        throw new Exception('<div class="alert alert-info">日志文件不存在!<div>');
    }
    if (empty($logs)) {
        throw new Exception('<div class="alert alert-info">没有上传日志!<div>');
    }
} catch (Exception $e) {
    echo $e->getMessage();
    require_once APP_ROOT . '/application/footer.php';
    exit;
}
?>
<div class="col-md-12">
    <div id="logs" class="datagrid table-bordered">
        <div class="input-control search-box search-box-circle has-icon-left has-icon-right" id="searchboxExample2" style="margin-bottom: 10px;">
            <input id="inputSearchExample2" type="search" class="form-control search-input input-sm" placeholder="日志搜索...">
            <label for="inputSearchExample2" class="input-control-icon-left search-icon"><i class="icon icon-search"></i></label>
            <a href="#" class="input-control-icon-right search-clear-btn"><i class="icon icon-remove"></i></a>
        </div>
        <div class="datagrid-container"></div>
    </div>
    <p class="text-muted" style="font-size:10px;"><i class="modal-icon icon-info"></i> 建议使用分辨率 ≥ 1366*768px; 当前日志文件: <?php echo $logFile; ?></p>
</div>
<link href="<?php static_cdn(); ?>/public/static/zui/lib/datagrid/zui.datagrid.min.css" rel="stylesheet">
<script src="<?php static_cdn(); ?>/public/static/zui/lib/datagrid/zui.datagrid.min.js"></script>
<script>
    // 更改页面布局
    $(document).ready(function() {
        $("body").removeClass("container").addClass("container-fixed-lg");
    });

    // logs 数据表格
    $('#logs').datagrid({
        dataSource: {
            height: 800,
            cols: [{
                    label: '当前名称',
                    name: 'orgin',
                },
                {
                    label: '源文件名',
                    name: 'source',
                    html: true,
                },
                {
                    label: '上传时间',
                    name: 'date',
                    html: true,
                },
                {
                    label: '上传IP及端口',
                    name: 'ip',
                    html: true,
                },
                {
                    label: 'User-Agent',
                    name: 'user_agent',
                    html: true,
                },
                {
                    label: '路径',
                    name: 'path',
                    html: true,
                },
                {
                    label: '大小',
                    name: 'size',
                    html: true,
                },
                {
                    label: 'MD5',
                    name: 'md5',
                    html: true,
                },
                {
                    label: '鉴黄状态',
                    name: 'checkImg',
                    html: true,
                },
                {
                    label: '来源',
                    name: 'from',
                    html: true,
                },
                {
                    label: '管理',
                    name: 'manage',
                    html: true,
                },
            ],
            array: [
                <?php foreach ($logs as $k => $v) : ?> {
                        orgin: '<?php echo $k; ?>',
                        source: '<input class="form-control input-sm" type="text" value="<?php echo $v['source']; ?>" readonly>',
                        date: '<?php echo $v['date']; ?>',
                        ip: '<a href="http://ip.tool.chinaz.com/<?php echo $v['ip']; ?>" target="_blank"><?php echo $v['ip'] . ':' . $v['port']; ?></a>',
                        // 备用 ip: '<a href="https://www.ip138.com/iplookup.asp?ip=< ?php echo $v['ip']; ?>&action=2" target="_blank">< ?php echo $v['ip'] . ':' . $v['port']; ?></a>',
                        user_agent: '<input class="form-control input-sm" type="text" value="<?php echo $v['user_agent']; ?>" readonly>',
                        path: '<input class="form-control input-sm" type="text" value="<?php echo $v['path']; ?>" readonly>',
                        size: '<?php echo $v['size']; ?>',
                        md5: '<input class="form-control input-sm" type="text" value="<?php echo $v['md5']; ?>" readonly>',
                        checkImg: '<?php echo $v['checkImg']; ?>',
                        from: '<?php echo $v['from']; ?>',
                        manage: "<div class='btn-group'><a href='<?php echo $config['domain'] . $v['path']; ?>' target='_blank' class='btn btn-mini btn-success'>查看</a> <a href='/application/info.php?img=<?php echo $v['path']; ?>' target='_blank' class='btn btn-mini'>信息</a> <a href='/application/del.php?recycle_url=<?php echo $v['path']; ?>' target='_blank' class='btn btn-mini btn-info'>回收</a> <a href='/application/del.php?url=<?php echo $v['path']; ?>' target='_blank' class='btn btn-mini btn-danger'>删除</a></div>",
                    },
                <?php endforeach; ?>
            ]
        },
        sortable: true,
        hoverCell: true,
        showRowIndex: true,
        responsive: true,
        height: 666,
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
    var logMyDataGrid = $('#logs').data('zui.datagrid');

    // var myDate = new Date();
    // logMyDataGrid.showMessage(myDate.getFullYear() + '年' + (myDate.getMonth() + 1) + '月上传日志已加载完毕...... ', 'primary', 2500);

    logMyDataGrid.showMessage('上传日志已加载完毕...... ', 'primary', 2500);

    // 按照 `name` 列降序排序
    logMyDataGrid.sortBy('date', 'desc');
</script>
<?php
require_once APP_ROOT . '/application/footer.php';
