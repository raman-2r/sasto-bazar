<?php
// backend/admin/manage_users.php
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
    $pdo->prepare("DELETE FROM userdetails WHERE user_id = ?")->execute([$id]);
    $message = "User deleted successfully!";
} elseif ($action === 'toggle_role') {
    $id = $_GET['id'];
    $current_role = $_GET['role'];
    $new_role = ($current_role === 'admin') ? 'user' : 'admin';
    $pdo->prepare("UPDATE userdetails SET role = ? WHERE user_id = ?")->execute([$new_role, $id]);
    $message = "Role updated successfully!";
}

$users = $pdo->query("SELECT * FROM userdetails ORDER BY user_id DESC")->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Users - SastoBazar</title>
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
                <li><a href="manage_users.php" class="active">Manage Users</a></li>
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
                <h1>Manage Users</h1>
            </header>

            <?php if($message): ?>
                <div class="alert alert-success"><?php echo $message; ?></div>
            <?php endif; ?>

            <div class="table-card">
                <h2>Users List</h2>
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Phone</th>
                            <th>Role</th>
                            <th>Reg Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($users as $user): ?>
                        <tr>
                            <td><?php echo $user['user_id']; ?></td>
                            <td><?php echo htmlspecialchars($user['name']); ?></td>
                            <td><?php echo htmlspecialchars($user['email']); ?></td>
                            <td><?php echo htmlspecialchars($user['phone']); ?></td>
                            <td><span class="badge status-<?php echo $user['role'] == 'admin' ? 'admin' : 'user'; ?>"><?php echo htmlspecialchars($user['role']); ?></span></td>
                            <td><?php echo htmlspecialchars($user['registration_date']); ?></td>
                            <td class="action-buttons">
                                <a href="manage_users.php?action=toggle_role&id=<?php echo $user['user_id']; ?>&role=<?php echo $user['role']; ?>" class="btn btn-sm btn-info" onclick="return confirm('Toggle role for this user?');">Toggle Role</a>
                                <?php if($user['user_id'] != $_SESSION['user_id']): ?>
                                <a href="manage_users.php?action=delete&id=<?php echo $user['user_id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Delete this user?');">Delete</a>
                                <?php endif; ?>
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
