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

// Mark notification as read if requested
if (isset($_POST['mark_read']) && isset($_POST['notification_id'])) {
    $stmt = $db->prepare("UPDATE notifications SET is_read = 1 WHERE notification_id = ? AND user_id = ?");
    $stmt->execute([$_POST['notification_id'], $_SESSION['user_id']]);
}

// Get all notifications
$stmt = $db->prepare("
    SELECT * FROM notifications 
    WHERE user_id = ? 
    ORDER BY created_at DESC
");
$stmt->execute([$_SESSION['user_id']]);
$notifications = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Notifications - MedTrack</title>
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

        .notification-card {
            background-color: white;
            color: #6b0a0a;
            padding: 20px;
            border-radius: 10px;
            margin-bottom: 15px;
            position: relative;
        }

        .notification-card.unread {
            border-left: 4px solid #4CAF50;
        }

        .notification-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 10px;
        }

        .notification-title {
            font-size: 18px;
            font-weight: bold;
        }

        .notification-time {
            font-size: 14px;
            color: #666;
        }

        .notification-type {
            padding: 5px 10px;
            border-radius: 15px;
            font-size: 12px;
            font-weight: bold;
            margin-left: 10px;
        }

        .type-order {
            background-color: #2196F3;
            color: white;
        }

        .type-reminder {
            background-color: #FFC107;
            color: black;
        }

        .type-system {
            background-color: #9C27B0;
            color: white;
        }

        .notification-message {
            margin-top: 10px;
            color: #333;
        }

        .mark-read-btn {
            position: absolute;
            top: 10px;
            right: 10px;
            padding: 5px 10px;
            background-color: #4CAF50;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 12px;
        }

        .no-notifications {
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
        <button>Orders</button>
        <div class="bottom-buttons">
            <button>Settings</button>
            <a href="logout.php" class="sidebar-link">Log-out</a>
        </div>
    </div>

    <div class="main">
        <h1>Notifications</h1>
        
        <?php if (empty($notifications)): ?>
            <div class="no-notifications">
                <h2>No Notifications</h2>
                <p>You don't have any notifications at the moment.</p>
            </div>
        <?php else: ?>
            <?php foreach ($notifications as $notification): ?>
                <div class="notification-card <?php echo $notification['is_read'] ? '' : 'unread'; ?>">
                    <div class="notification-header">
                        <div class="notification-title">
                            <?php echo htmlspecialchars($notification['title']); ?>
                            <span class="notification-type type-<?php echo $notification['type']; ?>">
                                <?php echo ucfirst($notification['type']); ?>
                            </span>
                        </div>
                        <div class="notification-time">
                            <?php echo date('F j, Y h:i A', strtotime($notification['created_at'])); ?>
                        </div>
                    </div>
                    <div class="notification-message">
                        <?php echo htmlspecialchars($notification['message']); ?>
                    </div>
                    <?php if (!$notification['is_read']): ?>
                        <form method="POST" style="display: inline;">
                            <input type="hidden" name="notification_id" value="<?php echo $notification['notification_id']; ?>">
                            <input type="hidden" name="mark_read" value="1">
                            <button type="submit" class="mark-read-btn">Mark as Read</button>
                        </form>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</body>
</html> 