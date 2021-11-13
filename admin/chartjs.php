<?php
/*
 * 统计中心
 */
require_once '../application/header.php';
require_once APP_ROOT . '/config/api_key.php';
require_once APP_ROOT . '/api/application/apiFunction.php';
require_once APP_ROOT . '/application/chart.php';

// 检测登录
if (!is_online()) {
    checkLogin();
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

<div class="row">
    <style>
        .autoshadow {
            box-shadow: 0 1px 4px rgba(0, 0, 0, 0.3), 0 0 40px rgba(0, 0, 0, 0.1);
            margin: 0px 0px 10px 10px;
            width: 130px;
            height: 80px;
            text-align: center;
        }
    </style>
    <div class="row">

        <div class="clo-md-12">
            <div class="alert alert-warning">统计时间：<?php echo $char_data['total_time']; ?></div>
        </div>

        <div class="col-md-12">
            <div class="col-md-2 alert  alert-success autoshadow">今日上传
                <hr />
                <?php printf("%u 张", preg_replace('/\D/s', '', $char_data['number'][0])); ?>
            </div>
            <div class="col-md-2 alert  alert-success autoshadow">昨日上传
                <hr />
                <?php printf("%u 张", preg_replace('/\D/s', '', $char_data['number'][1])); ?>
            </div>
            <div class="col-md-2 alert alert-primary autoshadow">
                累计上传
                <hr />
                <?php printf("%u 张", read_total_json('filenum')); ?>
            </div>

            <div class="col-md-2 alert alert-primary autoshadow">
                缓存文件
                <hr />
                <?php printf("%u 张", getFileNumber(APP_ROOT . $config['path'] . 'cache/')); ?>
            </div>
            <div class="col-md-2 alert alert-primary autoshadow">
                可疑图片
                <hr />
                <?php printf("%u 张", getFileNumber(APP_ROOT . $config['path'] . 'suspic/')); ?>
            </div>
            <div class="col-md-2 alert alert-primary autoshadow">
                文件夹
                <hr />
                <?php printf("%d 个", read_total_json('dirnum')); ?>
            </div>
            <div class="col-md-2 alert alert-primary autoshadow">
                占用存储
                <hr />
                <?php echo getDistUsed(disk_total_space('.') - disk_free_space('.')); ?>
            </div>
            <div class="col-md-2 alert alert-primary autoshadow">
                剩余空间
                <hr />
                <?php echo getDistUsed(disk_free_space('.')); ?>
            </div>
        </div>
        <div class="col-md-12">
            <h4 style="text-align: center;">最近30上传趋势与空间占用（上传/张 占用/MB）</h4>
            <canvas id="linChart-30"></canvas>
        </div>
    </div>


    <script src="https://cdn.bootcdn.net/ajax/libs/Chart.js/3.5.0/chart.min.js"></script>
    <script>
        // 最近30上传趋势与空间占用-折线图
        var ctx = document.getElementById("linChart-30").getContext('2d');
        var myChart = new Chart(ctx, {
            "type": "line",
            "data": {

                "labels": [<?php echo rtrim($chart_date, ','); ?>],
                "datasets": [{
                        "label": "上传数量",
                        "data": [<?php echo rtrim($chart_number, ','); ?>],
                        "borderColor": "#4BC0C0",
                        "lineTension": 0.5,
                        "fill": true,
                        "backgroundColor": "rgba(120,116,126,0.2)", //背景色

                    },
                    {
                        "label": "占用硬盘", // 名称
                        "data": [<?php echo rtrim($chart_disk, ','); ?>], // 数据集
                        "borderColor": "rgb(255, 99, 132)", // 线条颜色
                        "lineTension": 0.5, // 曲度
                        "borderDash": [5, 5],
                        "fill": true,
                        "backgroundColor": "rgba(255, 99, 132, 0.2)", // 背景色
                    },

                ]

            },
            options: {}

        });
    </script>

    <?php require_once APP_ROOT . '/application/footer.php';
