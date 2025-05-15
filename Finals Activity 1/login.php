<?php
session_start();
require 'config/database.php';

// Establish PDO connection
$database = new Database();
$conn = $database->getConnection();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    // Admin login (hardcoded)
    if ($email === "admin@gmail.com" && $password === "admin123") {
        $_SESSION['user_type'] = 'admin';
        $_SESSION['user_name'] = 'Administrator';
        header("Location: admin/admin_dashboard.php");
        exit;
    }

    // Regular user login (from DB)
    $stmt = $conn->prepare("SELECT id, password, first_name FROM users WHERE email = :email");
    $stmt->bindParam(':email', $email);
    $stmt->execute();
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user) {
        if (password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_type'] = 'user';
            $_SESSION['user_name'] = $user['first_name'];
            header("Location: dashboard.php");
            exit;
        }
    }

    $_SESSION['error'] = "❌ Invalid email or password.";
    header("Location: login.php");
    exit;
}
?>

<!-- HTML STARTS HERE -->
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>MedTrack Login</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link href="https://fonts.googleapis.com/css2?family=Special+Elite&display=swap" rel="stylesheet" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" />

  <style>
    body { background-color: #6a0a0a; }
    .logo-text { font-family: 'Special Elite', monospace; }
    .left-panel {
      width: 350px;
      background: linear-gradient(180deg, #6a0a0a 0%, #a87f7f 100%);
      border-top-left-radius: 0.5rem;
      border-bottom-left-radius: 0.5rem;
      position: relative;
    }
    @media (min-width: 640px) { .left-panel { width: 400px; } }
    @media (min-width: 768px) { .left-panel { width: 450px; } }

    .overlay {
      position: absolute;
      inset: 0;
      background: linear-gradient(to bottom, #6a0a0a, #a87f7f);
      border-top-left-radius: 0.5rem;
      border-bottom-left-radius: 0.5rem;
    }
    .curve-bottom {
      position: absolute;
      bottom: 0;
      left: 0;
      width: 100%;
      height: 100px;
      background: black;
      border-top-left-radius: 100px;
      clip-path: ellipse(100% 100% at 0% 100%);
    }
    .right-panel {
      background-color: black;
      width: 350px;
      padding: 1.5rem;
      border-top-right-radius: 0.5rem;
      border-bottom-right-radius: 0.5rem;
      display: flex;
      flex-direction: column;
      align-items: center;
    }
    @media (min-width: 640px) { .right-panel { width: 400px; } }
    @media (min-width: 768px) { .right-panel { width: 450px; } }

    .icon-circle {
      background-color: white;
      border-radius: 9999px;
      padding: 0.75rem;
      width: 60px;
      height: 60px;
      display: flex;
      align-items: center;
      justify-content: center;
      margin-bottom: 0.5rem;
    }

    .input-field {
      width: 100%;
      background-color: white;
      color: black;
      padding: 0.5rem 0.75rem;
      padding-left: 2.25rem;
      border-radius: 0.375rem;
      outline: none;
    }
    .icon-left {
      position: absolute;
      left: 0.5rem;
      top: 50%;
      transform: translateY(-50%);
      color: #4b5563;
    }
    .btn-login {
      width: 100%;
      background-color: #b33a3a;
      color: white;
      font-weight: bold;
      padding: 0.5rem;
      border-radius: 0.375rem;
      margin-top: 0.75rem;
    }
    .btn-register {
      width: 100%;
      background-color: white;
      color: black;
      font-weight: bold;
      padding: 0.5rem;
      border-radius: 0.375rem;
    }
    .error-message {
      color: #ff4444;
      background-color: #ffebee;
      padding: 0.5rem;
      border-radius: 0.375rem;
      margin-bottom: 1rem;
      width: 100%;
    }
  </style>
</head>
<body class="min-h-screen flex items-center justify-center p-4">
  <div class="flex max-w-4xl w-full rounded-lg overflow-hidden">
    
    <!-- Left Panel -->
    <div class="left-panel">
      <div class="overlay"></div>
      <div class="relative z-10 flex flex-col items-center justify-center h-full p-6">
        <h1 class="text-white text-3xl mb-4 logo-text">MedTrack</h1>
        <img src="./images/heart.jpg" alt="Heart Logo" class="w-[150px] h-[150px] object-contain" />
      </div>
      <div class="curve-bottom"></div>
    </div>

    <!-- Right Panel (Form) -->
    <div class="right-panel">
      <div class="flex flex-col items-center mb-4">
        <div class="icon-circle">
          <i class="fas fa-user fa-lg text-black"></i>
        </div>
        <h2 class="text-white text-xl font-bold tracking-wide">LOGIN</h2>
      </div>

      <?php if (isset($_SESSION['error'])): ?>
        <div class="error-message">
          <?php 
            echo htmlspecialchars($_SESSION['error']);
            unset($_SESSION['error']);
          ?>
        </div>
      <?php endif; ?>

      <form class="w-full max-w-xs space-y-3 text-white text-sm" method="POST" action="">
        <label class="block" for="email">
          Email:
          <div class="relative mt-1">
            <input type="email" id="email" name="email" class="input-field" required />
            <i class="fas fa-envelope icon-left"></i>
          </div>
        </label>

        <label class="block" for="password">
          Password:
          <div class="relative mt-1">
            <input type="password" id="password" name="password" class="input-field" required />
            <i class="fas fa-lock icon-left"></i>
          </div>
        </label>

        <div class="flex justify-between items-center text-xs mt-1">
          <label class="flex items-center space-x-1">
            <input type="checkbox" name="remember" class="w-3 h-3" />
            <span>Remember me</span>
          </label>
          <a href="forgot_password.php" class="hover:underline">Forgot Password?</a>
        </div>

        <button type="submit" class="btn-login">Login</button>

        <div class="text-center text-white mt-2 mb-2">OR</div>

        <button type="button" class="btn-register" onclick="window.location.href='register.php'">Register</button>
      </form>
    </div>
  </div>
</body>
</html>
