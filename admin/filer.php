<?php

/**
 * Powered by https://github.com/rehiy/web-indexr
 */

/**开始 - 自定义修改 */
require_once __DIR__ . '/../app/function.php';
require_once __DIR__ . '/../config/config.php';

// 开启tinyfilemanager文件管理
if (!$config['file_manage']) {
    require_once APP_ROOT . '/app/header.php';
    echo '<h4 class="alert alert-danger">文件管理已关闭~~</h4>';
    header("refresh:3;url=" . $config['domain'] . '?manag-closed');
    exit(require_once APP_ROOT . '/app/footer.php');
}
/**结束 - 自定义修改 */

// 目录绝对路径，结尾不加 `/`
// RexHelper::$root = $_SERVER['DOCUMENT_ROOT'];
RexHelper::$root = $_SERVER['DOCUMENT_ROOT'];


// 系统用户列表，密码类型 MD5
RexHelper::$users[$config['user']] = array(
    'password' => $config['password']
);

// 可编辑文件后缀，开头不加 `.`
// RexHelper::$text_suff[] = "jsx";
// RexHelper::$text_suff[] = "php5";

// 文件排除规则，仅正则表达式
RexHelper::$ignore_list = array(
    '/^\.git|.php|.htaccess|robots.txt|favicon.ico|README.md/', '/^admin|api|app|config|docs|install|public/'
);

session_start();
?>
<!DOCTYPE html>
<html lang="zh-Hans-CN">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="renderer" content="webkit" />
    <meta name="force-rendering" content="webkit" />
    <meta name="author" content="Icret EasyImage2.0">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>文件管理 - <?php echo $config['title']; ?></title>
    <meta name="keywords" content="<?php echo  $config['keywords']; ?>" />
    <meta name="description" content="<?php echo  $config['description']; ?>" />
    <link rel="shortcut icon" href="<?php static_cdn(); ?>/favicon.ico" type="image/x-icon" />
    <link rel="stylesheet" href="<?php static_cdn(); ?>/public/static/zui/css/zui.min.css">
    <script type="application/javascript" src="<?php static_cdn(); ?>/public/static/zui/lib/jquery/jquery-3.6.0.min.js"></script>
    <script type="application/javascript" src="<?php static_cdn(); ?>/public/static/zui/js/zui.min.js"></script>
    <style type="text/css">
        body {
            font-size: 14px;
        }

        a {
            text-decoration: none;
        }

        .login {
            margin: 100px auto;
            max-width: 95%;
            width: 320px;
        }

        .view-image {
            max-width: 95%;
            width: 320px;
        }
    </style>
</head>

<body>
    <?php
    // 模块参数
    $view = G('view');
    $action = G('action');
    $path = G('path', '/');
    // 已登陆
    if (RexAction::user_info()) {
        if ($action) {
            RexAction::action($path, $action);
        } else {
            RExplorer::body_navbar($path, $view);
            echo '<div class="container">';
            RExplorer::body_breadcrumb($path);
            RExplorer::index($path, $view);
            echo '</div>';
        }
    }
    // 未登录
    else {
        if ($action == 'login') {
            RexAction::action($path, $action);
        } else {
            RExplorer::view_login($path);
        }
    }
    ?>
    <script type="text/javascript">
        function do_cofirm(url, act) {
            var i = 0;
            while (i++ < 3) {
                if (!confirm(i + '.重要的操作要重复问三遍，您确定要' + act + '吗？')) {
                    return false;
                }
            }
            location.href = url;
        }
    </script>
</body>

</html>

<?php
/**
 * 获取参数
 * @param string $name
 * @param mixed $defv
 * @return mixed
 */
function G($name, $defv = '')
{
    if (isset($_REQUEST[$name])) {
        return $_REQUEST[$name];
    }
    return $defv;
}

/**
 * 页面主类
 */
class RExplorer
{
    /**
     * 显示网站目录的项目内容
     */
    static function index($path, $view)
    {
        $method = 'view_' . $view;
        if (method_exists('RExplorer', $method)) {
            return self::$method($path);
        }
        // 查看路径
        $sapath = RexHelper::path_rtoa($path);
        if (is_dir($sapath)) {
            return self::view_list($path);
        }
        if (is_file($sapath)) {
            return self::view_edit($path);
        }
        echo '<strong class="red">路径不存在或无权限访问！</strong>';
    }

