<?php
//递归函数实现遍历指定文件下的目录与文件数量
function total($dirname,&$dirnum,&$filenum){
    $dir=opendir($dirname);
    echo readdir($dir); //读取当前目录文件
    echo readdir($dir); //读取上级目录文件
    while($filename=readdir($dir)){
        //要判断的是$dirname下的路径是否是目录
        $newfile=$dirname."/".$filename;
        //is_dir()函数判断的是当前脚本的路径是不是目录
        if(is_dir($newfile)){
            //通过递归函数再遍历其子目录下的目录或文件
            total($newfile,$dirnum,$filenum);
            $dirnum++;
        }else{
            $filenum++;
        }
    }
    closedir($dir);
}

total("D:\phpStudy2018\PHPTutorial\WWW\EasyImages2.0\i",$dirnum,$filenum);
echo "目录总数：".$dirnum."<br>";
echo "文件总数：".$filenum."<br>";
//遍历指定文件目录与文件数量结束


function upTime($day = '2018-08-03'){
    return 1 + ceil((time()-strtotime($day))/60/60/24);//现在的时间减去过去指定日期，ceil()进一函数
    }

echo '网站已经正常运行'.upTime().'天';
