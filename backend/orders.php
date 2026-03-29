<?php
// backend/orders.php
session_start();
require_once 'config.php';

header('Content-Type: application/json');

$data = json_decode(file_get_contents('php://input'), true) ?? $_POST;
$action = $data['action'] ?? $_GET['action'] ?? '';

switch ($action) {
    case 'place_order':
        if (!isset($_SESSION['user_id'])) {
            echo json_encode(['success' => false, 'message' => 'Please login to place an order.']);
            exit;
        }

        $product_id = $data['product_id'] ?? 0;
        $order_date = date('Y-m-d H:i:s');
        $user_id = $_SESSION['user_id'];
        
        // Ensure product is available
        $checkStmt = $pdo->prepare("SELECT sell_status FROM productdetails WHERE Product_id = ?");
        $checkStmt->execute([$product_id]);
        $product = $checkStmt->fetch();

        if ($product && $product['sell_status'] == 'available') {
            $stmt = $pdo->prepare("INSERT INTO ordertable (Product_id, order_date, payment_status, user_id, customer_id) VALUES (?, ?, 'pending', ?, ?)");
            if ($stmt->execute([$product_id, $order_date, $user_id, $user_id])) {
                $order_id = $pdo->lastInsertId();
                echo json_encode(['success' => true, 'message' => 'Order placed successfully. Proceeding to payment.', 'order_id' => $order_id]);
            } else {
                echo json_encode(['success' => false, 'message' => 'Failed to place order.']);
            }
        } else {
            echo json_encode(['success' => false, 'message' => 'Product is not available.']);
        }
        break;

    case 'fetch_my_orders':
        if (!isset($_SESSION['user_id'])) {
            echo json_encode(['success' => false, 'message' => 'Unauthorized']);
            exit;
        }

        $stmt = $pdo->prepare("SELECT o.*, p.product_name, p.product_image, p.product_price, pm.payment_status as pm_status
                               FROM ordertable o 
                               JOIN productdetails p ON o.Product_id = p.Product_id 
                               LEFT JOIN paymenttable pm ON o.order_id = pm.order_id
                               WHERE o.user_id = ? 
                               ORDER BY o.order_id DESC");
        $stmt->execute([$_SESSION['user_id']]);
        $orders = $stmt->fetchAll();

        echo json_encode(['success' => true, 'orders' => $orders]);
        break;

    default:
        echo json_encode(['success' => false, 'message' => 'Invalid action']);
        break;
}
?>