    /**
     * 用户登录视图
     * @param string $path 路径
     */
    static function view_login($path)
    {
        echo '
        <div class="login"">
            <form class="form-horizontal"  method="post" action="?action=login">
            <h5 class="card-title text-center mb-5">文件管理 <small>v' . RexHelper::$version . '</small></h5>
                <div class="form-group">
                    <label for="exampleInputEmail3">账号</label>
                    <input type="text" class="form-control" name="username" id="exampleInputEmail3" placeholder="登录账号">
                </div>
                <div class="form-group">
                    <label for="exampleInputInviteCode3">密码</label>
                    <input type="password" class="form-control" name="password" id="exampleInputInviteCode3" placeholder="登录密码">
                </div>                
                <button type="submit" class="btn btn-primary">登录</button>
            </form>
        </div>';
    }

    /**
     * 目录内容视图
     * @param string $path 路径
     */
    static function view_list($path)
    {
        $sapath = RexHelper::path_rtoa($path);
        $fslist = iterator_to_array(new FilesystemIterator($sapath, 256));
        ksort($fslist, SORT_NATURAL);
        usort($fslist, function ($a, $b) {
            if ($a->isDir() && !$b->isDir()) {
                return -1;
            }
            if (!$a->isDir() && $b->isDir()) {
                return 1;
            }
            return 0;
        });
        echo '
        <div class="table-responsive table-condensed">
            <table class="table table-hover table-striped table-bordered text-nowrap">
                <thead>
                    <tr>
                        <th scope="col">名称</th>
                        <th scope="col">大小</th>
                        <th scope="col" class="d-none d-sm-table-cell">权限</th>
                        <th scope="col" class="d-none d-md-table-cell">所有者</th>
                        <th scope="col">创建时间</th>
                        <th scope="col">操作</th>
                    </tr>
                </thead>
                <tbody>
        ';
        foreach ($fslist as $item) {
            $suffix = $item->isDir() ? '/' : '';
            $srpath = RexHelper::path_ator($item->getRealPath());
            if (RexHelper::is_ignore($item->getFileName())) {
                continue;
            }

            echo '<tr>';
            if (RexHelper::file_catetory($srpath) == 'image') {
                echo '
                <td>
                    <img data-toggle="lightbox" src="../app/thumb.php?img=', $srpath, '" data-image="' . $srpath . '" data-caption="查看原图" class="img-thumbnail" alt="查看原图" width="80">
                </td>';
            } else {
                echo '
                <td>
                    <a href="?path=' . $srpath . '">' . $item->getFileName() . $suffix . '</a>
                </td>';
            }
            echo '
                <td>' . ($item->isFile() ? RexHelper::format_bytes($item->getSize()) : '-') . '</td>
                <td class="d-none d-sm-table-cell">' . substr(sprintf('%o', $item->getPerms()), -4) . '</td>
                <td class="d-none d-md-table-cell">' . $item->getOwner() . ':' . $item->getGroup() . '<td>' . date('Y-m-d H:i', $item->getCTime()) . '</td>
                <td><a href="?path=' . $srpath . '">查看</a>|<a href="?view=rename&path=' . $srpath . '">改名</a>|<a href="?view=chmod&path=' . $srpath . '">权限</a>|<a href="javascript:;" onclick="do_cofirm(\'?action=delete&path=' . $srpath . '\', \'删除\')">删除</a></td>
            </tr>
            ';
        }
        echo '
                </tbody>
            </table>
        ';
    }

    /**
     * 新增目录视图
     * @param string $path 路径
     */
    static function view_newdir($path)
    {
        echo '
            <div class="newdir">
                <form method="post" action="?action=newdir&path=' . $path . '">
                    <div class="row mb-3">
                        <label class="col-sm-2 col-form-label">目录名</label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control" name="filename">
                        </div>
                    </div>
                    <div class="row mb-4">
                        <label class="col-sm-2 col-form-label">权限模式</label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control" name="mode" value="0777">
                        </div>
                    </div>
                    <div class="d-grid">
                        <button type="submit" class="btn btn-dark">创建目录</button>
                    </div>
                </form>
            </div>
        ';
    }

