<?php
// Set the password you want to hash
$plainPassword = 'daniel110803@';

// Hash the password
$hashedPassword = password_hash($plainPassword, PASSWORD_DEFAULT);

// Display the result
echo "<h1>Password Hashing Tool</h1>";
echo "<p><strong>Plain Password:</strong> " . htmlspecialchars($plainPassword) . "</p>";
echo "<p><strong>Hashed Password (copy this):</strong></p>";
echo "<textarea rows='3' cols='70' readonly>" . $hashedPassword . "</textarea>";

// You can also uncomment the line below to verify it works (optional)
// if (password_verify($plainPassword, $hashedPassword)) {
//     echo '<p style="color:green;">Verification Successful!</p>';
// } else {
//     echo '<p style="color:red;">Verification Failed!</p>';
// }
?>