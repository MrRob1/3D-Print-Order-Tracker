<?php
session_start();
require_once '../config.php';

$username = $_POST['username'];
$password = md5($_POST['password']); // Use md5 for hashing, but consider stronger options in production

$sql = "SELECT id FROM admin_users WHERE username = :username AND password = :password";
$stmt = $pdo->prepare($sql);
$stmt->bindParam(':username', $username);
$stmt->bindParam(':password', $password);
$stmt->execute();

if ($stmt->rowCount() > 0) {
    $_SESSION['loggedin'] = true;
    header("Location: /admin/orders.php");
    exit;
} else {
    echo "<script>alert('Invalid username or password');window.location.href='login.php';</script>";
}