    /**
     * 新增文件视图
     * @param string $path 路径
     */
    static function view_newfile($path)
    {
        echo '
            <div class="newfile">
            <form method="post" action="?action=newfile&path=' . $path . '">
                    <div class="row mb-3">
                        <label class="col-sm-2 col-form-label">文件名</label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control" name="filename" placeholder="如：newfile.txt">
                        </div>
                    </div>
                    <div class="row mb-4">
                        <label class="col-sm-2 col-form-label">内容</label>
                        <div class="col-sm-10">
                            <textarea class="form-control" name="content" rows="10"></textarea>
                        </div>
                    </div>
                    <div class="d-grid">
                        <button type="submit" class="btn btn-dark">创建文件</button>
                    </div>
                </form>
            </div>
        ';
    }

    /**
     * 编辑文件内容视图
     * @param string $path 路径
     */
    static function view_edit($path)
    {
        $sapath = RexHelper::path_rtoa($path);
        $category = RexHelper::file_catetory($sapath);
        switch ($category) {
            case 'text':
                $c = htmlspecialchars(file_get_contents($sapath));
                echo '
                    <div class="edit">
                        <form method="post" action="?action=edit&path=' . $path . '">
                            <div class="mb-4">
                                <textarea class="form-control" name="content" rows="30">' . $c . '</textarea>
                            </div>
                            <div class="d-grid">
                ';
                if (is_writable($sapath)) {
                    echo '<button type="submit" class="btn btn-dark">保存文件</button>';
                } else {
                    echo '<button class="btn btn-dark" disabled>文件不可写</button>';
                }
                echo '
                            </div>
                        </form>
                    </div>
                ';
                return;
            case 'image':
                echo '
                    <div class="card view-image">
                        <img src="', $path, '" class="card-img-top">
                        <div class="card-body">
                            <a href="', $path, '" class="card-link">查看原图</a>
                        </div>
                    </div>
                ';
                return;
            default:
                echo '
                    <div class="card">
                        <div class="card-body">
                            <a href="', $path, '" class="card-link">下载文件</a>
                        </div>
                    </div>
                ';
                return;
        }
    }

    /**
     * 上传文件视图
     * @param string $path 路径
     */
    static function view_upload($path)
    {
        echo '
            <div class="upload">
                <form method="post" enctype="multipart/form-data" action="?action=upload&path=' . $path . '">
                    <div class="row mb-4">
                        <label class="col-sm-2 col-form-label">远程 URL</label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control" name="url">
                        </div>
                    </div>
                    <div class="row mb-4">
                        <label class="col-sm-2 col-form-label">本地文件</label>
                        <div class="col-sm-10">
                            <input type="file" class="form-control" name="file">
                        </div>
                    </div>
                    <div class="d-grid">
                        <button type="submit" class="btn btn-dark">立即上传</button>
                    </div>
                </form>
            </div>
        ';
    }

    /**
     * 重命名文件视图
     * @param string $path 路径
     */
    static function view_rename($path)
    {
        echo '
            <div class="rename">
                <form method="post"  action="?action=rename&path=' . $path . '">
                    <div class="row mb-3">
                        <label class="col-sm-2 col-form-label">原名称</label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control" value="' . basename($path) . '" readonly>
                        </div>
                    </div>
                    <div class="row mb-4">
                        <label class="col-sm-2 col-form-label">新名称</label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control" name="filename">
                        </div>
                    </div>
                    <div class="d-grid">
                        <button type="submit" class="btn btn-dark">重命名</button>
                    </div>
                </form>
            </div>
        ';
    }

    /**
     * 编辑权限视图
     * @param string $path 路径
     */
    static function view_chmod($path)
    {
        $sapath = RexHelper::path_rtoa($path);
        $perms = substr(sprintf("%o", fileperms($sapath)), -4);
        echo '
            <div class="chmod">
                <form method="post"  action="?action=chmod&path=' . $path . '">
                    <div class="row mb-3">
                        <label class="col-sm-2 col-form-label">名称</label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control" value="' . basename($path) . '" disabled>
                        </div>
                    </div>
                    <div class="row mb-4">
                        <label class="col-sm-2 col-form-label">权限模式</label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control" name="mode" value="' . $perms . '">
                        </div>
                    </div>
                    <div class="d-grid">
                        <button type="submit" class="btn btn-dark">设置</button>
                    </div>
                </form>
            </div>
        ';
    }

