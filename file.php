<?php
require __DIR__ . '/libs/function.php';
require APP_ROOT . '/libs/class.upload.php';
require APP_ROOT . '/libs/WaterMask.php';

// 检查是否开启api上传
if ($config['apiStatus']) {header('Access-Control-Allow-Origin:*');}

$handle = new upload($_FILES['file'], 'zh_CN');

if ($handle->uploaded) {
    // 允许上传的mime类型
    $handle->allowed = array('image/*');
    // 文件命名
    $handle->file_new_name_body = imgName();
    // 最大上传限制
    $handle->file_max_sizes = $config['maxSize'];
    // 最大宽度
    $handle->image_max_width = $config['maxWidth'];
    // 最大高度
    $handle->image_max_height = $config['maxHeight'];
    // 最小宽度
    $handle->image_min_width = $config['minWidth'];
    // 最小高度
    $handle->image_min_height = $config['minHeight'];
    // 转换图片为指定格式
    $handle->image_convert = $config['imgConvert'];

    //等比例缩减图片
    if ($config['imgRatio']) {
        $handle->image_x = $config['image_x'];
    }
    // 存储图片路径:images/201807/
    $handle->process(APP_ROOT . config_path());

    // 设置水印
    if ($config['watermark'] > 0) {
        switch ($config['watermark']) {
            case 1: // 文字水印 过滤gif
                if (isAnimatedGif($handle->file_src_pathname) === 0) {
                    $arr = [
                        #  水印图片路径（如果不存在将会被当成是字符串水印）
                         'res' => $config['waterText'],
                        #  水印显示位置
                         'pos' => $config['waterPosition'],
                        #  不指定name(会覆盖原图，也就是保存成thumb.jpeg)
                         'name' => $handle->file_dst_pathname,
                        'font' => $config['textFont'],
                        'fontSize' => $config['textSize'],
                        'color' => $config['textColor'],
                    ];
                    Imgs::setWater($handle->file_dst_pathname, $arr);
                }
                break;
            case 2: // 图片水印
                if (isAnimatedGif($handle->file_src_pathname) === 0) {
                    $arr = [
                        #  水印图片路径（如果不存在将会被当成是字符串水印）
                         'res' => $config['waterImg'],
                        #  水印显示位置
                         'pos' => $config['waterPosition'],
                        #  不指定name(会覆盖原图，也就是保存成thumb.jpeg)
                         'name' => $handle->file_dst_pathname,
                    ];
                    Imgs::setWater($handle->file_dst_pathname, $arr);
                }
                break;
            default:
                echo $handle->error;
                break;
        }
    }

    // 图片完整相对路径:images/201807/0ed7ccfd4dab9cbc.jpg
    if ($handle->processed) {
        header('Content-type:text/json');
        // 上传成功后返回json数据
        $reJson = array(
            "result" => 'success',
            "url" => $config['domain'] . config_path() . $handle->file_dst_name,
        );
        echo json_encode($reJson);
        $handle->clean();
    } else {
        // 上传错误 返回错误信息
        $reJson = array(
            "result" => 'failed',
            "message" => $handle->error,
        );
        echo json_encode($reJson,JSON_UNESCAPED_UNICODE);
    }
    
    // 利用 imagecreatefrom*压缩不太好用，不过可以预防病毒
    if ($config['imgcompress_percent'] > 0 && $handle->file_dst_name_ext != 'gif') {
        $percent = $config['imgcompress_percent']; //图片压缩比
        list($width, $height) = getimagesize($handle->file_dst_pathname); //获取原图尺寸
        //缩放尺寸
        $newwidth = $width * $percent;
        $newheight = $height * $percent;

        // 创建一个透明的背景图片
        $dst_im = imagecreatetruecolor($newwidth, $newheight);
        $bg = imagecolorallocatealpha($dst_im, 0, 0, 0, 127);
        imagefill($dst_im, 0, 0, $bg);
        imagesavealpha($dst_im, true);

        if ($handle->file_dst_name_ext === 'jpg') {
            $src_im = imagecreatefromjpeg($handle->file_dst_pathname);
            imagecopyresized($dst_im, $src_im, 0, 0, 0, 0, $newwidth, $newheight, $width, $height);
            imagejpeg($dst_im, $handle->file_dst_pathname); //输出压缩后的图片
            imagedestroy($dst_im);
            imagedestroy($src_im);
        } elseif ($handle->file_dst_name_ext === 'png') {
            $src_im = imagecreatefrompng($handle->file_dst_pathname);
            imagecopyresized($dst_im, $src_im, 0, 0, 0, 0, $newwidth, $newheight, $width, $height);
            imagepng($dst_im, $handle->file_dst_pathname); //输出压缩后的图片
            imagedestroy($dst_im);
            imagedestroy($src_im);
        } elseif ($handle->file_dst_name_ext === 'gif') {
            $src_im = imagecreatefromgif($handle->file_dst_pathname);
            imagecopyresized($dst_im, $src_im, 0, 0, 0, 0, $newwidth, $newheight, $width, $height);
            imagegif($dst_im, $handle->file_dst_pathname); //输出压缩后的图片
            imagedestroy($dst_im);
            imagedestroy($src_im);
        } elseif ($handle->file_dst_name_ext === 'wbmp') {
            $src_im = imagecreatefromwbmp($handle->file_dst_pathname);
            imagecopyresized($dst_im, $src_im, 0, 0, 0, 0, $newwidth, $newheight, $width, $height);
            imagewbmp($dst_im, $handle->file_dst_pathname); //输出压缩后的图片
            imagedestroy($dst_im);
            imagedestroy($src_im);
        }

    }

    unset($handle);
}