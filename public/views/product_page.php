<?php
  // Include the database connection file
  include('../config/connectt.php');

  // Get the watch details from the database
  $watchId = $_GET['id'] ?? 1;
  $stmt = $pdo->prepare('SELECT * FROM products WHERE id = ?');
  $stmt->execute([$watchId]);
  $watch = $stmt->fetch();

  // Handle wishlist action
  if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_to_wishlist'])) {
    session_start();
    
    if (!isset($_SESSION['user_id'])) {
        header("Location: /watch_store/public/views/signup_login.php");
        exit();
    }
    
    $product_id = $_POST['product_id'];
    $user_id = $_SESSION['user_id'];
    
    // Check if user exists in the database first
    $userCheckQuery = "SELECT id FROM users WHERE id = :user_id";
    $userCheckStmt = $pdo->prepare($userCheckQuery);
    $userCheckStmt->bindParam(':user_id', $user_id);
    $userCheckStmt->execute();
    
    if (!$userCheckStmt->rowCount() > 0) {
        // User doesn't exist in database - session is invalid
        header("Location: /watch_store/public/views/signup_login.php?error=invalid_session");
        exit();
    }
    
    // Check if item already exists in wishlist
    $wishlistCheckQuery = "SELECT * FROM wishlist WHERE user_id = :user_id AND product_id = :product_id";
    $wishlistCheckStmt = $pdo->prepare($wishlistCheckQuery);
    $wishlistCheckStmt->bindParam(':user_id', $user_id);
    $wishlistCheckStmt->bindParam(':product_id', $product_id);
    $wishlistCheckStmt->execute();
    
    if ($wishlistCheckStmt->rowCount() == 0) {
        // Item not in wishlist, add it
        try {
            $insertQuery = "INSERT INTO wishlist (user_id, product_id) VALUES (:user_id, :product_id)";
            $insertStmt = $pdo->prepare($insertQuery);
            $insertStmt->bindParam(':user_id', $user_id);
            
            $insertStmt->bindParam(':product_id', $product_id);
            $insertStmt->execute();
            
            // Set success message
            // $_SESSION['wishlist_message'] = "Product added to wishlist!";
        } catch (PDOException $e) {
            // Log error and set error message
            error_log("Wishlist error: " . $e->getMessage());
            // $_SESSION['wishlist_message'] = "Error adding to wishlist.";
        }
    } else {
        // Item already in wishlist
        // $_SESSION['wishlist_message'] = "Product already in your wishlist!";
    }
    
    header("Location: " . $_SERVER['REQUEST_URI']);
    exit();
  }

  // Handle add to cart action
  if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_to_cart'])) {
    session_start();
    
    if (!isset($_SESSION['user_id'])) {
        header("Location: /watch_store/public/views/signup_login.php");
        exit();
    }
    
    $product_id = $_POST['product_id'];
    $user_id = $_SESSION['user_id'];
    $quantity = $_POST['quantity'];
    
    $userCheckQuery = "SELECT id FROM users WHERE id = :user_id";
    $userCheckStmt = $pdo->prepare($userCheckQuery);
    $userCheckStmt->bindParam(':user_id', $user_id);
    $userCheckStmt->execute();
    
    if (!isset($_SESSION['user_id']) ) {
  
        header("Location: /watch_store/public/views/signup_login.php?error=invalid_session");
        exit();
    }
    
    $cartQuery = "SELECT id FROM cart WHERE user_id = :user_id";
    $cartStmt = $pdo->prepare($cartQuery);
    $cartStmt->bindParam(':user_id', $user_id);
    $cartStmt->execute();
    
    if ($cartStmt->rowCount() > 0) {
        $cart = $cartStmt->fetch(PDO::FETCH_ASSOC);
        $cart_id = $cart['id'];
    } else {
        try {
            $createCartQuery = "INSERT INTO cart (user_id, created_at) VALUES (:user_id, NOW())";
            $createCartStmt = $pdo->prepare($createCartQuery);
            $createCartStmt->bindParam(':user_id', $user_id);
            $createCartStmt->execute();
            $cart_id = $pdo->lastInsertId();
        } catch (PDOException $e) {
            // Log error and redirect with meaningful message
            error_log("Cart creation error: " . $e->getMessage());
            header("Location: " . $_SERVER['PHP_SELF'] . "?error=cart_creation");
            exit();
        }
    }
    
    $checkQuery = "SELECT * FROM cart_items WHERE cart_id = :cart_id AND product_id = :product_id";
    $checkStmt = $pdo->prepare($checkQuery);
    $checkStmt->bindParam(':cart_id', $cart_id);
    $checkStmt->bindParam(':product_id', $product_id);
    $checkStmt->execute();
    
    if ($checkStmt->rowCount() > 0) {
        $updateQuery = "UPDATE cart_items SET quantity = quantity + :quantity WHERE cart_id = :cart_id AND product_id = :product_id";
        $updateStmt = $pdo->prepare($updateQuery);
        $updateStmt->bindParam(':quantity', $quantity);
        $updateStmt->bindParam(':cart_id', $cart_id);
        $updateStmt->bindParam(':product_id', $product_id);
        $updateStmt->execute();
    } else {
        $insertQuery = "INSERT INTO cart_items (cart_id, product_id, quantity, added_at) VALUES (:cart_id, :product_id, :quantity, NOW())";
        $insertStmt = $pdo->prepare($insertQuery);
        $insertStmt->bindParam(':cart_id', $cart_id);
        $insertStmt->bindParam(':product_id', $product_id);
        $insertStmt->bindParam(':quantity', $quantity);
        $insertStmt->execute();
    }
    
    header("Location: " . $_SERVER['REQUEST_URI']);
    exit();
}