    /**
     * 压缩文件视图
     * @param string $path 路径
     */
    static function view_zip($path)
    {
        echo '
            <div class="zip">
                <form method="post" action="?action=zip&path=' . $path . '">
                    <div class="row mb-3">
                        <label class="col-sm-2 col-form-label">压缩文件名</label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control" name="filename" value="/test.zip">
                        </div>
                    </div>
                    <div class="row mb-4">
                        <label class="col-sm-2 col-form-label">文件列表</label>
                        <div class="col-sm-10">
                            <textarea class="form-control" name="content" rows="10">' . $path . '</textarea>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <label class="col-sm-2 col-form-label">去除根路径</label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control" name="trimpath" placeholder="/www">
                        </div>
                    </div>
                    <div class="d-grid">
                        <button type="submit" class="btn btn-dark">压缩</button>
                    </div>
                </form>
                <div class="alert alert-secondary mt-2" role="alert">
                    注意，所有路径都相对于目录 ' . RexHelper::$root . '<br />
                    文件列表：每个路径一行，支持排除路径<br />
                    包含路径示例：/www/app<br />
                    排除路径示例：exclude /www/app/log<br />
                </div>
            </div>
        ';
    }

    /**
     * 解压缩文件视图
     * @param string $path 路径
     */
    static function view_unzip($path)
    {
        echo '
            <div class="unzip">
                <form method="post" action="?action=unzip&path=' . $path . '">
                    <div class="row mb-3">
                        <label class="col-sm-2 col-form-label">压缩包路径</label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control" name="filename" value="/test.zip">
                        </div>
                    </div>
                    <div class="row mb-3">
                        <label class="col-sm-2 col-form-label">解压缩路径</label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control" name="root" value="' . $path . '">
                        </div>
                    </div>
                    <div class="d-grid">
                        <button type="submit" class="btn btn-dark">解压</button>
                    </div>
                    <div class="alert alert-secondary mt-2" role="alert">
                        注意，所有路径都相对于' . RexHelper::$root . '
                    </div>
                </form>
            </div>
        ';
    }

    /**
     * 全局操作菜单
     * @param string $path 路径
     * @param string $view 视图
     */
    static function body_navbar($path, $view)
    {
        $pdir = $path;
        if (!is_dir(RexHelper::path_rtoa($path))) {
            $pdir = dirname($path);
        }
        $member = RexAction::user_info();
        echo '
        <nav class="navbar navbar-inverse" role="navigation">
        <div class="container-fluid">
          <!-- 导航头部 -->
          <div class="navbar-header">
            <!-- 移动设备上的导航切换按钮 -->
            <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse-example">
              <span class="sr-only">切换导航</span>
              <span class="icon-bar"></span>
              <span class="icon-bar"></span>
              <span class="icon-bar"></span>
            </button>
            <!-- 品牌名称或logo -->
            <a class="navbar-brand" href="?">文件管理</a>
          </div>
          <!-- 导航项目 -->
          <div class="collapse navbar-collapse navbar-collapse-example">
            <!-- 一般导航项目 -->
            <ul class="nav navbar-nav">
                <li class="' . ($view == 'newdir' ? 'active' : '') . '"><a href="?view=newdir&path=' . $pdir . '">新建目录</a></li>
                <li class="' . ($view == 'newfile' ? 'active' : '') . '"><a href="?view=newfile&path=' . $pdir . '">新建文件</a></li>
                <li class="' . ($view == 'upload' ? 'active' : '') . '"><a href="?view=upload&path=' . $pdir . '">上传文件</a></li>
                <li class="' . ($view == 'zip' ? 'active' : '') . '"><a href="?view=zip&path=' . $pdir . '">打包目录</a></li>
                <li class="' . ($view == 'unzip' ? 'active' : '') . '"><a href="?view=unzip&path=' . $pdir . '">解压文件</a></li>
                <li class="' . ($view == 'opreset' ? 'active' : '') . '"><a href="?action=opreset&path=' . $path . '">清空OPCache</a></li>
            </ul>
            <!-- 右侧的导航项目 -->
            <ul class="nav navbar-nav navbar-right">
              <li><a href="#">欢迎您, ' . $member['username'] . '</a></li>
              <li><a href="?action=logout">注销</a></li>
            </ul>
          </div>
        </div>
      </nav>';
    }

