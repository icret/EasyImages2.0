### 上传成功后返回JSON示例
```json
    {
        "result":"success",
        "code":200,
        "url":"https:\/\/i2.100024.xyz\/2023\/01\/24\/10gwv0y-0.webp",
        "srcName":"195124",
        "thumb":"https:\/\/png.cm\/application\/thumb.php?img=\/i\/2023\/01\/24\/10gwv0y-0.webp",
        "del":"https:\/\/png.cm\/application\/del.php?hash=bW8vWG4vcG8yM2pLQzRJUGI0dHlTZkN4L2grVmtwUTFhd1A4czJsbHlMST0="
    }
```
- 返回示例解释
  `result` 返回状态
  `code` 返回状态编号 参考[常见状态代码](./常见状态代码.md)
  `url` 文件链接
  `srcName` 原始名称
  `thumb` 缩略图
  `del` 文件删除链接

### 上传示例 仅供参考

- html

```html
<form action="http://127.0.0.1/api/index.php" method="post" enctype="multipart/form-data">
    <input type="file" name="image" accept="image/*" required>
    <input type="text" name="token" placeholder="在tokenList文件找到token并输入" required> 
    <input type="submit" value="上传">
</form>

```
- Python

```Python
import requests

# 本地图片文件路径
image_path = "/path/to/your/image.jpg"

# token值，需从实际来源获取（例如读取tokenList文件）
token = "your_token_value_here"

# 目标URL
url = "http://127.0.0.1/api/index.php"

# 构建请求参数
files = {'image': open(image_path, 'rb')}
data = {'token': token}

# 发送POST请求
response = requests.post(url, files=files, data=data)

# 检查响应状态码
if response.status_code == 200:
    print("Upload successful.")
else:
    print(f"Upload failed with status code {response.status_code}.")
```
- Curl

```CURL
curl -X POST http://127.0.0.1/api/index.php \
-F "image=@/path/to/your/file/example.jpg" \
-F "token=your_token"
```

- JQuery

```JAVASCRIPT
// 获取文件和token
var file = document.querySelector('input[type="file"]').files[0];
var token = $('input[name="token"]').val();

// 创建FormData对象
var formData = new FormData();
formData.append('image', file);
formData.append('token', token);

// 发起上传请求
$.ajax({
    url: 'http://127.0.0.1/api/index.php',
    type: 'POST',
    data: formData,
    processData: false,
    contentType: false,
    success: function(response) {
        console.log('文件上传成功');
    },
    error: function(xhr, status, error) {
        console.error('文件上传失败: ' + error);
    }
});

```
