<?php
// backend/payments.php
session_start();
require_once 'config.php';

header('Content-Type: application/json');

$data = json_decode(file_get_contents('php://input'), true) ?? $_POST;
$action = $data['action'] ?? $_GET['action'] ?? '';

switch ($action) {
    case 'confirm_payment':
        if (!isset($_SESSION['user_id'])) {
            echo json_encode(['success' => false, 'message' => 'Unauthorized']);
            exit;
        }

        $order_id = $data['order_id'] ?? 0;
        
        // Fetch order details
        $stmt = $pdo->prepare("SELECT Product_id, user_id, payment_status FROM ordertable WHERE order_id = ?");
        $stmt->execute([$order_id]);
        $order = $stmt->fetch();

        if ($order && $order['user_id'] == $_SESSION['user_id']) {
            if ($order['payment_status'] == 'confirmed') {
                echo json_encode(['success' => false, 'message' => 'Payment already confirmed.']);
                exit;
            }

            // Begin transaction
            $pdo->beginTransaction();
            try {
                // Insert into paymenttable
                $stmt2 = $pdo->prepare("INSERT INTO paymenttable (order_id, payment_status, product_id) VALUES (?, 'confirmed', ?)");
                $stmt2->execute([$order_id, $order['Product_id']]);
                
                // Update order status
                $stmt3 = $pdo->prepare("UPDATE ordertable SET payment_status = 'confirmed' WHERE order_id = ?");
                $stmt3->execute([$order_id]);
                
                // Update product sell_status
                $stmt4 = $pdo->prepare("UPDATE productdetails SET sell_status = 'sold' WHERE Product_id = ?");
                $stmt4->execute([$order['Product_id']]);

                $pdo->commit();
                echo json_encode(['success' => true, 'message' => 'Payment confirmed! Product marked as sold.']);
            } catch (Exception $e) {
                $pdo->rollBack();
                echo json_encode(['success' => false, 'message' => 'Payment confirmation failed.']);
            }
        } else {
            echo json_encode(['success' => false, 'message' => 'Order not found or access denied.']);
        }
        break;

    default:
        echo json_encode(['success' => false, 'message' => 'Invalid action']);
        break;
}
?>
