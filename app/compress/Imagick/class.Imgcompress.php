<?php

/**
 * @param string $src 源图
 * @param float $percent 压缩比例
 * 简单图床修改版
 * 此类压缩单文件
 * https://www.cnblogs.com/jiji/p/12928028.html
 */



class Imgcompress
{
    private $src;
    private $image;
    private $imageinfo;
    private $percent = 0.5;

    /**
     * 图片压缩
     * @param string $src 源图
     * @param float $percent 压缩比例
     */
    public function __construct($src, $percent = 1)
    {
        $this->src = $src;
        $this->percent = $percent;
    }

    /** 高清压缩图片
     * @param string $saveName 提供图片名（可不带扩展名，用源图扩展名）用于保存。或不提供文件名直接显示
     */
    public function compressImg($saveName = '')
    {
        // 源图损坏或 GD 不支持该格式时安全返回，避免 imagecreatefrom*() 致命错误
        if (!$this->_openImage()) {
            return false;
        }
        if (!empty($saveName)) {
            return $this->_saveImage($saveName);
        }
        $this->_showImage();
        return true;
    }

    /**
     * 内部：打开图片
     * @return bool 成功|失败
     */
    private function _openImage()
    {
        if (!is_file($this->src)) {
            return false;
        }
        $info = @getimagesize($this->src);
        if (!is_array($info) || empty($info[0]) || empty($info[1]) || empty($info[2])) {
            return false;
        }
        $type = strtolower((string) image_type_to_extension($info[2], false));
        // 只处理 GD 有对应 imagecreatefrom* 的格式（tif/tga/ico 等不支持，直接跳过）
        if (!in_array($type, array('jpeg', 'png', 'gif', 'bmp', 'webp'), true) || !function_exists('imagecreatefrom' . $type)) {
            return false;
        }
        $this->imageinfo = array(
            'width' => (int) $info[0],
            'height' => (int) $info[1],
            'type' => $type,
            'attr' => isset($info[3]) ? $info[3] : '',
        );
        $fun = 'imagecreatefrom' . $type;
        $this->image = @$fun($this->src);
        if (!$this->image) {
            return false;
        }
        return $this->_thumpImage();
    }

    /**
     * 内部：操作图片
     */
    private function _thumpImage()
    {
        $percent = min(1, max(0.01, (float) $this->percent));
        $new_width = max(1, (int) round($this->imageinfo['width'] * $percent));
        $new_height = max(1, (int) round($this->imageinfo['height'] * $percent));
        $image_thump = @imagecreatetruecolor($new_width, $new_height);
        if (!$image_thump) {
            return false;
        }
        /**
         * 保留图片透明通道 
         * 简单图床 EasyImage 2.0 2021-5-9 21:48:59 
         * 参考: https://www.imooc.com/wenda/detail/581249
         */
        imagealphablending($image_thump, false);
        imagesavealpha($image_thump, true);

        //将原图复制带图片载体上面，并且按照一定比例压缩,极大的保持了清晰度
        imagecopyresampled($image_thump, $this->image, 0, 0, 0, 0, $new_width, $new_height, $this->imageinfo['width'], $this->imageinfo['height']);

        if (PHP_VERSION_ID < 80500) {
            imagedestroy($this->image);
        }
        $this->image = $image_thump;
        return true;
    }

    /**
     * 输出图片:保存图片则用saveImage()
     */
    private function _showImage()
    {
        header('Content-Type: image/' . $this->imageinfo['type']);
        $funcs = "image" . $this->imageinfo['type'];
        $funcs($this->image);
    }

    /**
     * 保存图片到硬盘：
     * @param  string $dstImgName 1、可指定字符串不带后缀的名称，使用源图扩展名 。2、直接指定目标图片名带扩展名。
     */
    private function _saveImage($dstImgName)
    {
        if (empty($dstImgName)) return false;
        $allowImgs = array('.jpg', '.jpeg', '.png', '.bmp', '.wbmp', '.gif','.webp');   // 如果目标图片名有后缀就用目标图片扩展名 后缀，如果没有，则用源图的扩展名
        $dstExt = strrchr($dstImgName, ".");
        $sourseExt = strrchr($this->src, ".");
        if (!empty($dstExt)) $dstExt = strtolower($dstExt);
        if (!empty($sourseExt)) $sourseExt = strtolower($sourseExt);

        //有指定目标名扩展名
        if (!empty($dstExt) && in_array($dstExt, $allowImgs)) {
            $dstName = $dstImgName;
        } elseif (!empty($sourseExt) && in_array($sourseExt, $allowImgs)) {
            $dstName = $dstImgName . $sourseExt;
        } else {
            $dstName = $dstImgName . $this->imageinfo['type'];
        }
        $funcs = "image" . $this->imageinfo['type'];
        if (!function_exists($funcs)) {
            return false;
        }
        return $funcs($this->image, $dstName);
    }

    /**
     * 销毁图片
     */
    public function __destruct()
    {
        if ($this->image && PHP_VERSION_ID < 80500) {
            imagedestroy($this->image);
        }
    }
}
