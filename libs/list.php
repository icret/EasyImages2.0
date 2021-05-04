<?php
require_once __DIR__ . '/header.php';

if ($config['showSwitch']) {

	$fileArr = getFile('..' . config_path());

	if (isset($_GET['num'])) {
		$keyNum = $_GET['num'];
	} else {
		$keyNum = $config['listNumber'];
	}

	if ($fileArr[0] == null) {
		echo '<p class="text-danger" style="center">今天还没有上传的图片哟~~ <br />快来上传第一张吧~！</p>';
	} else {
		foreach ($fileArr as $key => $value) {
			if ($key < $keyNum) {
				$boxImg = $config['domain'] . config_path() . $value;
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
	}
} else {
	echo '<p class="text-danger" style="center">管理员关闭了预览哦~~</p>';
}

echo '
<div class="col-md-12">
	<span class="label label-success label-outline">	今日上传:' . getFileNumber(__DIR__ . '/../' . config_path()) . ' 张</span>
	<span class="label label-warning  label-outline">	昨日上传:' . getFileNumber(__DIR__ . '/../' . $config['path'] . date("Y/m/d/", strtotime("-1 day"))) . ' 张</span>	
	<a href="?num=' . getFileNumber(__DIR__ . '/../' . config_path()) . '" ><span class="label label-info  label-outline">	今日全部上传</span></a>
	<span class="label label-danger  label-outline">	存储占用:' . getDistUsed(disk_total_space(__DIR__) - disk_free_space(__DIR__)) . '</span>
</div>
	';
require_once './footer.php';
