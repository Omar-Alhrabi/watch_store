<?php
// controllers/add_to_cart.php
session_start();
include_once '../config/connectt.php';

// Set header to return JSON response
header('Content-Type: application/json');

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode([
        'success' => false,
        'message' => 'Please login to add items to cart'
    ]);
    exit;
}

// Get post data
$product_id = $_POST['product_id'] ?? 0;
$quantity = $_POST['quantity'] ?? 1;

// Validate inputs
if (!is_numeric($product_id) || $product_id <= 0) {
    echo json_encode([
        'success' => false,
        'message' => 'Invalid product ID'
    ]);
    exit;
}

if (!is_numeric($quantity) || $quantity <= 0) {
    echo json_encode([
        'success' => false,
        'message' => 'Invalid quantity'
    ]);
    exit;
}

// Check product availability
$stmt = $pdo->prepare('SELECT stock FROM products WHERE id = ?');
$stmt->execute([$product_id]);
$product = $stmt->fetch();

if (!$product) {
    echo json_encode([
        'success' => false,
        'message' => 'Product not found'
    ]);
    exit;
}

if ($product['stock'] < $quantity) {
    echo json_encode([
        'success' => false,
        'message' => 'Not enough stock available'
    ]);
    exit;
}

// Check if product already exists in cart
$stmt = $pdo->prepare('SELECT id, quantity FROM cart_items WHERE user_id = ? AND product_id = ?');
$stmt->execute([$_SESSION['user_id'], $product_id]);
$cartItem = $stmt->fetch();

try {
    $pdo->beginTransaction();
    
    if ($cartItem) {
        // Update existing cart item
        $newQuantity = $cartItem['quantity'] + $quantity;
        $stmt = $pdo->prepare('UPDATE cart_items SET quantity = ? WHERE id = ?');
        $stmt->execute([$newQuantity, $cartItem['id']]);
    } else {
        // Insert new cart item
        $stmt = $pdo->prepare('INSERT INTO cart_items (user_id, product_id, quantity) VALUES (?, ?, ?)');
        $stmt->execute([
            $_SESSION['user_id'],
            $product_id,
            $quantity
        ]);
    }
    
    $pdo->commit();
    
    echo json_encode([
        'success' => true,
        'message' => 'Product added to cart successfully'
        
    ]);
} catch (PDOException $e) {
    $pdo->rollBack();
    error_log($e->getMessage());
    
    echo json_encode([
        'success' => false,
        'message' => 'Database error occurred'
    ]);
}
?>