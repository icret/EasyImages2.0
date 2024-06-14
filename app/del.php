<?php

/**
 * 删除/回收文件页面
 * @author Icret
 * 2023-3-15 11:01:52
 */

require __DIR__ . '/function.php';

if (empty($_REQUEST)) {
    exit(json_encode(array(
        'code' => 200,
        'msg'  => '无效请求',
        'type' => 'success',
        'icon' => 'exclamation-sign',
        'mode' => 'get',
        'url'  => null
    ), JSON_UNESCAPED_UNICODE));
}


// 解密删除
if (isset($_GET['hash'])) {
    $delHash = $_GET['hash'];
    $delHash = urlHash($delHash, 1);

    if ($config['image_recycl']) {
        // 如果开启回收站则进入回收站
        if (checkImg($delHash, 3, 'recycle/') === true) {

            any_upload($delHash, $delHash, 'delete'); // FTP删除

            exit(json_encode(array(
                'code' => 200,
                'msg'  => '删除成功',
                'type' => 'success',
                'icon' => 'ok-sign',
                'mode' => 'delete',
                'url'  => $delHash
            ), JSON_UNESCAPED_UNICODE));
        } else {
            exit(json_encode(array(
                'code' => 404,
                'msg'  => '文件不存在',
                'type' => 'danger',
                'icon' => 'exclamation-sign',
                'mode' => 'delete',
                'url'  => $delHash
            ), JSON_UNESCAPED_UNICODE));
        }
    } else {
        getDel($delHash, 'url'); // 直接删除

        any_upload($delHash, $delHash, 'delete'); // FTP删除

        exit(json_encode(array(
            'code' => 200,
            'msg'  => '删除成功',
            'type' => 'success',
            'icon' => 'ok-sign',
            'mode' => 'delete',
            'url'  => $delHash
        ), JSON_UNESCAPED_UNICODE));
    }
    exit(json_encode(array(
        'code' => 404,
        'msg'  => '删除失败',
        'type' => 'danger',
        'icon' => 'exclamation-sign',
        'mode' => 'delete',
        'url'  => $delHash
    ), JSON_UNESCAPED_UNICODE));
}

// 非管理员不可访问
if (!is_who_login('admin')) exit('Permission denied');

// 广场 - 批量删除文件
if (isset($_POST['del_url_array'])) {
    $del_url_array = $_POST['del_url_array'];
    $del_num = count($del_url_array);
    for ($i = 0; $i < $del_num; $i++) {
        getDel($del_url_array[$i], 'url');
        // FTP删除
        any_upload($del_url_array[$i], $del_url_array[$i], 'delete');
    }
    echo json_encode(array(
        'code' => 200,
        'msg'  => '删除成功',
        'type' => 'success',
        'icon' => 'ok-sign',
        'mode' => 'delete',
        'url'  => $del_url_array
    ), JSON_UNESCAPED_UNICODE);
}

// 广场 - 批量回收文件
if (isset($_POST['recycle_url_array'])) {
    $recycle_url_array = $_POST['recycle_url_array'];
    $del_num = count($recycle_url_array);
    for ($i = 0; $i < $del_num; $i++) {
        checkImg($recycle_url_array[$i], 3);
    }
}

if (isset($_POST['url'])) $postURL = strip_tags($_POST['url']);

// 广场|日志 - 单文件删除
if (isset($_POST['mode']) && $_POST['mode'] === 'delete') {
    $reslut = easyimage_delete($postURL, 'url');
    // FTP删除
    any_upload($postURL, $postURL, 'delete');

    if ($reslut) {
        exit(json_encode(array(
            'code' => 200,
            'msg'  => '删除成功',
            'type' => 'success',
            'icon' => 'ok-sign',
            'mode' => 'delete',
            'url'  => $postURL
        ), JSON_UNESCAPED_UNICODE));
    } else {
        exit(json_encode(array(
            'code' => 404,
            'msg'  => '删除失败',
            'type' => 'danger',
            'icon' => 'exclamation-sign',
            'mode' => 'delete',
            'url'  => $postURL
        ), JSON_UNESCAPED_UNICODE));
    }
}

// 广场|日志 - 回收文件
if (isset($_POST['mode']) && $_POST['mode'] === 'recycle') {
    if (is_file(APP_ROOT . $postURL)) {
        checkImg($postURL, 3);
        exit(json_encode(array(
            'code' => 200,
            'msg' => '回收成功',
            'type' => 'success',
            'icon' => 'ok-sign',
            'mode' => 'recycle',
            'url' => $postURL
        ), JSON_UNESCAPED_UNICODE));
    } else {
        exit(json_encode(array(
            'code' => 404,
            'msg'  => '回收失败',
            'type' => 'danger',
            'icon' => 'exclamation-sign',
            'mode' => 'delete',
            'url'  => $postURL
        ), JSON_UNESCAPED_UNICODE));
    }
}

// 管理页面 - 删除版本信息文件
if (isset($_POST['mode']) && $_POST['mode'] === 'del_version_file') {
    try {
        @unlink(APP_ROOT . $postURL);
        $re = json_encode(array(
            'code' => 200,
            'msg'  => '删除成功',
            'type' => 'success',
            'icon' => 'ok-sign',
            'mode' => 'delete',
            'url'  => $postURL
        ), JSON_UNESCAPED_UNICODE);
        throw new Exception('更新版本号失败');
    } catch (Exception $e) {
        $re = json_encode(array(
            'code' => 404,
            'msg'  => $e->getMessage(),
            'type' => 'danger',
            'icon' => 'exclamation-sign',
            'mode' => 'delete',
            'url'  => $postURL
        ), JSON_UNESCAPED_UNICODE);
    } finally {
        exit($re);
    }
}

