<?php
require __DIR__ . '/../src/bootstrap.php';
require_login(); $error = ''; $success = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!valid_csrf()) { $error = 'Invalid form token.'; }
    else {
        $course = trim((string)($_POST['course'] ?? ''));
        $rating = filter_var($_POST['rating'] ?? null, FILTER_VALIDATE_INT);
        $comment = trim((string)($_POST['comment'] ?? ''));
        if ($course === '' || strlen($course) > 100 || $rating === false || $rating < 1 || $rating > 5 || strlen($comment) > 1000) {
            $error = 'Provide a course (max 100 chars), rating 1-5 and comment (max 1000 chars).';
        } else {
            try {
                $stmt = db('form')->prepare('INSERT INTO feedback (username, course, rating, comment) VALUES (?, ?, ?, ?)');
                $stmt->execute([$_SESSION['username'], $course, $rating, $comment]);
                $success = 'Feedback saved in CAT2.';
            } catch (PDOException $ex) { error_log($ex->getMessage()); $error = 'Unable to save feedback; check logs.'; }
        }
    }
}
page_header('Course feedback');
if ($error) echo '<p class="error">'.e($error).'</p>';
if ($success) echo '<p class="success">'.e($success).'</p>';
?>
<form method="post"><input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>">
<label>Course <input name="course" required maxlength="100"></label>
<label>Rating <select name="rating" required><option value="">Choose rating</option><option value="1">1</option><option value="2">2</option><option value="3">3</option><option value="4">4</option><option value="5">5</option></select></label>
<label>Comment <textarea name="comment" maxlength="1000" rows="4"></textarea></label>
<button type="submit">Submit feedback</button></form>
<h2>Saved feedback (latest 20)</h2>
<table><thead><tr><th>User</th><th>Course</th><th>Rating</th><th>Comment</th></tr></thead><tbody>
<?php
try {
    $rows = db('form')->query('SELECT username, course, rating, comment FROM feedback ORDER BY id DESC LIMIT 20');
    foreach ($rows as $row) {
        echo '<tr><td>'.e($row['username']).'</td><td>'.e($row['course']).'</td><td>'.e((string)$row['rating']).'</td><td>'.e($row['comment'] ?? '').'</td></tr>';
    }
} catch (PDOException $ex) { error_log($ex->getMessage()); echo '<tr><td colspan="4">Unable to read feedback.</td></tr>'; }
?>
</tbody></table>
<?php page_footer(); ?>
