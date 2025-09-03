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

// Get prescription history
$stmt = $db->prepare("
    SELECT o.*, GROUP_CONCAT(m.name SEPARATOR ', ') as medicines
    FROM orders o
    JOIN order_items oi ON o.order_id = oi.order_id
    JOIN medicines m ON oi.medicine_id = m.medicine_id
    WHERE o.user_id = ?
    GROUP BY o.order_id
    ORDER BY o.created_at DESC
");
$stmt->execute([$_SESSION['user_id']]);
$orders = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Prescription History - MedTrack</title>
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

        .history-card {
            background-color: white;
            color: #6b0a0a;
            padding: 20px;
            border-radius: 10px;
            margin-bottom: 15px;
        }

        .history-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 10px;
            padding-bottom: 10px;
            border-bottom: 1px solid #eee;
        }

        .order-date {
            font-size: 14px;
            color: #666;
        }

        .order-status {
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

        .order-details {
            margin-top: 10px;
        }

        .order-medicines {
            margin: 10px 0;
            color: #333;
        }

        .order-total {
            font-weight: bold;
            margin-top: 10px;
        }

        .no-history {
            text-align: center;
            padding: 20px;
            background-color: white;
            color: #6b0a0a;
            border-radius: 10px;
        }
    </style>
</head>
<body>
    <div class="sidebar">
        <img src="./images/heart.jpg" alt="Logo" class="sidebar-logo">
        <a href="dashboard.php" class="sidebar-link">Order Medicines</a>
        <a href="medication-reminders.php" class="sidebar-link">Medication Reminders</a>
        <a href="prescription-history.php" class="sidebar-link">Prescription History</a>
        <div class="bottom-buttons">
            <button>Settings</button>
            <a href="logout.php" class="sidebar-link">Log-out</a>
        </div>
    </div>

    <div class="main">
        <h1>Prescription History</h1>
        
        <?php if (empty($orders)): ?>
            <div class="no-history">
                <h2>No Order History</h2>
                <p>You haven't placed any orders yet.</p>
            </div>
        <?php else: ?>
            <?php foreach ($orders as $order): ?>
                <div class="history-card">
                    <div class="history-header">
                        <div class="order-date">
                            <?php echo date('F j, Y h:i A', strtotime($order['created_at'])); ?>
                        </div>
                        <div class="order-status status-<?php echo strtolower($order['status']); ?>">
                            <?php echo ucfirst($order['status']); ?>
                        </div>
                    </div>
                    <div class="order-details">
                        <div class="order-medicines">
                            <strong>Medicines:</strong> <?php echo htmlspecialchars($order['medicines']); ?>
                        </div>
                        <div class="order-total">
                            Total Amount: $<?php echo number_format($order['total_amount'], 2); ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</body>
</html> 