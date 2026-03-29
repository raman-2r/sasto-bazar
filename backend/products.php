<?php
// backend/products.php
session_start();
require_once 'config.php';

header('Content-Type: application/json');

$action = $_GET['action'] ?? $_POST['action'] ?? '';

switch ($action) {
    case 'fetch_all':
        // Fetch all products that are 'available' and 'display_home'=1
        // Allow optional category filter and search query
        $category = $_GET['category'] ?? '';
        $search = $_GET['search'] ?? '';

        $query = "SELECT p.*, u.name as seller_name FROM productdetails p 
                  JOIN userdetails u ON p.user_id = u.user_id 
                  WHERE p.sell_status = 'available' AND p.display_home = 1";
        $params = [];

        if (!empty($category)) {
            $query .= " AND p.category_name = ?";
            $params[] = $category;
        }

        if (!empty($search)) {
            $query .= " AND (p.product_name LIKE ? OR p.category_name LIKE ?)";
            $params[] = "%$search%";
            $params[] = "%$search%";
        }

        // Newest first based on ID
        $query .= " ORDER BY p.Product_id DESC";
        
        $stmt = $pdo->prepare($query);
        $stmt->execute($params);
        $products = $stmt->fetchAll();
        
        echo json_encode(['success' => true, 'products' => $products]);
        break;

    case 'fetch_one':
        $product_id = $_GET['id'] ?? 0;
        if (!$product_id) {
            echo json_encode(['success' => false, 'message' => 'Product ID required.']);
            exit;
        }

        $stmt = $pdo->prepare("SELECT p.*, u.name as seller_name, u.phone as seller_phone, u.email as seller_email 
                               FROM productdetails p 
                               JOIN userdetails u ON p.user_id = u.user_id 
                               WHERE p.Product_id = ?");
        $stmt->execute([$product_id]);
        $product = $stmt->fetch();

        if ($product) {
            echo json_encode(['success' => true, 'product' => $product]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Product not found.']);
        }
        break;

    case 'add_product':
        if (!isset($_SESSION['user_id'])) {
            echo json_encode(['success' => false, 'message' => 'Unauthorized']);
            exit;
        }

        $product_name = $_POST['product_name'] ?? '';
        $category_name = $_POST['category_name'] ?? '';
        $product_price = $_POST['product_price'] ?? '';
        $product_age = $_POST['product_age'] ?? '';
        $product_bio = $_POST['product_bio'] ?? '';
        $sell_status = $_POST['sell_status'] ?? 'available';
        
        $image_name = 'default.jpg'; // fallback

        // Handle image upload
        if (isset($_FILES['product_image']) && $_FILES['product_image']['error'] == UPLOAD_ERR_OK) {
            $tmp_name = $_FILES['product_image']['tmp_name'];
            $file_name = basename($_FILES['product_image']['name']);
            $ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
            
            // Validate extension
            if (in_array($ext, ['jpg', 'jpeg', 'png', 'webp'])) {
                // Ensure unique name
                $new_file_name = uniqid() . '_' . $file_name;
                $upload_dir = '../images/products/';
                if (!is_dir($upload_dir)) {
                    mkdir($upload_dir, 0777, true);
                }
                $destination = $upload_dir . $new_file_name;
                
                if (move_uploaded_file($tmp_name, $destination)) {
                    $image_name = $new_file_name;
                }
            } else {
                echo json_encode(['success' => false, 'message' => 'Invalid image format. Allow jpg, jpeg, png, webp.']);
                exit;
            }
        }

        $stmt = $pdo->prepare("INSERT INTO productdetails (user_id, product_name, category_name, product_price, product_image, product_age, product_bio, sell_status, display_home) VALUES (?, ?, ?, ?, ?, ?, ?, ?, 1)");
        
        if ($stmt->execute([$_SESSION['user_id'], $product_name, $category_name, $product_price, $image_name, $product_age, $product_bio, $sell_status])) {
            echo json_encode(['success' => true, 'message' => 'Product listed successfully.']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to list product.']);
        }
        break;

    case 'delete_product':
        if (!isset($_SESSION['user_id'])) {
            echo json_encode(['success' => false, 'message' => 'Unauthorized']);
            exit;
        }
        $data = json_decode(file_get_contents('php://input'), true);
        $product_id = $data['product_id'] ?? 0;

        // Ensure the product belongs to the user OR the user is an admin
        $stmt = $pdo->prepare("SELECT user_id, product_image FROM productdetails WHERE Product_id = ?");
        $stmt->execute([$product_id]);
        $product = $stmt->fetch();

        if ($product) {
            if ($product['user_id'] == $_SESSION['user_id'] || $_SESSION['role'] === 'admin') {
                $delStmt = $pdo->prepare("DELETE FROM productdetails WHERE Product_id = ?");
                $delStmt->execute([$product_id]);
                echo json_encode(['success' => true, 'message' => 'Product deleted.']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Forbidden']);
            }
        } else {
            echo json_encode(['success' => false, 'message' => 'Product not found.']);
        }
        break;

    default:
        echo json_encode(['success' => false, 'message' => 'Invalid action']);
}
?>
