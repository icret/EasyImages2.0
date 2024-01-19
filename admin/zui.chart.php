<?php
/*
 * 统计中心
 */
require_once '../app/header.php';
require_once APP_ROOT . '/app/chart.php';

// 检测登录和是否开启统计
if (!$config['chart_on'] || !is_who_login('admin')) exit(header('Location: ' . $config['domain'] . '?hart#closed'));

// 检测登录
if (!is_who_login('admin')) {
    checkLogin();
    exit(require_once APP_ROOT . '/app/footer.php');
}
// 删除统计文件
if (isset($_POST['del_total'])) {
    @deldir($_POST['del_total']);
    echo '
		<script>
		new $.zui.Messager("重新统计成功!", {
			type: "success", // 定义颜色主题 
			icon: "ok-sign" // 定义消息图标
		}).show();
		</script>
		';
    // 延时1s刷新
    Header("refresh:1;url=chart.php");
}
// 统计图表
// array_reverse($arr,true) 倒叙数组并保持键值关系
$char_data = read_chart_total();
if (is_array($char_data)) {
    $chart_date =  '';
    foreach (array_reverse($char_data['date'], true) as $value) {
        $chart_date .= $value;
    }
    $chart_date = str_replace(date('Y/'), '', $chart_date); // 删除年份

    $chart_number = '';
    foreach (array_reverse($char_data['number'], true) as $value) {
        $chart_number .= $value;
    }

    $chart_disk = '';
    foreach (array_reverse($char_data['disk'], true) as $value) {
        $chart_disk .= $value;
    }
}
?>
<style>
    .autoshadow {
        box-shadow: 0 1px 4px rgba(0, 0, 0, 0.3), 0 0 40px rgba(0, 0, 0, 0.1);
        margin: 0px 0px 10px 10px;
        width: 90px;
        height: 80px;
        text-align: center;
    }
</style>
<div class="row">
    <div class="clo-md-12 col-xs-12">
        <div class="alert alert-warning">
            <form action="chart.php" method="post">
                <span>统计时间:<?php echo $char_data['total_time']; ?></span>
                <input type="hidden" name="del_total" value="<?php echo APP_ROOT . '/admin/logs/counts/'; ?>">
                <button class="btn btn-mini btn-primary"><i class="icon icon-spin icon-refresh"></i>重新统计</button>
            </form>
        </div>
    </div>
    <div class="col-md-12 col-xs-12">
        <div class="col-xs-3 alert  alert-success autoshadow">今日上传
            <hr />
            <?php echo  read_total_json('todayUpload'); ?> 张
        </div>
        <div class="col-xs-3 alert  alert-success autoshadow">昨日上传
            <hr />
            <?php echo  read_total_json('yestUpload'); ?> 张
        </div>
        <div class="col-xs-3 alert alert-primary autoshadow">
            累计上传
            <hr />
            <?php printf("%u 张", read_total_json('filenum')); ?>
        </div>

        <div class="col-xs-3 alert alert-primary autoshadow">
            缓存文件
            <hr />
            <?php printf("%u 张", getFileNumber(APP_ROOT . $config['path'] . 'cache/')); ?>
        </div>
        <div class="col-xs-3 alert alert-primary autoshadow">
            可疑图片
            <hr />
            <?php printf("%u 张", getFileNumber(APP_ROOT . $config['path'] . 'suspic/')); ?>
        </div>
        <div class="col-xs-3 alert alert-primary autoshadow">
            文件夹
            <hr />
            <?php printf("%d 个", read_total_json('dirnum')); ?>
        </div>
        <div class="col-xs-3 alert alert-primary autoshadow">
            总空间
            <hr />
            <?php echo getDistUsed(disk_total_space('.')); ?>
        </div>
        <div class="col-xs-3 alert alert-primary autoshadow">
            已用空间
            <hr />
            <?php echo getDistUsed(disk_total_space('.') - disk_free_space('.')); ?>
        </div>
        <div class="col-xs-3 alert alert-primary autoshadow">
            剩余空间
            <hr />
            <?php echo getDistUsed(disk_free_space('.')); ?>
        </div>
        <div class="col-xs-3 alert alert-primary autoshadow">
            图片占用
            <hr />
            <?php echo read_total_json('usage_space'); ?>
        </div>
        <div class="col-xs-3 alert alert-primary autoshadow">
            当前版本
            <hr />
            <?php echo APP_VERSION; ?>
        </div>
    </div>
    <div class="col-md-12  col-xs-12">
        <div class="col-md-6  col-xs-12">
            <h4>文件统计(张)</h4>
            <canvas id="myBarChart" width="960" height="400"></canvas>
        </div>
        <div class="col-md-6  col-xs-12">
            <h4 class=" col-md-offset-2">硬盘统计:(GB)</h4>
            <canvas id="diskPieChart" width="960" height="400"></canvas>
        </div>
    </div>
    <div class="col-sm-12  col-xs-12" style="text-align: center;">
        <hr />
        <h4>最近30上传趋势与空间占用(上传/张 占用/MB)</h4>
        <h4 class="text-danger hidden-lg">手机请启用横屏浏览</h4>
        <canvas id="myChart" width="1080" height="200"></canvas>
    </div>
