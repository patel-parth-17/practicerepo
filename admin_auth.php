<?php
if (session_status() !== PHP_SESSION_ACTIVE) {
    ini_set('session.use_strict_mode', '1');
    $isHttps = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off';
    session_set_cookie_params([
        'httponly' => true,
        'secure' => $isHttps,
        'samesite' => 'Lax',
    ]);
    session_start();
}

function e($value)
{
    return htmlspecialchars(is_scalar($value) ? (string) $value : '', ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

function requestString(array $source, $key)
{
    return isset($source[$key]) && is_string($source[$key]) ? $source[$key] : '';
}

function csrfToken()
{
    if (!isset($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function verifyCsrfToken($token)
{
    return isset($_SESSION['csrf_token'])
        && is_string($token)
        && hash_equals($_SESSION['csrf_token'], $token);
}

function requireAdmin()
{
    if (!isset($_SESSION['admin_id'])) {
        header('Location: index.php');
        exit;
    }
}

function requireUser()
{
    if (isset($_SESSION['admin_id'])) {
        header('Location: admin.php');
        exit;
    }
    if (!isset($_SESSION['user_id'])) {
        header('Location: index.php?role=user');
        exit;
    }
}
