<?php
// Database connection parameters
$host = 'localhost';
$username = 'root';
$password = '';

try {
    // Create connection without database
    $pdo = new PDO("mysql:host=$host", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Create database if not exists
    $sql = "CREATE DATABASE IF NOT EXISTS medtrack";
    $pdo->exec($sql);
    echo "Database created successfully<br>";
    
    // Select the database
    $pdo->exec("USE medtrack");
    
    // Create users table
    $sql = "CREATE TABLE IF NOT EXISTS users (
        id INT(11) NOT NULL AUTO_INCREMENT,
        first_name VARCHAR(50) DEFAULT NULL,
        last_name VARCHAR(50) DEFAULT NULL,
        phone VARCHAR(15) DEFAULT NULL,
        email VARCHAR(100) DEFAULT NULL,
        password VARCHAR(255) DEFAULT NULL,
        PRIMARY KEY (id),
        UNIQUE KEY email (email)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci";
    $pdo->exec($sql);
    echo "Users table created successfully<br>";
    
    // Create medicines table
    $sql = "CREATE TABLE IF NOT EXISTS medicines (
        medicine_id INT(11) NOT NULL AUTO_INCREMENT,
        name VARCHAR(100) NOT NULL,
        description TEXT DEFAULT NULL,
        price DECIMAL(10,2) NOT NULL,
        image_url VARCHAR(255) DEFAULT NULL,
        stock_quantity INT(11) NOT NULL DEFAULT 0,
        low_stock_threshold INT(11) DEFAULT 10,
        created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        PRIMARY KEY (medicine_id),
        KEY idx_medicine_name (name)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci";
    $pdo->exec($sql);
    echo "Medicines table created successfully<br>";
    
    // Create orders table
    $sql = "CREATE TABLE IF NOT EXISTS orders (
        order_id INT(11) NOT NULL AUTO_INCREMENT,
        user_id INT(11) NOT NULL,
        total_amount DECIMAL(10,2) NOT NULL,
        status ENUM('pending','processing','preparing','ready','completed','cancelled') DEFAULT 'pending',
        payment_status ENUM('pending','paid','unpaid') DEFAULT 'pending',
        created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        PRIMARY KEY (order_id),
        KEY user_id (user_id),
        KEY idx_order_status (status),
        KEY idx_payment_status (payment_status),
        CONSTRAINT orders_ibfk_1 FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci";
    $pdo->exec($sql);
    echo "Orders table created successfully<br>";
    
    // Create order_items table
    $sql = "CREATE TABLE IF NOT EXISTS order_items (
        item_id INT(11) NOT NULL AUTO_INCREMENT,
        order_id INT(11) NOT NULL,
        medicine_id INT(11) NOT NULL,
        quantity INT(11) NOT NULL,
        price DECIMAL(10,2) NOT NULL,
        created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (item_id),
        KEY order_id (order_id),
        KEY medicine_id (medicine_id),
        CONSTRAINT order_items_ibfk_1 FOREIGN KEY (order_id) REFERENCES orders (order_id) ON DELETE CASCADE,
        CONSTRAINT order_items_ibfk_2 FOREIGN KEY (medicine_id) REFERENCES medicines (medicine_id) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci";
    $pdo->exec($sql);
    echo "Order items table created successfully<br>";
    
    // Create medication_reminders table
    $sql = "CREATE TABLE IF NOT EXISTS medication_reminders (
        reminder_id INT(11) NOT NULL AUTO_INCREMENT,
        user_id INT(11) NOT NULL,
        medicine_id INT(11) NOT NULL,
        dosage VARCHAR(100) NOT NULL,
        frequency VARCHAR(100) NOT NULL,
        start_date DATE NOT NULL,
        end_date DATE DEFAULT NULL,
        status ENUM('active','completed','cancelled') DEFAULT 'active',
        created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (reminder_id),
        KEY user_id (user_id),
        KEY medicine_id (medicine_id),
        CONSTRAINT medication_reminders_ibfk_1 FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE CASCADE,
        CONSTRAINT medication_reminders_ibfk_2 FOREIGN KEY (medicine_id) REFERENCES medicines (medicine_id) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci";
    $pdo->exec($sql);
    echo "Medication reminders table created successfully<br>";
    
    // Create prescriptions table
    $sql = "CREATE TABLE IF NOT EXISTS prescriptions (
        prescription_id INT(11) NOT NULL AUTO_INCREMENT,
        user_id INT(11) NOT NULL,
        medicine_id INT(11) NOT NULL,
        dosage VARCHAR(100) NOT NULL,
        instructions TEXT NOT NULL,
        start_date DATE NOT NULL,
        end_date DATE DEFAULT NULL,
        status ENUM('active','expired') DEFAULT 'active',
        created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (prescription_id),
        KEY user_id (user_id),
        KEY medicine_id (medicine_id),
        KEY idx_prescription_status (status),
        CONSTRAINT prescriptions_ibfk_1 FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE CASCADE,
        CONSTRAINT prescriptions_ibfk_2 FOREIGN KEY (medicine_id) REFERENCES medicines (medicine_id) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci";
    $pdo->exec($sql);
    echo "Prescriptions table created successfully<br>";
    
    // Create notifications table
    $sql = "CREATE TABLE IF NOT EXISTS notifications (
        notification_id INT(11) NOT NULL AUTO_INCREMENT,
        user_id INT(11) NOT NULL,
        title VARCHAR(255) NOT NULL,
        message TEXT NOT NULL,
        type ENUM('order','reminder','system') NOT NULL,
        is_read TINYINT(1) NOT NULL DEFAULT 0,
        created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (notification_id),
        KEY user_id (user_id),
        CONSTRAINT notifications_ibfk_1 FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci";
    $pdo->exec($sql);
    echo "Notifications table created successfully<br>";
    
    // Insert sample medicines
    $sql = "INSERT INTO medicines (name, description, price, image_url, stock_quantity) VALUES
        ('Paracetamol', 'Pain reliever and fever reducer', 5.00, 'paracetamol.jpg', 100),
        ('Ibuprofen', 'Nonsteroidal anti-inflammatory drug', 10.00, 'ibuprofen.jpg', 50),
        ('Amoxicillin', 'Antibiotic for bacterial infections', 15.00, 'amoxicillin.jpg', 75),
        ('Omeprazole', 'Reduces stomach acid production', 12.00, 'omeprazole.jpg', 60),
        ('Cetirizine', 'Antihistamine for allergies', 8.00, 'cetirizine.jpg', 90)";
    $pdo->exec($sql);
    echo "Sample medicines inserted successfully<br>";
    
    echo "<br>Database setup completed successfully! <a href='login.php'>Go to Login</a>";
    
} catch(PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?> 