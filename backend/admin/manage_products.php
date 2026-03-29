<?php
// backend/admin/manage_products.php
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
    $pdo->prepare("DELETE FROM productdetails WHERE Product_id = ?")->execute([$id]);
    $message = "Product deleted successfully!";
} elseif ($action === 'toggle_home') {
    $id = $_GET['id'];
    $current = (int)$_GET['current'];
    $new = $current === 1 ? 0 : 1;
    $pdo->prepare("UPDATE productdetails SET display_home = ? WHERE Product_id = ?")->execute([$new, $id]);
    $message = "Display on Home toggled!";
} elseif ($action === 'toggle_status') {
    $id = $_GET['id'];
    $current = $_GET['current'];
    $new = $current === 'available' ? 'sold' : 'available';
    $pdo->prepare("UPDATE productdetails SET sell_status = ? WHERE Product_id = ?")->execute([$new, $id]);
    $message = "Sell status toggled!";
}

$products = $pdo->query("SELECT p.*, u.name as seller_name FROM productdetails p JOIN userdetails u ON p.user_id = u.user_id ORDER BY p.Product_id DESC")->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Products - SastoBazar</title>
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
                <li><a href="manage_products.php" class="active">Manage Products</a></li>
                <li><a href="manage_orders.php">Manage Orders</a></li>
                <li><a href="manage_transactions.php">Transactions</a></li>
                <li><a href="../../frontend/index.html">Back to Site</a></li>
                <li><a href="#" id="admin-logout">Logout</a></li>
            </ul>
        </aside>

        <!-- Main Content -->
        <main class="main-content">
            <header class="top-header">
                <h1>Manage Products</h1>
            </header>

            <?php if($message): ?>
                <div class="alert alert-success"><?php echo $message; ?></div>
            <?php endif; ?>

            <div class="table-card">
                <h2>Products List</h2>
                <table>
                    <thead>
                        <tr>
                            <th>Image</th>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Category</th>
                            <th>Price</th>
                            <th>Seller</th>
                            <th>Status / Home</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($products as $product): ?>
                        <tr>
                            <td><img src="../../images/products/<?php echo htmlspecialchars($product['product_image']); ?>" class="prod-img" alt="img"></td>
                            <td><?php echo $product['Product_id']; ?></td>
                            <td><?php echo htmlspecialchars($product['product_name']); ?></td>
                            <td><?php echo htmlspecialchars($product['category_name']); ?></td>
                            <td>Rs. <?php echo htmlspecialchars($product['product_price']); ?></td>
                            <td><?php echo htmlspecialchars($product['seller_name']); ?></td>
                            <td>
                                <span class="badge status-<?php echo strtolower($product['sell_status']); ?>"><?php echo htmlspecialchars($product['sell_status']); ?></span>
                                <span class="badge status-<?php echo $product['display_home'] ? 'admin' : 'secondary'; ?>"><?php echo $product['display_home'] ? 'Home' : 'Hidden'; ?></span>
                            </td>
                            <td class="action-buttons">
                                <a href="manage_products.php?action=toggle_home&id=<?php echo $product['Product_id']; ?>&current=<?php echo $product['display_home']; ?>" class="btn btn-sm btn-info" title="Toggle Display Home">Home</a>
                                <a href="manage_products.php?action=toggle_status&id=<?php echo $product['Product_id']; ?>&current=<?php echo $product['sell_status']; ?>" class="btn btn-sm btn-warning" title="Toggle Status">Status</a>
                                <a href="manage_products.php?action=delete&id=<?php echo $product['Product_id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Delete this product?');">Delete</a>
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
