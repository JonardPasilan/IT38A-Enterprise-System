<!DOCTYPE html>
<html>
<head>
    <style>
        .medicine-card {
            display: flex;
            justify-content: space-between;
            padding: 15px;
            border-bottom: 1px solid #eee;
        }
        .low-stock {
            color: var(--accent);
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="dashboard">
        <div class="sidebar">
        </div>
        <div class="main-content">
            <h1>Medicine Inventory</h1>
            <div class="module-card">
                <button>➕ Add New Medicine</button>
                <div class="medicine-card">
                    <span>Paracetamol 500mg</span>
                    <span class="low-stock">Stock: 5 (Low)</span>
                    <span>
                        <button>Edit</button>
                        <button>Delete</button>
                    </span>
                </div>
            </div>
        </div>
    </div>
</body>
</html>

<?php