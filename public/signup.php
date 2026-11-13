<?php
require __DIR__ . '/../src/bootstrap.php';
$error = ''; $success = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!valid_csrf()) { $error = 'Invalid form token; refresh and try again.'; }
    else {
        $username = trim((string)($_POST['username'] ?? ''));
        $email = trim((string)($_POST['email'] ?? ''));
        $password = (string)($_POST['password'] ?? '');
        if (!preg_match('/^[a-zA-Z0-9_]{3,30}$/', $username)) { $error = 'Username must be 3-30 letters, numbers or underscores.'; }
        elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) { $error = 'Enter a valid email.'; }
        elseif (strlen($password) < 8) { $error = 'Password must be at least 8 characters.'; }
        else {
            try {
                $stmt = db()->prepare('INSERT INTO users (username, email, password_hash) VALUES (?, ?, ?)');
                $stmt->execute([$username, $email, password_hash($password, PASSWORD_DEFAULT)]);
                $success = 'Account created. You can now log in.';
            } catch (PDOException $ex) {
                if ($ex->getCode() === '23000') { $error = 'Username or email is already registered.'; }
                else { error_log($ex->getMessage()); $error = 'Database error; check server logs.'; }
            }
        }
    }
}
page_header('Sign up');
if ($error) echo '<p class="error">'.e($error).'</p>';
if ($success) echo '<p class="success">'.e($success).'</p>';
?>
<form method="post"><input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>">
<label>Student ID <input required name="username" pattern="[a-zA-Z0-9_]{3,30}" maxlength="30" value="<?= e((string)($_POST['username'] ?? '')) ?>"></label>
<label>Email <input required type="email" name="email" value="<?= e((string)($_POST['email'] ?? '')) ?>"></label>
<label>Password <input required type="password" name="password" minlength="8"></label>
<button type="submit">Register</button></form>
<?php page_footer(); ?>
