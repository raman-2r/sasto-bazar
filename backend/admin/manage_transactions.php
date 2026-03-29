<?php
// backend/admin/manage_transactions.php
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
    $pdo->prepare("DELETE FROM paymenttable WHERE payment_id = ?")->execute([$id]);
    $message = "Transaction record deleted successfully!";
}

$transactions = $pdo->query("SELECT pm.*, p.product_name, p.product_price, u.name as buyer_name, s.name as seller_name, o.order_date
                             FROM paymenttable pm
                             JOIN ordertable o ON pm.order_id = o.order_id
                             JOIN productdetails p ON o.Product_id = p.Product_id
                             JOIN userdetails u ON o.user_id = u.user_id
                             JOIN userdetails s ON p.user_id = s.user_id
                             ORDER BY pm.payment_id DESC")->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Transactions - SastoBazar</title>
    <link rel="stylesheet" href="../../assets/css/admin.css">
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
                <li><a href="manage_orders.php">Manage Orders</a></li>
                <li><a href="manage_transactions.php" class="active">Transactions</a></li>
                <li><a href="../../frontend/index.html">Back to Site</a></li>
                <li><a href="#" id="admin-logout">Logout</a></li>
            </ul>
        </aside>

        <!-- Main Content -->
        <main class="main-content">
            <header class="top-header">
                <h1>Manage Transactions</h1>
            </header>

            <?php if($message): ?>
                <div class="alert alert-success"><?php echo $message; ?></div>
            <?php endif; ?>

            <div class="table-card">
                <h2>Transaction History</h2>
                <table>
                    <thead>
                        <tr>
                            <th>Txn ID</th>
                            <th>Order ID</th>
                            <th>Product</th>
                            <th>Amount</th>
                            <th>Buyer</th>
                            <th>Seller</th>
                            <th>Date</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($transactions as $txn): ?>
                        <tr>
                            <td>#<?php echo $txn['payment_id']; ?></td>
                            <td>#<?php echo $txn['order_id']; ?></td>
                            <td><?php echo htmlspecialchars($txn['product_name']); ?></td>
                            <td>Rs. <?php echo htmlspecialchars($txn['product_price']); ?></td>
                            <td><?php echo htmlspecialchars($txn['buyer_name']); ?></td>
                            <td><?php echo htmlspecialchars($txn['seller_name']); ?></td>
                            <td><?php echo htmlspecialchars($txn['order_date']); ?></td>
                            <td><span class="badge status-<?php echo strtolower($txn['payment_status']); ?>"><?php echo htmlspecialchars($txn['payment_status']); ?></span></td>
                            <td class="action-buttons">
                                <a href="manage_transactions.php?action=delete&id=<?php echo $txn['payment_id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Delete transaction record?');">Delete</a>
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
