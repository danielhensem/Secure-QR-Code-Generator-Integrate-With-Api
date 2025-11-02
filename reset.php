<?php
include_once("componet/conn.php");
date_default_timezone_set('Asia/Kuala_Lumpur');

$token = $_GET['token'] ?? '';
$valid = false;
$userId = null;

if ($token) {
    $stmt = $con->prepare("SELECT id, reset_expires FROM users WHERE reset_token = ? LIMIT 1");
    $stmt->bind_param('s', $token);
    $stmt->execute();
    $stmt->bind_result($id, $expires);
    if ($stmt->fetch()) {
        if ($expires && strtotime($expires) > time()) {
            $valid = true;
            $userId = $id;
        }
    }
    $stmt->close();
}
?>
<!doctype html>
<html>
<head><meta charset="utf-8"><title>Set New Password</title></head>
<body>
<?php if (!$valid): ?>
  <p>Invalid or expired token.</p>
  <p><a href="forgot.php">Request a new reset</a></p>
<?php else: ?>
  <h2>Set a new password</h2>
  <form action="process_reset.php" method="post">
    <input type="hidden" name="token" value="<?= htmlspecialchars($token) ?>">
    <label>New password:</label><br>
    <input type="password" name="pwd" required minlength="6"><br><br>
    <label>Confirm password:</label><br>
    <input type="password" name="pwd2" required minlength="6"><br><br>
    <button type="submit">Update password</button>
  </form>
<?php endif; ?>
</body>
</html>