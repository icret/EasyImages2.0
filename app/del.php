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
    // 解密并收敛路径，防止解密结果被构造成路径穿越
    $error = null;
    $delHash = resolve_image_path(urlHash($_GET['hash'], 1, 'del'), $error);

    // 拒绝越界与穿越
    if (in_array($error, array('traversal', 'outside'), true)) {
        http_response_code(403);
        exit(json_encode(array(
            'code' => 403,
            'msg'  => '非法路径',
            'type' => 'danger',
            'icon' => 'exclamation-sign',
            'mode' => 'delete',
            'url'  => null
        ), JSON_UNESCAPED_UNICODE));
    }

    // 文件不存在
    if ($delHash === false) {
        exit(json_encode(array(
            'code' => 404,
            'msg'  => '文件不存在',
            'type' => 'danger',
            'icon' => 'exclamation-sign',
            'mode' => 'delete',
            'url'  => null
        ), JSON_UNESCAPED_UNICODE));
    }

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
        // 直接删除：以 unlink 实际结果为准，失败时不再谎报成功
        if (getDel($delHash, 'url') === true) {
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

$postURL = isset($_POST['url']) ? strip_tags((string) $_POST['url']) : '';

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
    // 路径收敛到存储目录，避免用任意路径探测站点文件存在性
    $error = null;
    $recyclePath = resolve_image_path($postURL, $error);
    if ($recyclePath !== false && is_file(APP_ROOT . $recyclePath) && checkImg($recyclePath, 3) === true) {
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
        // 只删除固定的版本信息文件，不接受用户传入路径（防止管理员态删除站点任意文件）
        $delFile = '/admin/logs/version/version.json';
        if (!is_file(APP_ROOT . $delFile)) {
            throw new Exception('版本信息文件不存在');
        }
        // 删除失败同样要如实返回，不能在成功分支里无条件抛错
        if (!@unlink(APP_ROOT . $delFile)) {
            throw new Exception('更新版本号失败');
        }
        $re = json_encode(array(
            'code' => 200,
            'msg'  => '删除成功',
            'type' => 'success',
            'icon' => 'ok-sign',
            'mode' => 'delete',
            'url'  => $postURL
        ), JSON_UNESCAPED_UNICODE);
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
        // 目录收敛，限制删除目录只能在存储目录内
        $error = null;
        $safeDir = resolve_image_path($postURL, $error, 'dir');
        if ($safeDir === false) {
            throw new Exception('删除文件夹不存在或路径非法');
        }
        $delDir = APP_ROOT . $safeDir;
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
    // 目录收敛，限制删除目录只能在存储目录内
    $error = null;
    $safeDir = resolve_image_path($_POST['dateDir'], $error, 'dir');
    if ($safeDir === false) {
        exit(json_encode(array(
            'code' => 404,
            'msg'  => '删除文件夹不存在或路径非法',
            'type' => 'danger',
            'icon' => 'exclamation-sign',
            'mode' => 'delDir',
            'url'  => null
        ), JSON_UNESCAPED_UNICODE));
    }
    $delDir = APP_ROOT . $safeDir;
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
