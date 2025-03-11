<?php
$host="localhost";
$dbname="watch_store";
$username="root";
$password="";
$dsn="mysql:host=$host;dbname=$dbname";
try {
    $conn = new PDO($dsn, $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    echo "Database Connection Failed: " . $e->getMessage();
}

$categories = isset($_GET['categories']) ? $_GET['categories'] : 'all';

$query = "SELECT p.*, ws.strap_type, ws.water_resistant, ws.material, ws.type, ws.color, ws.dial_size, ws.target_audience
          FROM products p 
          LEFT JOIN watch_specs ws ON p.id = ws.product_id";

if ($categories !== 'all') {
    $query .= " WHERE p.category_name = :categories";
}

$stmt = $conn->prepare($query);
if ($categories !== 'all') {
    $stmt->bindParam(':categories', $categories);
}
$stmt->execute();
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_to_cart'])) {
    session_start();
    
    if (!isset($_SESSION['user_id'])) {
        header("Location: /watch_store/public/views/signup_login.php");
        exit();
    }
    
    $product_id = $_POST['product_id'];
    $user_id = $_SESSION['user_id'];
    $quantity = 1;
    
    // تحقق من وجود المستخدم في جدول users
    $userCheckQuery = "SELECT id FROM users WHERE id = :user_id";
    $userCheckStmt = $conn->prepare($userCheckQuery);
    $userCheckStmt->bindParam(':user_id', $user_id);
    $userCheckStmt->execute();
    
    if ($userCheckStmt->rowCount() == 0) {
        // إذا لم يكن المستخدم موجودًا، أعد توجيهه إلى صفحة التسجيل/تسجيل الدخول
        header("Location: /watch_store/public/views/signup_login.php");
        exit();
    }
    
    // Check if user exists in the database first
    $userCheckQuery = "SELECT id FROM users WHERE id = :user_id";
    $userCheckStmt = $conn->prepare($userCheckQuery);
    $userCheckStmt->bindParam(':user_id', $user_id);
    $userCheckStmt->execute();
    
    if ($userCheckStmt->rowCount() == 0) {
        // User doesn't exist in database - session is invalid
        session_unset();
        session_destroy();
        header("Location: /watch_store/public/views/signup_login.php?error=invalid_session");
        exit();
    }
    
    // Now proceed with cart operations
    $cartQuery = "SELECT id FROM cart WHERE user_id = :user_id";
    $cartStmt = $conn->prepare($cartQuery);
    $cartStmt->bindParam(':user_id', $user_id);
    $cartStmt->execute();
    
    if ($cartStmt->rowCount() > 0) {
        $cart = $cartStmt->fetch(PDO::FETCH_ASSOC);
        $cart_id = $cart['id'];
    } else {
        try {
            $createCartQuery = "INSERT INTO cart (user_id, created_at) VALUES (:user_id, NOW())";
            $createCartStmt = $conn->prepare($createCartQuery);
            $createCartStmt->bindParam(':user_id', $user_id);
            $createCartStmt->execute();
            $cart_id = $conn->lastInsertId();
        } catch (PDOException $e) {
            // Log error and redirect with meaningful message
            error_log("Cart creation error: " . $e->getMessage());
            header("Location: " . $_SERVER['PHP_SELF'] . "?error=cart_creation");
            exit();
        }
    }
    
    $checkQuery = "SELECT * FROM cart_items WHERE cart_id = :cart_id AND product_id = :product_id";
    $checkStmt = $conn->prepare($checkQuery);
    $checkStmt->bindParam(':cart_id', $cart_id);
    $checkStmt->bindParam(':product_id', $product_id);
    $checkStmt->execute();
    
    if ($checkStmt->rowCount() > 0) {
        $updateQuery = "UPDATE cart_items SET quantity = quantity + :quantity WHERE cart_id = :cart_id AND product_id = :product_id";
        $updateStmt = $conn->prepare($updateQuery);
        $updateStmt->bindParam(':quantity', $quantity);
        $updateStmt->bindParam(':cart_id', $cart_id);
        $updateStmt->bindParam(':product_id', $product_id);
        $updateStmt->execute();
    } else {
        $insertQuery = "INSERT INTO cart_items (cart_id, product_id, quantity, added_at) VALUES (:cart_id, :product_id, :quantity, NOW())";
        $insertStmt = $conn->prepare($insertQuery);
        $insertStmt->bindParam(':cart_id', $cart_id);
        $insertStmt->bindParam(':product_id', $product_id);
        $insertStmt->bindParam(':quantity', $quantity);
        $insertStmt->execute();
    }
    
    header("Location: " . $_SERVER['PHP_SELF'] . (isset($_GET['categories']) ? "?categories=".$_GET['categories']."&success=1" : "?success=1"));
    exit();
}

