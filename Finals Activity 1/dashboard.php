<?php
session_start();

// Redirect if not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

require_once 'config/database.php';

// Initialize database
$database = new Database();
$db = $database->getConnection();

// Initialize cart if not exists
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// Handle add to cart
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_to_cart'])) {
    $medicine_id = $_POST['medicine_id'];
    $quantity = $_POST['quantity'] ?? 1;
    
    // Get medicine details
    $stmt = $db->prepare("SELECT * FROM medicines WHERE medicine_id = ?");
    $stmt->execute([$medicine_id]);
    $medicine = $stmt->fetch();
    
    if ($medicine) {
        if (isset($_SESSION['cart'][$medicine_id])) {
            $_SESSION['cart'][$medicine_id]['quantity'] += $quantity;
        } else {
            $_SESSION['cart'][$medicine_id] = [
                'name' => $medicine['name'],
                'price' => $medicine['price'],
                'quantity' => $quantity
            ];
        }
        
        // Add notification
        $stmt = $db->prepare("INSERT INTO notifications (user_id, title, message, type) VALUES (?, ?, ?, 'order')");
        $stmt->execute([
            $_SESSION['user_id'],
            'Added to Cart',
            "Added {$medicine['name']} to your cart"
        ]);
    }
}

// Handle place order
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['place_order'])) {
    if (!empty($_SESSION['cart'])) {
        // Calculate total
        $total = 0;
        foreach ($_SESSION['cart'] as $item) {
            $total += $item['price'] * $item['quantity'];
        }
        
        // Create order
        $stmt = $db->prepare("INSERT INTO orders (user_id, total_amount, status) VALUES (?, ?, 'pending')");
        $stmt->execute([$_SESSION['user_id'], $total]);
        $order_id = $db->lastInsertId();
        
        // Add order items
        $stmt = $db->prepare("INSERT INTO order_items (order_id, medicine_id, quantity, price) VALUES (?, ?, ?, ?)");
        foreach ($_SESSION['cart'] as $medicine_id => $item) {
            $stmt->execute([$order_id, $medicine_id, $item['quantity'], $item['price']]);
        }
        
        // Add notification
        $stmt = $db->prepare("INSERT INTO notifications (user_id, title, message, type) VALUES (?, ?, ?, 'order')");
        $stmt->execute([
            $_SESSION['user_id'],
            'Order Placed',
            "Your order #$order_id has been placed and is pending approval"
        ]);
        
        // Clear cart
        $_SESSION['cart'] = [];
    }
}

// Search functionality
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$query = "SELECT * FROM medicines";
if (!empty($search)) {
    $query .= " WHERE name LIKE :search OR description LIKE :search";
}

$stmt = $db->prepare($query);
if (!empty($search)) {
    $searchTerm = "%$search%";
    $stmt->bindParam(':search', $searchTerm);
}
$stmt->execute();
$medicines = $stmt->fetchAll();

