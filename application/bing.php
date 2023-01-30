<?php

/**
 * php抓取bing每日图片并保存到服务器
 * 作者：mengkun (mkblog.cn)
 * 日期：2016/12/23
 * 修改：Icret
 * 修改日期：2023-01-30
 */

include_once '../config/config.php';

$path =  __DIR__ . '/..' . $config['path'] . $config['delDir']; // 设置图片缓存文件夹
$filename = date("Ymd") . '.jpg';            // 用年月日来命名新的文件名
if (!file_exists($path . $filename))        // 如果文件不存在，则说明今天还没有进行缓存
{
    if (!file_exists($path)) //如果目录不存在
    {
        mkdir($path, 0777);  //创建缓存目录
    }
    $str = file_get_contents('http://cn.bing.com/HPImageArchive.aspx?format=js&idx=0&n=1'); //读取必应api，获得相应数据
    $str = json_decode($str, true);
    $imgurl = 'http://cn.bing.com' . $str['images'][0]['url'];  //获取图片url
    $img = grabImage($imgurl, $path .  $filename);              //读取并保存图片
    /*
    $handle = fopen("dat.txt", "a");                            //用于存放图片信息，如果不需要保存图片的相关信息，可以把下面这些去掉。
    if ($handle) {
        $copyright = $str['images'][0]['copyright'];            //说明
        $startdate = $str['images'][0]['startdate'];
        $fullstartdate = $str['images'][0]['fullstartdate'];
        $enddate = $str['images'][0]['enddate'];
        $urlbase = $str['images'][0]['urlbase'];
        $copyrightlink = $str['images'][0]['copyrightlink'];
        $quiz = $str['images'][0]['quiz'];
        $wp = $str['images'][0]['wp'];
        $hsh = $str['images'][0]['hsh'];
        $drk = $str['images'][0]['drk'];
        $top = $str['images'][0]['top'];
        $bot = $str['images'][0]['bot'];
        $tempArr = array(
            "imgurl" => $imgurl, "copyright" => $copyright, "startdate" => $startdate,
            "fullstartdate" => $fullstartdate, "enddate" => $enddate, "urlbase" => $urlbase,
            "copyrightlink" => $copyrightlink, "quiz" => $quiz, "wp" => $wp,
            "hsh" => $hsh, "drk" => $drk, "top" => $top, "bot" => $bot
        );   //将相关信息放进数组中
        fwrite($handle, json_encode($tempArr) . "\r\n"); //最终以json格式保存在文本文档中
        fclose($handle);
    }
    */
}
/**
 * 远程抓取图片并保存
 * @param $url 图片url
 * @param $filename 保存名称和路径
 */
function grabImage($url, $filename = "")
{
    if ($url == "") return false;       //如果$url地址为空，直接退出
    if ($filename == "")                //如果没有指定新的文件名
    {
        $ext = strrchr($url, ".");      //得到$url的图片格式
        $filename = date("Ymd") . $ext; //用天月面时分秒来命名新的文件名
    }
    ob_start();                         //打开输出
    readfile($url);                     //输出图片文件
    $img = ob_get_contents();           //得到浏览器输出
    ob_end_clean();                     //清除输出并关闭
    $size = strlen($img);               //得到图片大小
    $fp2 = @fopen($filename, "a");
    fwrite($fp2, $img);                 //向当前目录写入图片文件，并重新命名
    fclose($fp2);
    return $filename;                   //返回新的文件名
}

header("Content-type: image/jpeg");
exit(file_get_contents($path . $filename, true));
