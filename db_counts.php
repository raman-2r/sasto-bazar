<?php
require 'backend/config.php';
try {
    $stmt = $pdo->query("SELECT COUNT(*) FROM userdetails");
    echo "Users: " . $stmt->fetchColumn() . "\n";
    $stmt = $pdo->query("SELECT COUNT(*) FROM productdetails");
    echo "Products: " . $stmt->fetchColumn() . "\n";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
?>
