/**
 * 来自于copy_btn.js paste.js合并
 * 简单图床-复制
 * 2023-01-30
 */
var copyBtn = document.getElementsByClassName('copyBtn1')[0];
copyBtn.onclick = function () {
    var copyVal = document.getElementById("links");
    copyVal.select();
    try {
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
    } catch (err) {
        alert(err);
    }
}

var copyBtn = document.getElementsByClassName('copyBtn2')[0];
copyBtn.onclick = function () {
    var copyVal = document.getElementById("bbscode");
    copyVal.select();
    try {
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
    } catch (err) {
        alert(err);
    }
}

var copyBtn = document.getElementsByClassName('copyBtn3')[0];
copyBtn.onclick = function () {
    var copyVal = document.getElementById("markdown");
    copyVal.select();
    try {
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
    } catch (err) {
        alert(err);
    }
}

var copyBtn = document.getElementsByClassName('copyBtn4')[0];
copyBtn.onclick = function () {
    var copyVal = document.getElementById("html");
    copyVal.select();
    try {
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
    } catch (err) {
        alert(err);
    }
}

var copyBtn = document.getElementsByClassName('copyBtn5')[0];
copyBtn.onclick = function () {
    var copyVal = document.getElementById("thumb");
    copyVal.select();
    try {
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
    } catch (err) {
        alert(err);
    }
}

var copyBtn = document.getElementsByClassName('copyBtn6')[0];
copyBtn.onclick = function () {
    var copyVal = document.getElementById("del");
    copyVal.select();
    try {
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
    } catch (err) {
        alert(err);
    }
}

// 按钮状态

$('#btnLinks, #btnBbscode, #btnMarkDown, #btnHtml, #btnThumb, #btnDel').on('click', function () {
    $(this).button('loading').delay(2000).queue(function () {
        $(this).button('reset');
    })
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
                var links = document.getElementById("links");
                links.innerHTML += result.url + "\n";

                var bbscode = document.getElementById("bbscode");
                bbscode.innerHTML += "[img]" + result.url + "[/img]\n";

                var markdown = document.getElementById("markdown");
                markdown.innerHTML += "![](" + result.url + ")\n";

                var html = document.getElementById("html");
                html.innerHTML += "&lt;img src=\"" + result.url + "\" /&#62;\n";

                var del = document.getElementById("thumb");
                del.innerHTML += result.thumb + "\n";

                var del = document.getElementById("del");
                del.innerHTML += result.del + "\n";

                $.zui.messager.show('粘贴上传成功...', {
                    icon: 'bell',
                    time: 4000,
                    type: 'success',
                    placement: 'top'
                });

            } else {
                $.zui.messager.show('上传失败...' + result.message, {
                    icon: 'bell',
                    time: 4000,
                    type: 'primary',
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
        xhr.open('POST', './application/upload.php', true);
        xhr.send(formData);
    });
})();
/******************************************************************/

// 导航状态
$('.nav-pills').find('a').each(function () {
    if (this.href == document.location.href) {
        $(this).parent().addClass('active'); // this.className = 'active';
    }
});