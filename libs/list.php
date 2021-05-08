<?php
require_once __DIR__ . '/header.php';

if ($config['showSwitch']) {
    $path = $_GET['date'] ??  date('Y/m/d/');
    $keyNum = $_GET['num'] ?? $config['listNumber'];

    $fileArr = getFile('..' . config_path($path));

    if ($fileArr[0]) {
        foreach ($fileArr as $key => $value) {
            if ($key < $keyNum) {
                $boxImg = $config['domain'] . config_path($path) . $value;
                echo '<div class="col-md-4 col-sm-6 col-lg-3"><div class="card listNum"><img data-toggle="lightbox"  data-image="' . $boxImg . '" src="' . $boxImg . '" 
				class="img-thumbnail" alt="简单图床-EasyImage" >
					<a href="' . $boxImg . '" target="_blank">		
						<div class="pull-left" style="margin-top:5px;">
						<span class="label label-success">打开原图</span>
						</div> 	
					</a>
					<a href="//' . $_SERVER['HTTP_HOST'] . '/api/del.php?url=' . $boxImg . '" target="_blank">
						<div class="pull-right" style="margin-top:5px;">
							<span class="label label-primary">删除图片</span>
						</div> 	
					</a>		 
					</div>
				</div>';
            }
        }
    } else {
        echo '<p class="text-danger" style="center">今天还没有上传的图片哟~~ <br />快来上传第一张吧~！</p>';
    }
} else {
    echo '<p class="text-danger" style="center">管理员关闭了预览哦~~</p>';
}

$todayUpload =  getFileNumber('../' . config_path());	// 今日上传数量
$yesterday =  date("Y/m/d/", strtotime("-1 day"));	// 昨日日期
$yesterdayUpload = getFileNumber('../' . $config['path'] . $yesterday);	// 昨日上传数量

$spaceUsed = getDistUsed(disk_total_space(__DIR__) - disk_free_space(__DIR__));		// 占用空间

$httpUrl = array(
    'date' => $path,
    'num' => getFileNumber('..' . config_path($path)),
);

echo '
<div class="col-md-12">
	<a href="list.php" ><span class="label label-success label-outline">	今日上传:' . $todayUpload . ' 张</span></a>
	<a href="list.php?date=' . $yesterday . '" ><span class="label label-warning  label-outline">	昨日上传:' . $yesterdayUpload . ' 张</span></a>
	<a href="list.php?' . http_build_query($httpUrl) . '" ><span class="label label-info  label-outline">	全部上传</span></a>
	<span class="label label-danger  label-outline">	存储占用:' . $spaceUsed . '</span>
</div>
	';
require_once './footer.php';
