<?php

/**
 * @author Icret
 * 获取GitHub最新版本号
 * 简单图床 EasyImage2.0 2021-5-10 14:17:25
 */

class getVersion
{
    private $url;
    public function __construct($url)
    {
        $this->url = $url;
    }

    public function readJson($name = 'tag_name')
    {
        if (file_exists(__DIR__ . '/../admin/logs/version/version.json')) {
            $file = fopen(__DIR__ . '/../admin/logs/version/version.json', 'r');
            $test = fread($file, filesize(__DIR__ . '/../admin/logs/version/version.json'));
            $version = json_decode($test, true);
            return $version[$name];
            fclose($file);
        } else {
            $this->downJson();
        }
    }

    public function downJson()
    {

        if (!is_dir(__DIR__ . '/../admin/logs/version/')) {
            mkdir(__DIR__ . '/../admin/logs/version/', 0755, true);
        }

        $version = $this->geturl($this->url);
        $version = json_decode($version, true);
        $file = fopen(__DIR__ . '/../admin/logs/version/version.json', 'w+');
        fwrite($file, $version);
        fclose($file);
    }

    public function geturl($url)
    {
        $headerArray = array("Content-type:application/json;", "Accept:application/json");
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
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