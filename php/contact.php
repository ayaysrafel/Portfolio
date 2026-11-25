<?php
require __DIR__ . '/config.php';
header('Content-Type: application/json; charset=utf-8');

function reply(bool $ok, string $msg, int $code = 200): void {
    http_response_code($code);
    echo json_encode(['ok' => $ok, 'message' => $msg]);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') reply(false, 'Invalid request.', 405);

// Honeypot: real people leave this hidden field empty, bots fill it in.
if (!empty($_POST['website'])) reply(true, 'Thanks! Your message was sent.');

$name    = trim($_POST['name'] ?? '');
$email   = trim($_POST['email'] ?? '');
$subject = trim($_POST['subject'] ?? '');
$message = trim($_POST['message'] ?? '');

if ($name === '' || $subject === '' || $message === '') reply(false, 'Please fill in every field.', 422);
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) reply(false, 'Please enter a valid email address.', 422);
if (mb_strlen($name) > 100 || mb_strlen($subject) > 150 || mb_strlen($message) > 3000) {
    reply(false, 'One of your entries is too long.', 422);
}

try {
    $pdo = db();
    $ip = $_SERVER['REMOTE_ADDR'] ?? null;

    // Simple spam limit: max 5 messages per hour from one IP.
    $check = $pdo->prepare('SELECT COUNT(*) FROM messages WHERE ip_address = ? AND created_at > (NOW() - INTERVAL 1 HOUR)');
    $check->execute([$ip]);
    if ((int) $check->fetchColumn() >= 5) reply(false, 'Too many messages. Please try again later.', 429);

    $stmt = $pdo->prepare('INSERT INTO messages (name, email, subject, message, ip_address) VALUES (?, ?, ?, ?, ?)');
    $stmt->execute([$name, $email, $subject, $message, $ip]);
    reply(true, 'Thanks! Your message was sent. I will reply by email soon.');
} catch (Throwable $e) {
    reply(false, 'Something went wrong on the server. Please use the Gmail button instead.', 500);
}