// Start session if not already started to display wishlist messages
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title><?php echo htmlspecialchars($watch['name'])?></title>
  <link rel="stylesheet" href="../assets/css/product_page.css" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" />
  <link rel="stylesheet" href="../assets/css/navbar.css">
  <link rel="stylesheet" href="../assets/css/footer.css">
  <style>
    .wishlist-message {
      padding: 10px;
      margin: 10px 0;
      border-radius: 5px;
      text-align: center;
    }
    .success-message {
      background-color: #d4edda;
      color: #155724;
      border: 1px solid #c3e6cb;
    }
    .info-message {
      background-color: #d1ecf1;
      color: #0c5460;
      border: 1px solid #bee5eb;
    }
    .wishlist-btn.active {
      color: red;
    }
    .wishlist-btn i.fas {
      color: red;
    }
  </style>
</head>
<body>
  
<?php include './components/navbar.php'; ?>

  <main class="product-container">
    <!-- Display wishlist messages if any -->
    <?php if (isset($_SESSION['wishlist_message'])): ?>
      <div class="wishlist-message <?php echo strpos($_SESSION['wishlist_message'], 'already') !== false ? 'info-message' : 'success-message'; ?>">
        <?php 
          echo $_SESSION['wishlist_message']; 
          unset($_SESSION['wishlist_message']); // Clear the message after displaying
        ?>
      </div>
    <?php endif; ?>
    
    <div class="product-image">
      <?php
        // Use the watch image from database
        $imageUrl =  $watch['image'] ? "/watch_store/dashboard/assets/productImages/" . $watch['image'] :  "/watch_store/dashboard/assets/productImages/placeholder.jpg";
      ?>
      <img src="<?php echo htmlspecialchars($imageUrl); ?>" alt="Timex Waterbury Traditional Chronograph" />
   
    </div>

    <div class="product-details">
      <br>
      <br>
      <br>

      <div class="product-status">Brand : <?php echo " " . htmlspecialchars($watch['brand'])?></div>

      <h1 class="product-title"><?php echo htmlspecialchars($watch['name'])?></h1>
      
      <div class="product-size"><?php echo htmlspecialchars($watch['description'])?></div>

      <div class="product-color">
        <span class="color-label">Color:</span>
        <span class="color-value"><?php echo htmlspecialchars($watch['color'])?></span>
      </div>

      <div class="color-options">
        <div class="color-swatch selected">
          <img src="<?php echo htmlspecialchars($imageUrl); ?>" alt="Stainless Steel Watch" />
        </div>
      </div>
      
      <!-- Add to Cart Form -->
      <form method="post">
        <div class="product-price">
          <div class="current-price"><?php echo htmlspecialchars($watch['price']).'$'?></div>
          <div class="payment-options">
            <?php if ($watch['stock'] > 0): ?>
              <span class="in-stock"><?php echo htmlspecialchars($watch['stock']) . " "?>In Stock</span>
            <?php else: ?>
              <span class="out-of-stock">Out of Stock</span>
            <?php endif; ?>
          </div>
          
          <div class="stock-input-container">
            <label for="stock_count" class="stock-label">Select Quantity</label>
            <input 
              id="stock_count"
              class="stock-input"
              name="quantity"
              type="number"
              value="1"
              min="1"
              max="<?php echo htmlspecialchars($watch['stock'])?>"
            />
            <input 
              type="hidden"
              name="product_id"
              value="<?php echo htmlspecialchars($watch['id'])?>"
            />
          </div>
        </div>

        <div class="product-actions">
          <!-- Wishlist button inside a form -->
          <button type="submit" name="add_to_wishlist" class="wishlist-btn">
            <?php
              // Check if product is in wishlist to show filled heart
              $in_wishlist = false;
              if (isset($_SESSION['user_id'])) {
                $user_id = $_SESSION['user_id'];
                $product_id = $watch['id'];
                
                $wishlistQuery = "SELECT * FROM wishlist WHERE user_id = :user_id AND product_id = :product_id";
                $wishlistStmt = $pdo->prepare($wishlistQuery);
                $wishlistStmt->bindParam(':user_id', $user_id);
                $wishlistStmt->bindParam(':product_id', $product_id);
                $wishlistStmt->execute();
                
                if ($wishlistStmt->rowCount() > 0) {
                  $in_wishlist = true;
                }
              }
            ?>
            <i class="<?php echo $in_wishlist ? 'fas' : 'far'; ?> fa-heart"></i>
          </button>
          
          <button class="add-to-bag-btn" name="add_to_cart">
            Add to Bag
            <i class="fas fa-arrow-right"></i>
          </button>
        </div>
      </form>
    </div>
  </main>

  <?php include './components/footer.html'; ?>

  <script src="../assets/js/navbar.js"></script>
</body>
</html>