    /**
     * 路径转为导航
     * @param string $path 路径
     */
    static function body_breadcrumb($path)
    {
        echo '
            <nav class="mb-3">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="?path=/">HOME</a></li>
        ';
        $full = '';
        $items = explode('/', trim($path, '/'));
        foreach ($items as $item) {
            $full .= '/' . $item;
            echo '<li class="breadcrumb-item"><a href="?path=', $full, '">', $item, '</a></li>';
        }
        echo '
                </ol>
            </nav>
        ';
    }

    /**
     * URL重定向
     * @param string $url 重定向的URL地址
     * @param integer $sec 重定向的等待时间（秒）
     * @param string $msg 重定向前的提示信息
     * @return void
     */
    static function url_redirect($url, $sec, $msg = '')
    {
        $url = str_replace(array("\n", "\r"), '', $url);
        $msg = $sec == 1 ? '操作成功！' : '操作失败！';
        $cat = $sec == 1 ? 'success' : 'danger';
        echo '
            <div class="container pt-3">
                <div class="alert alert-' . $cat . '" role="alert">
                    ' . $msg . $sec . '秒后重定向到 ' . $url . '
                </div>
             </div>
            <script type="text/javascript">
                setTimeout(function() {
                    location.href = "' . $url . '";
                }, ' . $sec * 1000 . ');
            </script>
        ';
        exit('</body></html>');
    }
}

/**
 * 回调方法类
 */
class RexAction
{
    static function action($path, $action)
    {
        $method = 'act_' . $action;
        if (!method_exists('RexAction', $method)) {
            RExplorer::url_redirect('?r=fail', 2, '接口不存在！');
        }
        self::$method($path);
    }

    /**
     * 用户信息
     * @return array | null
     */
    static function user_info()
    {
        if (isset($_SESSION[RexHelper::$ssid])) {
            return $_SESSION[RexHelper::$ssid];
        }
    }

    /**
     * 用户登录操作
     */
    static function act_login()
    {
        $uname = G('username');
        if (empty(RexHelper::$users[$uname])) {
            RExplorer::url_redirect('?r=fail', 2, '用户不存在！');
        }
        if (RexHelper::$users[$uname]['password'] != hash('sha256', (G('password')))) {
            RExplorer::url_redirect('?r=fail', 2, '密码错误！');
        }
        $_SESSION[RexHelper::$ssid] = array(
            'username' => $uname,
        );
        RExplorer::url_redirect('?r=ok', 1);
    }

    /**
     * 用户登出操作
     */
    static function act_logout()
    {
        unset($_SESSION[RexHelper::$ssid]);
        RExplorer::url_redirect('?r=ok', 1);
    }

    /**
     * 保存新目录
     * @param string $path 路径
     */
    static function act_newdir($path)
    {
        $filename = G('filename');
        if (!$filename) {
            RExplorer::url_redirect('?path=' . $path, 2, '目录名称无效！');
        }
        $mode = intval(G('mode'), 8);
        $sapath = RexHelper::path_rtoa($path);
        $target = $sapath . DIRECTORY_SEPARATOR . $filename;
        if (file_exists($target)) {
            RExplorer::url_redirect('?path=' . $path, 2, ' 目标已存在！');
        }
        if (@mkdir($target, $mode)) {
            RExplorer::url_redirect('?path=' . $path, 1);
        }
        RExplorer::url_redirect('?path=' . $path, 2);
    }

    /**
     * 新建文件
     * @param string $path 路径
     */
    static function act_newfile($path)
    {
        $filename = G('filename');
        if (!$filename || strpos($filename, '.') === false) {
            RExplorer::url_redirect('?path=' . $path, 2, '文件名或扩展名无效！');
        }
        $content = G('content');
        $sapath = RexHelper::path_rtoa($path);
        is_dir($sapath) || $sapath = dirname($sapath); // 降级
        $target = $sapath . DIRECTORY_SEPARATOR . $filename;
        if (file_put_contents($target, $content) !== false) {
            RExplorer::url_redirect('?path=' . $path, 1);
        }
        RExplorer::url_redirect('?path=' . $path, 2);
    }

