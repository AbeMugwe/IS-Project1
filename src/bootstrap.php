<?php
declare(strict_types=1);

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start([
        'cookie_httponly' => true,
        'cookie_samesite' => 'Lax',
        'use_strict_mode' => true,
    ]);
}

function db(string $which = 'auth'): PDO
{
    $configPath = dirname(__DIR__) . '/config.php';
    if (!is_file($configPath)) {
        http_response_code(500);
        exit('Missing server configuration. Copy config.example.php to config.php.');
    }
    $config = require $configPath;
    $dbname = $which === 'form' ? $config['form_database'] : $config['auth_database'];
    $dsn = sprintf('mysql:host=%s;dbname=%s;charset=utf8mb4', $config['host'], $dbname);
    return new PDO($dsn, $config['user'], $config['password'], [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ]);
}
function e(string $value): string { return htmlspecialchars($value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'); }
function csrf_token(): string {
    if (empty($_SESSION['csrf'])) { $_SESSION['csrf'] = bin2hex(random_bytes(32)); }
    return $_SESSION['csrf'];
}
function valid_csrf(): bool {
    return isset($_POST['csrf'], $_SESSION['csrf']) && is_string($_POST['csrf'])
        && hash_equals($_SESSION['csrf'], $_POST['csrf']);
}
function require_login(): void {
    if (empty($_SESSION['user_id'])) { header('Location: /login.php'); exit; }
}
function page_header(string $title): void {
    echo '<!doctype html><html lang="en"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>'.e($title).' - IS Project 1</title><link rel="stylesheet" href="/style.css"></head><body><main class="container"><nav><a href="/">Home</a> <a href="/signup.php">Sign up</a> <a href="/login.php">Log in</a> <a href="/feedback.php">Feedback</a> <a href="/logout.php">Log out</a></nav><h1>'.e($title).'</h1>';
}
function page_footer(): void { echo '</main></body></html>'; }
