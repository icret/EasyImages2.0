<?php
/*
 * 统计中心
 */
require_once '../app/header.php';
require_once APP_ROOT . '/app/chart.php';

// 检测登录和是否开启统计
if (!$config['chart_on'] || !is_who_login('admin')) exit(header('Location: ' . $config['domain'] . '?hart#closed'));

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
    // 延时2s刷新
    Header("refresh:2;url=chart.php");
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
        border: 1px;
        margin: 0px 0px 10px 10px;
        width: 90px;
        height: 80px;
        text-align: center;
    }

    .autoshadow:hover {
        width: 105px;
        height: 80px;
        border: 2px;
        box-shadow: 3px 2px 3px 2px rgba(19, 17, 36, 0.5);
    }
</style>
<div class="row">
    <div class="clo-md-12">
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
    <div class="col-md-12 col-xs-12">
        <hr />
        <div class="col-md-6 col-xs-12">
            <span>硬盘使用量</span>
            <div id="Piedisk" style="width:350px;height: 350px;"></div>
        </div>
        <div class="col-md-6 col-xs-12">
            <div id="myPieChart" style="width:350px;height: 350px;"></div>
        </div>
    </div>
    <div class="col-md-12 col-xs-12">
        <hr />
        <span>最近30日上传趋势与空间占用</span>
        <div id="myLineChart" style="width: 100%;height: 300px;"></div>
    </div>
</div>
<script src="<?php static_cdn(); ?>/public/static/echarts/echarts.min.js"></script>
<script>
    // 基于准备好的dom，初始化echarts实例
    var myChart = echarts.init(document.getElementById('Piedisk'));

    // 指定图表的配置项和数据
    var Piedisk = {
        tooltip: {
            formatter: "{a} <br/>{b} : {c}%"
        },
        series: [{
            name: '硬盘使用量',
            type: 'gauge',
            detail: {
                valueAnimation: true,
                formatter: '{value}%'
            },
            data: [{
                value: <?php echo round((disk_total_space('.') - disk_free_space('.')) / disk_total_space('.') * 100, 2); ?>,
                name: '已使用'
            }]
        }]
    };
    // 使用刚指定的配置项和数据显示图表。
    myChart.setOption(Piedisk);

    // 文件统计-折线图
    var myChart = echarts.init(document.getElementById('myLineChart'));
    window.onresize = function() {
        myChart.resize();
    };

    var LineChart = {
        color: ['#EA644A', '#38B03F'],
        title: {
            // text: '最近30日上传趋势与空间占用'
        },
        tooltip: {
            trigger: 'axis',
            axisPointer: {
                type: 'cross',
                label: {
                    backgroundColor: '#6a7985'
                }
            }
        },
        legend: {
            data: ['上传', '占用']
        },
        toolbox: {
            feature: {
                saveAsImage: {}
            }
        },
        grid: {
            left: '3%',
            right: '4%',
            bottom: '3%',
            containLabel: true
        },
        xAxis: {
            type: 'category',
            boundaryGap: false,
            data: [<?php echo rtrim($chart_date, ','); ?>],
        },
        yAxis: {
            type: 'value',
            boundaryGap: [0, '100%']
        },
        dataZoom: [{ // 放大镜
                type: 'inside',
                start: 50,
                end: 100
            },
            {
                start: 0,
                end: 10
            }
        ],
        series: [{
                name: '占用',
                type: 'line',
                stack: 'x',
                smooth: true,
                areaStyle: {},
                emphasis: {
                    focus: 'series'
                },
                data: [<?php echo rtrim($chart_disk, ','); ?>],
            }, {
                name: '上传',
                type: 'line',
                stack: 'x',
                smooth: true,
                areaStyle: {},
                emphasis: {
                    focus: 'series'
                },
                data: [<?php echo rtrim($chart_number, ','); ?>],
            },

        ]
    };
    myChart.setOption(LineChart);

    // 硬盘统计-饼状图
    var myChart = echarts.init(document.getElementById('myPieChart'));

    myPieChart = {
        color: ['#38B03F', '#353535'],
        title: {
            // text: '硬盘使用统计:（GB）',
            left: 'center'
        },
        tooltip: {
            trigger: 'item',
            formatter: '{a} <br/>{b} : {c} GB ({d}%)'
        },
        legend: {
            orient: 'vertical',
            left: 'left',
            data: ['剩余空间', '已用空间']
        },
        series: [{
            name: '硬盘使用:',
            type: 'pie',
            radius: '55%',
            center: ['50%', '60%'],
            data: [{
                    value: <?php echo  round(disk_free_space('.') / 1024 / 1024 / 1024, 2); ?>,
                    name: '剩余空间',
                },
                {
                    value: <?php echo round((disk_total_space('.') - disk_free_space('.')) / 1024 / 1024 / 1024, 2); ?>,
                    name: '已用空间'
                },
            ],
            emphasis: {
                itemStyle: {
                    shadowBlur: 10,
                    shadowOffsetX: 0,
                    shadowColor: 'rgba(0, 0, 0, 0.5)'
                }
            }
        }]
    };

    // 使用刚指定的配置项和数据显示图表。
    myChart.setOption(myPieChart);

    let currentIndex = -1;

    setInterval(function() {
        var dataLen = myPieChart.series[0].data.length;
        // 取消之前高亮的图形
        myChart.dispatchAction({
            type: 'downplay',
            seriesIndex: 0,
            dataIndex: currentIndex
        });
        currentIndex = (currentIndex + 1) % dataLen;
        // 高亮当前图形
        myChart.dispatchAction({
            type: 'highlight',
            seriesIndex: 0,
            dataIndex: currentIndex
        });
        // 显示 tooltip
        myChart.dispatchAction({
            type: 'showTip',
            seriesIndex: 0,
            dataIndex: currentIndex
        });
    }, 1000);

    // 更改网页标题
    document.title = "图床统计信息 - <?php echo $config['title']; ?>"
</script>

<?php require_once APP_ROOT . '/app/footer.php';
