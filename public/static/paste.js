/**
 * 复制、截图 简单图床修改版
 */
(function () {
    document.addEventListener('paste', function (e) {
        var items = ((e.clipboardData || window.clipboardData).items) || [];
        var file = null;

        if (items && items.length) {
            for (var i = 0; i < items.length; i++) {
                if (items[i].type.indexOf('image') !== -1) {
                    file = items[i].getAsFile();
                    break;
                }
            }
        }

        if (!file) {
            alert('粘贴内容非图片！');
            return;
        }
        var formData = new FormData();
        formData.append('file', file);

        var xhr = new XMLHttpRequest();
        xhr.onload = function () {
            try {
                var result = JSON.parse(this.responseText);
                if (result.result === 'success') {
                    var links = document.getElementById("links");
                    links.innerHTML += result.url + "\n";

                    var bbscode = document.getElementById("bbscode");
                    bbscode.innerHTML += "[img]" + result.url + "[/img]\n";

                    var markdown = document.getElementById("markdown");
                    markdown.innerHTML += "![](" + result.url + ")\n";

                    var html = document.getElementById("html");
                    html.innerHTML += "&lt;img src=\"" + result.url + "\" /&#62;\n";
                } else {
                    alert('上传失败1');
                }
            } catch (e) {
                alert('上传失败2');
            }
        };
        xhr.onerror = function () {
            alert('上传失败3');
        };
        xhr.open('POST', './file.php', true);
        xhr.send(formData);
    });
})();