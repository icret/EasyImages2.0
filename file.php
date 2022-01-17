
<?php
require __DIR__ . '/application/function.php';
require APP_ROOT . '/application/class.upload.php';
require APP_ROOT . '/application/WaterMask.php';

$handle = new Upload($_FILES['file'], 'zh_CN');

if ($handle->uploaded) {
    // 允许上传的mime类型
    $handle->allowed = array('image/*');
    // 文件命名
    $handle->file_new_name_body = imgName($handle->file_src_name_body);
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

    /*
    // 创建缩略图 开启后会个别返回文件失败，暂时没找到替代方案，如果启用此项目，需要将list.php中的get_online_thumbnail改成return_thumbnail_images函数
    if ($config['thumbnail']) {
        @creat_thumbnail_images($handle->file_dst_name);
    }
    */

    // 图片完整相对路径:/i/2021/05/03/k88e7p.jpg
    if ($handle->processed) {
        header('Content-type:text/json');
        // 上传成功后返回json数据
        $imageUrl = $config['imgurl'] . config_path() . $handle->file_dst_name;

        // 判断PHP版本启用删除
        if (PHP_VERSION >= '7') {
            $delUrl = $config['domain']  . '/application/del.php?hash=' . urlHash(config_path() . $handle->file_dst_name, 0);
        } else {
            $delUrl = "Sever PHP version lower 7.0 and does't support deletion";
        }

        $reJson = array(
            "result" => "success",
            "code"   => 200,
            "url"    => $imageUrl,
            "del"    => $delUrl,
        );
        echo json_encode($reJson);
        $handle->clean();
    } else {
        // 上传错误 code:403 客户端文件有问题
        $reJson = array(
            "result"    =>  "failed",
            "code"      =>  403,
            "message"   =>  $handle->error,
            //"log"       =>  $handle->log,
        );
        unset($handle);
        header('Content-Type:application/json; charset=utf-8');
        exit(json_encode($reJson, JSON_UNESCAPED_UNICODE));
    }

    // 上传日志控制
    if ($config['upload_logs']) {
        require_once APP_ROOT . '/application/logs-write.php';
        @write_log(config_path() . $handle->file_dst_name, $handle->file_src_name, $handle->file_dst_pathname, $handle->file_src_size);
    }

    unset($handle);
}
