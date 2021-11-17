
<?php
require __DIR__ . '/application/function.php';
require APP_ROOT . '/application/class.upload.php';
require APP_ROOT . '/application/WaterMask.php';

$handle = new Upload($_FILES['file'], 'zh_CN');

if ($handle->uploaded) {
    // 允许上传的mime类型
    $handle->allowed = array('image/*');
    // 文件命名
    $handle->file_new_name_body = imgName();
    // 最大上传限制
    //$handle->file_max_sizes = $config['maxSize'];
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

    /* 等比例缩减图片 放到前端了
    if ($config['imgRatio']) {
        $handle->image_resize = true;
        $handle->image_x = $config['image_x'];
        $handle->image_y = $config['image_y'];
    }
    */

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
                        'font' => APP_ROOT . $config['textFont'],
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
                        'res' => APP_ROOT . $config['waterImg'],
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

    // 图片完整相对路径:/i/2021/05/03/k88e7p.jpg
    if ($handle->processed) {
        header('Content-type:text/json');
        // 上传成功后返回json数据
        $imageUrl = $config['imgurl'] . config_path() . $handle->file_dst_name;

        // 判断PHP版本启用删除
        $ver = substr(PHP_VERSION, 0, 3);
        if ($ver >= '7.0') {
            $delUrl = $config['domain']  . '/application/del.php?hash=' . urlHash(config_path() . $handle->file_dst_name, 0);
        } else {
            $delUrl = 'PHP≥7.0 才能启用删除！';
        }

        // 创建缩略图
        @creat_cache_images($handle->file_dst_name);

        $reJson = array(
            "result" => 'success',
            "url" => $imageUrl,
            "del" =>  $delUrl,
        );
        echo json_encode($reJson);
        $handle->clean();
    } else {
        // 上传错误 返回错误信息
        $reJson = array(
            "result" => 'failed',
            "message" => $handle->error,
        );
        echo json_encode($reJson, JSON_UNESCAPED_UNICODE);
    }

    // 压缩图片 后压缩模式，不影响前台输出速度
    if ($config['compress']) {
        if (!isAnimatedGif($handle->file_dst_pathname)) {
            require 'application/compress/Imagick/class.Imgcompress.php';
            $img = new Imgcompress($handle->file_dst_pathname, 1);
            $img->compressImg($handle->file_dst_pathname);
            // 释放
            ob_flush();
            flush();
        }
    }
    // 上传日志控制
    if ($config['upload_logs'] == true) {
        require_once APP_ROOT . '/application/logs-write.php';
        @write_log(config_path() . $handle->file_dst_name,md5_file(APP_ROOT.config_path() . $handle->file_dst_name));
    }

    unset($handle);

    // 图片违规检查
    if ($config['checkImg']) {
        require_once APP_ROOT . '/config/api_key.php';
        @checkImg($imageUrl);
    }
}
