<?php
// https://blog.csdn.net/MyDream229/article/details/80009012
class Imgs
{
    /**
     * 取得图片宽
     * @param string $src 图片相对路径或绝对路径
     */
    public static function get_width($src)
    {
        return imagesx($src);
    }

    /**
     * 取得图片高
     * @param string $src 图片相对路径或绝对路径
     */
    public static function get_height($src)
    {
        return imagesy($src);
    }

    /**
     * 图片缩放函数
     * @param string $src 图片相对路径或绝对路径
     * @param int $w 缩略图宽
     * @param int $h 缩略图高
     * @return array code：状态。msg：提示信息
     **/
    public static function thumb($src, $w = null, $h = null)
    {
        if (empty($src)) {
            return array('code' => false, 'msg' => '请指定$src');
        }

        $temp = pathinfo($src);
        #  文件名
        $name = $temp["basename"];
        #  文件所在的文件夹
        $dir = $temp["dirname"];
        #  文件扩展名
        $extension = $temp["extension"];
        #  缩略图保存路径,新的文件名为*.thumb.jpg
        $savepath = "{$dir}/thumb_{$name}";

        #  获取图片的基本信息
        $info = getimagesize($src);
        #  获取图片宽度
        $width = $info[0];
        #  获取图片高度
        $height = $info[1];
        if (!empty($w)) {
            $temp_w = $w; #  计算原图缩放后的宽度
            $temp_h = intval($height * ($w / $width)); #  计算原图缩放后的高度
        } else {
            $temp_w = intval($width * ($h / $height)); #  计算原图缩放后的宽度
            $temp_h = $h; #  计算原图缩放后的高度
        }

        #  创建画布
        $temp_img = imagecreatetruecolor($temp_w, $temp_h);
        @imagealphablending($temp_img, false); //这里很重要,意思是不合并颜色,直接用$img图像颜色替换,包括透明色;3-2
        @imagesavealpha($temp_img, true); //这里很重要,意思是不要丢了$thumb图像的透明色;3-3
        switch ($info[2]) {
            case 1:
                $im = imagecreatefromgif($src);
                imagecopyresampled($temp_img, $im, 0, 0, 0, 0, $temp_w, $temp_h, $width, $height);
                imagegif($temp_img, $savepath, 100);
                break;
            case 2:
                $im = imagecreatefromjpeg($src);
                imagecopyresampled($temp_img, $im, 0, 0, 0, 0, $temp_w, $temp_h, $width, $height);
                imagejpeg($temp_img, $savepath, 100);
                break;
            case 3:
                $im = imagecreatefrompng($src);
                imagesavealpha($im, true); //这里很重要;3-1
                imagecopyresampled($temp_img, $im, 0, 0, 0, 0, $temp_w, $temp_h, $width, $height);
                imagepng($temp_img, $savepath, 100);
                break;
            case 6:
                $im = imagecreatefrombmp($src);
                imagesavealpha($im, true); //这里很重要;3-1
                imagecopyresampled($temp_img, $im, 0, 0, 0, 0, $temp_w, $temp_h, $width, $height);
                imagebmp($temp_img, $savepath, 100);
                break;
            case 18:
                $im = imagecreatefromwebp($src);
                imagesavealpha($im, true); //这里很重要;3-1
                imagecopyresampled($temp_img, $im, 0, 0, 0, 0, $temp_w, $temp_h, $width, $height);
                imagewebp($temp_img, $savepath, 100);
                break;
        }
        imagedestroy($im);
        return $savepath;
    }

