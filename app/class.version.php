<?php

/**
 * @author Icret
 * 获取GitHub最新版本号
 * EasyImage2.0 简单图床 创建时间: 2021-5-10 14:17:25
 * 修改时间: 2023-02-10 11:23:30
 * @param int 864000 10天的unix时间,既每隔十天获取一次版本
 */

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
        // 文件不存在时 filemtime() 会告警，先判存在
        $this->fileModifiTime = is_file($this->filePath) ? (int) filemtime($this->filePath) : 0;
        $this->time = time();
    }

    public function readJson($name = 'tag_name')
    {
        // 过期或不存在时刷新缓存
        if (!is_file($this->filePath) || ($this->time - $this->fileModifiTime > 864000)) {
            $this->downJson();
        }

        $raw = is_file($this->filePath) ? @file_get_contents($this->filePath) : false;
        $version = is_string($raw) ? json_decode($raw, true) : null;
        if (!is_array($version) || !array_key_exists($name, $version)) {
            return null;
        }
        return $version[$name];
    }

    public function downJson()
    {
        if (!is_dir($this->dir)) {
            @mkdir($this->dir, 0755, true);
        }

        $version = $this->geturl($this->url);
        // 只在拿到合法 JSON 时落盘，避免把错误页/空串写进缓存
        if (!is_string($version) || json_decode($version, true) === null) {
            throw new Exception('版本信息不是合法 JSON');
        }
        if (@file_put_contents($this->filePath, $version, LOCK_EX) === false) {
            throw new Exception('版本信息写入失败');
        }
    }

    public function geturl($url)
    {
        $headerArray = array("Content-type:application/json;", "Accept:application/json");
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_TIMEOUT_MS, 3000); // 超时时间
        // 保持 TLS 证书校验，避免中间人篡改版本信息
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headerArray);
        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/86.0.4240.198 Safari/537.36');
        $output = curl_exec($ch);
        if (PHP_VERSION_ID < 80500) {
            curl_close($ch);
        }
        // 原实现再次 json_encode 会把 JSON 文本二次编码，返回原始响应文本即可
        return is_string($output) ? $output : '';
    }
}

/////////// TEST /////////
/*
$url = "https://api.github.com/repositories/188228357/releases/latest";
$test = new getVersion($url);
echo $test->readJson();
*/