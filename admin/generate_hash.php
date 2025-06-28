<?php
$password = 'admin123';
$options = [
    'cost' => 10
];
$hash = password_hash($password, PASSWORD_BCRYPT, $options);
echo "Password: $password\n";
echo "Hash: $hash\n";

// Verify the hash works
$verify = password_verify($password, $hash);
echo "Verification test: " . ($verify ? "SUCCESS" : "FAILED") . "\n";
?> 