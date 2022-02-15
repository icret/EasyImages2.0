## EasyImage 简单图床 2.0
> 始于2018年7月，支持多文件上传,简单无数据库,返回图片url,markdown,bbscode,html的一款图床程序
演示地址：[https://png.cm/](https://png.cm/) 
之前一直用的图床程序是:[PHP多图长传程序2.4.3](https://www.jb51.net/codes/40544.html)
由于版本过老并且使用falsh上传，在当前html5流行大势所趋下，遂利用基础知识新写了一个以html5为默认上传并且支持flash,向下兼容至IE9。
>

[演示](https://png.cm/) &nbsp;
[Chrome 拓展](https://github.com/icret/EasyImage-Browser-Extension) &nbsp;
[使用手册](https://www.kancloud.cn/easyimage/easyimage/) &nbsp;
[问题反馈](https://support.qq.com/products/367633) &nbsp;
[QQ群](https://shang.qq.com/wpa/qunwpa?idkey=3feb4e8be8f1839f71e53bf2e876de36afc6889b2630c33c877d8df5a5583a6f)

[![PHP](https://img.shields.io/badge/PHP->=5.6-orange.svg)](http://php.net)
[![Release](https://img.shields.io/github/v/release/icret/EasyImages2.0)](https://github.com/icret/EasyImages2.0/releases)
[![Issues](https://img.shields.io/github/issues/icret/EasyImages2.0)](https://github.com/icret/EasyImages2.0/issues)
[![stargazers](https://img.shields.io/github/stars/icret/EasyImages2.0)](https://github.com/icret/EasyImages2.0/stargazers)
[![Code size](https://img.shields.io/github/languages/code-size/icret/EasyImages2.0?color=blueviolet)](https://github.com/icret/EasyImages2.0)
[![License](https://img.shields.io/badge/license-GPL_V2.0-yellowgreen.svg)](https://github.com/icret/EasyImages2.0/blob/master/LICENSE)

>本人善写bug 发现bug可提交 [issues](https://github.com/icret/EasyImages2.0/issues) 追求稳定请下载 [稳定版](https://github.com/icret/EasyImages2.0/releases)

## 目录
[特点](#特点) &nbsp;[使用注意](#使用注意) &nbsp;[安装](#安装) &nbsp;[程序升级](#程序升级) &nbsp;[安全配置](#安全配置) &nbsp;[更新日志](#更新日志) &nbsp;[支持开发者](#支持开发者) &nbsp;[界面演示](#界面演示) &nbsp;[兼容](#兼容) &nbsp;[鸣谢](#鸣谢) &nbsp;[开源许可](#开源许可) &nbsp;

## 特点

- [x] 支持仅登录后上传
- [x] 支持设置图片质量
- [x] 支持文字/图片水印
- [x] 支持设置图片指定宽/高
- [x] 支持上传图片转换为指定格式
- [x] 支持限制最低宽度/高度上传
- [x] 支持API
- [x] 在线管理图片
- [x] 支持网站统计
- [x] 支持设置广告
- [x] 支持图片鉴黄
- [x] 支持自定义代码
- [x] 支持上传IP黑白名单
- [x] 支持创建仅上传用户
- [x] 更多支持请安装尝试···

## 使用注意

1. 请将所有文件赋予`0755`和`www`权限
2. 对`PHP`不太熟悉的请不要将图床程序放置于二级目录
3. 请关闭防跨站或删除域名文件夹内的`user.ini`文件 如宝塔面板
4. 网站域名与图片域名必须填写，如果只有一个域名请填写成一样的
5. 第一次使用会执行安装程序并生成`install.lock` 不执行安装可以删除`install`目录
6. 第一次访问会检查环境并在`config`目录下生成`EasyImage.lock`
7. 可以使用谷歌浏览器的调试模式查看错误`F12->console`
8. 出现`undefined function imagecreatefromwebp()`是因为GD没安装webp
9. 出现`upload File size exceeds the maximum value` 调整`PHP`上传大小
10. 出现`Warning: is_dir(): open_basedir restriction in effect`解决方法同`3`
11. 出现`Fatal error: Allowed memory size......`主机内存或分配给PHP的内存不够 解决方法百度
12. 不出验证码: 权限问题见问题1 CDN缓存了/开防火墙了

## 安装

- windows:
- 下载简单图床 [最新版](https://github.com/icret/EasyImages2.0/archive/refs/heads/master.zip) | [稳定版](https://github.com/icret/EasyImages2.0/releases) 上传至web目录

- Linux:

- `git clone https://github.com/icret/EasyImages2.0.git` 至web目录赋予www:www和0755权限

 ## 程序升级
 
- 备份`config`(没有增加上传用户和api可以只保留`config.php`文件)目录和`上传目录`
- 将新程序下载至网站目录解压覆盖，然后将备份的文件替换既完成升级

## 安全配置

- Apache环境在上传目录添加配置文件`.htaccess` 使上传目录不可运行PHP程序（默认已经存在)

```Apache
<FilesMatch "\.(?i:php|php3|php4|php5)">
Order allow,deny
Deny from all
</FilesMatch>
```
- Nginx环境禁止多个目录运行`PHP`程序：

```Nginx
    # "i|public"是你要禁止的目录 放到listen段落之后才生效
    location ~* ^/(i|public)/.*\.(php|php5)$
   {
      deny all;
    }
```

- Lighthttpd环境禁止多个目录运行`PHP`程序：

```Lighthttpd
$HTTP["url"] =~ "^/(i|public)/" {
	fastcgi.server = ()
}
```
 - 或者参考：[https://blog.png.cm/996.html](https://blog.png.cm/996.html)

## 更新日志

<details><summary><mark>点击查看2.0版更新日志</mark></summary>

* 2022-2-13 v2.5.1
- 异步执行鉴黄
- 取消检测imagick扩展
- 修复可能导致检测弹窗弹出失败

* 2022-2-7 v2.5.0
- 修复静态文件调用失败

* 2022-2-6 v2.4.9
- 修复静态文件引用

* 2022-2-6 v2.4.9
- 修复flash和silverlight路径引用(>IE9不影响)
- markdown html alt值改为源文件名
- 升级jquery-3.4.1至3.6.0
- 调整了静态文件位置

* 2022-2-5 v2.4.8
- 调整缩略图内存至128M
- 修复无可疑图片时显示错误
- 修复转换为webp时会复制一份bug
- 修复开启登录上传后无法上传的bug
- 插件检测的敏感信息转移到管理目录
- 增加安装时检测.user.ini
- 增加检测鉴黄接口是否可以正确访问
- 增加异步处理文件,上传完毕后处理速度变快了
- 增加 [nsfwjs](https://github.com/infinitered/nsfwjs) 接口方式检测违规图片
  - 作者测试时用的`docker`搭建 `docker`地址:[zengdawei/nsfw_restful_api
](https://hub.docker.com/r/zengdawei/nsfw_restful_api)
  - 使用注意 程序期望nsfwjs返回json 并且如下格式：
```json
[
    {
        "className": "Drawing",
        "probability": 0.824431836605072
    },
    {
        "className": "Hentai",
        "probability": 0.16360442340373993
    },
    {
        "className": "Neutral",
        "probability": 0.007620695047080517
    },
    {
        "className": "Porn",
        "probability": 0.004154415801167488
    },
    {
        "className": "Sexy",
        "probability": 0.00018858206749428064
    }
]
```
- 增加WordPress上大名鼎鼎的实时缩略图生成TimThumb
  - TimeThumb为本图床修改版,会缓存到缓存文件夹方便下次调用

* 2022-1-27 v2.4.7
- 优化页面排版
- 更改部分命名
- 增加后端压缩率
- 增加可以显示多条公告
- 增加上传后是否显示删除
- 增加可以关闭广场/统计导航|页面
- 调整登录和退出文件位置
- 调整二维码内容为每个页面
- 更换验证码库并不再区分大小写
- 修复一处有概率暴露图片绝对路径的bug

* 2022-1-22 v2.4.6
- 视图优化
- 删除重复内容
- 增加图片信息页面
- 增加上传黑/白名单
- 修复因关闭上传日志而导致的无法鉴黄和后端压缩图片
- 修复安装时更改管理员账号失败
- 修复更改管理员账户后无法退出

* 2022-1-13 v2.4.5
- 修复一处权限问题
- 修复恢复可疑图片
- 增加复制提示
- 增加默认上传后首选显示链接

* 2022-1-3 v2.4.5 beta
- 增加复制提示
- 更新安装代码
- 更改前端样式
- 更新上传格式
- 重构了密码验证
- 使用md5存储密码
- 增加后台设置提示
- 增加更改网站配色
- 增加缩略图索引格式
- 调整后台分类及位置
- 增加在线修改账号密码
- 增加以源文件名称命名
- 增加缩略图两种生成方式和开关
- 修复开启前端压缩导致的上传图片异常
- 屏蔽因缺少PHP扩展而不能生成缩略图的格式

* 2021-12-25 v2.4.4
- 更改favicon.ico
- 修复缩略图数量统计
- 增加缩略图生成开关
- 日志增加更多文件信息
- 前端增加裁剪和压缩质量
- 上传失败将会输出更多信息
- 修复前端压缩图片不能关闭问题
- 修复上传设置中错误和页面显示
- 调整网站设置->上传设置的排序
- 将快捷操作中心转移到网站设置中
- 修复因生成缩略图导致的前端数据返回失败
- 增加简单图床chrome浏览器插件，可自行配置网站->[EasyImage-Browser-Extension](https://github.com/icret/EasyImage-Browser-Extension)

* 2021-11-17 v2.4.3
- 增加登录验证码
- 二级目录安装
- 一些优化

* 2021-11-14 v2.4.2
- 增加上传日志

* 2021-11-12 v2.4.1
- 增加缓存周期配置
- 增加上传统计
- 增加viewjs
- 更新依赖件
- 修复统计错误

* 2021-11-9 v2.4.0
- 增加统计缓存
- 增加最近30天上传统计与占用空间图表
- 增加初始化安装（可能会不支持二级目录安装，可删除install文件夹初始化)
- 增加在线编辑配置(之前是需要修改config.php文件，现在可以直接网站端修改了)
- 删除广场会导致浏览速度变慢的代码
- 删除快捷配置会导致浏览速度变慢的代码

* 2021-11-3 v2.3.2
- 增加广场图片缓存
- 重构广场样式

* 2021-11-3 v2.3.1
- 增加监黄接口
- 增加审核违规图片
- 修复对php5.6的支持
- 修复二级目录的安装

* 2021-10-24 v2.3.0
- 将服务器环境监测改为第一次打开时自动检测（如需再次展示需删除config目录下的EasyImage.lock）
- 增加快捷操作中心显示服务信息
- 增加对上传文件的命名方式（详见config.php文件里的注释）
- 增加隐私政策、服务条款、DMCA
- 增加自定义静态文件CDN源
- 增加dns-prefetch
- 删除了tinyfilemanager文件管理（感觉没什么用）
- 一些bug得以修复

* 2021-5-22 v2.2.0
- 增加根目录静态属性
- 增加浏览页面懒加载
- 增加浏览页面启用选定日期查看图片
- 增加版本检测 ***每月10日06点和25日01点检测Github是否更新***
- 增加上传压缩 ***此压缩有可能使图片变大！特别是小图片 也有一定概率改变图片方向***
- 增加批量压缩目录 ***TinyPng或本机压缩，本机压缩出现的问题***
- 修复title
- 修复二级目录安装
- 修复对PHP5.6的兼容 ***建议使用php7.0及以上！***

* 2021-5-8 v2.1.1
- 修复上传界面上传失败提示信息bug
- 浏览页面重构
- 删除页面添加登录删除
- 调整首页显示
- 将调整图片长宽放置前端，减小资源开销
- 其他小调整

* 2021-5-2 v2.1
- 将tinyfilemanager配置文件简单翻译并集成到config.php
- 增加底部自定义信息
- 增加检测PHP环境，给与提示
- 增加删除图片url（服务器不会保存删除链接）
- 恢复随机浏览20张上传图片 可以设定浏览数量和关闭浏览
- - 随机浏览图片可以在线删除
- 可以使用 https://png.cm/libs/list.php?num=100 定义浏览数量
- 修复一些调用
- 更改二维码显示方式
- 开启api 需要token验证上传
- 重构并修复check.php相关文件
- 重构部分代码
- 更改目录结构
- 增加安全性配置
- * Apache配置文件默认设置上传目录不可运行 

```Apache
RewriteEngine on RewriteCond % !^$
RewriteRule i/(.*).(php)$ – [F]
RewriteRule public/(.*).(php)$ – [F]
RewriteRule config/(.*).(php)$ – [F]
```

- * Nginx请在Nginx配置：

```Nginx
 # 禁止运行php的目录
    location ~* ^/(i|public|config)/.*\.(php|php5)$
    {
     deny all;
    }
```
- - 或者参考：https://blog.png.cm/939.html
- 一些精简

* 2021-4-14 v2.0.2.1 Dev1
- 更新静态文件版本
- 请所有更新过2.0.2.1版本升级到此版本
- 更改一些描述
- md5提交登录验证
- 登录上传也显示公告

* 2021-03-28 v2.0.2.1
- 更新管理程序，修复部分漏洞
- 修复不能等比例缩小图片 
- 支持php8

* 2019-6-26 v2.0.2.0
- 精简压缩代码，使得不再压缩后反而变大
- 删除异域上传功能，不再支持异域上传
- 修复开启登录后无法粘贴密码
- 后台控制上传数量,上传格式
- 其他一些优化

* 2019-6-14 v2.0.1.9
- 增加复制链接按钮
- 增加暂停上传按钮
- 增加QQ截图，剪切板上传
- 增加文字/图片水印透明度
- 恢复开启/关闭api上传
- 恢复支持水印文字颜色
- 恢复支持远程上传图片
- 修复安装时候的权限
- 修复管理无法多选的问题
- 修复上传透明png背景变为纯黑的问题
- 修复成功上传图片但前端无法获取链接
- 修复在centos64 lnmp1.6 php7.1环境下的图片信息读取问题
- 修改图片压缩方式，速度更快，相比之前提高5倍以上
- 更改管理路径
- 更改上传路径，文件名更短
- 更改上传显示方式为缩略图
- 关闭添加图片后自动上传
- 纪念一下2019年，将版本号改为2.0.1.9

* 2019-5-23 v2.0
- 在继承上个版本（1.6.4）的基础上进行了全新优化
- 修复上传经常失败的问题
- 删除一些不常用但会增加功耗的过程
- 全新的压缩 将文件继续缩小
- 全新的目录系统，精简代码
- 设置仅允许在config.php修改，注释更加明了，即使没有代码基础也可以操作
- 增加新的文件管理系统，感谢 tinyfilemanager
- ~~支持文字/图片水印 可自定义文字颜色~~
- ~~支持文字水印背景颜色~~
- ~~支持文字水印透明度~~
- ~~支持删除远程上传文件~~ -> 不再支持删除远程文件
- ~~(支持开启/关闭api自定义文字水印)~~
- ~~支持删除自定义删除图片(仅管理员)~~
</details>

<details><summary><mark>与1.6.4版本差别</mark></summary>

##### 不建议再使用 [EasyImage 1.6.4版本](https://github.com/icret/easyImages)

- 在继承上个版本（[1.6.4](https://github.com/icret/easyImages "1.6.4")）的基础上进行了全新优化
- 修复上传经常失败的问题
- 删除一些不常用但会增加功耗的过程 （删除的在下边会有标记）
- 全新的压缩 将文件继续缩小
- 全新的目录系统，精简代码
- 设置仅允许在config.php修改，注释更加明了，即使没有代码基础也可以操作
- 增加新的文件管理系统，感谢 tinyfilemanager
- ~~支持文字/图片水印 可自定义文字颜色~~
- ~~支持文字水印背景颜色~~
- ~~支持文字水印透明度~~
- ~~支持删除远程上传文件~~ -> 不再支持删除远程文件
- ~~(支持开启/关闭api自定义文字水印)~~
- ~~支持删除自定义删除图片(仅管理员)~~

</details>

 ## 支持开发者
 |支付宝支持|微信支持| 
 |:----:|:----:|
 |![支付宝支持](https://png.cm/sponsor/images/alipay.jpg)|![微信支持](https://png.cm/sponsor/images/wechat.jpg)|
 
 ## 界面演示
 
 ![简单图床 - 上传界面](https://blog.png.cm/usr/uploads/2022/02/674074848.png)
 ![简单图床 - 广场界面](https://blog.png.cm/usr/uploads/2022/02/3053540273.png)
 ![简单图床 - 后台界面](https://blog.png.cm/usr/uploads/2022/02/2657944724.png)
 ![简单图床 - 统计界面](https://blog.png.cm/usr/uploads/2022/02/1305032567.png)
  
## 兼容
 - 最低`PHP 5.6`,推荐`PHP 7.0`及以上版本，需要PHP支持`Fileinfo,iconv,zip,mbstring,openssl`扩展,如果缺失会导致无法上传/删除图片
 - 文件上传视图提供文件列表管理和文件批量上传功能，允许拖拽（需要`HTML5`支持）来添加上传文件，支持上传大图片，优先使用`HTML5`旧得浏览器自动使用`Flash和Silverlight`的方式兼容

## 鸣谢
 
  - [verot](https://github.com/verot/class.upload.php "verot" )
  - [ZUI](https://github.com/easysoft/zui "ZUI" )
  
## 开源许可

 - [GPL-2.0](https://github.com/icret/EasyImages2.0/blob/master/LICENSE) 
 - Copyright © 2018 EasyImage dev By [Icret](https://github.com/icret)

 * have fun!

 [![Stargazers over time](https://starchart.cc/icret/EasyImages2.0.svg)](https://github.com/icret/EasyImages2.0/stargazers)
