<?php
session_start();
if (!isset($_SESSION['user'])) { http_response_code(401); exit(); }
require_once 'config/db.php';

$search = trim($_GET['search'] ?? '');

if ($search !== '') {
    $stmt = mysqli_prepare($conn, "SELECT product_id, product_name, quantity, price, product_image
        FROM tbl_products
        WHERE (product_name LIKE ? OR product_id LIKE ?) AND is_deleted = 0
        ORDER BY product_id ASC");
    $like = '%' . $search . '%';
    mysqli_stmt_bind_param($stmt, 'ss', $like, $like);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
} else {
    $result = mysqli_query($conn, "SELECT product_id, product_name, quantity, price, product_image
        FROM tbl_products WHERE is_deleted = 0 ORDER BY product_id ASC");
}

if (mysqli_num_rows($result) === 0) {
    echo '<tr><td colspan="6" class="empty-state">No products found.</td></tr>';
    exit();
}

while ($row = mysqli_fetch_assoc($result)) {
    $qty = (int)$row['quantity'];
    if ($qty === 0)      { $status = 'Out of Stock'; $badge = 'bg-danger'; }
    elseif ($qty < 10)  { $status = 'Low Stock';    $badge = 'bg-warning text-dark'; }
    else                { $status = 'In Stock';      $badge = 'bg-success'; }

    $img = htmlspecialchars($row['product_image'] ?? 'default.png');
    echo '
    <tr>
        <td>' . htmlspecialchars($row['product_id']) . '</td>
        <td>' . htmlspecialchars($row['product_name']) . '</td>
        <td>' . $qty . '</td>
        <td>₱' . number_format($row['price'], 2) . '</td>
        <td><span class="badge ' . $badge . '">' . $status . '</span></td>
        <td>
            <div class="action-cell">
                <button class="view-btn editBtn"
                    data-id="'    . $row['product_id']   . '"
                    data-name="'  . htmlspecialchars($row['product_name']) . '"
                    data-price="' . $row['price']        . '"
                    data-quantity="' . $qty              . '"
                    data-bs-toggle="modal" data-bs-target="#editModal"
                    title="Edit">
                    <i class="fas fa-pen"></i>
                </button>
                <button class="print-btn deleteBtn"
                    data-id="' . $row['product_id'] . '"
                    data-bs-toggle="modal" data-bs-target="#deleteModal"
                    title="Delete">
                    <i class="fas fa-trash"></i>
                </button>
            </div>
        </td>
    </tr>';
}
?>
