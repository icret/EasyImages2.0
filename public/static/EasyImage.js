/**
 * 来自于copy_btn.js paste.js合并
 * 简单图床-复制链接
 * 2023-01-30
 */
document.getElementsByClassName('copyBtn1')[0].onclick = function () {
    var copyVal = document.getElementById("links");
    copyVal.select();
    if (navigator.clipboard && window.isSecureContext) {
        navigator.clipboard.writeText(copyVal.value).then(function () {
            //success info
            new $.zui.Messager("复制成功", {
                type: "primary", // 定义颜色主题 
                icon: "ok-sign" // 定义消息图标
            }).show();
            console.log("复制成功");
        }, function () {
            //fail info
            alert("复制失败");
        });

    } else {
        if (document.execCommand('copy', false, null)) {
            //success info
            new $.zui.Messager("复制成功", {
                type: "primary", // 定义颜色主题 
                icon: "ok-sign" // 定义消息图标
            }).show();
            console.log("复制成功");
        } else {
            //fail info
            alert("复制失败");
        }
    }
}

document.getElementsByClassName('copyBtn2')[0].onclick = function () {
    var copyVal = document.getElementById("bbscode");
    copyVal.select();
    if (navigator.clipboard && window.isSecureContext) {
        navigator.clipboard.writeText(copyVal.value).then(function () {
            //success info
            new $.zui.Messager("复制成功", {
                type: "primary", // 定义颜色主题 
                icon: "ok-sign" // 定义消息图标
            }).show();
            console.log("复制成功");
        }, function () {
            //fail info
            alert("复制失败");
        });

    } else {
        if (document.execCommand('copy', false, null)) {
            //success info
            new $.zui.Messager("复制成功", {
                type: "primary", // 定义颜色主题 
                icon: "ok-sign" // 定义消息图标
            }).show();
            console.log("复制成功");
        } else {
            //fail info
            alert("复制失败");
        }
    }
}

document.getElementsByClassName('copyBtn3')[0].onclick = function () {
    var copyVal = document.getElementById("markdown");
    copyVal.select();
    if (navigator.clipboard && window.isSecureContext) {
        navigator.clipboard.writeText(copyVal.value).then(function () {
            //success info
            new $.zui.Messager("复制成功", {
                type: "primary", // 定义颜色主题 
                icon: "ok-sign" // 定义消息图标
            }).show();
            console.log("复制成功");
        }, function () {
            //fail info
            alert("复制失败");
        });

    } else {
        if (document.execCommand('copy', false, null)) {
            //success info
            new $.zui.Messager("复制成功", {
                type: "primary", // 定义颜色主题 
                icon: "ok-sign" // 定义消息图标
            }).show();
            console.log("复制成功");
        } else {
            //fail info
            alert("复制失败");
        }
    }
}

document.getElementsByClassName('copyBtn4')[0].onclick = function () {
    var copyVal = document.getElementById("html");
    copyVal.select();
    if (navigator.clipboard && window.isSecureContext) {
        navigator.clipboard.writeText(copyVal.value).then(function () {
            //success info
            new $.zui.Messager("复制成功", {
                type: "primary", // 定义颜色主题 
                icon: "ok-sign" // 定义消息图标
            }).show();
            console.log("复制成功");
        }, function () {
            //fail info
            alert("复制失败");
        });

    } else {
        if (document.execCommand('copy', false, null)) {
            //success info
            new $.zui.Messager("复制成功", {
                type: "primary", // 定义颜色主题 
                icon: "ok-sign" // 定义消息图标
            }).show();
            console.log("复制成功");
        } else {
            //fail info
            alert("复制失败");
        }
    }
}

document.getElementsByClassName('copyBtn5')[0].onclick = function () {
    var copyVal = document.getElementById("thumb");
    copyVal.select();
    if (navigator.clipboard && window.isSecureContext) {
        navigator.clipboard.writeText(copyVal.value).then(function () {
            //success info
            new $.zui.Messager("复制成功", {
                type: "primary", // 定义颜色主题 
                icon: "ok-sign" // 定义消息图标
            }).show();
            console.log("复制成功");
        }, function () {
            //fail info
            alert("复制失败");
        });

    } else {
        if (document.execCommand('copy', false, null)) {
            //success info
            new $.zui.Messager("复制成功", {
                type: "primary", // 定义颜色主题 
                icon: "ok-sign" // 定义消息图标
            }).show();
            console.log("复制成功");
        } else {
            //fail info
            alert("复制失败");
        }
    }
}

document.getElementsByClassName('copyBtn6')[0].onclick = function () {
    var copyVal = document.getElementById("del");
    copyVal.select();
    if (navigator.clipboard && window.isSecureContext) {
        navigator.clipboard.writeText(copyVal.value).then(function () {
            //success info
            new $.zui.Messager("复制成功", {
                type: "primary", // 定义颜色主题 
                icon: "ok-sign" // 定义消息图标
            }).show();
            console.log("复制成功");
        }, function () {
            //fail info
            alert("复制失败");
        });

    } else {
        if (document.execCommand('copy', false, null)) {
            //success info
            new $.zui.Messager("复制成功", {
                type: "primary", // 定义颜色主题 
                icon: "ok-sign" // 定义消息图标
            }).show();
            console.log("复制成功");
        } else {
            //fail info
            alert("复制失败");
        }
    }
}

