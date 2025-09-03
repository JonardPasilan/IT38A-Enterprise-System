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

// Handle reminder actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $reminder_id = $_POST['reminder_id'] ?? null;
    $action = $_POST['action'] ?? '';
    
    if ($reminder_id && $action) {
        switch ($action) {
            case 'mark_taken':
                $stmt = $db->prepare("UPDATE medication_reminders SET status = 'completed' WHERE reminder_id = ? AND user_id = ?");
                $stmt->execute([$reminder_id, $_SESSION['user_id']]);
                break;
            case 'snooze':
                $stmt = $db->prepare("UPDATE medication_reminders SET start_date = DATE_ADD(start_date, INTERVAL 30 MINUTE) WHERE reminder_id = ? AND user_id = ?");
                $stmt->execute([$reminder_id, $_SESSION['user_id']]);
                break;
            case 'ignore':
                $stmt = $db->prepare("UPDATE medication_reminders SET status = 'cancelled' WHERE reminder_id = ? AND user_id = ?");
                $stmt->execute([$reminder_id, $_SESSION['user_id']]);
                break;
        }
    }
}

// Get active reminders
$stmt = $db->prepare("
    SELECT mr.*, m.name as medicine_name, m.description 
    FROM medication_reminders mr 
    JOIN medicines m ON mr.medicine_id = m.medicine_id 
    WHERE mr.user_id = ? AND mr.status = 'active' 
    ORDER BY mr.start_date ASC
");
$stmt->execute([$_SESSION['user_id']]);
$reminders = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Medication Reminders - MedTrack</title>
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

        .reminder-card {
            background-color: white;
            color: #6b0a0a;
            padding: 20px;
            border-radius: 10px;
            margin-bottom: 15px;
        }

        .reminder-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 10px;
        }

        .reminder-title {
            font-size: 18px;
            font-weight: bold;
        }

        .reminder-time {
            font-size: 14px;
            color: #666;
        }

        .reminder-actions {
            display: flex;
            gap: 10px;
            margin-top: 10px;
        }

        .reminder-actions button {
            padding: 8px 15px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 14px;
            transition: background-color 0.3s;
        }

        .mark-taken {
            background-color: #4CAF50;
            color: white;
        }

        .snooze {
            background-color: #FFC107;
            color: black;
        }

        .ignore {
            background-color: #f44336;
            color: white;
        }

        .no-reminders {
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
        <h1>Medication Reminders</h1>
        
        <?php if (empty($reminders)): ?>
            <div class="no-reminders">
                <h2>No Active Reminders</h2>
                <p>You don't have any active medication reminders at the moment.</p>
            </div>
        <?php else: ?>
            <?php foreach ($reminders as $reminder): ?>
                <div class="reminder-card">
                    <div class="reminder-header">
                        <div class="reminder-title"><?php echo htmlspecialchars($reminder['medicine_name']); ?></div>
                        <div class="reminder-time">
                            <?php echo date('h:i A', strtotime($reminder['start_date'])); ?>
                        </div>
                    </div>
                    <div class="reminder-details">
                        <p><strong>Dosage:</strong> <?php echo htmlspecialchars($reminder['dosage']); ?></p>
                        <p><strong>Frequency:</strong> <?php echo htmlspecialchars($reminder['frequency']); ?></p>
                    </div>
                    <div class="reminder-actions">
                        <form method="POST" style="display: inline;">
                            <input type="hidden" name="reminder_id" value="<?php echo $reminder['reminder_id']; ?>">
                            <input type="hidden" name="action" value="mark_taken">
                            <button type="submit" class="mark-taken">Mark as Taken</button>
                        </form>
                        <form method="POST" style="display: inline;">
                            <input type="hidden" name="reminder_id" value="<?php echo $reminder['reminder_id']; ?>">
                            <input type="hidden" name="action" value="snooze">
                            <button type="submit" class="snooze">Snooze (30 min)</button>
                        </form>
                        <form method="POST" style="display: inline;">
                            <input type="hidden" name="reminder_id" value="<?php echo $reminder['reminder_id']; ?>">
                            <input type="hidden" name="action" value="ignore">
                            <button type="submit" class="ignore">Ignore</button>
                        </form>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</body>
</html> 