    /**
     * 修改文件
     * @param string $path 路径
     */
    static function act_edit($path)
    {
        $content = G('content');
        $sapath = RexHelper::path_rtoa($path);
        if (file_put_contents($sapath, $content) !== false) {
            RExplorer::url_redirect('?path=' . $path, 1);
        }
        RExplorer::url_redirect('?path=' . $path, 2);
    }

    /**
     * 删除路径(文件或目录)
     * @param string $path 路径
     */
    static function act_delete($path)
    {
        $sapath = RexHelper::path_rtoa($path);
        if (is_file($sapath)) {
            if (@unlink($sapath)) {
                RExplorer::url_redirect('?path=' . dirname($path), 1);
            }
            RExplorer::url_redirect('?path=' . dirname($path), 2);
        }
        if (is_dir($sapath)) {
            if (@rmdir($sapath)) {
                RExplorer::url_redirect('?path=' . dirname($path), 1);
            }
            RExplorer::url_redirect('?path=' . $path, 2, '非空或权限不足！');
        }
        RExplorer::url_redirect('?path=' . $path, 2, '不是有效文件或目录！');
    }

    /**
     * 上传文件
     * @param string $path 路径
     */
    static function act_upload($path)
    {
        $sapath = RexHelper::path_rtoa($path);
        // 从远程获取
        if ($fileurl = G('url')) {
            if ($data = file_get_contents($fileurl)) {
                $target = $sapath . DIRECTORY_SEPARATOR . basename($fileurl);
                if (file_put_contents($target, $data)) {
                    RExplorer::url_redirect('?path=' . $path, 1);
                }
                RExplorer::url_redirect('?path=' . $path, 2, '保存文件失败');
            }
            RExplorer::url_redirect('?path=' . $path, 2, '获取源文件失败');
        }
        // 从本地上传
        if (!empty($_FILES['file'])) {
            if ($_FILES['file']['error']) {
                $msg = '错误代码 ' . $_FILES['file']['error'];
                RExplorer::url_redirect('?path=' . $path, 2, $msg);
            }
            $target = $sapath . DIRECTORY_SEPARATOR . $_FILES['file']['name'];
            if (file_exists($target)) {
                $msg = $_FILES['file']['name'] . ' 文件已存在！';
                RExplorer::url_redirect('?path=' . $path, 2, $msg);
            }
            if (@move_uploaded_file($_FILES['file']['tmp_name'], $target)) {
                RExplorer::url_redirect('?path=' . $path, 1);
            }
            RExplorer::url_redirect('?path=' . $path, 2);
        }
        // 参数错误
        RExplorer::url_redirect('?path=' . $path, 2, '参数错误');
    }

    /**
     * 重命名路径
     * @param string $path 路径
     */
    static function act_rename($path)
    {
        $filename = G('filename');
        if (!$filename) {
            RExplorer::url_redirect('?view=rename&path=' . $path, 2, '名称不能为空！');
        }
        $sapath = RexHelper::path_rtoa($path);
        if (is_file($sapath) && strpos($filename, '.') === false) {
            RExplorer::url_redirect('?view=rename&path=' . $path, 2, '文件扩展名无效！');
        }
        $target = dirname($sapath) . DIRECTORY_SEPARATOR . $filename;
        if (@rename($sapath, $target)) {
            RExplorer::url_redirect('?path=' . dirname($path), 1);
        }
        RExplorer::url_redirect('?path=' . dirname($path), 2);
    }

    /**
     * 编辑权限
     * @param string $path 路径
     */
    static function act_chmod($path)
    {
        $mode = intval(G('mode'), 8);
        if (!$mode) {
            RExplorer::url_redirect('?view=chmod&path=' . $path, 2, '权限模式无效！');
        }
        $sapath = RexHelper::path_rtoa($path);
        if (@chmod($sapath, $mode)) {
            RExplorer::url_redirect('?path=' . dirname($path), 1);
        }
        RExplorer::url_redirect('?path=' . dirname($path), 2);
    }

