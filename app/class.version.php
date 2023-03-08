<?php

/**
 * @author Icret
 * 获取GitHub最新版本号
 * EasyImage2.0 简单图床 创建时间: 2021-5-10 14:17:25
 * 修改时间: 2023-02-10 11:23:30
 * @param int 864000 10天的unix时间,既每隔十天获取一次版本
 */

error_reporting(0); // 关闭所有PHP错误报告

class getVersion
{
    private $url;
    private $dir;            //文件所在文件夹
    private $filePath;       //文件绝对路径
    private $fileName;       //文件名称
    private $fileModifiTime; // 文件最后修改时间
    private $time;           // 当前时间

    public function __construct($url)
    {
        $this->url = $url;
        $this->dir = __DIR__ . '/../admin/logs/version/';
        $this->fileName = 'version.json';
        $this->filePath = $this->dir  . $this->fileName;
        $this->fileModifiTime = filemtime($this->filePath);
        $this->time = time();
    }

    public function readJson($name = 'tag_name')
    {
        if (is_file($this->filePath)) {

            if ($this->time - $this->fileModifiTime > 864000) {
                $this->downJson();
            } else {
                $file = fopen($this->filePath, 'r');
                $test = fread($file, filesize($this->filePath));
                $version = json_decode($test, true);
                return $version[$name];
                fclose($file);
            }
        } else {
            $this->downJson();
        }
    }

    public function downJson()
    {

        if (!is_dir($this->dir)) {
            mkdir($this->dir, 0755, true);
        }

        $version = $this->geturl($this->url);
        $version = json_decode($version, true);
        $file = fopen($this->filePath, 'w+');
        fwrite($file, $version);
        fclose($file);
    }

    public function geturl($url)
    {
        $headerArray = array("Content-type:application/json;", "Accept:application/json");
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_TIMEOUT_MS, 666); // 超时时间
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headerArray);
        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/86.0.4240.198 Safari/537.36');
        $output = curl_exec($ch);
        curl_close($ch);
        $output = json_encode($output, true);
        return $output;
    }
}

/////////// TEST /////////
/*
$url = "https://api.github.com/repositories/188228357/releases/latest";
$test = new getVersion($url);
echo $test->readJson();
*/