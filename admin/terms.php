<?php
/*
 * 使用条款页面
 */
require_once __DIR__ . '/../app/header.php';

/** 顶部广告 */
if ($config['ad_top']) echo $config['ad_top_info'];

/** 加载使用条款 */
if (empty($config['terms'])) {
  echo '<div class="alert alert-danger">Terms not set!<br />未设置使用条款</div>';
} else {
  echo $config['terms'];
  // echo '<div style="margin-bottom: 80px;"></div>';
}

/** 底部广告 */
if ($config['ad_bot']) echo $config['ad_bot_info'];
?>

<script>
  // Title
  document.title = '使用条款 - <?php echo $config['title']; ?>'
</script>

<?php
require_once __DIR__ . '/../app/footer.php';
