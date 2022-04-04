<?php
/*
 * 使用条款页面
 */
require_once __DIR__ . '/../application/header.php';

if (empty($config['terms'])) {
  echo '<div class="alert alert-danger">Terms not set!<br />使用条款未设置</div>';
} else {
  echo $config['terms'];
}

echo "
<script>
  // Title
  document.title = '使用条款 - " . $config['title'] . "';
</script>
";

require_once __DIR__ . '/../application/footer.php';
