<?php

/**
 * @author Icret
 * 获取GitHub最新版本号
 * 简单图床 EasyImage2.0 2021-5-10 14:17:25
 */

class getVerson
{
    private $url;
    public function __construct($url)
    {
        $this->url = $url;
    }

    public function readJson()
    {
        if (file_exists(__DIR__ . '/../admin/logs/verson/verson.json')) {
            $file = fopen(__DIR__ . '/../admin/logs/verson/verson.json', 'r');
            $test = fread($file, filesize(__DIR__ . '/../admin/logs/verson/verson.json'));
            $verson = json_decode($test, true);
            return $verson['tag_name'];
            fclose($file);
        } else {
            $this->downJson();
        }
    }

    public function downJson()
    {

        if(!is_dir(__DIR__.'/../admin/logs/verson/'))
        {
            mkdir(__DIR__.'/../admin/logs/verson/',0755,true);
        }

        $verson = $this->geturl($this->url);
        $verson = json_decode($verson, true);
        $file = fopen(__DIR__ . '/../admin/logs/verson/verson.json', 'w+');
        fwrite($file, $verson);
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
$test = new getVerson($url);
echo $test->readJson();
*/