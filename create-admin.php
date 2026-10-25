<?php
if (PHP_SAPI !== 'cli') {
    http_response_code(404);
    exit;
}

require_once __DIR__ . '/db.php';

$email = strtolower(trim($argv[1] ?? ''));
$password = getenv('NEXA_ADMIN_PASSWORD');

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    fwrite(STDERR, "Usage: NEXA_ADMIN_PASSWORD=<password> php create-admin.php <email>\n");
    exit(1);
}
if ($password === false || strlen($password) < 12) {
    fwrite(STDERR, "Set NEXA_ADMIN_PASSWORD to a password of at least 12 characters.\n");
    exit(1);
}

$passwordHash = password_hash($password, PASSWORD_DEFAULT);
$statement = $conn->prepare(
    'INSERT INTO admin_users (email, password_hash) VALUES (?, ?) ON DUPLICATE KEY UPDATE password_hash = ?'
);
$statement->execute([$email, $passwordHash, $passwordHash]);
putenv('NEXA_ADMIN_PASSWORD');
fwrite(STDOUT, "Admin account created or updated for {$email}.\n");
