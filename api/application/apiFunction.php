<?php

require_once  './../config/api_key.php';
require_once  './../config/config.php';

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