    /**
     * 图片添加水印
     * @param string $src  1、图片相对路径或绝对路径  2、以逗号隔开的宽高值（'800,600'）
     * @param array  属性值：
     * res：水印资源（1、图片相对路径或绝对路径，2、字符串）
     * pos：图片水印添加的位置，取值范围：0~9
     * 0：随机位置，在1~8之间随机选取一个位置
     * 1：顶部居左 2：顶部居中
     * 3：顶部居右 4：左边居中
     * 5：图片中心 6：右边居中
     * 7：底部居左 8：底部居中
     * 9：底部居右
     * font：    字体库（相对路径或绝对路径）
     * fontSize：文字大小
     * color：   水印文字的字体颜色（255,255,255）
     * name：    图片保存名称
     * @return array    code：状态、 msg：提示信息、 url:图片地址
     **/
    public static function setWater($src, $arr = array())
    {
        if (empty($src)) {
            return array('code' => false, 'msg' => '请指定$src');
        }

        $def = array(
            'res' => '小川编程',
            'pos' => 7,
            'font' => './1.ttf',
            'fontSize' => 24,
            'color' => '255,255,255,0',
            'name' => null,
        );
        $def = array_merge($def, $arr);
        /**
         * 判断$src是不是图片，不是就创建画布
         */
        if (!file_exists($src)) {
            if (empty($def['name'])) {
                return array('code' => false, 'msg' => '请指定图片名称');
            }

            # 计算画布宽高
            $obj = explode(',', $src);
            if (count($obj) != 2) {
                return array(
                    'code' => false,
                    'msg' => '请给正确的宽高，或你给的不是一个有效的地址！'
                );
            }

            $srcImg_w = is_numeric($obj[0]) ? $obj[0] : 400;
            $srcImg_h = is_numeric($obj[1]) ? $obj[1] : 300;
            # 创建透明画布 一共3个步骤，在下边有标记
            $dst_img = @imagecreatetruecolor($srcImg_w, $srcImg_h);
            @imagealphablending($dst_img, false); //这里很重要,意思是不合并颜色,直接用$img图像颜色替换,包括透明色;3-2
            @imagesavealpha($dst_img, true); //这里很重要,意思是不要丢了$thumb图像的透明色;3-3
        } else {
            #  获取图片信息
            $srcInfo = @getimagesize($src);
            $srcImg_w = $srcInfo[0];
            $srcImg_h = $srcInfo[1];
            if (empty($def['name'])) {
                $def['name'] = $src;
            }

            #  动态的把图片导入内存中
            switch ($srcInfo[2]) {
                case 1:
                    $dst_img = imagecreatefromgif($src);
                    break;

                case 2:
                    $dst_img = imagecreatefromjpeg($src);
                    break;

                case 3:
                    $dst_img = imagecreatefrompng($src);
                    imagesavealpha($dst_img, true); //这里很重要;3-1
                    break;

                case 6:
                    $dst_img = imagecreatefrombmp($src);
                    imagesavealpha($dst_img, true); //这里很重要;3-1
                    break;
                case 18:
                    $dst_img = imagecreatefromwebp($src);
                    imagesavealpha($dst_img, true); //这里很重要;3-1
                    break;

                default:
                    return array('code' => false, 'msg' => '目标图片类型错误');
                    exit;
            }
        }
        /**
         * 计算出水印宽高
         */
        if (!file_exists($def['res'])) {
            if (!file_exists($def['font'])) {
                return array('code' => false, 'msg' => '字体库不存在');
            }

            $box = @imagettfbbox($def['fontSize'], 0, $def['font'], $def['res']);
            $logow = max($box[2], $box[4]) - min($box[0], $box[6]);
            $logoh = max($box[1], $box[3]) - min($box[5], $box[7]);
        } else {
            $resInfo = @getimagesize($def['res']);
            $res_w = $resInfo[0];
            $res_h = $resInfo[1];
            if ($srcImg_w < $res_w || $srcImg_h < $res_h) {
                return array('code' => false, 'msg' => '水印图片过大');
            }

            #  动态的把图片导入内存中
            switch ($resInfo[2]) {
                case 1:
                    $markim = imagecreatefromgif($def['res']);
                    break;
                case 2:
                    $markim = imagecreatefromjpeg($def['res']);
                    break;
                case 3:
                    $markim = imagecreatefrompng($def['res']);
                    break;
                case 6:
                    $markim = imagecreatefrombmp($def['res']);
                    break;
                case 18:
                    $markim = imagecreatefromwebp($def['res']);
                    break;
                default:
                    return array('code' => false, 'msg' => '水印图片类型错误');
                    exit;
            }
            $logow = $res_w;
            $logoh = $res_h;
        }
        /**
         * 计算水印显示位置
         */
        if ($def['pos'] == 0) {
            $def['pos'] = rand(1, 9);
        }

        switch ($def['pos']) {
            case 1:
                $x = +10;
                $y = +10 + $def['fontSize'];
                break;

            case 2:
                $x = ($srcImg_w - $logow) / 2;
                $y = +10 + $def['fontSize'];
                break;

            case 3:
                $x = $srcImg_w - $logow - 10;
                $y = +10 + $def['fontSize'];
                break;

            case 4:
                $x = +10;
                $y = ($srcImg_h - $logoh) / 2 + $def['fontSize'];
                break;

            case 5:
                $x = ($srcImg_w - $logow) / 2;
                $y = ($srcImg_h - $logoh) / 2 + $def['fontSize'];
                break;

            case 6:
                $x = $srcImg_w - $logow - 10;
                $y = ($srcImg_h - $logoh) / 2 + $def['fontSize'];
                break;

            case 7:
                $x = +10;
                $y = $srcImg_h - $logoh + $def['fontSize'] - 10;
                break;

            case 8:
                $x = ($srcImg_w - $logow) / 2;
                $y = $srcImg_h - $logoh + $def['fontSize'] - 10;
                break;

            case 9:
                $x = $srcImg_w - $logow - 10;
                $y = $srcImg_h - $logoh + $def['fontSize'] - 10;
                break;

            default:
                return array('code' => false, 'msg' => '水印位置不支持');
                exit;
        }
        /**
         * 把图片水印或文字水印，加到目标图片中
         */
        if (file_exists($def['res'])) {
            imagecopy($dst_img, $markim, $x, $y, 0, 0, $logow, $logoh);
            imagedestroy($markim);
        } else {
            $rgb = explode(',', $def['color']);
            if (count($rgb) != 4) {
                return array('code' => false, 'msg' => '请给正确的字体颜色');
            }

            if (!is_numeric($rgb[0]) || !is_numeric($rgb[1]) || !is_numeric($rgb[2]) || !is_numeric($rgb[3])) {
                return array('code' => false, 'msg' => '请给正确的字体颜色');
            }

            if ($rgb[0] > 255 || $rgb[1] > 255 || $rgb[2] > 255 || $rgb[3] > 127) {
                return array('code' => false, 'msg' => '请给正确的字体颜色');
            }

            // ceil(127 - 127 * $rgb[3]) 将CSS中的Alpha 0-1 转换为PHP Alpha 127-0 并取整
            $def['color'] = imagecolorallocatealpha($dst_img, $rgb[0], $rgb[1], $rgb[2], ceil(127 - 127 * $rgb[3]));
            imagettftext(
                $dst_img,
                $def['fontSize'],
                0,
                $x,
                $y,
                $def['color'],
                $def['font'],
                $def['res']
            );
        }
        /**
         * 保存处理过的图片（有水印了的图片）
         */
        $name = explode('.', $def['name']);
        $num = count($name) - 1;
        switch (strtolower($name[$num])) {
            case 'jpeg':
                imagejpeg($dst_img, $def['name']);
                break;
            case 'jpg':
                imagejpeg($dst_img, $def['name']);
                break;
            case 'png':
                imagepng($dst_img, $def['name']);
                break;
            case 'gif':
                imagegif($dst_img, $def['name']);
                break;
            case 'bmp':
                imagebmp($dst_img, $def['name']);
                break;
            case 'webp':
                imagewebp($dst_img, $def['name']);
                break;
            default:
                return array('code' => false, 'msg' => '保存图片类型有误');
                break;
        }
        #  销毁图片内存资源
        imagedestroy($dst_img);
        return array('code' => true, 'msg' => '添加水印成功', 'url' => $def['name']);
    }
}
