<?php
// Script to insert/update Admin Account
require_once __DIR__ . '/config/db.php';
require_once __DIR__ . '/includes/auth.php';

$email = 'admin@gmail.com';
$password = 'admin123';
$username = 'admin';
$full_name = 'System Administrator';
$role = 'super_admin'; // 'super_admin' ensures access to all features in admin.php

echo "<h1>Setting up Admin Account</h1>";

// 1. Hash the password
$password_hash = hashPassword($password);

// 2. Check if admin exists
$stmt = $conn->prepare("SELECT id FROM ipark_admins WHERE email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    // Update existing
    $stmt->close();
    $stmt = $conn->prepare("UPDATE ipark_admins SET password_hash = ?, is_active = 1, role = ? WHERE email = ?");
    $stmt->bind_param("sss", $password_hash, $role, $email);
    $action = "Updated existing admin";
} else {
    // Insert new
    $stmt->close();
    $stmt = $conn->prepare("INSERT INTO ipark_admins (username, email, password_hash, full_name, role, is_active) VALUES (?, ?, ?, ?, ?, 1)");
    $stmt->bind_param("sssss", $username, $email, $password_hash, $full_name, $role);
    $action = "Created new admin";
}

if ($stmt->execute()) {
    echo "<h2 style='color:green'>Success: $action</h2>";
    echo "<ul>";
    echo "<li>Email: <b>$email</b></li>";
    echo "<li>Password: <b>$password</b></li>";
    echo "</ul>";
    echo "<p><a href='index.php'>Go to Login Page</a></p>";
} else {
    echo "<h2 style='color:red'>Error: " . $conn->error . "</h2>";
}
?>