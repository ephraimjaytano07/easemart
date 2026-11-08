<?php
session_start();
if (!isset($_SESSION['user'])) { header("Location: login.php"); exit(); }
require_once 'config/db.php';

$product_id   = intval($_POST['product_id'] ?? 0);
$product_name = trim($_POST['product_name'] ?? '');
$price        = floatval($_POST['price'] ?? 0);
$quantity     = intval($_POST['quantity'] ?? 0);

if (!$product_id || empty($product_name)) {
    header("Location: inventory.php?success=error");
    exit();
}

/* Handle optional new image */
if (!empty($_FILES['product_image']['name'])) {
    $ext     = strtolower(pathinfo($_FILES['product_image']['name'], PATHINFO_EXTENSION));
    $allowed = ['jpg','jpeg','png','webp','avif','gif'];
    if (in_array($ext, $allowed)) {
        $image_name = uniqid('product_', true) . '.' . $ext;
        $dest       = __DIR__ . '/assets/images/products/' . $image_name;
        if (!is_dir(dirname($dest))) mkdir(dirname($dest), 0755, true);
        move_uploaded_file($_FILES['product_image']['tmp_name'], $dest);

        $stmt = mysqli_prepare($conn, "UPDATE tbl_products SET product_name=?, price=?, quantity=?, product_image=? WHERE product_id=?");
        mysqli_stmt_bind_param($stmt, "sdssi", $product_name, $price, $quantity, $image_name, $product_id);
    }
} else {
    $stmt = mysqli_prepare($conn, "UPDATE tbl_products SET product_name=?, price=?, quantity=? WHERE product_id=?");
    mysqli_stmt_bind_param($stmt, "sdsi", $product_name, $price, $quantity, $product_id);
}

if (mysqli_stmt_execute($stmt)) {
    header("Location: inventory.php?success=updated");
} else {
    header("Location: inventory.php?success=error");
}
exit();
?>
