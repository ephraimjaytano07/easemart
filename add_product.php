<?php
session_start();
if (!isset($_SESSION['user'])) { header("Location: login.php"); exit(); }
require_once 'config/db.php';

$product_name = trim($_POST['product_name'] ?? '');
$price        = floatval($_POST['price'] ?? 0);
$quantity     = intval($_POST['quantity'] ?? 0);
$image_name   = 'default.png';

if (empty($product_name)) {
    header("Location: inventory.php?success=error");
    exit();
}

/* Handle file upload */
if (!empty($_FILES['product_image']['name'])) {
    $ext       = strtolower(pathinfo($_FILES['product_image']['name'], PATHINFO_EXTENSION));
    $allowed   = ['jpg','jpeg','png','webp','avif','gif'];
    if (in_array($ext, $allowed)) {
        $image_name = uniqid('product_', true) . '.' . $ext;
        $dest       = __DIR__ . '/assets/images/products/' . $image_name;
        if (!is_dir(dirname($dest))) mkdir(dirname($dest), 0755, true);
        move_uploaded_file($_FILES['product_image']['tmp_name'], $dest);
    }
}

$stmt = mysqli_prepare($conn, "INSERT INTO tbl_products (product_name, price, product_image, quantity) VALUES (?, ?, ?, ?)");
mysqli_stmt_bind_param($stmt, "sdsi", $product_name, $price, $image_name, $quantity);

if (mysqli_stmt_execute($stmt)) {
    header("Location: inventory.php?success=added");
} else {
    header("Location: inventory.php?success=error");
}
exit();
?>
