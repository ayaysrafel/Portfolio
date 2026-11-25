<?php
session_start();
require __DIR__ . '/config.php';

if (isset($_GET['logout'])) { session_destroy(); header('Location: admin.php'); exit; }
if (isset($_POST['password'])) {
    if (hash_equals(ADMIN_PASSWORD, $_POST['password'])) { $_SESSION['ok'] = true; }
    else { $error = 'Wrong password.'; }
}
$h = fn($s) => htmlspecialchars((string) $s, ENT_QUOTES, 'UTF-8');
?>
<!DOCTYPE html>
<html lang="en"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">
<title>Messages</title>
<style>
body{font-family:system-ui,sans-serif;max-width:860px;margin:2rem auto;padding:0 1rem;color:#12213f}
.msg{border:1px solid #d5dbe8;border-radius:8px;padding:1rem;margin:1rem 0}
.msg.new{border-left:5px solid #f4b942}.meta{color:#5b6580;font-size:.9rem}
input,button{padding:.6rem;font:inherit}a{color:#1f4fd8}
</style></head><body>
<?php if (empty($_SESSION['ok'])): ?>
  <h1>Admin login</h1>
  <?php if (!empty($error)) echo '<p>' . $h($error) . '</p>'; ?>
  <form method="post"><input type="password" name="password" placeholder="Password" required> <button>Log in</button></form>
<?php else:
  $pdo = db();
  if (isset($_GET['read'])) { $pdo->prepare('UPDATE messages SET is_read = 1 WHERE id = ?')->execute([(int) $_GET['read']]); header('Location: admin.php'); exit; }
  if (isset($_GET['delete'])) { $pdo->prepare('DELETE FROM messages WHERE id = ?')->execute([(int) $_GET['delete']]); header('Location: admin.php'); exit; }
  $rows = $pdo->query('SELECT * FROM messages ORDER BY created_at DESC')->fetchAll();
?>
  <h1>Messages (<?= count($rows) ?>)</h1><p><a href="?logout=1">Log out</a></p>
  <?php if (!$rows) echo '<p>No messages yet.</p>'; ?>
  <?php foreach ($rows as $r): ?>
    <div class="msg <?= $r['is_read'] ? '' : 'new' ?>">
      <strong><?= $h($r['subject']) ?></strong>
      <div class="meta">From <?= $h($r['name']) ?> (<a href="mailto:<?= $h($r['email']) ?>"><?= $h($r['email']) ?></a>) on <?= $h($r['created_at']) ?></div>
      <p><?= nl2br($h($r['message'])) ?></p>
      <?php if (!$r['is_read']): ?><a href="?read=<?= (int) $r['id'] ?>">Mark as read</a> | <?php endif; ?>
      <a href="?delete=<?= (int) $r['id'] ?>" onclick="return confirm('Delete this message?')">Delete</a>
    </div>
  <?php endforeach; endif; ?>
</body></html>
