<?php
require __DIR__.'/libs/function.php';
require APP_ROOT.'/libs/class.upload.php';

// 检查是否开启api上传
if($config['apiStatus']){header('Access-Control-Allow-Origin:*');}

$handle = new upload($_FILES['file'],'zh_CN');

if($handle->uploaded){
    // 允许上传的mime类型
    $handle->allowed = array ('image/*');
    // 文件命名
    $handle->file_new_name_body = uniqid();
    // 最大上传限制
    $handle->file_max_sizes = $config['maxSize'];
    // 最大宽度
    $handle->image_max_width = $config['maxWidth'];
    // 最大高度
    $handle->image_max_height = $config['maxHeight'];
    // 最小宽度
    $handle->image_min_width = $config['minWidth'];
    // 最小高度
    $handle->image_min_height = $config['minHeight'];    
    // 转换图片为指定格式
    $handle->image_convert = $config['imgConvert'];
    //PNG图片压缩
    $handle->png_compression = $config['zipPNG'];
    //JPEG图片压缩
    $handle->jpeg_quality = $config['zipJPEG'];
    
    //等比例缩减图片
    if($config['imgRatio']){
        $handle->image_x = $config['image_x'];
    }

    // 设置水印
    if ($config['watermark'] > 0){
        switch ($config['watermark']){
            case 1: // 文字水印 过滤gif
                if (isAnimatedGif($handle->file_src_pathname)===0){
                    $handle->image_text = $config['waterText'];
                    $handle->image_text_direction = $config['textDirection'];
                    $handle->image_text_color = $config['textColor'];
                    $handle->image_text_opacity = $config['textOpacity'];
                    $handle->image_text_font = $config['textFont'];
                    $handle->image_text_size = $config['textSize'];
                    $handle->image_text_padding = $config['textPadding'];
                    $handle->image_text_position = $config['waterPosition'];
                }
                break;
            case 2: // 图片水印
                if (isAnimatedGif($handle->file_src_pathname)===0){
                    $handle->image_watermark             = $config['waterImg'];
                    $handle->image_watermark_position    = $config['waterPosition'];
                    $handle->image_watermark_no_zoom_in  = true;
                    $handle->image_watermark_no_zoom_out = true;
                }
                break;
            default:
                echo $handle->error;
                break;
        }
    }

    // 存储图片路径:images/201807/
    $handle->process(APP_ROOT.config_path());

    // 图片完整相对路径:images/201807/0ed7ccfd4dab9cbc.jpg
    if ($handle->processed){		
        header('Content-type:text/json');
        // 上传成功后返回json数据
        $reJson = array (
            "result"    =>  'success',
            "url"       =>  $config['domain'].config_path().$handle->file_dst_name,
        );
        echo json_encode($reJson);		
        $handle->clean();
    }else{
        // 上传错误 返回错误信息
        $reJson = array (
            "result"      =>  'failed',
            "message"     =>  $handle->error
        );
        echo json_encode($reJson);
        echo $handle->error;
    }

    if($config['jpg_zip_php']>0 && $handle->file_dst_name_ext=='jpg'){        
        $file = __DIR__.config_path().$handle->file_dst_name;
        $percent = $config['jpg_zip_php'];  //图片压缩比
        list($width, $height) = getimagesize($file); //获取原图尺寸
        //缩放尺寸
        $newwidth = $width * $percent;
        $newheight = $height * $percent;
        $src_im = imagecreatefromjpeg($file);
        $dst_im = imagecreatetruecolor($newwidth, $newheight);
        imagecopyresized($dst_im, $src_im, 0, 0, 0, 0, $newwidth, $newheight, $width, $height);
        imagejpeg($dst_im,$file); //输出压缩后的图片
        imagedestroy($dst_im);
        imagedestroy($src_im);
    }
    unset($handle);
}