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
if (empty($_GET['session']) || $_GET['session'] !== md5($config['password'] . date('YMDH'))) exit;

require APP_ROOT . '/admin/logs/upload/' . date('Y-m') . '.php';
?>
<div id="logs" class="datagrid table-bordered">
    <div class="input-control search-box search-box-circle has-icon-left has-icon-right" id="searchboxExample2" style="margin-bottom: 10px;">
        <input id="inputSearchExample2" type="search" class="form-control search-input input-sm" placeholder="日志搜索...">
        <label for="inputSearchExample2" class="input-control-icon-left search-icon"><i class="icon icon-search"></i></label>
        <a href="#" class="input-control-icon-right search-clear-btn"><i class="icon icon-remove"></i></a>
    </div>
    <div class="datagrid-container"></div>
</div>
<p class="text-muted" style="font-size:10px;">当前日志路径: /admin/logs/upload/<?php echo date('Y-m'); ?>.php</p>
<script>
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
                        manage: "<a href='<?php echo $config['domain'] . $v['path']; ?>' target='_blank' class='btn btn-mini btn-success'>查看</a> <a href='/application/del.php?recycle_url=<?php echo $v['path']; ?>' target='_blank' class='btn btn-mini btn-info'>回收</a> <a href='/application/del.php?url=<?php echo $v['path']; ?>' target='_blank' class='btn btn-mini btn-danger'>删除</a> ",
                    },
                <?php endforeach; ?>
            ]
        },
        sortable: true,
        hoverCell: true,
        showRowIndex: true,
        responsive: true,
        height: 640,
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

    var myDate = new Date();
    
    logMyDataGrid.showMessage(myDate.getFullYear() + '年' + (myDate.getMonth() + 1) + '月上传日志已加载...... ', 'primary', 2500);

    // 按照 `name` 列降序排序
    logMyDataGrid.sortBy('date', 'desc');
</script>