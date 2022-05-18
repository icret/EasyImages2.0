
<?php
/**
 * 读取上传日志
 */

require_once __DIR__ . '/function.php';

// 非管理员不可访问!
if (!is_who_login('admin')) {
    exit;
}

echo '<pre class="pre-scrollable bg-primary" style="font-size: 13px;">';
echo read_upload_logs();
echo '</pre>';
