1. 下载并安装[uPic releases][1]或者[Mac App Store][2] | [App Store][3]（测试版本：uPic v0.21.1）
2. 创建自定义图床并按照截图填写
3. 返回uPic主菜单栏, 选择默认上传为自定义图床

![EasyImage简单图床使用uPic上传图片](images/uPic1.avif)

![EasyImage简单图床使用uPic上传图片](images/uPic.avif)

```uPic
API地址:https://png.cm/api/index.php // 输入你网站api地址
请求方式: POST
文件字段名: image
添加其他字段：
    - 增加Header字段: content-type multipart/form-data
    - 增加Body字段: token 1c17b11693cb5ec63859b091c5b9c1b2 // 使用你的token
url路径: ["url"]
```

  [1]: https://github.com/gee1k/uPic/releases
  [2]: https://apps.apple.com/cn/app/id1549159979
  [3]: https://apps.apple.com/us/app/id1510718678
