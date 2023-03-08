<?php

/**
 * 删除/回收文件页面
 * @author Icret
 * 2022-2-23 11:01:52
 */
require_once __DIR__ . '/function.php';

if (!is_who_login('admin')) {
  exit('Not Logged!');
}

// 删除文件
if (isset($_POST['del_url_array'])) {
  $del_url_array = $_POST['del_url_array'];
  $del_num = count($del_url_array);
  for ($i = 0; $i < $del_num; $i++) {
    getDel($del_url_array[$i], 'url');
    // FTP
    // any_upload($del_url_array[$i], null, 'delete');
  }
}

// 回收文件
if (isset($_POST['recycle_url_array'])) {
  $recycle_url_array = $_POST['recycle_url_array'];
  $del_num = count($recycle_url_array);
  for ($i = 0; $i < $del_num; $i++) {
    checkImg($recycle_url_array[$i], 3);
  }
}
