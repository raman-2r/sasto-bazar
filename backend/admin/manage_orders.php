<?php
// backend/admin/manage_orders.php
session_start();
require_once '../config.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../../frontend/login.html");
    exit;
}

$action = $_GET['action'] ?? '';
$message = '';

if ($action === 'delete') {
    $id = $_GET['id'];
    $pdo->prepare("DELETE FROM ordertable WHERE order_id = ?")->execute([$id]);
    $message = "Order deleted successfully!";
} elseif ($action === 'set_status') {
    $id = $_GET['id'];
    $new_status = $_GET['status']; // e.g. 'confirmed', 'pending', 'cancelled'
    $pdo->prepare("UPDATE ordertable SET payment_status = ? WHERE order_id = ?")->execute([$new_status, $id]);
    
    if ($new_status === 'confirmed') {
        // Also insert into payment table and change product status if admin manually confirmed
        $order = $pdo->prepare("SELECT Product_id FROM ordertable WHERE order_id = ?");
        $order->execute([$id]);
        $prod = $order->fetch();
        if ($prod) {
            $pdo->prepare("INSERT IGNORE INTO paymenttable (order_id, payment_status, product_id) VALUES (?, 'confirmed', ?)")->execute([$id, $prod['Product_id']]);
            $pdo->prepare("UPDATE productdetails SET sell_status = 'sold' WHERE Product_id = ?")->execute([$prod['Product_id']]);
        }
    }
    
    $message = "Order status updated!";
}

$orders = $pdo->query("SELECT o.*, p.product_name, p.product_image, u.name as buyer_name, s.name as seller_name FROM ordertable o 
                       JOIN productdetails p ON o.Product_id = p.Product_id 
                       JOIN userdetails u ON o.user_id = u.user_id 
                       JOIN userdetails s ON p.user_id = s.user_id
                       ORDER BY o.order_id DESC")->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Orders - SastoBazar</title>
    <link rel="stylesheet" href="../../assets/css/admin.css">
    <style>
        .prod-img { width: 50px; height: 50px; object-fit: cover; border-radius: 4px; }
    </style>
</head>
<body>
    <div class="admin-container">
        <!-- Sidebar -->
        <aside class="sidebar">
            <div class="sidebar-header">
                <h2>Admin Panel</h2>
            </div>
            <ul class="nav-links">
                <li><a href="dashboard.php">Dashboard</a></li>
                <li><a href="manage_users.php">Manage Users</a></li>
                <li><a href="manage_products.php">Manage Products</a></li>
                <li><a href="manage_orders.php" class="active">Manage Orders</a></li>
                <li><a href="manage_transactions.php">Transactions</a></li>
                <li><a href="../../frontend/index.html">Back to Site</a></li>
                <li><a href="#" id="admin-logout">Logout</a></li>
            </ul>
        </aside>

        <!-- Main Content -->
        <main class="main-content">
            <header class="top-header">
                <h1>Manage Orders</h1>
            </header>

            <?php if($message): ?>
                <div class="alert alert-success"><?php echo $message; ?></div>
            <?php endif; ?>

            <div class="table-card">
                <h2>Orders List</h2>
                <table>
                    <thead>
                        <tr>
                            <th>Image</th>
                            <th>Order ID</th>
                            <th>Product</th>
                            <th>Buyer</th>
                            <th>Seller</th>
                            <th>Date</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($orders as $order): ?>
                        <tr>
                            <td><img src="../../images/products/<?php echo htmlspecialchars($order['product_image']); ?>" class="prod-img" alt="img"></td>
                            <td>#<?php echo $order['order_id']; ?></td>
                            <td><?php echo htmlspecialchars($order['product_name']); ?></td>
                            <td><?php echo htmlspecialchars($order['buyer_name']); ?></td>
                            <td><?php echo htmlspecialchars($order['seller_name']); ?></td>
                            <td><?php echo htmlspecialchars($order['order_date']); ?></td>
                            <td><span class="badge status-<?php echo strtolower($order['payment_status']); ?>"><?php echo htmlspecialchars($order['payment_status']); ?></span></td>
                            <td class="action-buttons">
                                <?php if($order['payment_status'] !== 'confirmed'): ?>
                                <a href="manage_orders.php?action=set_status&status=confirmed&id=<?php echo $order['order_id']; ?>" class="btn btn-sm btn-success" title="Mark Confirmed">Confirm</a>
                                <?php endif; ?>
                                <a href="manage_orders.php?action=delete&id=<?php echo $order['order_id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Delete this order?');">Delete</a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </main>
    </div>
    <script src="../../assets/js/admin.js"></script>
</body>
</html>
