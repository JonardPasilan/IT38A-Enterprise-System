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

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'add':
                $image_name = '';
                if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
                    $upload_dir = '../images/medicines/';
                    if (!file_exists($upload_dir)) {
                        mkdir($upload_dir, 0777, true);
                    }
                    $image_name = time() . '_' . basename($_FILES['image']['name']);
                    move_uploaded_file($_FILES['image']['tmp_name'], $upload_dir . $image_name);
                }
                
                $stmt = $db->prepare("INSERT INTO medicines (name, description, price, image_url, stock_quantity, low_stock_threshold) VALUES (?, ?, ?, ?, ?, ?)");
                $stmt->execute([
                    $_POST['name'],
                    $_POST['description'],
                    $_POST['price'],
                    $image_name,
                    $_POST['stock_quantity'],
                    $_POST['low_stock_threshold']
                ]);
                break;

            case 'edit':
                $image_name = $_POST['current_image'];
                if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
                    $upload_dir = '../images/medicines/';
                    if (!file_exists($upload_dir)) {
                        mkdir($upload_dir, 0777, true);
                    }
                    // Delete old image if exists
                    if ($_POST['current_image'] && file_exists($upload_dir . $_POST['current_image'])) {
                        unlink($upload_dir . $_POST['current_image']);
                    }
                    $image_name = time() . '_' . basename($_FILES['image']['name']);
                    move_uploaded_file($_FILES['image']['tmp_name'], $upload_dir . $image_name);
                }
                
                $stmt = $db->prepare("UPDATE medicines SET name = ?, description = ?, price = ?, image_url = ?, stock_quantity = ?, low_stock_threshold = ? WHERE medicine_id = ?");
                $stmt->execute([
                    $_POST['name'],
                    $_POST['description'],
                    $_POST['price'],
                    $image_name,
                    $_POST['stock_quantity'],
                    $_POST['low_stock_threshold'],
                    $_POST['medicine_id']
                ]);
                break;

            case 'delete':
                // Delete image file if exists
                $stmt = $db->prepare("SELECT image_url FROM medicines WHERE medicine_id = ?");
                $stmt->execute([$_POST['medicine_id']]);
                $image_url = $stmt->fetchColumn();
                if ($image_url && file_exists('../images/medicines/' . $image_url)) {
                    unlink('../images/medicines/' . $image_url);
                }
                
                $stmt = $db->prepare("DELETE FROM medicines WHERE medicine_id = ?");
                $stmt->execute([$_POST['medicine_id']]);
                break;
        }
    }
}

// Get all medicines
$stmt = $db->query("SELECT * FROM medicines ORDER BY name");
$medicines = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Medicines - MedTrack Admin</title>
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

        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        .add-btn {
            background-color: #4CAF50;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
            font-size: 14px;
        }

        .medicines-table {
            width: 100%;
            background-color: white;
            color: #6b0a0a;
            border-radius: 10px;
            overflow: hidden;
        }

        .medicines-table th,
        .medicines-table td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #eee;
        }

        .medicines-table th {
            background-color: #f5f5f5;
            font-weight: bold;
        }

        .action-btn {
            padding: 6px 12px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 12px;
            margin-right: 5px;
        }

        .edit-btn {
            background-color: #2196F3;
            color: white;
        }

        .delete-btn {
            background-color: #f44336;
            color: white;
        }

        .modal {
    display: none;
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0,0,0,0.5);
    overflow-y: auto;
    padding: 40px 0; /* space at top/bottom */
}