</div>
<script type="application/javascript" src="<?php static_cdn(); ?>/public/static/zui/lib/chart/zui.chart.min.js"></script>
<script>
    // 文件统计-柱状图
    var data = {
        labels: ["今日上传", "昨日上传", "累计上传", "缓存文件", "可疑图片", "已创建文件夹"],
        datasets: [{
            label: "文件统计",
            color: 'green',
            data: [<?php echo str_replace('"', '', $char_data['number'][0] .  $char_data['number'][1]  . read_total_json('filenum') . ',' . getFileNumber(APP_ROOT . $config['path'] . 'cache/') . ',' . getFileNumber(APP_ROOT . $config['path'] . 'suspic/') . ',' . read_total_json('dirnum')); ?>]
        }]
    };

    var options = {
        responsive: true,

        scaleShowLabels: true, // 展示标签
        scaleLabelPlacement: "outside",
        // Interpolated JS string - 坐标刻度格式化文本
        scaleLabel: "<%=value%>",
        scaleShowLabels: true,
        //Boolean - 是否启用缩放动画
        animateScale: true,
        // Number - 自定义坐标网格时起始刻度值
        scaleStartValue: 0,
        // Boolean - 是否启用响应式设计，在窗口尺寸变化时进行重绘
        responsive: true,
    };
    var myBarChart = $('#myBarChart').barChart(data, options);

    // 最近30上传趋势与空间占用-折线图    
    var ctx = $("#myChart").get(0).getContext("2d");

    // 使用$.zui.Chart构造Chart实例
    var myNewChart = new $.zui.Chart(ctx);

    var data = {
        // labels 数据包含依次在X轴上显示的文本标签
        labels: [<?php echo rtrim($chart_date, ','); ?>],
        datasets: [{
            // 数据集名称，会在图例中显示
            label: "上传",
            color: "green",
            // 数据集
            data: [<?php echo rtrim($chart_number, ','); ?>]
        }, {
            label: "占用",
            color: "red",
            data: [<?php echo rtrim($chart_disk, ','); ?>]
        }]
    };

    var options = {
        //Boolean - 是否启用缩放动画
        animateScale: true,
        // Number - 自定义坐标网格时起始刻度值
        scaleStartValue: 0,
        // Boolean - 是否启用响应式设计，在窗口尺寸变化时进行重绘
        responsive: true,
    };

    var myLineChart = $("#myChart").lineChart(data, options);

    // 硬盘统计-饼状图
    var data = [{
        value: <?php echo round(disk_free_space('.') / 1024 / 1024 / 1024, 2); ?>,
        color: "green", // 使用颜色名称
        label: "剩余空间"
    }, {
        value: <?php echo round((disk_total_space('.') - disk_free_space('.')) / 1024 / 1024 / 1024, 2); ?>,

        color: "red", // 自定义颜色
        // highlight: "#FF5A5E", // 自定义高亮颜色
        label: "已用空间"
    }];
    var options = {
        scaleShowLabels: true, // 展示标签
        scaleLabelPlacement: "outside",
        // Interpolated JS string - 坐标刻度格式化文本
        scaleLabel: "<%=value%>GB",
        scaleShowLabels: true,
        //Boolean - 是否启用缩放动画
        animateScale: true,
        // Number - 自定义坐标网格时起始刻度值
        scaleStartValue: 0,
        // Boolean - 是否启用响应式设计，在窗口尺寸变化时进行重绘
        responsive: true,
    };

    // 创建饼图
    var myPieChart = $("#diskPieChart").pieChart(data, options);

    // Title
    document.title = "图床统计信息 - <?php echo $config['title']; ?>";
</script>

<?php require_once APP_ROOT . '/app/footer.php';
