<?php

require_once __DIR__ . '/tokenList.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/config/config.php';

// Token 生成
function privateToken($length = 32)
{
    $output = '';
    for ($a = 0; $a < $length; $a++) {
        $output .= chr(mt_rand(65, 122));    //生成php随机数 
    }
    return md5($output);
}

// 检查Token
function checkToken($token)
{
    global $tokenList;
    $token = preg_replace('/[\W]/', '', $token); // 过滤非字母数字，删除空格

    if (in_array($token, $tokenList)) {
        return True;
    } else {
        exit('此Token不存在：' . $token);
    }
}

// 通过Token查找用户ID
function getID($token)
{
    global $tokenList;
    $token = preg_replace('/[\W]/', '', $token); // 过滤非字母数字，删除空格
    $key = array_search($token, $tokenList);
    if ($key) {
        return $key;
    } else {
        return ('没有这个用户ID');
    }
};