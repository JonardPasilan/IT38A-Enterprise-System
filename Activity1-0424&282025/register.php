<?php
session_start();


$username = $password = $confirm_password = "";
$username_err = $password_err = $confirm_password_err = "";


if ($_SERVER["REQUEST_METHOD"] == "POST") {

    
    if (empty(trim($_POST["username"]))) {
        $username_err = "Please enter a username.";
    } else {
        $username = trim($_POST["username"]);
    }

   
    if (empty(trim($_POST["password"]))) {
        $password_err = "Please enter a password.";
    } elseif (strlen(trim($_POST["password"])) < 6) {
        $password_err = "Password must be at least 6 characters long.";
    } else {
        $password = trim($_POST["password"]);
    }


    if (empty(trim($_POST["confirm_password"]))) {
        $confirm_password_err = "Please confirm your password.";
    } else {
        $confirm_password = trim($_POST["confirm_password"]);
        if ($password !== $confirm_password) {
            $confirm_password_err = "Password did not match.";
        }
    }

    
    if (empty($username_err) && empty($password_err) && empty($confirm_password_err)) {
      
        $_SESSION["username"] = $username;
        $_SESSION["loggedin"] = true;
        header("Location: login.php");
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            background-color: #f1f1f1;
            margin: 0;
        }
        .register-container {
            background-color: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgb(94, 11, 11);
            width: 300px;
        }
        h2 {
            text-align: center;
            margin-bottom: 20px;
        }
        input[type="text"], input[type="password"] {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            border: 1px solid #ccc;
            border-radius: 4px;
            box-sizing: border-box;
        }
        .btn {
            width: 100%;
            padding: 10px;
            background-color:rgb(94, 11, 11);
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
        .btn:hover {
            background-color: rgb(94, 11, 11);
        }
        .error {
            color: red;
            text-align: center;
            margin-bottom: 10px;
        }
    </style>
</head>
<body>

<div class="register-container">
    <h2>Register</h2>
    
   
    <?php 
    if (!empty($username_err)) echo "<div class='error'>$username_err</div>";
    if (!empty($password_err)) echo "<div class='error'>$password_err</div>";
    if (!empty($confirm_password_err)) echo "<div class='error'>$confirm_password_err</div>";
    ?>

    <form method="POST" action="">
        <input type="text" name="username" placeholder="Username" value="<?php echo $username; ?>" required>
        <input type="password" name="password" placeholder="Password" required>
        <input type="password" name="confirm_password" placeholder="Confirm Password" required>
        <button type="submit" class="btn">Register</button>
    </form>

    <p style="text-align: center; margin-top: 10px;">Already have an account? <a href="login.php">Login here</a></p>
</div>

</body>
</html>