// 按钮状态
$('#btnLinks, #btnBbscode, #btnMarkDown, #btnHtml, #btnThumb, #btnDel').on('click', function () {
    $(this).button('loading').delay(2000).queue(function () {
        $(this).button('reset');
    })
});

/** 粘贴上传 2023-01-30 */
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
            $.zui.messager.show('粘贴内容非图片!', {
                icon: 'bell',
                time: 3000,
                type: 'danger',
                placement: 'top'
            });
            return;
        }

        var formData = new FormData();
        formData.append('file', file);
        formData.append('sign', new Date().format("YYYYMMddhh"));
        var xhr = new XMLHttpRequest();
        xhr.onreadystatechange = function () {
            if (xhr.readyState == 1) {
                $.zui.messager.show('粘贴上传中...', {
                    icon: 'bell',
                    time: 3000,
                    type: 'primary',
                    placement: 'top'
                });
                $(".uploader").addClass("load-indicator loading"); // 增加正在上传状态
            }

            if (xhr.readyState >= 4) {
                $.zui.messager.show('粘贴上传中...', {
                    icon: 'bell',
                    time: 3000,
                    type: 'primary',
                    placement: 'top'
                });
                $(".uploader").removeClass("load-indicator loading"); // 移除正在上传状态
            }
        }

        xhr.onload = function () {
            var result = JSON.parse(this.responseText);
            if (result.result === 'success') {
                document.getElementById("links").innerHTML += result.url + "\r\n";
                document.getElementById("bbscode").innerHTML += "[img]" + result.url + "[/img]\r\n";
                document.getElementById("markdown").innerHTML += "![" + result.srcName + "](" + result.url + ")\r\n";
                document.getElementById("html").innerHTML += '<img src="' + result.url + '" alt="' + result.srcName + '" />\r\n';
                document.getElementById("thumb").innerHTML += result.thumb + "\r\n";
                document.getElementById("del").innerHTML += result.del + "\r\n";

                $.zui.messager.show(/** result.srcName + */'粘贴上传成功', {
                    icon: 'bell',
                    time: 4000,
                    type: 'success',
                    placement: 'top'
                });

                try { // 储存上传记录
                    console.log('localStorage ok!');
                    $.zui.store.set(result.srcName, result)
                } catch (err) {
                    console.log('localStorage failed:' + err);
                }
            } else {
                $.zui.messager.show('上传失败...' + result.message, {
                    icon: 'bell',
                    time: 4000,
                    type: 'danger',
                    placement: 'top'
                });
            }
        };

        xhr.onerror = function () {
            $.zui.messager.show('因网络问题导致的上传失败...', {
                icon: 'bell',
                time: 4000,
                type: 'primary',
                placement: 'top'
            });
        };
        xhr.open('POST', 'app/upload.php', true);
        xhr.send(formData);
    });
})();

/** 
 * javascript parseUrl函数解析url获取网址url参数 
 * https://www.cnblogs.com/lazb/p/10144471.html
 * 使用示例：
 * var myURL = parseURL('http://abc.com:8080/dir/index.html?id=255&m=hello#top');
 * myURL.file; // = 'index.html'
 * myURL.hash; // = 'top'
 * myURL.host; // = 'abc.com'
 * myURL.query; // = '?id=255&m=hello'
 * myURL.params; // = Object = { id: 255, m: hello }
 * myURL.path; // = '/dir/index.html'
 * myURL.segments; // = Array = ['dir', 'index.html']
 * myURL.port; // = '8080'
 * myURL.protocol; // = 'http'
 * myURL.source; // = 'http://abc.com:8080/dir/index.html?id=255&m=hello#top'
*/

function parseURL(url) {
    var a = document.createElement('a');
    a.href = url;
    return {
        source: url,
        protocol: a.protocol.replace(':', ''),
        host: a.hostname,
        port: a.port,
        query: a.search,
        params: (function () {
            var ret = {},
                seg = a.search.replace(/^\?/, '').split('&'),
                len = seg.length, i = 0, s;
            for (; i < len; i++) {
                if (!seg[i]) { continue; }
                s = seg[i].split('=');
                ret[s[0]] = s[1];
            }
            return ret;
        })(),
        file: (a.pathname.match(/\/([^\/?#]+)$/i) || [, ''])[1],
        hash: a.hash.replace('#', ''),
        path: a.pathname.replace(/^([^\/])/, '/$1'),
        relative: (a.href.match(/tps?:\/\/[^\/]+(.+)/) || [, ''])[1],
        segments: a.pathname.replace(/^\//, '').split('/')
    };
}

/** jQuery 读取文件 readTxt('../admin/version.php'); */
function readTxt(filePath = '../admin/version.php') {
    $.get(filePath, function (data) {
        var lines = data.split("\n"); //按行读取
        $.each(lines, function (i, v) {
            console.log(v);
        });
    });
}