// 管理页面 - 回收站恢复文件
if (isset($_POST['mode']) && $_POST['mode'] === 'recycle_reimg') {
    try {
        if (re_checkImg($postURL, 'recycle/') === true) {
            $re = json_encode(array(
                'code' => 200,
                'msg'  => '恢复成功',
                'type' => 'success',
                'icon' => 'ok-sign',
                'mode' => 'delete',
                'url'  => $postURL
            ), JSON_UNESCAPED_UNICODE);
        } else {
            throw new Exception('恢复失败');
        }
    } catch (Exception $e) {
        $re = json_encode(array(
            'code' => 404,
            'msg'  => $e->getMessage(),
            'type' => 'danger',
            'icon' => 'exclamation-sign',
            'mode' => 'delete',
            'url'  => $postURL
        ), JSON_UNESCAPED_UNICODE);
    } finally {
        exit($re);
    }
}

// 管理页面 - 监黄恢复文件
if (isset($_POST['mode']) && $_POST['mode'] === 'suspic_reimg') {
    try {
        if (re_checkImg($postURL, 'suspic/') === true) {
            $re = json_encode(array(
                'code' => 200,
                'msg'  => '恢复成功',
                'type' => 'success',
                'icon' => 'ok-sign',
                'mode' => 'delete',
                'url'  => $postURL
            ), JSON_UNESCAPED_UNICODE);
        } else {
            throw new Exception('恢复失败');
        }
    } catch (Exception $e) {
        $re = json_encode(array(
            'code' => 404,
            'msg'  => $e->getMessage(),
            'type' => 'danger',
            'icon' => 'exclamation-sign',
            'mode' => 'delete',
            'url'  => $postURL
        ), JSON_UNESCAPED_UNICODE);
    } finally {
        exit($re);
    }
}

// 管理页面 - 删除非空目录
if (isset($_POST['mode']) && $_POST['mode'] === 'delDir') {
    try {
        $delDir = APP_ROOT . $config['path'] . $postURL; // 限制删除目录
        if (deldir($delDir)) {
            $re = json_encode(array(
                'code' => 200,
                'msg'  => '删除文件夹成功',
                'type' => 'success',
                'icon' => 'ok-sign',
                'mode' => 'delete',
                'url'  => $postURL
            ), JSON_UNESCAPED_UNICODE);
        } else {
            throw new Exception('删除文件夹失败');
        }
    } catch (Exception $e) {
        $re = json_encode(array(
            'code' => 404,
            'msg'  => $e->getMessage(),
            'type' => 'danger',
            'icon' => 'exclamation-sign',
            'mode' => 'delete',
            'url'  => $postURL
        ), JSON_UNESCAPED_UNICODE);
    } finally {
        exit($re);
    }
}

// 管理页面 - 删除指定日期文件夹
if (isset($_POST['dateDir'])) {
    $delDir = APP_ROOT . $config['path'] . $_POST['dateDir'];
    if (deldir($delDir)) {
        echo json_encode(array(
            'code' => 200,
            'msg'  => '删除成功',
            'type' => 'success',
            'icon' => 'ok-sign',
            'mode' => 'delDir',
            'url'  => $delDir
        ), JSON_UNESCAPED_UNICODE);
        exit;
    } else {
        exit(json_encode(array(
            'code' => 404,
            'msg'  => '删除失败',
            'type' => 'danger',
            'icon' => 'exclamation-sign',
            'mode' => 'delDir',
            'url'  => $delDir
        ), JSON_UNESCAPED_UNICODE));
    }
}

// 管理页面 - 删除指定文件
if (isset($_POST['url_admin_inc'])) {

    $del_url = $_POST['url_admin_inc'];
    if ($config['hide_path']) {
        $del_url = $config['domain'] . $config['path'] . parse_url($del_url)['path'];
    }

    if (easyimage_delete($del_url, 'url') === TRUE) {
        exit(json_encode(array(
            'code' => 200,
            'msg'  => '删除成功',
            'type' => 'success',
            'icon' => 'ok-sign',
            'mode' => 'delete',
            'url'  => $del_url
        ), JSON_UNESCAPED_UNICODE));
    }

    exit(json_encode(array(
        'code' => 404,
        'msg'  => '删除失败',
        'type' => 'danger',
        'icon' => 'exclamation-sign',
        'mode' => 'delete',
        'url'  => $del_url
    ), JSON_UNESCAPED_UNICODE));
}

// 管理页面 - 重置OPcache缓存
if (isset($_POST['mode']) && $_POST['mode'] === 'OPcache') {

    if (!function_exists('opcache_reset')) {
        exit(json_encode(array(
            'code' => 404,
            'msg'  => '未开启OPcache',
            'type' => 'danger',
            'icon' => 'exclamation-sign',
            'mode' => 'delete',
        ), JSON_UNESCAPED_UNICODE));
    }

    try {
        if (opcache_reset() === true) {
            $re = json_encode(array(
                'code' => 200,
                'msg'  => '重置缓存成功',
                'type' => 'success',
                'icon' => 'ok-sign',
                'mode' => 'delete',
            ), JSON_UNESCAPED_UNICODE);
        } else {
            throw new Exception('重置缓存失败');
        }
    } catch (Exception $e) {
        $re = json_encode(array(
            'code' => 404,
            'msg'  => $e->getMessage(),
            'type' => 'danger',
            'icon' => 'exclamation-sign',
            'mode' => 'delete',
        ), JSON_UNESCAPED_UNICODE);
    } finally {
        exit($re);
    }
}
