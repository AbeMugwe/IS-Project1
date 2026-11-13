<?php require __DIR__ . '/../src/bootstrap.php'; require_login(); page_header('Dashboard'); ?>
<p class="success">Welcome, Banana University STudent Portal <strong><?= e((string)$_SESSION['username']) ?></strong>!</p>
<p><a href="/feedback.php">Submit course feedback</a></p>
<?php page_footer(); ?>
