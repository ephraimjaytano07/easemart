<?php
session_start();
require_once 'config/db.php';

if (!isset($_SESSION['user'])) {
    http_response_code(401);
    echo json_encode(['error' => 'Unauthorized']);
    exit();
}

$action = $_GET['action'] ?? '';

if ($action === 'get_products') {
    $res = mysqli_query($conn, "SELECT * FROM tbl_products WHERE is_deleted = 0 ORDER BY product_name ASC");
    echo json_encode(mysqli_fetch_all($res, MYSQLI_ASSOC));
    exit();
}

if ($action === 'checkout') {
    $name   = mysqli_real_escape_string($conn, $_POST['customer'] ?? '');
    $total  = floatval($_POST['total']  ?? 0);
    $paid   = floatval($_POST['paid']   ?? 0);
    $change = floatval($_POST['change'] ?? 0);
    $cart   = json_decode($_POST['cart'] ?? '[]', true);

    if (empty($name) || $total <= 0 || empty($cart)) {
        echo "error";
        exit();
    }

    // Save transaction
    $stmt = mysqli_prepare($conn, "INSERT INTO tbl_transactions (customer_name, total_amount, amount_paid, amount_change) VALUES (?, ?, ?, ?)");
    mysqli_stmt_bind_param($stmt, "sddd", $name, $total, $paid, $change);
    mysqli_stmt_execute($stmt);

    // Deduct stock
    foreach ($cart as $item) {
        $id  = intval($item['id']);
        $qty = intval($item['qty']);
        $upd = mysqli_prepare($conn, "UPDATE tbl_products SET quantity = quantity - ? WHERE product_id = ? AND quantity >= ? AND is_deleted = 0");
        mysqli_stmt_bind_param($upd, "iii", $qty, $id, $qty);
        mysqli_stmt_execute($upd);
    }

    echo "success";
    exit();
}
?>