// Calculate cart total
$cart_total = 0;
foreach ($_SESSION['cart'] as $item) {
    $cart_total += $item['price'] * $item['quantity'];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>MedTrack Dashboard</title>
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            font-family: Arial, sans-serif;
        }

        body {
            display: flex;
            min-height: 100vh;
            background-color: #6b0a0a;
            color: white;
        }

        .sidebar {
            width: 180px;
            background-image: url('./images/background.jpg');
            background-size: cover;
            background-position: center;
            display: flex;
            flex-direction: column;
            align-items: center;
            padding-top: 15px;
            position: relative;
        }

        .sidebar-logo {
            width: 120px;
            margin-bottom: 20px;
        }

        .sidebar-link, .sidebar button {
            display: block;
            width: 100%;
            padding: 10px 12px;
            margin: 5px 0;
            background-color: white;
            color: #6b0a0a;
            border-radius: 25px;
            font-weight: bold;
            font-size: 14px;
            text-align: center;
            text-decoration: none;
            transition: background-color 0.3s ease;
        }

        .sidebar-link:hover, .sidebar button:hover {
            background-color: #f0f0f0;
        }

        .main {
            flex: 1;
            padding: 20px;
        }

        .main-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        .search-bar {
            display: flex;
            align-items: center;
            margin-left: 30px;
        }

        .search-bar input[type="text"] {
            padding: 6px 12px;
            border-radius: 25px 0 0 25px;
            border: none;
            outline: none;
            font-size: 14px;
            color: #333;
        }

        .search-bar button {
            padding: 6px 10px;
            background-color: white;
            border: none;
            border-radius: 0 25px 25px 0;
            cursor: pointer;
            font-size: 14px;
        }

        .cart-section {
            background-color: white;
            color: #6b0a0a;
            padding: 20px;
            border-radius: 10px;
            margin-bottom: 20px;
        }

        .cart-items {
            margin: 10px 0;
        }

        .cart-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px 0;
            border-bottom: 1px solid #eee;
        }

        .cart-total {
            font-weight: bold;
            margin-top: 10px;
            text-align: right;
        }

        .place-order-btn {
            background-color: #4CAF50;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
            font-size: 14px;
            margin-top: 10px;
        }

        .medicines {
            display: flex;
            gap: 20px;
            flex-wrap: wrap;
            justify-content: flex-start;
        }

        .medicine-card {
            background-color: white;
            color: #6b0a0a;
            width: 180px;
            padding: 15px;
            border-radius: 10px;
            text-align: center;
        }

        .medicine-image {
            width: 100%;
            height: 100px;
            object-fit: cover;
            margin-bottom: 10px;
            border-radius: 6px;
        }

        .medicine-name {
            font-weight: bold;
            font-size: 16px;
            margin-bottom: 6px;
        }

        .medicine-description {
            font-size: 12px;
            margin-bottom: 6px;
            color: #333;
        }

        .medicine-price {
            font-size: 14px;
            font-weight: bold;
            margin-bottom: 8px;
        }

        .quantity-input {
            width: 60px;
            padding: 5px;
            margin-bottom: 8px;
            text-align: center;
        }

        .add-to-cart-btn {
            background-color: #4CAF50;
            color: white;
            border: none;
            padding: 6px 12px;
            border-radius: 8px;
            cursor: pointer;
            font-size: 12px;
            width: 100%;
        }
    </style>
</head>
<body>
    <div class="sidebar">
        <img src="./images/heart.jpg" alt="Logo" class="sidebar-logo">
        <a href="dashboard.php" class="sidebar-link">Order Medicines</a>
        <a href="medication-reminders.php" class="sidebar-link">Medication Reminders</a>
        <a href="prescription-history.php" class="sidebar-link">Prescription History</a>
        <a href="notifications.php" class="sidebar-link">Notifications</a>
        <div class="bottom-buttons">
            <button>Settings</button>
            <a href="logout.php" class="sidebar-link">Log-out</a>
        </div>
    </div>

    <div class="main">
        <div class="main-header">
            <h1>MedTrack</h1>
            <form class="search-bar" method="GET">
                <input type="text" name="search" placeholder="Search medicines..." value="<?php echo htmlspecialchars($search); ?>">
                <button type="submit">🔍</button>
            </form>
        </div>

        <?php if (!empty($_SESSION['cart'])): ?>
            <div class="cart-section">
                <h2>Shopping Cart</h2>
                <div class="cart-items">
                    <?php foreach ($_SESSION['cart'] as $medicine_id => $item): ?>
                        <div class="cart-item">
                            <div>
                                <strong><?php echo htmlspecialchars($item['name']); ?></strong>
                                <span>x<?php echo $item['quantity']; ?></span>
                            </div>
                            <div>
                                ₱<?php echo number_format($item['price'] * $item['quantity'], 2); ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
                <div class="cart-total">
                    Total: ₱<?php echo number_format($cart_total, 2); ?>
                </div>
                <form method="POST" style="text-align: right;">
                    <input type="hidden" name="place_order" value="1">
                    <button type="submit" class="place-order-btn">Place Order</button>
                </form>
            </div>
        <?php endif; ?>

        <div class="medicines">
            <?php foreach ($medicines as $medicine): ?>
                <div class="medicine-card">
                    <img src="./images/medicines/<?php echo htmlspecialchars($medicine['image_url']); ?>" 
                         alt="<?php echo htmlspecialchars($medicine['name']); ?>" 
                         class="medicine-image">
                    <div class="medicine-name"><?php echo htmlspecialchars($medicine['name']); ?></div>
                    <div class="medicine-description"><?php echo htmlspecialchars($medicine['description']); ?></div>
                    <div class="medicine-price">₱<?php echo number_format($medicine['price'], 2); ?></div>
                    <form method="POST">
                        <input type="hidden" name="medicine_id" value="<?php echo $medicine['medicine_id']; ?>">
                        <input type="number" name="quantity" value="1" min="1" class="quantity-input">
                        <button type="submit" name="add_to_cart" class="add-to-cart-btn">Add to Cart</button>
                    </form>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</body>
</html>