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
    unset($handle);
}