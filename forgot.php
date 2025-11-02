<?php
// minimal forgot password form
?>
<!doctype html>
<html>
<head><meta charset="utf-8"><title>Forgot Password</title></head>
<body>
  <h2>Forgot Password</h2>
  <form action="send_reset.php" method="post">
    <label for="email">Enter your email:</label><br>
    <input type="email" name="email" id="email" required><br><br>
    <button type="submit">Send reset link</button>
  </form>
</body>
</html>