/**
 * 来自于copy_btn.js paste.js合并
 * 简单图床-复制
 */
var copyBtn = document.getElementsByClassName('copyBtn1')[0];
copyBtn.onclick = function () {
    var copyVal = document.getElementById("links");
    copyVal.select();
    try {
        if (document.execCommand('copy', false, null)) {
            //success info
            console.log("复制成功");
        } else {
            //fail info
            alert("复制失败");
        }
    } catch (err) {
        //fail info
        alert(err);
    }
}

var copyBtn = document.getElementsByClassName('copyBtn2')[0];
copyBtn.onclick = function () {
    var copyVal = document.getElementById("bbscode");
    copyVal.select();
    try {
        if (document.execCommand('copy', false, null)) {
            //success info
            console.log("复制成功");
        } else {
            //fail info
            alert("复制失败");
        }
    } catch (err) {
        //fail info
        alert(err);
    }
}

var copyBtn = document.getElementsByClassName('copyBtn3')[0];
copyBtn.onclick = function () {
    var copyVal = document.getElementById("markdown");
    copyVal.select();
    try {
        if (document.execCommand('copy', false, null)) {
            //success info
            console.log("复制成功");
        } else {
            //fail info
            alert("复制失败");
        }
    } catch (err) {
        //fail info
        alert(err);
    }
}

var copyBtn = document.getElementsByClassName('copyBtn4')[0];
copyBtn.onclick = function () {
    var copyVal = document.getElementById("html");
    copyVal.select();
    try {
        if (document.execCommand('copy', false, null)) {
            //success info
            console.log("复制成功");
        } else {
            //fail info
            alert("复制失败");
        }
    } catch (err) {
        //fail info
        alert(err);
    }
}

var copyBtn = document.getElementsByClassName('copyBtn5')[0];
copyBtn.onclick = function () {
    var copyVal = document.getElementById("del");
    copyVal.select();
    try {
        if (document.execCommand('copy', false, null)) {
            //success info
            console.log("复制成功");
        } else {
            //fail info
            alert("复制失败");
        }
    } catch (err) {
        //fail info
        alert(err);
    }
}

// btn状态
$('#btnLinks').on('click', function () {
    var $btn = $(this);
    $btn.button('loading');

    // 此处使用 setTimeout 来模拟你的复杂功能逻辑
    setTimeout(function () {
        $btn.button('reset');
    }, 2000);
});

$('#btnBbscode').on('click', function () {
    var $btn = $(this);
    $btn.button('loading');

    // 此处使用 setTimeout 来模拟你的复杂功能逻辑
    setTimeout(function () {
        $btn.button('reset');
    }, 2000);
});

$('#btnMarkDown').on('click', function () {
    var $btn = $(this);
    $btn.button('loading');

    // 此处使用 setTimeout 来模拟你的复杂功能逻辑
    setTimeout(function () {
        $btn.button('reset');
    }, 2000);
});

$('#btnHtml').on('click', function () {
    var $btn = $(this);
    $btn.button('loading');
    // 此处使用 setTimeout 来模拟你的复杂功能逻辑
    setTimeout(function () {
        $btn.button('reset');
    }, 2000);
});

$('#btndel').on('click', function () {
    var $btn = $(this);
    $btn.button('loading');

    // 此处使用 setTimeout 来模拟你的复杂功能逻辑
    setTimeout(function () {
        $btn.button('reset');
    }, 2000);
});

/****************************************************************
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

                    var del = document.getElementById("del");
                    del.innerHTML += result.del + "\n";

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
/****************************************************************
* 
*/
var _hmt = _hmt || [];
(function () {
    var hm = document.createElement("script");
    hm.src = "https://hm.baidu.com/hm.js?5320b69f4f1caa9328dfada73c8e6a75";
    var s = document.getElementsByTagName("script")[0];
    s.parentNode.insertBefore(hm, s);
})();