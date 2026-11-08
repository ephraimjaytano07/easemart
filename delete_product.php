<?php
session_start();
if (!isset($_SESSION['user'])) { header("Location: login.php"); exit(); }
require_once 'config/db.php';

$product_id = intval($_POST['product_id'] ?? 0);

if (!$product_id) {
    header("Location: inventory.php?success=error");
    exit();
}

$stmt = mysqli_prepare($conn, "UPDATE tbl_products SET is_deleted = 1 WHERE product_id = ?");
mysqli_stmt_bind_param($stmt, "i", $product_id);

if (mysqli_stmt_execute($stmt)) {
    header("Location: inventory.php?success=deleted");
} else {
    header("Location: inventory.php?success=error");
}
exit();
?>
