<?php
// backend/admin/dashboard.php
session_start();
require_once '../config.php';

// Check if admin is logged in
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../../frontend/login.html");
    exit;
}

// Fetch Stats
$total_users = $pdo->query("SELECT COUNT(*) FROM userdetails")->fetchColumn();
$total_products = $pdo->query("SELECT COUNT(*) FROM productdetails")->fetchColumn();
$total_orders = $pdo->query("SELECT COUNT(*) FROM ordertable")->fetchColumn();
$total_transactions = $pdo->query("SELECT COUNT(*) FROM paymenttable")->fetchColumn();

// Fetch Recent Logins / Registrations (simulated by checking users)
$recent_users = $pdo->query("SELECT name, email, role, registration_date FROM userdetails ORDER BY user_id DESC LIMIT 5")->fetchAll();
$recent_products = $pdo->query("SELECT p.product_name, p.product_price, u.name as seller, p.sell_status FROM productdetails p JOIN userdetails u ON p.user_id = u.user_id ORDER BY p.Product_id DESC LIMIT 5")->fetchAll();
$recent_orders = $pdo->query("SELECT o.order_id, p.product_name, u.name as buyer, o.order_date, o.payment_status FROM ordertable o JOIN productdetails p ON o.Product_id = p.Product_id JOIN userdetails u ON o.user_id = u.user_id ORDER BY o.order_id DESC LIMIT 5")->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - SastoBazar</title>
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
                <li><a href="dashboard.php" class="active">Dashboard</a></li>
                <li><a href="manage_users.php">Manage Users</a></li>
                <li><a href="manage_products.php">Manage Products</a></li>
                <li><a href="manage_orders.php">Manage Orders</a></li>
                <li><a href="manage_transactions.php">Transactions</a></li>
                <li><a href="../../frontend/index.html">Back to Site</a></li>
                <li><a href="#" id="admin-logout">Logout</a></li>
            </ul>
        </aside>

        <!-- Main Content -->
        <main class="main-content">
            <header class="top-header">
                <h1>Dashboard Overview</h1>
                <div class="admin-info">Welcome, <?php echo htmlspecialchars($_SESSION['name']); ?></div>
            </header>

            <section class="stats-grid">
                <div class="stat-card">
                    <h3>Total Users</h3>
                    <div class="stat-value"><?php echo $total_users; ?></div>
                </div>
                <div class="stat-card">
                    <h3>Total Products</h3>
                    <div class="stat-value"><?php echo $total_products; ?></div>
                </div>
                <div class="stat-card">
                    <h3>Total Orders</h3>
                    <div class="stat-value"><?php echo $total_orders; ?></div>
                </div>
                <div class="stat-card">
                    <h3>Total Transactions</h3>
                    <div class="stat-value"><?php echo $total_transactions; ?></div>
                </div>
            </section>

            <div class="tables-grid">
                <!-- Recent Users -->
                <div class="table-card">
                    <h2>Recent Users / Logon Activity</h2>
                    <table>
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($recent_users as $ru): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($ru['name']); ?></td>
                                <td><?php echo htmlspecialchars($ru['email']); ?></td>
                                <td><?php echo htmlspecialchars($ru['registration_date']); ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

                <!-- Recent Products -->
                <div class="table-card">
                    <h2>Recent Products</h2>
                    <table>
                        <thead>
                            <tr>
                                <th>Product Name</th>
                                <th>Price</th>
                                <th>Seller</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($recent_products as $rp): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($rp['product_name']); ?></td>
                                <td>Rs. <?php echo htmlspecialchars($rp['product_price']); ?></td>
                                <td><?php echo htmlspecialchars($rp['seller']); ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

                <!-- Recent Orders -->
                <div class="table-card" style="grid-column: 1 / -1;">
                    <h2>Recent Orders</h2>
                    <table>
                        <thead>
                            <tr>
                                <th>Order ID</th>
                                <th>Product</th>
                                <th>Buyer</th>
                                <th>Date</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($recent_orders as $ro): ?>
                            <tr>
                                <td>#<?php echo htmlspecialchars($ro['order_id']); ?></td>
                                <td><?php echo htmlspecialchars($ro['product_name']); ?></td>
                                <td><?php echo htmlspecialchars($ro['buyer']); ?></td>
                                <td><?php echo htmlspecialchars($ro['order_date']); ?></td>
                                <td><span class="badge status-<?php echo strtolower($ro['payment_status']); ?>"><?php echo htmlspecialchars($ro['payment_status']); ?></span></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </main>
    </div>
    <script src="../../assets/js/admin.js"></script>
</body>
</html>
