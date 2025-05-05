<!-- admin-dashboard.html -->
<!DOCTYPE html>
<html>
<head>
    <style>
        :root {
            --primary:rgb(82, 9, 9);
            --secondary: rgb(82, 9, 9);
            --accent: #E76F51;
            --light: #F8F9FA;
        }
        body {
            font-family: 'Segoe UI', sans-serif;
            margin: 0;
            padding: 0;
            background: var(--light);
        }
        .dashboard {
            display: grid;
            grid-template-columns: 250px 1fr;
            min-height: 100vh;
        }
        .sidebar {
            background: var(--secondary);
            color: white;
            padding: 20px;
        }
        .nav-link {
            display: block;
            color: white;
            padding: 12px;
            margin: 5px 0;
            text-decoration: none;
            border-radius: 5px;
        }
        .nav-link:hover {
            background: var(--primary);
        }
        .main-content {
            padding: 20px;
        }
        .module-card {
            background: white;
            padding: 20px;
            border-radius: 10px;
            margin-bottom: 20px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
    </style>
</head>
<body>
    <div class="dashboard">
        <div class="sidebar">
            <h2>MedTrack Admin</h2>
            <a href="inventory.html" class="nav-link">📦 Inventory</a>
            <a href="orders.html" class="nav-link">📋 Orders</a>
            <a href="billing.html" class="nav-link">💵 Billing</a>
            <a href="reports.html" class="nav-link">📊 Reports</a>
            <a href="reminders.html" class="nav-link">⏰ Reminders</a>
            
        </div>
        <div class="main-content">
            <h1>Admin Dashboard</h1>
            <div class="module-card">
                <h2>Quick Actions</h2>
                <p>Select a module from the sidebar to begin</p>
            </div>
        </div>
    </div>
</body>
</html>