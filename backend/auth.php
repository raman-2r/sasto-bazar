<?php
// backend/auth.php
session_start();
require_once 'config.php';

header('Content-Type: application/json');

// Ensure that we get JSON payloads from Fetch API or POST arrays
$data = json_decode(file_get_contents('php://input'), true) ?? $_POST;

$action = $data['action'] ?? $_GET['action'] ?? '';

switch ($action) {
    case 'register':
        $name = trim($data['name'] ?? '');
        $email = trim($data['email'] ?? '');
        $phone = trim($data['phone'] ?? '');
        $password = trim($data['password'] ?? '');
        $address = trim($data['address'] ?? '');
        $gender = trim($data['gender'] ?? '');
        
        if (empty($name) || empty($email) || empty($password)) {
            echo json_encode(['success' => false, 'message' => 'Please fill all required fields.']);
            exit;
        }

        // Check user existence
        $stmt = $pdo->prepare("SELECT user_id FROM userdetails WHERE email = ?");
        $stmt->execute([$email]);
        if ($stmt->fetch()) {
            echo json_encode(['success' => false, 'message' => 'Email already registered.']);
            exit;
        }

        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $date = date('Y-m-d H:i:s');

        $stmt = $pdo->prepare("INSERT INTO userdetails (name, email, phone, password, address, gender, role, registration_date, image) VALUES (?, ?, ?, ?, ?, ?, 'user', ?, 'default.png')");
        if ($stmt->execute([$name, $email, $phone, $hashed_password, $address, $gender, $date])) {
            echo json_encode(['success' => true, 'message' => 'Registration successful! You can now login.']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Registration failed.']);
        }
        break;

    case 'login':
        $email = trim($data['email'] ?? '');
        $password = trim($data['password'] ?? '');

        if (empty($email) || empty($password)) {
            echo json_encode(['success' => false, 'message' => 'Email and password are required.']);
            exit;
        }

        $stmt = $pdo->prepare("SELECT * FROM userdetails WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password'])) {
            // Update token or login time if needed (as per requirements track login via token field)
            $token = bin2hex(random_bytes(16));
            $updateTokenStmt = $pdo->prepare("UPDATE userdetails SET token = ? WHERE user_id = ?");
            $updateTokenStmt->execute([$token, $user['user_id']]);

            // Set sessions
            $_SESSION['user_id'] = $user['user_id'];
            $_SESSION['role'] = $user['role'];
            $_SESSION['name'] = $user['name'];
            $_SESSION['email'] = $user['email'];
            
            echo json_encode([
                'success' => true, 
                'message' => 'Login successful', 
                'role' => $user['role'],
                'user' => [
                    'id' => $user['user_id'],
                    'name' => $user['name']
                ]
            ]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Invalid email or password.']);
        }
        break;

    case 'logout':
        session_unset();
        session_destroy();
        echo json_encode(['success' => true, 'message' => 'Logged out successfully.']);
        break;

    case 'check':
        if (isset($_SESSION['user_id'])) {
            echo json_encode([
                'loggedIn' => true, 
                'user' => [
                    'id' => $_SESSION['user_id'], 
                    'name' => $_SESSION['name'], 
                    'role' => $_SESSION['role'],
                    'email' => $_SESSION['email']
                ]
            ]);
        } else {
            echo json_encode(['loggedIn' => false]);
        }
        break;

    default:
        echo json_encode(['success' => false, 'message' => 'Invalid action.']);
        break;
}
?>
