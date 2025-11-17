<?php
try {
    $pdo = new PDO('mysql:host=127.0.0.1;dbname=capstone_inventory', 'root', '');
    $stmt = $pdo->query("SHOW TABLES LIKE 'staff'");
    if ($stmt->rowCount() > 0) {
        echo "Staff table exists\n";
    } else {
        echo "Staff table does not exist\n";
    }
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}