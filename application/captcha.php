<?php
session_start(); //设置session

$width = 150; //设置图片宽为300像素
$height = 40; //设置图片高为40像素

$image = imagecreatetruecolor($width, $height); //设置验证码大小的函数
$bgcolor = imagecolorallocate($image, 255, 255, 255); //验证码颜色RGB为(255,255,255)#ffffff
imagefill($image, 0, 0, $bgcolor); //区域填充

$cap_code = "";
for($i=0;$i<4;$i++){
	$fontsize = 12; //设置字体大小
	$fontcolor = imagecolorallocate($image, rand(0,120), rand(0,120), rand(0,120));
	//数字越大，颜色越浅，这里是深颜色0-120
	$data = 'QWERTYUIOPASDFGHJKLZXCVBNMqwertyuiopasdfghjklzxcvbnm1234567890'; //添加字符串
	$fontcontent = substr($data, rand(0,strlen($data)),1); //去除值，字符串截取方法
	$cap_code.=$fontcontent; //.=连续定义变量
	
	$x = ($i*150/4)+rand(5,10);
	$y = rand(5,10);
	//设置坐标
	
	imagestring($image, $fontsize, $x, $y, $fontcontent, $fontcolor);
}

$_SESSION['code'] = $cap_code; //存到session

//设置干扰元素，设置雪花点
for($i=0;$i<300;$i++){
	$inputcolor = imagecolorallocate($image, rand(50,200), rand(20,200), rand(50,200));
	//设置颜色，20-200颜色比数字浅，不干扰阅读
	imagesetpixel($image, rand(1,149), rand(1,39), $inputcolor);
	//画一个单一像素的元素
}

//增加干扰元素，设置横线(先设置线的颜色，在设置横线)
for ($i=0;$i<4;$i++) { 
	$linecolor = imagecolorallocate($image, rand(20,220), rand(20,220),rand(20,220));
	//设置线的颜色

	imageline($image, rand(1,149), rand(1,39), rand(1,299), rand(1,149), $linecolor);	

}

//因为有些浏览器，访问的content-type会是文本型，所以我们需要设置成图片的格式类型
header('Content-Type:image/png');

imagepng($image); //建立png函数
imagedestroy($image);