    /**
     * 压缩操作
     * @param string $path 路径
     */
    static function act_zip($path)
    {
        $filename = G('filename');
        if (!$filename || !strpos($filename, '.')) {
            RExplorer::url_redirect('?path=' . $path, 2, '压缩文件名无效！');
        }
        $filename = RexHelper::path_rtoa($filename);
        if (file_exists($filename)) {
            RExplorer::url_redirect('?path=' . $path, 2, '压缩文件已存在！');
        }
        $content = G('content');
        if (!$content) {
            RExplorer::url_redirect('?path=' . $path, 2, '压缩内容无效！');
        }
        $include = array();
        $exclude = array();
        $items = explode(PHP_EOL, $content);
        foreach ($items as $item) {
            if (strpos($item, 'exclude ') === 0) {
                $exclude[] = RexHelper::path_rtoa(trim(substr($item, 8)));
            } else {
                $include[] = RexHelper::path_rtoa($item);
            }
        }
        if (empty($include)) {
            RExplorer::url_redirect('?path=' . $path, 2, '压缩内容无效！');
        }
        $zip = new ZipHelper();
        $trimpath =  RexHelper::path_rtoa(G('trimpath'));
        if ($zip->zip($filename, $msg, $include, $exclude, $trimpath)) {
            RExplorer::url_redirect('?path=' . $path, 1, $msg);
        }
        RExplorer::url_redirect('?path=' . $path, 2, $msg);
    }

    /**
     * 解压缩文件
     * @param string $path 路径
     */
    static function act_unzip($path)
    {
        $root = RexHelper::path_rtoa(G('root'));
        $filename = RexHelper::path_rtoa(G('filename'));
        if (!$filename) {
            $msg = '压缩文件路径无效！';
            RExplorer::url_redirect('?path=' . $path, 2, $msg);
        }
        if (!$root) {
            $msg = '解压缩路径无效！';
            RExplorer::url_redirect('?path=' . $path, 2, $msg);
        }
        $zip = new ZipHelper();
        if ($zip->unzip($filename, $root, $msg)) {
            RExplorer::url_redirect('?path=' . $path, 1, $msg);
        }
        RExplorer::url_redirect('?path=' . $path, 2, $msg);
    }

    /**
     * 清空OPCache
     * @param string $path 路径
     */
    static function act_opreset($path)
    {
        if (opcache_reset()) {
            RExplorer::url_redirect('?path=' . $path, 1);
        }
        RExplorer::url_redirect('?path=' . $path, 2);
    }
}

class RexHelper
{
    static $root = __DIR__;

    static $version = '1.4';

    static $ssid = 'rexplorer_sid';

    static $users = array();

    static $text_suff = array(
        'sql', 'tpl', 'php', 'htm', 'html', 'ts', 'js', 'css',
        'bat', 'sh', 'md', 'log', 'txt', 'json', 'env', 'ini',
    );

    static $img_suff = array(
        'jpg', 'jpeg', 'png', 'gif', 'bmp', 'webp', 'ico', 'jfif',
        'tif', 'tga', 'svg'
    );

    static $ignore_list = array();

    /**
     * 优化容量显示
     * @param string $path 路径
     */
    static function format_bytes($size)
    {
        $units = array(' B', ' KB', ' MB', ' GB', ' TB');
        for ($i = 0; $size >= 1024 && $i < 4; $i++) $size /= 1024;
        return round($size, 2) . $units[$i];
    }

    /**
     * 绝对路径转相对路径
     * @param string $path 路径
     * @return string
     */
    static function path_ator($path)
    {
        $path = substr($path, strlen(self::$root));
        if (DIRECTORY_SEPARATOR != '/') {
            $path = str_replace(DIRECTORY_SEPARATOR, '/', $path);
        }
        return $path;
    }

    /**
     * 相对路径转绝对路径
     * @param string $path 路径
     * @return string
     */
    static function path_rtoa($path)
    {
        $path = self::$root . DIRECTORY_SEPARATOR . trim($path, '/\\');
        if (DIRECTORY_SEPARATOR != '/') {
            return str_replace('/', DIRECTORY_SEPARATOR, $path);
        }
        return $path;
    }

