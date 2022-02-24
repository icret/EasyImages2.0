<?php
require_once __DIR__ . '/../application/function.php';
require_once APP_ROOT . '/api/function_API.php';
require_once APP_ROOT . '/application/class.upload.php';
require_once APP_ROOT . '/config/api_key.php';

header('Access-Control-Allow-Origin:*');
$token = preg_replace('/[\W]/', '', $_POST['token']); // 获取Token并过滤非字母数字，删除空格;

// 检查api合法性
check_api($token);

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

$handle = new Upload($_FILES['image'], 'zh_CN');

if ($handle->uploaded) {
    // 允许上传的mime类型
    $handle->allowed = array('image/*');
    // 文件命名
    $handle->file_new_name_body = imgName($handle->file_src_name_body) . '_' . $tokenList[$token]['id'];
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

    // 存储图片路径:images/201807/
    $handle->process('../' . config_path());

    // 图片完整相对路径:/i/2021/05/03/k88e7p.jpg
    if ($handle->processed) {
        header('Content-type:text/json');
        // 上传成功后返回json数据
        $pathIMG = config_path() . $handle->file_dst_name;
        $imageUrl = $config['imgurl'] . $pathIMG;

        // 关闭上传后显示加密删除链接
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

        $reJson = array(
            "result"    => "success",
            "code"      => 200,
            "url"       => $imageUrl,
            "srcName"   => $handle->file_src_name_body,
            "thumb"     => $config['domain'] . '/application/thumb.php?img=' . $pathIMG,
            "del"       => $delUrl,
        );
        echo json_encode($reJson, JSON_UNESCAPED_UNICODE);
        $handle->clean();
    } else {
        // 上传错误 code:403 客户端文件有问题
        $reJson = array(
            "result"    =>  "failed",
            "code"      =>  403,
            "message"   =>  $handle->error,
        );

        exit(json_encode($reJson, JSON_UNESCAPED_UNICODE));
    }

    /** 后续处理 */
    require APP_ROOT . '/application/process.php';

    // 使用fastcgi_finish_request操作
    if (function_exists('fastcgi_finish_request')) {
        fastcgi_finish_request();
        // 普通模式鉴黄
        @process_checkImg($imageUrl);
        // 日志
        if ($config['upload_logs']) @write_log($pathIMG, $handle->file_src_name, $handle->file_dst_pathname, $handle->file_src_size);
        // 水印        
        @water($handle->file_dst_pathname);
        // 压缩        
        @compress($handle->file_dst_pathname);
    } else {
        // 普通模式鉴黄
        @process_checkImg($imageUrl);
        // 日志
        if ($config['upload_logs']) write_log($pathIMG, $handle->file_src_name, $handle->file_dst_pathname, $handle->file_src_size);
        // 水印
        @water($handle->file_dst_pathname);
        // 压缩
        @compress($handle->file_dst_pathname);
    }

    unset($handle);
}
