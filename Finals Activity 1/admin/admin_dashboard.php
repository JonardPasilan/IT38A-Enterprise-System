<?php
session_start();
require_once '../config/database.php';

if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'admin') {
    header("Location: ../login.php");
    exit;
}

// Initialize variables with default values
$totalMedicines = 0;
$pendingOrders = 0;
$lowStock = 0;
$recentOrders = [];
$lowStockMedicines = [];

// Database connection
$database = new Database();
$conn = $database->getConnection();

// Get dashboard statistics
try {
    // Total medicines
    $stmt = $conn->query("SELECT COUNT(*) as total FROM medicines");
    if ($stmt) {
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        $totalMedicines = $result ? $result['total'] : 0;
    }

    // Pending orders
    $stmt = $conn->query("SELECT COUNT(*) as total FROM orders WHERE status = 'pending'");
    if ($stmt) {
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        $pendingOrders = $result ? $result['total'] : 0;
    }

    // Low stock medicines
    $stmt = $conn->query("SELECT COUNT(*) as total FROM medicines WHERE stock_quantity <= 10");
    if ($stmt) {
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        $lowStock = $result ? $result['total'] : 0;
    }

    // Recent orders
    $stmt = $conn->query("SELECT o.*, u.first_name, u.last_name 
                         FROM orders o 
                         JOIN users u ON o.user_id = u.id 
                         ORDER BY o.created_at DESC 
                         LIMIT 5");
    if ($stmt) {
        $recentOrders = $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
    }

    // Low stock medicines list
    $stmt = $conn->query("SELECT * FROM medicines WHERE stock_quantity <= 10");
    if ($stmt) {
        $lowStockMedicines = $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
    }

} catch(PDOException $e) {
    // Log the error but don't display it to users
    error_log("Database Error: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - MedTrack</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Special+Elite&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
</head>
<body class="bg-gray-100">
    <!-- Navigation -->
    <nav class="bg-[#6a0a0a] text-white p-4">
        <div class="container mx-auto flex justify-between items-center">
            <h1 class="text-2xl font-bold">MedTrack Admin</h1>
            <div class="flex items-center space-x-4">
                <span>Welcome, <?php echo htmlspecialchars($_SESSION['user_name']); ?></span>
                <a href="../logout.php" class="bg-red-700 px-4 py-2 rounded hover:bg-red-800">Logout</a>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="container mx-auto p-6">
        <!-- Stats Overview -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
            <div class="bg-white p-6 rounded-lg shadow-md">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-blue-100 text-blue-500">
                        <i class="fas fa-pills text-2xl"></i>
                    </div>
                    <div class="ml-4">
                        <h3 class="text-gray-500 text-sm">Total Medicines</h3>
                        <p class="text-2xl font-semibold"><?php echo $totalMedicines; ?></p>
                    </div>
                </div>
            </div>
            <div class="bg-white p-6 rounded-lg shadow-md">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-yellow-100 text-yellow-500">
                        <i class="fas fa-clock text-2xl"></i>
                    </div>
                    <div class="ml-4">
                        <h3 class="text-gray-500 text-sm">Pending Orders</h3>
                        <p class="text-2xl font-semibold"><?php echo $pendingOrders; ?></p>
                    </div>
                </div>
            </div>
            <div class="bg-white p-6 rounded-lg shadow-md">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-red-100 text-red-500">
                        <i class="fas fa-exclamation-triangle text-2xl"></i>
                    </div>
                    <div class="ml-4">
                        <h3 class="text-gray-500 text-sm">Low Stock Alerts</h3>
                        <p class="text-2xl font-semibold"><?php echo $lowStock; ?></p>
                    </div>
                </div>
            </div>
            <div class="bg-white p-6 rounded-lg shadow-md">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-green-100 text-green-500">
                        <i class="fas fa-chart-line text-2xl"></i>
                    </div>
                    <div class="ml-4">
                        <h3 class="text-gray-500 text-sm">Total Sales</h3>
                        <p class="text-2xl font-semibold">₱0</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Sections -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Recent Orders -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <h2 class="text-xl font-semibold mb-4">Recent Orders</h2>
                <div class="overflow-x-auto">
                    <table class="min-w-full">
                        <thead>
                            <tr class="bg-gray-50">
                                <th class="px-4 py-2 text-left">Order ID</th>
                                <th class="px-4 py-2 text-left">Patient</th>
                                <th class="px-4 py-2 text-left">Status</th>
                                <th class="px-4 py-2 text-left">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($recentOrders as $order): ?>
                            <tr class="border-t">
                                <td class="px-4 py-2">#<?php echo $order['order_id']; ?></td>
                                <td class="px-4 py-2"><?php echo htmlspecialchars($order['first_name'] . ' ' . $order['last_name']); ?></td>
                                <td class="px-4 py-2">
                                    <span class="px-2 py-1 rounded-full text-xs <?php echo $order['status'] === 'pending' ? 'bg-yellow-100 text-yellow-800' : 'bg-green-100 text-green-800'; ?>">
                                        <?php echo ucfirst($order['status']); ?>
                                    </span>
                                </td>
                                <td class="px-4 py-2">
                                    <a href="manage_orders.php" class="text-blue-500 hover:text-blue-700">View</a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Low Stock Alerts -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <h2 class="text-xl font-semibold mb-4">Low Stock Alerts</h2>
                <div class="overflow-x-auto">
                    <table class="min-w-full">
                        <thead>
                            <tr class="bg-gray-50">
                                <th class="px-4 py-2 text-left">Medicine</th>
                                <th class="px-4 py-2 text-left">Current Stock</th>
                                <th class="px-4 py-2 text-left">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($lowStockMedicines as $medicine): ?>
                            <tr class="border-t">
                                <td class="px-4 py-2"><?php echo htmlspecialchars($medicine['name']); ?></td>
                                <td class="px-4 py-2"><?php echo $medicine['stock_quantity']; ?></td>
                                <td class="px-4 py-2">
                                    <a href="manage_medicines.php" class="text-blue-500 hover:text-blue-700">Update Stock</a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="mt-8 grid grid-cols-1 md:grid-cols-3 gap-6">
            <a href="manage_medicines.php" class="bg-white p-6 rounded-lg shadow-md hover:shadow-lg transition-shadow">
                <div class="flex items-center">
                    <i class="fas fa-pills text-3xl text-blue-500"></i>
                    <div class="ml-4">
                        <h3 class="text-lg font-semibold">Manage Medicines</h3>
                        <p class="text-gray-500">Add, update, or remove medicines</p>
                    </div>
                </div>
            </a>
            <a href="manage_orders.php" class="bg-white p-6 rounded-lg shadow-md hover:shadow-lg transition-shadow">
                <div class="flex items-center">
                    <i class="fas fa-shopping-cart text-3xl text-green-500"></i>
                    <div class="ml-4">
                        <h3 class="text-lg font-semibold">Manage Orders</h3>
                        <p class="text-gray-500">Process and track orders</p>
                    </div>
                </div>
            </a>
            <a href="manage_reminders.php" class="bg-white p-6 rounded-lg shadow-md hover:shadow-lg transition-shadow">
                <div class="flex items-center">
                    <i class="fas fa-bell text-3xl text-yellow-500"></i>
                    <div class="ml-4">
                        <h3 class="text-lg font-semibold">Medication Reminders</h3>
                        <p class="text-gray-500">Schedule and manage reminders</p>
                    </div>
                </div>
            </a>
        </div>
    </div>
</body>
</html>