    /**
     * 获取文件扩展名类型
     * @param string $path 文件路径
     * @return string
     */
    static function file_catetory($path)
    {
        if ($ext = pathinfo($path, PATHINFO_EXTENSION)) {
            if (in_array($ext, self::$text_suff)) {
                return 'text';
            }

            if (in_array($ext, self::$img_suff)) {
                return 'image';
            }
        }
        return '';
    }

    /**
     * 检测是否需要排除
     * @param string $name 文件名称
     * @return string
     */
    static function is_ignore($name)
    {
        if (!empty(self::$ignore_list)) {
            foreach (self::$ignore_list as $expr) {
                if (preg_match($expr, $name)) {
                    return true;
                }
            }
        }
        return false;
    }
}

/**
 * 压缩类
 */
class ZipHelper
{
    /**
     * 解压缩之
     * @param string $filename 文件名
     * @param string $path 解压路径
     * @param string $msg 错误消息
     * @return boolean
     */
    function unzip($filename, $path, &$msg = '')
    {
        if (!$filename) {
            $msg = '压缩文件名无效！';
            return false;
        }
        $zip = new ZipArchive();
        $msg = $zip->open($filename);
        if (true !== $msg) {
            $msg = var_export($msg, true);
            return false;
        }
        $zip->extractTo($path);
        $zip->close();
        return true;
    }

    /**
     * 压缩之
     * @param string $filename 文件名
     * @param string $msg 错误消息
     * @param array $include 包含文件列表
     * @param array $exclude 排除文件列表
     * @param string $trimpath 删除根路径
     * @param string $comment 注释内容
     * @return boolean
     */
    function zip($filename, &$msg = '', $include = array(), $exclude = array(), $trimpath = '', $comment = 'default')
    {
        if (!$filename) {
            $msg = '压缩文件名无效！';
            return false;
        }
        if (empty($include)) {
            $msg = '压缩内容无效！';
            return false;
        }
        if ('default' == $comment) {
            $comment = basename($filename) . PHP_EOL . 'Generate at ' . date('Y-m-d H:i:s') . PHP_EOL . 'Powerd by RExplorer.';
        }
        try {
            $zip = new ZipArchive();
            $res = $zip->open($filename, ZipArchive::CREATE);
            if ($res !== true) {
                $msg = var_export($res, true);
                return false;
            }
            if ($comment) {
                $zip->setArchiveComment($comment);
            }
            if ($trimpath) {
                $trimpath = rtrim($trimpath, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;
            }
            $substart = strlen($trimpath);
            foreach ($include as $source) {
                $this->zip_dir($zip, $source, $exclude, $substart);
            }
            $zip->close();
            return true;
        } catch (Exception $ex) {
            $msg = $ex->getMessage();
            return false;
        }
    }

    /**
     * 递归压缩整个目录
     * @param ZipArchive $zip Zip 实例
     * @param string $source 包含的路径
     * @param array $exclude 排除的路径
     * @param int $substart 开始截取的路径字符串(用于去除路径中的根目录路径)
     */
    function zip_dir(&$zip, $source, $exclude, $substart = 0)
    {
        if (is_dir($source)) {
            $source = rtrim($source, DIRECTORY_SEPARATOR);
            if ($handle = opendir($source)) {
                while (false !== ($f = readdir($handle))) {
                    if ('.' == $f || '..' == $f) {
                        continue;
                    }
                    $filename = $source . DIRECTORY_SEPARATOR . $f;
                    if (is_dir($filename)) {
                        if ($exclude && in_array($filename, $exclude)) {
                            continue;
                        }
                        $this->zip_dir($zip, $filename, $exclude, $substart);
                    } else {
                        if ($exclude && in_array($filename, $exclude)) {
                            continue;
                        }
                        $zip->addFile($filename, substr($filename, $substart));
                    }
                }
                closedir($handle);
            }
        } else {
            if ($exclude && in_array($source, $exclude)) {
                return;
            }
            $zip->addFile($source);
        }
    }
}

/** 环境检测 */
require_once APP_ROOT . '/app/check.php';
/** 底部广告 */
if ($config['ad_bot']) echo $config['ad_bot_info'];
/** 引入底部 */
require_once APP_ROOT . '/app/footer.php';
