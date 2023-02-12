- 上传成功后返回JSON

```json
    {
        "result":"success","code":200,
        "url":"https:\/\/i2.100024.xyz\/2023\/01\/24\/10gwv0y-0.webp",
        "srcName":"195124",
        "thumb":"https:\/\/png.cm\/application\/thumb.php?img=\/i\/2023\/01\/24\/10gwv0y-0.webp",
        "del":"https:\/\/png.cm\/application\/del.php?hash=bW8vWG4vcG8yM2pLQzRJUGI0dHlTZkN4L2grVmtwUTFhd1A4czJsbHlMST0="
    }
```

- html示例 

```html
<form action="http://127.0.0.1/api/index.php" method="post" enctype="multipart/form-data">
    <input type="file" name="image" accept="image/*">
    <input type="text" name="token" placeholder="在tokenList文件找到token并输入" /> <input type="submit" />
</form>
```
- Python示例

```python
import requests

url = "https://png.cm/api/index.php"

payload = "-----011000010111000001101001\r\nContent-Disposition: form-data; name=\"token\"\r\n\r\n8337effca0ddfcd9c5899f3509b23657\r\n-----011000010111000001101001\r\nContent-Disposition: form-data; name=\"image\"\r\n\r\n195124.jpg\r\n-----011000010111000001101001--\r\n\r\n"
headers = {"content-type": "multipart/form-data; boundary=---011000010111000001101001"}

response = requests.request("POST", url, data=payload, headers=headers)

print(response.text)
```
- curl示例

```curl
curl --request POST \
  --url https://png.cm/api/index.php \
  --header 'content-type: multipart/form-data' \
  --form token=8337effca0ddfcd9c5899f3509b23657 \
  --form image=@195124.jpg
```
- JQuery示例

```jQuery
const form = new FormData();
form.append("token", "8337effca0ddfcd9c5899f3509b23657");
form.append("image", "195124.jpg");

const settings = {
  "async": true,
  "crossDomain": true,
  "url": "https://png.cm/api/index.php",
  "method": "POST",
  "headers": {},
  "processData": false,
  "contentType": false,
  "mimeType": "multipart/form-data",
  "data": form
};

$.ajax(settings).done(function (response) {
  console.log(response);
});
```
