
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