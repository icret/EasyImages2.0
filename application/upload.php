
<?php
require __DIR__ . '/function.php';
require __DIR__ . '/class.upload.php';

// 检查登录
if ($config['mustLogin']) {
    checkLogin();
}

// 黑/白IP名单上传
if ($config['check_ip']) {
    if (checkIP(null, $config['check_ip_list'], $config['check_ip_model'])) {
        // 上传错误 code:403 未授权IP
        exit(json_encode(array(
            "result"    =>  "failed",
            "code"      =>  403,
            "message"   =>  "黑名单内或白名单外用户不允许上传",
        )));
    }
}

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

        // 关闭上传后显示加密删除链接
        if ($config['show_user_hash_del']) {
            // 判断PHP版本启用删除
            if (PHP_VERSION >= '7') {
                $delUrl = $config['domain']  . '/application/del.php?hash=' . urlHash(config_path() . $handle->file_dst_name, 0);
            } else {
                $delUrl = "Sever PHP version lower 7.0";
            }
        } else {
            $delUrl = "Admin closed delete";
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
        // 上传错误 code:400 客户端文件有问题
        $reJson = array(
            "result"    =>  "failed",
            "code"      =>  400,
            "message"   =>  $handle->error,
        );
        unset($handle);
        header('Content-Type:application/json; charset=utf-8');
        unset($handle);
        exit(json_encode($reJson, JSON_UNESCAPED_UNICODE));
    }


    /** 后续处理 */
    require __DIR__ . '/process.php';
    /*
    require __DIR__ . '/FsockService.php';

    // 使用fosksock异步申请鉴黄
    if ($config['checkImg']) {
        $process = array(
            'auth' => md5($config['domain'] . $config['password']),
            'img' => $imageUrl
        );
        @request_asynchronous('/application/process.php', 'GET', $process, $config['domain']);
    }
    */
    // 普通模式鉴黄
    @process_checkImg($imageUrl);

    // 使用fastcgi_finish_request操作
    if (function_exists('fastcgi_finish_request')) {
        fastcgi_finish_request();
        // 日志
        if ($config['upload_logs']) @write_log(config_path() . $handle->file_dst_name, $handle->file_src_name, $handle->file_dst_pathname, $handle->file_src_size);
        // 水印        
        @water($handle->file_dst_pathname);
        // 压缩        
        @compress($handle->file_dst_pathname);
    } else {
        // 日志
        if ($config['upload_logs']) write_log(config_path() . $handle->file_dst_name, $handle->file_src_name, $handle->file_dst_pathname, $handle->file_src_size);
        // 水印
        @water($handle->file_dst_pathname);
        // 压缩
        @compress($handle->file_dst_pathname);
    }


    unset($handle);
}
