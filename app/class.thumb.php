<?php
/* Author: Dejan  QQ: 673008865
 * 缩略图 - 图片等比例缩放
 ________________    ________________    ________________
 |                |  |                |  |                |
 |                |  |      top       |  |                |
 |________________|  |                |  |                |
 |                |  |________________|  |                |
 |     middle     |  |                |  |                |
 |                |  |                |  |________________|
 |----------------|  |                |  |                |
 |                |  |                |  |      bottom    |
 |                |  |                |  |                |
 |________________|  |________________|  |________________|

 */

// namespace Thumb;
// https://github.com/aileshe/Thumb

class Thumb
{
    private static $img_type; # 原图片格式

    /**
     * 输出图象到浏览器
     * @param  String  $filename  原图路径
     * @param  String  $width     预生成缩略图宽度
     * @param  String  $height    预生成缩略图高度
     * @param  String  $valign    [middle|top|bottom],默认 居中
     */
    public static function show($filename, $width, $height, $valign = 'middle')
    {
        $thumb = self::make($filename, $width, $height, $valign);
        header('Content-Type:image/' . self::$img_type);
        echo $thumb;
    }

    /**
     * 保存缩略图文件
     * @param  String  $filename  原图路径
     * @param  String  $output    缩略图输出路径
     * @param  String  $width     预生成缩略图宽度
     * @param  String  $height    预生成缩略图高度
     * @param  String  $valign    [middle|top|bottom],默认 居中
     */
    public static function out($filename, $output, $width, $height, $valign = 'middle')
    {
        $thumb = self::make($filename, $width, $height, $valign);
        $fh = fopen($output, 'wb'); # 二进制文件
        fwrite($fh, $thumb);
        fclose($fh);
    }

    /**
     * 输出图象到浏览器并保存缩略图文件
     * @param  String  $filename  原图路径
     * @param  String  $output    缩略图输出路径
     * @param  String  $width     预生成缩略图宽度
     * @param  String  $height    预生成缩略图高度
     * @param  String  $valign    [middle|top|bottom],默认 居中
     */
    public static function showOut($filename, $output, $width, $height, $valign = 'middle')
    {
        $thumb = self::make($filename, $width, $height, $valign);
        $fh = fopen($output, 'wb'); # 二进制文件
        fwrite($fh, $thumb);
        fclose($fh);
        header('Content-Type:image/' . self::$img_type);
        echo $thumb;
    }

    /**
     * 缩略图生成函数
     * @param  String  $filename  原图路径
     * @param  String  $width     预生成缩略图宽度
     * @param  String  $height    预生成缩略图高度
     * @param  String  $valign    [middle|top|bottom],默认 居中
     * @return FileString         原始图象流
     */
    private static function make($filename, $width, $height, $valign = 'middle')
    {
        ini_set('gd.jpeg_ignore_warning', true);
        /**
         * 2022-1-4 23:34:37 EasyImage更改图像类型判断
         * getimagesize索引2给出的是图像的类型，返回的是数字
         * 1 = GIF，2 = JPG，3 = PNG，4 = SWF，5 = PSD，6 = BMP，7 = TIFF(intel byte order)，8 = TIFF(motorola byte order)，
         * 9 = JPC，10 = JP2，11 = JPX，12 = JB2，13 = SWC，14 = IFF，15 = WBMP，16 = XBM
         */
        $filetype = array(
            0 => 'unknown',
            1 => 'gif',
            2 => 'jpeg',
            3 => 'png',
            4 => 'swf',
            5 => 'psd',
            6 => 'bmp',
            7 => 'tif_ii',
            8 => 'tiff_mm',
            9 => 'jpc',
            10 => 'jp2',
            11 => 'jpx',
            12 => 'jb2',
            13 => 'swc',
            14 => 'iff',
            15 => 'wbmp',
            16 => 'xbm',
            17 => 'ico',
            18 => 'webp'
        );
        # 获取图片信息
        $imginfo = getimagesize($filename);
        $img_w = $imginfo[0];
        $img_h = $imginfo[1];
        self::$img_type = $filetype[$imginfo[2]];


        $thumb_h = $height; # 固定背景画布的高度
        $height = $img_h / ($img_w / $width); # 图片等比例缩放后的高度=原图的高度÷(原图的宽度÷背景画布固定宽带)

        # 创建新的背景画布
        if ($height >= $thumb_h) {
            $thumb = imagecreatetruecolor($width, $thumb_h);
            @imagealphablending($thumb, false); //这里很重要,意思是不合并颜色,直接用$img图像颜色替换,包括透明色;2-1
            @imagesavealpha($thumb, true); //这里很重要,意思是不要丢了$thumb图像的透明色;2-2 EasyImage修改
        } else {
            $thumb = imagecreatetruecolor($width, $height);
            @imagealphablending($thumb, false); //这里很重要,意思是不合并颜色,直接用$img图像颜色替换,包括透明色;2-1
            @imagesavealpha($thumb, true); //这里很重要,意思是不要丢了$thumb图像的透明色;2-2 EasyImage修改
            $thumb_h = $height;
        }

        # 载入要缩放的图片
        $loadimg = 'imagecreatefrom' . self::$img_type;
        $tmp_img = $loadimg($filename);


        switch ($valign) {
            case 'top': {
                    $dst_y = 0;
                    break;
                }
            case 'middle': {
                    $dst_y = ($img_h - $img_w / $width * $thumb_h) / 2;
                    break;
                }
            case 'bottom': {
                    $dst_y = $img_h - $img_w / $width * $thumb_h;
                    break;
                }
            default: {
                    $dst_y = 0;
                    break;
                }
        }

        # 合成缩略图
        imagecopyresampled($thumb, $tmp_img, 0, 0, 0, $dst_y, $width, $height, $img_w, $img_h);

        ob_clean();
        # 展示图片
        ob_start();
        $showimg = 'image' . self::$img_type;
        $showimg($thumb); # 输出原始图象流
        $thumb_img = ob_get_clean();

        # 释放资源
        imagedestroy($tmp_img);
        imagedestroy($thumb);
        return $thumb_img;
    }
}
