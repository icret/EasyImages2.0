<?php

/**
 * | 此文件用来存放各种key
 * | 2021-5-8 22:04:55
 */


/*
 * Token list 请在此填写需要配置Token的用户 前边编号有助于识别上传者ID
 * 格式： ID（数字，需要从0开始,顺序添加）=> Token（注意后边',')
 */

$tokenList = array(
    0 => '8337effca0ddfcd9c5899f3509b23657',
    1 => '1c17b11693cb5ec63859b091c5b9c1b2',
);

$tinyImag_key = [//Api_Key
    // 填写 TinyImag Key 申请地址：https://tinypng.com/developers
    'TinyImag' => ''

];



/**
* moderatecontent key
* 图片监黄 key 从 https://moderatecontent.com/ 获取key并填入/config/api_key.php的图片检查key
*/
$moderatecontent = array(
    'url'   =>  'https://api.moderatecontent.com/moderate/?key=',
    'key'   =>  ''
);