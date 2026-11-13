<?php require __DIR__ . '/../src/bootstrap.php'; require_login(); page_header('Dashboard'); ?>
<p class="success">Login successful. Welcome, <strong><?= e((string)$_SESSION['username']) ?></strong>!</p>
<p><a href="/feedback.php">Submit course feedback</a></p>
<?php page_footer(); ?>
