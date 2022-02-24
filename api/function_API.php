<?php

require_once './../config/api_key.php';
require_once './../config/config.php';

// Token 生成
function privateToken($length = 32)
{
    $output = '';
    for ($a = 0; $a < $length; $a++) {
        $output .= chr(mt_rand(65, 122));    //生成php随机数 
    }
    return md5($output);
}

// 通过Token查找用户ID
function getID($token)
{
    global $tokenList;
    $token = preg_replace('/[\W]/', '', $token); // 过滤非字母数字，删除空格
    $key = array_search($token, $tokenList);
    if ($key >= 0) {
        return $key;
    } else {
        return ('没有这个用户ID');
    }
}

// 通过ID查找用户Token
function getIDToken($id)
{
    global $tokenList;
    $id = preg_replace('/[\W]/', '', $id); // 过滤非字母数字，删除空格
    foreach ($tokenList as $key => $value) {
        if ($key == $id) {
            return $value;
        }
    }
}

/**
 * 检查是否开启api上传
 * code:201 访问成功但是服务端关闭API上传
 * code:202 访问成功但是Token错误
 */
function check_api($token)
{
    global $config;
    global $tokenList;

    if (!$config['apiStatus']) {
        // API关闭 服务端关闭API上传
        $reJson = array(
            "result" => 'failed',
            'code' => 201,
            'message' => 'API Closed',
        );
        exit(json_encode($reJson, JSON_UNESCAPED_UNICODE));
    }

    if (!in_array($tokenList[$token], $tokenList)) {
        // Token 是否存在
        $reJson = array(
            "result" => 'failed',
            'code' => 202,
            'message' => 'Token Error',
        );
        exit(json_encode($reJson, JSON_UNESCAPED_UNICODE));
    }

    if ($tokenList[$token]['expired'] < time()) {
        // Token 是否过期
        $reJson = array(
            "result" => 'failed',
            'code' => 203,
            'message' => 'Token Expired',
        );
        exit(json_encode($reJson, JSON_UNESCAPED_UNICODE));
    }
}
