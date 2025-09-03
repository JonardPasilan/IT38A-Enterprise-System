<?php
session_start();

// Check if user is logged in and is admin
if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}

require_once '../config/database.php';

// Initialize database
$database = new Database();
$db = $database->getConnection();

// Handle status updates
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'update_status') {
    $order_id = $_POST['order_id'];
    $new_status = $_POST['status'];
    
    // Update order status
    $stmt = $db->prepare("UPDATE orders SET status = ? WHERE order_id = ?");
    $stmt->execute([$new_status, $order_id]);
    
    // Get user_id for notification
    $stmt = $db->prepare("SELECT user_id FROM orders WHERE order_id = ?");
    $stmt->execute([$order_id]);
    $user_id = $stmt->fetchColumn();
    
    // Add notification
    $stmt = $db->prepare("INSERT INTO notifications (user_id, title, message, type) VALUES (?, ?, ?, 'order')");
    $stmt->execute([
        $user_id,
        'Order Status Updated',
        "Your order #$order_id status has been updated to: " . ucfirst($new_status)
    ]);
}

// Get all orders with user and item details
$stmt = $db->query("
    SELECT o.*, 
           u.first_name, u.last_name, u.email,
           GROUP_CONCAT(CONCAT(m.name, ' (', oi.quantity, ')') SEPARATOR ', ') as items
    FROM orders o
    JOIN users u ON o.user_id = u.id
    JOIN order_items oi ON o.order_id = oi.order_id
    JOIN medicines m ON oi.medicine_id = m.medicine_id
    GROUP BY o.order_id
    ORDER BY o.created_at DESC
");
$orders = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Orders - MedTrack Admin</title>
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
            background-image: url('../images/background.jpg');
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

        .sidebar-link {
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

        .sidebar-link:hover {
            background-color: #f0f0f0;
        }

        .main {
            flex: 1;
            padding: 20px;
        }

        .orders-table {
            width: 100%;
            background-color: white;
            color: #6b0a0a;
            border-radius: 10px;
            overflow: hidden;
        }

        .orders-table th,
        .orders-table td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #eee;
        }

        .orders-table th {
            background-color: #f5f5f5;
            font-weight: bold;
        }

        .status-badge {
            padding: 5px 10px;
            border-radius: 15px;
            font-size: 12px;
            font-weight: bold;
        }

        .status-pending {
            background-color: #FFC107;
            color: black;
        }

        .status-processing {
            background-color: #2196F3;
            color: white;
        }

        .status-preparing {
            background-color: #9C27B0;
            color: white;
        }

        .status-ready {
            background-color: #4CAF50;
            color: white;
        }

        .status-completed {
            background-color: #4CAF50;
            color: white;
        }

        .status-cancelled {
            background-color: #f44336;
            color: white;
        }

        .status-select {
            padding: 5px;
            border-radius: 4px;
            border: 1px solid #ddd;
        }

        .update-btn {
            background-color: #4CAF50;
            color: white;
            border: none;
            padding: 5px 10px;
            border-radius: 4px;
            cursor: pointer;
            font-size: 12px;
        }

        .order-details {
            margin-top: 5px;
            font-size: 12px;
            color: #666;
        }
    </style>
</head>
<body>
    <div class="sidebar">
        <img src="../images/heart.jpg" alt="Logo" class="sidebar-logo">
        <a href="admin_dashboard.php" class="sidebar-link">Dashboard</a>
        <a href="manage_medicines.php" class="sidebar-link">Manage Medicines</a>
        <a href="manage_orders.php" class="sidebar-link">Manage Orders</a>
        <a href="../logout.php" class="sidebar-link">Logout</a>
    </div>

    <div class="main">
        <h1>Manage Orders</h1>
        
        <table class="orders-table">
            <thead>
                <tr>
                    <th>Order ID</th>
                    <th>Customer</th>
                    <th>Items</th>
                    <th>Total</th>
                    <th>Status</th>
                    <th>Date</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($orders as $order): ?>
                    <tr>
                        <td>#<?php echo $order['order_id']; ?></td>
                        <td>
                            <?php echo htmlspecialchars($order['first_name'] . ' ' . $order['last_name']); ?>
                            <div class="order-details"><?php echo htmlspecialchars($order['email']); ?></div>
                        </td>
                        <td><?php echo htmlspecialchars($order['items']); ?></td>
                        <td>$<?php echo number_format($order['total_amount'], 2); ?></td>
                        <td>
                            <span class="status-badge status-<?php echo strtolower($order['status']); ?>">
                                <?php echo ucfirst($order['status']); ?>
                            </span>
                        </td>
                        <td><?php echo date('M j, Y h:i A', strtotime($order['created_at'])); ?></td>
                        <td>
                            <form method="POST" style="display: inline;">
                                <input type="hidden" name="action" value="update_status">
                                <input type="hidden" name="order_id" value="<?php echo $order['order_id']; ?>">
                                <select name="status" class="status-select">
                                    <option value="pending" <?php echo $order['status'] === 'pending' ? 'selected' : ''; ?>>Pending</option>
                                    <option value="processing" <?php echo $order['status'] === 'processing' ? 'selected' : ''; ?>>Processing</option>
                                    <option value="preparing" <?php echo $order['status'] === 'preparing' ? 'selected' : ''; ?>>Preparing</option>
                                    <option value="ready" <?php echo $order['status'] === 'ready' ? 'selected' : ''; ?>>Ready</option>
                                    <option value="completed" <?php echo $order['status'] === 'completed' ? 'selected' : ''; ?>>Completed</option>
                                    <option value="cancelled" <?php echo $order['status'] === 'cancelled' ? 'selected' : ''; ?>>Cancelled</option>
                                </select>
                                <button type="submit" class="update-btn">Update</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</body>
</html> 