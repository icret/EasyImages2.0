<?php

/**
 * Created by PhpStorm.
 * User: Zhang He
 * Date: 2015/6/8
 * Time: 10:14
 * https://www.cnblogs.com/CheeseZH/p/4566068.html
 */

class TinyImg
{
    /*Compress all images in folder $inputFolder and save final images in folder $outputFolder*/
    public function compressImgsFolder($key, $inputFolder, $outputFolder)
    {
        $images = $this->getFiles($inputFolder);
        if (empty($images)) {
            return false;
        }
        foreach ($images as $image) {
            $input = $inputFolder . $image;
            $output = $outputFolder . $image;
            print($input . " => 源文件<br>");
            print($output . " => 成功文件<br>");
            $this->compressImg($key, $input, $output);
        }
        return true;
    }
    /*Compress one image $input and save as $output*/
    public function compressImg($key, $input, $output)
    {
        $url = "https://api.tinify.com/shrink";
        $options = array(
            "http" => array(
                "method" => "POST",
                "header" => array(
                    "Content-type: image/png",
                    "Authorization: Basic " . base64_encode("api:$key")
                ),
                "content" => file_get_contents($input)
            ),
            "ssl" => array(
                /* Uncomment below if you have trouble validating our SSL certificate.
                   Download cacert.pem from: http://curl.haxx.se/ca/cacert.pem */
                "cafile" => __DIR__ . "/cacert.pem",
                "verify_peer" => true
            )
        );

        $result = fopen($url, "r", false, stream_context_create($options));
        if ($result) {
            /* Compression was successful, retrieve output from Location header. */
            foreach ($http_response_header as $header) {
                if (strtolower(substr($header, 0, 10)) === "location: ") {
                    file_put_contents($output, fopen(substr($header, 10), "rb", false));
                }
            }
        } else {
            /* Something went wrong! */
            print("Compression failed<br>");
        }
    }
    //get all files' fullname in $filedir
    public function getFiles($filedir)
    {
        $files = [];
        $dir = @dir($filedir);
        while (($file = $dir->read()) != false) {
            if ($file != "." and $file != "..") {
                $files[] = $file;
            }
        }
        $dir->close();
        return $files;
    }
}
