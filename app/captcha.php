<?php
session_start(); //设置session

require __DIR__ . "/function.php";

//创建背景画布
$img_w = 305;
/*宽*/
$img_h = 54;

$img = imagecreatetruecolor($img_w, $img_h);
$bg_color = imagecolorallocate($img, 0xcc, 0xcc, 0xcc);
imagefill($img, 0, 0, $bg_color);
//生成验证码
$count = 4;
$code = "";
/*生成的验证码内容范围*/
$charset = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789";
$str_len = strlen($charset) - 1;
/*for循环打印输出验证码*/
for ($i = 0; $i < $count; $i++) {
    $code .= $charset[rand(0, $str_len)];
}

/*strtolower函数将输入的验证码自动转换为小写，用户不需要特意区分大小写*/
$_SESSION['code'] = strtolower($code);

/*字体大小*/
$font_size = 24;

/*字体文件位置*/
$fontfile = APP_ROOT . $config['textFont'];
/* floor() 修复php>8.0精度丢失 v2.8.4 */
for ($i = 0; $i < $count; $i++) {
    $font_color = imagecolorallocate($img, mt_rand(0, 100), mt_rand(0, 50), mt_rand(0, 255));
    imagettftext(
        $img,
        $font_size,
        floor(mt_rand(0, 20) - mt_rand(0, 25)),
        floor($img_w * $i / 4) + floor(mt_rand(0, 15)),
        floor(mt_rand($img_h / 2, $img_h)),
        $font_color,
        realpath($fontfile),
        $code[$i]
    );
}
/*背景干扰点点*/
for ($i = 0; $i < 300; $i++) {
    $color = imagecolorallocate($img, mt_rand(0, 255), mt_rand(0, 255), mt_rand(0, 255));
    imagesetpixel($img, mt_rand(0, $img_w), mt_rand(0, $img_h), $color);
}
/*干扰线条*/
for ($i = 0; $i < 5; $i++) {
    $color = imagecolorallocate($img, mt_rand(0, 255), mt_rand(0, 255), mt_rand(0, 255));
    imageline($img, mt_rand(0, $img_w), 0, mt_rand(0, $img_h), $img_h, $color);
    imagesetpixel($img, mt_rand(0, $img_w), mt_rand(0, $img_h), $color);
}

// 因为有些浏览器，访问的content-type会是文本型，所以我们需要设置成图片的格式类型
header("content-type:image/png");
imagepng($img); //建立png函数
imagedestroy($img);
