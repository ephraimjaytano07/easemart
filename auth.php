<?php
session_start();
require_once 'config/db.php';

$action = $_GET['action'] ?? '';

// LOGIN
if ($action === 'login') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    if (empty($username) || empty($password)) {
        echo "failed";
        exit();
    }

    $stmt = mysqli_prepare($conn, "SELECT * FROM tbl_users WHERE username = ?");
    mysqli_stmt_bind_param($stmt, "s", $username);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $user = mysqli_fetch_assoc($result);

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user'] = $user['username'];
        $_SESSION['role'] = $user['role'];
        echo "success";
    } else {
        echo "failed";
    }
    exit();
}

// REGISTER
if ($action === 'register') {
    $username = trim($_POST['username'] ?? '');
    $email    = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if (empty($username) || empty($email) || empty($password)) {
        echo "error";
        exit();
    }

    $hashed = password_hash($password, PASSWORD_DEFAULT);

    $stmt = mysqli_prepare($conn, "INSERT INTO tbl_users (username, email, password) VALUES (?, ?, ?)");
    mysqli_stmt_bind_param($stmt, "sss", $username, $email, $hashed);

    if (mysqli_stmt_execute($stmt)) {
        echo "registered";
    } else {
        echo "error";
    }
    exit();
}
?>
