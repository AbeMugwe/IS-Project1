<?php
require __DIR__ . '/../src/bootstrap.php';
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!valid_csrf()) { $error = 'Invalid form token; refresh and try again.'; }
    else {
        $username = trim((string)($_POST['username'] ?? ''));
        $password = (string)($_POST['password'] ?? '');
        try {
            $stmt = db()->prepare('SELECT id, username, password_hash FROM users WHERE username = ?');
            $stmt->execute([$username]);
            $user = $stmt->fetch();
            if ($user && password_verify($password, $user['password_hash'])) {
                session_regenerate_id(true);
                $_SESSION['user_id'] = (int)$user['id'];
                $_SESSION['username'] = $user['username'];
                header('Location: /dashboard.php'); exit;
            }
            $error = 'Invalid username or password.';
        } catch (PDOException $ex) { error_log($ex->getMessage()); $error = 'Database error; check server logs.'; }
    }
}
page_header('Log in');
if ($error) echo '<p class="error">'.e($error).'</p>';
?>
<form method="post"><input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>">
<label>Username <input required name="username" value="<?= e((string)($_POST['username'] ?? '')) ?>"></label>
<label>Password <input required type="password" name="password"></label>
<button type="submit">Log in</button></form>
<?php page_footer(); ?>
