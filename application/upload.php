<?php

namespace Verot\Upload;

require __DIR__ . '/function.php';
require __DIR__ . '/class.upload.php';

// 检查登录
if ($config['mustLogin']) {
    if (checkLogin() !== 204 && checkLogin() !== 205) {
        exit(json_encode(array(
            "result"    =>  "failed",
            "code"      =>  401,
            "message"   =>  "本站已开启登陆上传,您尚未登陆",
        )));
    }
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

// 无文件
if (empty($_FILES['file'])) {
    exit(json_encode(
        array(
            "result"    =>  "failed",
            "code"      =>  204,
            "message"   =>  "没有选择上传的文件",
        )
    ));
}

$handle = new Upload($_FILES['file'], 'zh_CN');

if ($handle->uploaded) {
    // 允许上传的mime类型
    $handle->allowed = array('image/*');
    // 文件命名
    $handle->file_new_name_body = imgName($handle->file_src_name_body);

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
    if ($config['imgConvert']) {
        // 只转换非webp格式和非动态图片
        if ($handle->file_src_name_ext !== 'webp' && !isAnimatedGif($handle->file_src_pathname)) {
            $handle->image_convert = $config['imgConvert'];
            // PNG  图像的压缩级别，介于 1（快速但大文件）和 9（慢但较小文件）之间
            $handle->png_compression = 9 - round($config['compress_ratio'] / 11.2);
            // WEBP 图像的压缩质量 1-100
            $handle->webp_quality = $config['compress_ratio'];
            // JPEG 图像的压缩质量 1-100
            $handle->jpeg_quality = $config['compress_ratio'];
        }
    }

    /* 等比例缩减图片 放到前端了*/
    /*
    if ($config['imgRatio']) {
        $handle->image_resize = true;
        $handle->image_x = $config['image_x'];
        $handle->image_y = $config['image_y'];
        // 如果调整后的图像大于原始图像，则取消调整大小，以防止放大
        $handle->image_no_enlarging = true;
    }
    */

    /**
     * 为管理员和登陆用户创建自定义上传目录
     * 管理员上传目录为自定义目录
     * 上传者目录为其用户名
     * 2022年5月1日
     */

    // 默认目录
    $Img_path = config_path();

    // 开启管理员自定义目录
    if ($config['admin_path_status']) {
        if (checkLogin() == 204) {
            $Img_path = config_path($config['admin_path'] . date('/Y/m/d/'));
        }
    }

    // 开启上传者单独目录
    if ($config['guest_path_status']) {
        if (checkLogin() == 205) {
            $getCok = unserialize($_COOKIE['auth']);
            $Img_path = config_path($getCok[0] . date('/Y/m/d/'));
        }
    }

    // 存储图片路径:images/201807/
    $handle->process(APP_ROOT . $Img_path);

    // 图片完整相对路径:/i/2021/05/03/k88e7p.jpg
    if ($handle->processed) {
        header('Content-type:text/json');

        // 图片相对路径
        $pathIMG = $Img_path . $handle->file_dst_name;
        // 图片访问网址
        $imageUrl = rand_imgurl() . $pathIMG;
        // 后续处理地址
        $processUrl = $config['domain'] . $pathIMG;

        /** 
         * 以下为控制开启源图保护或者返回值隐藏config文件中的path目录所更改 
         * 2022年5月1日
         */

        // 隐藏config文件中的path目录,需要搭配网站设置
        if ($config['hide_path'] == 1) {
            $imageUrl = str_replace($config['path'], '/', $imageUrl);
        }

        // 源图保护 key值是由crc32加密的hide_key
        // $hide_original = $config['hide'] == 1 ? $config['domain'] . '/application/hide.php?key=' . urlHash($pathIMG, 0, crc32($config['hide_key'])) : $imageUrl;
        if ($config['hide'] == 1) {
            $imageUrl = $config['domain'] . '/application/hide.php?key=' . urlHash($pathIMG, 0, crc32($config['hide_key']));
        }

        // 关闭删除链接
        if ($config['show_user_hash_del']) {
            // 判断PHP版本启用删除
            if (PHP_VERSION >= '7') {
                $delUrl = $config['domain']  . '/application/del.php?hash=' . urlHash($pathIMG, 0);
            } else {
                $delUrl = "Sever PHP version lower 7.0";
            }
        } else {
            $delUrl = "Admin closed delete";
        }

        // 上传成功后返回json数据
        $reJson = array(
            "result"    => "success",
            "code"      => 200,
            "url"       => $imageUrl,
            "srcName"   => $handle->file_src_name_body,
            "thumb"     => $config['domain'] . '/application/thumb.php?img=' . $pathIMG,
            "del"       => $delUrl,
        );
        echo json_encode($reJson);
        $handle->clean();
    } else {
        // 上传错误 code:206 客户端文件有问题
        $reJson = array(
            "result"    =>  "failed",
            "code"      =>  206,
            "message"   =>  $handle->error,
        );
        unset($handle);
        header('Content-Type:application/json; charset=utf-8');
        exit(json_encode($reJson, JSON_UNESCAPED_UNICODE));
    }

    /** 后续处理 */
    require __DIR__ . '/process.php';
    // 使用fastcgi_finish_request操作
    if (function_exists('fastcgi_finish_request')) {
        fastcgi_finish_request();
        // 普通模式鉴黄
        @process_checkImg($processUrl);
        // 日志
        if ($config['upload_logs']) @write_log($pathIMG, $handle->file_src_name, $handle->file_dst_pathname, $handle->file_src_size);
        // 水印        
        @water($handle->file_dst_pathname);
        // 压缩        
        @compress($handle->file_dst_pathname);
    } else {
        // 普通模式鉴黄
        @process_checkImg($processUrl);
        // 日志
        if ($config['upload_logs']) write_log($pathIMG, $handle->file_src_name, $handle->file_dst_pathname, $handle->file_src_size);
        // 水印
        @water($handle->file_dst_pathname);
        // 压缩
        @compress($handle->file_dst_pathname);
    }
    unset($handle);
}