.modal-content {
    background-color: white;
    color: #6b0a0a;
    margin: 0 auto;
    padding: 20px;
    width: 90%;
    max-width: 600px;
    border-radius: 10px;
    max-height: calc(100vh - 80px); /* prevent it from exceeding screen */
    overflow-y: auto; /* scroll if content is too long */
}
        .form-group {
            margin-bottom: 15px;
        }

        .form-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }

        .form-group input {
            width: 100%;
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }

        .modal-buttons {
            text-align: right;
            margin-top: 20px;
        }

        .modal-buttons button {
            padding: 8px 16px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            margin-left: 10px;
        }

        .save-btn {
            background-color: #4CAF50;
            color: white;
        }

        .cancel-btn {
            background-color: #f44336;
            color: white;
        }

        .medicine-image {
            width: 50px;
            height: 50px;
            object-fit: cover;
            border-radius: 4px;
        }
        
        .form-group input[type="file"] {
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 4px;
            width: 100%;
        }
        
        .preview-image {
            max-width: 200px;
            max-height: 200px;
            margin-top: 10px;
            display: none;
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
        <div class="header">
            <h1>Manage Medicines</h1>
            <button class="add-btn" onclick="openAddModal()">Add New Medicine</button>
        </div>

        <table class="medicines-table">
            <thead>
                <tr>
                    <th>Image</th>
                    <th>Name</th>
                    <th>Description</th>
                    <th>Price</th>
                    <th>Stock</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($medicines as $medicine): ?>
                    <tr>
                        <td>
                            <?php if ($medicine['image_url']): ?>
                                <img src="../images/medicines/<?php echo htmlspecialchars($medicine['image_url']); ?>" 
                                     alt="<?php echo htmlspecialchars($medicine['name']); ?>" 
                                     class="medicine-image">
                            <?php endif; ?>
                        </td>
                        <td><?php echo htmlspecialchars($medicine['name']); ?></td>
                        <td><?php echo htmlspecialchars($medicine['description']); ?></td>
                        <td>₱<?php echo number_format($medicine['price'], 2); ?></td>
                        <td><?php echo $medicine['stock_quantity']; ?></td>
                        <td>
                            <button class="action-btn edit-btn" onclick="openEditModal(<?php echo htmlspecialchars(json_encode($medicine)); ?>)">Edit</button>
                            <form method="POST" style="display: inline;">
                                <input type="hidden" name="action" value="delete">
                                <input type="hidden" name="medicine_id" value="<?php echo $medicine['medicine_id']; ?>">
                                <button type="submit" class="action-btn delete-btn" onclick="return confirm('Are you sure you want to delete this medicine?')">Delete</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <!-- Edit Medicine Modal -->
    <div id="editModal" class="modal">
        <div class="modal-content">
            <h2>Edit Medicine</h2>
            <form method="POST" enctype="multipart/form-data">
                <input type="hidden" name="action" value="edit">
                <input type="hidden" name="medicine_id" id="edit_medicine_id">
                <input type="hidden" name="current_image" id="edit_current_image">
                <div class="form-group">
                    <label for="edit_name">Name:</label>
                    <input type="text" id="edit_name" name="name" required>
                </div>
                <div class="form-group">
                    <label for="edit_description">Description:</label>
                    <input type="text" id="edit_description" name="description" required>
                </div>
                <div class="form-group">
                    <label for="edit_price">Price:</label>
                    <input type="number" id="edit_price" name="price" step="0.01" required>
                </div>
                <div class="form-group">
                    <label for="edit_image">Medicine Image:</label>
                    <input type="file" id="edit_image" name="image" accept="image/*" onchange="previewImage(this, 'editPreview')">
                    <img id="editPreview" class="preview-image">
                    <div id="currentImageContainer"></div>
                </div>
                <div class="form-group">
                    <label for="edit_stock_quantity">Stock Quantity:</label>
                    <input type="number" id="edit_stock_quantity" name="stock_quantity" required>
                </div>
                <div class="form-group">
                    <label for="edit_low_stock_threshold">Low Stock Threshold:</label>
                    <input type="number" id="edit_low_stock_threshold" name="low_stock_threshold" required>
                </div>
                <div class="modal-buttons">
                    <button type="button" class="cancel-btn" onclick="closeModal('editModal')">Cancel</button>
                    <button type="submit" class="save-btn">Save Changes</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Add Medicine Modal -->
    <div id="addModal" class="modal">
        <div class="modal-content">
            <h2>Add New Medicine</h2>
            <form method="POST" enctype="multipart/form-data">
                <input type="hidden" name="action" value="add">
                <div class="form-group">
                    <label for="name">Name:</label>
                    <input type="text" id="name" name="name" required>
                </div>
                <div class="form-group">
                    <label for="description">Description:</label>
                    <input type="text" id="description" name="description" required>
                </div>
                <div class="form-group">
                    <label for="price">Price:</label>
                    <input type="number" id="price" name="price" step="0.01" required>
                </div>
                <div class="form-group">
                    <label for="image">Medicine Image:</label>
                    <input type="file" id="image" name="image" accept="image/*" onchange="previewImage(this, 'addPreview')" required>
                    <img id="addPreview" class="preview-image">
                </div>
                <div class="form-group">
                    <label for="stock_quantity">Stock Quantity:</label>
                    <input type="number" id="stock_quantity" name="stock_quantity" required>
                </div>
                <div class="form-group">
                    <label for="low_stock_threshold">Low Stock Threshold:</label>
                    <input type="number" id="low_stock_threshold" name="low_stock_threshold" required>
                </div>
                <div class="modal-buttons">
                    <button type="button" class="cancel-btn" onclick="closeModal('addModal')">Cancel</button>
                    <button type="submit" class="save-btn">Save</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function previewImage(input, previewId) {
            const preview = document.getElementById(previewId);
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    preview.src = e.target.result;
                    preview.style.display = 'block';
                }
                reader.readAsDataURL(input.files[0]);
            }
        }

        function openAddModal() {
            document.getElementById('addModal').style.display = 'block';
            document.getElementById('addPreview').style.display = 'none';
        }

        function openEditModal(medicine) {
            document.getElementById('edit_medicine_id').value = medicine.medicine_id;
            document.getElementById('edit_name').value = medicine.name;
            document.getElementById('edit_description').value = medicine.description;
            document.getElementById('edit_price').value = medicine.price;
            document.getElementById('edit_current_image').value = medicine.image_url;
            document.getElementById('edit_stock_quantity').value = medicine.stock_quantity;
            document.getElementById('edit_low_stock_threshold').value = medicine.low_stock_threshold;
            
            // Show current image if exists
            const currentImageContainer = document.getElementById('currentImageContainer');
            if (medicine.image_url) {
                currentImageContainer.innerHTML = `
                    <p>Current Image:</p>
                    <img src="../images/medicines/${medicine.image_url}" 
                         alt="Current Image" 
                         style="max-width: 200px; margin-top: 10px;">
                `;
            } else {
                currentImageContainer.innerHTML = '';
            }
            
            document.getElementById('editPreview').style.display = 'none';
            document.getElementById('editModal').style.display = 'block';
        }

        function closeModal(modalId) {
            document.getElementById(modalId).style.display = 'none';
        }

        // Close modal when clicking outside
        window.onclick = function(event) {
            if (event.target.className === 'modal') {
                event.target.style.display = 'none';
            }
        }
    </script>
</body>
</html> 