$brandQuery = "SELECT DISTINCT brand FROM products ORDER BY brand ASC";
$brandStmt = $conn->prepare($brandQuery);
$brandStmt->execute();
$brands = $brandStmt->fetchAll(PDO::FETCH_COLUMN);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/category.css">
    <link rel="stylesheet" href="../assets/css/navbar.css">
    <link rel="stylesheet" href="../assets/css/footer.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/2.1.2/sweetalert.min.js" integrity="sha512-AA1Bzp5Q0K1KanKKmvN/4d3IRKVlv9PYgwFPvm32nPO6QS8yH1HO7LbgB1pgiOxPtfeg5zEn2ba64MUcqJx6CA==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <title>Luxury Watches</title>
</head>

<body>
    <?php include './components/navbar.php'; ?>
    <main class="container">
        <aside class="sidebar">
            <h3>Filters</h3>
            <label for="priceRange">Price Range</label>
            <input id="priceRange" type="range" min="50" max="15000" value="7500">
            <span id="priceValue">$7500</span>
            <h4>Brand</h4>
            <select id="filter-brand">
                <option value="all">All Brands</option>
                <?php foreach ($brands as $brand): ?>
                <option value="<?= htmlspecialchars($brand) ?>">
                    <?= htmlspecialchars($brand) ?>
                </option>
                <?php endforeach; ?>
            </select>
            <h4>Color</h4>
            <select id="filter-color">
                <option value="all">All Colors</option>
                <option value="Black">Black</option>
                <option value="Silver">Silver</option>
                <option value="gold">gold</option>
                <option value="Rose Gold">Rose Gold</option>
                <option value="Space">Space</option>
                <option value="Gray">Gray</option>
            </select>
            <h4>Key Features</h4>
            <label><input type="checkbox" id="feature-chrono"> Chronograph</label>
            <label><input type="checkbox" id="feature-auto"> Automatic</label>
            <label><input type="checkbox" id="feature-waterproof"> Waterproof</label>
            <button class="reset" id="resetFilters">Reset Filters</button>
        </aside>
        <section class="products">
            <div class="top-header">
                <h2>Luxury Watches
                    <?= $categories !== 'all' ? ' - ' . ucfirst($categories) : '' ?>
                </h2>
                <p>Explore our exclusive collection of high-end luxury watches crafted with precision and elegance.</p>
            </div>
            <div class="product-list">
                <?php if (count($products) > 0): ?>
                    <?php foreach ($products as $product): ?>
                        <div class="card" data-brand="<?=$product['brand']?>" data-price="<?=$product['price']?>" data-color="<?=$product['color']?>">
                            <a href="../views/product_page.php?id=<?= $product['id'] ?>" class="product-link">
                                <img src="/watch_store/dashboard/assets/productImages/<?= htmlspecialchars($product['image'] ?? 'placeholder.jpg') ?>" alt="Giorgio Galli S2Ti Swiss Made Automatic 38mm">
                            </a>

                            <a href="../views/product_page.php?id=<?= $product['id'] ?>" class="product-title">
                                <h3><?= $product['name'] ?></h3>
                            </a>

                            <p class="size"><? $product['category_name'] ?></p>
                            <p class="price">$<?= $product['price'] ?></p>

                            <form method="post">
                                <input type="hidden" name="product_id" value="<?= $product['id'] ?>">
                                <button type="submit" name="add_to_cart">Add to Cart</button>
                            </form>
                        </div>
                        <?php endforeach; ?>
                        <?php else: ?>
                    <p>No results found.</p>
                <?php endif; ?>
            </div>
        </section>
    </main>
    <?php include './components/footer.html'; ?>
    <script src="../assets/js/navbar.js"></script>
    <script src="../assets/js/category.js"></script>
    <?php if (isset($_GET['success']) && $_GET['success'] == 1): ?>
    <script>
        swal("Success!", "Item added to cart successfully!", "success");
    </script>
    <?php endif; ?>
</body>

</html>