<?php
// backend/users.php
session_start();
require_once 'config.php';

header('Content-Type: application/json');

$data = json_decode(file_get_contents('php://input'), true) ?? $_POST;
$action = $data['action'] ?? $_GET['action'] ?? '';

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

switch ($action) {
    case 'fetch_profile':
        $stmt = $pdo->prepare("SELECT name, email, phone, address, gender, image, registration_date FROM userdetails WHERE user_id = ?");
        $stmt->execute([$_SESSION['user_id']]);
        $user = $stmt->fetch();
        if ($user) {
            echo json_encode(['success' => true, 'user' => $user]);
        } else {
            echo json_encode(['success' => false, 'message' => 'User not found.']);
        }
        break;

    case 'update_profile':
        $name = trim($data['name'] ?? '');
        $phone = trim($data['phone'] ?? '');
        $address = trim($data['address'] ?? '');
        $gender = trim($data['gender'] ?? '');
        
        // Also handling image upload if provided via FormData
        $image_name = '';
        if (isset($_FILES['profile_image']) && $_FILES['profile_image']['error'] == UPLOAD_ERR_OK) {
            $tmp_name = $_FILES['profile_image']['tmp_name'];
            $file_name = basename($_FILES['profile_image']['name']);
            $ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
            
            if (in_array($ext, ['jpg', 'jpeg', 'png'])) {
                $new_file_name = 'avatar_' . $_SESSION['user_id'] . '_' . uniqid() . '.' . $ext;
                $upload_dir = '../images/avatars/';
                if (!is_dir($upload_dir)) {
                    mkdir($upload_dir, 0777, true);
                }
                if (move_uploaded_file($tmp_name, $upload_dir . $new_file_name)) {
                    $image_name = $new_file_name;
                }
            }
        }

        if ($image_name !== '') {
            $stmt = $pdo->prepare("UPDATE userdetails SET name = ?, phone = ?, address = ?, gender = ?, image = ? WHERE user_id = ?");
            $res = $stmt->execute([$name, $phone, $address, $gender, $image_name, $_SESSION['user_id']]);
        } else {
            $stmt = $pdo->prepare("UPDATE userdetails SET name = ?, phone = ?, address = ?, gender = ? WHERE user_id = ?");
            $res = $stmt->execute([$name, $phone, $address, $gender, $_SESSION['user_id']]);
        }

        if ($res) {
            $_SESSION['name'] = $name; // update session var
            echo json_encode(['success' => true, 'message' => 'Profile updated successfully']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to update profile']);
        }
        break;

    case 'fetch_my_products':
        $stmt = $pdo->prepare("SELECT * FROM productdetails WHERE user_id = ? ORDER BY Product_id DESC");
        $stmt->execute([$_SESSION['user_id']]);
        $products = $stmt->fetchAll();
        echo json_encode(['success' => true, 'products' => $products]);
        break;

    default:
        echo json_encode(['success' => false, 'message' => 'Invalid action']);
        break;
}
?>
