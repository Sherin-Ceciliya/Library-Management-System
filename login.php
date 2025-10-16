<?php
session_start();
include('db_connect.php');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $sql = "SELECT * FROM users WHERE username='$username' AND password='$password'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        $_SESSION['username'] = $user['username'];
        $_SESSION['role'] = $user['role'];

        // For client-side storage
        $username_js = $user['username'];
        $role_js = $user['role'];

        // Redirect based on role
        if ($user['role'] == 'user') {
            echo "<script>
                    sessionStorage.setItem('username', '$username_js');
                    sessionStorage.setItem('role', '$role_js');
                    window.location.href = 'user_dashboard.php';
                  </script>";
            exit();
        } else if ($user['role'] == 'librarian') {
            echo "<script>
                    sessionStorage.setItem('username', '$username_js');
                    sessionStorage.setItem('role', '$role_js');
                    window.location.href = 'librarian_dashboard.php';
                  </script>";
            exit();
        }
    } else {
        $error = "Invalid username or password";
    }
}
?>


<!DOCTYPE html>
<html>
<head>
    <title>Library Login</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            background: url('./images/library-hero-2.jpg') no-repeat center center fixed;
            background-size: cover;
        }

        /* Reuse homepage header styles */
        header {
            background: #000;
            color: white;
            padding: 15px 30px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        header .logo {
            font-size: 24px;
            font-weight: bold;
        }

        nav a {
            color: white;
            text-decoration: none;
            margin: 0 15px;
            font-weight: 500;
        }

        nav a:hover {
            text-decoration: underline;
        }

        /* Login box */
        .login-container {
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 80vh;
        }

        .login-box {
            background: white;
            padding: 40px 30px;
            border-radius: 12px;
            width: 350px;
            box-shadow: 0 8px 20px rgba(0,0,0,0.3);
            text-align: center;
        }

        .login-box h2 {
            margin-bottom: 15px;
            color: #333;
        }

        .login-box p {
            color: #555;
            margin-bottom: 20px;
        }

        input[type=text], input[type=password] {
            width: 90%;
            padding: 10px;
            margin: 10px 0;
            border: 1px solid #ccc;
            border-radius: 6px;
        }

        input[type=submit] {
            background: #000000ff;
            color: white;
            padding: 12px;
            width: 95%;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-size: 16px;
        }

        input[type=submit]:hover {
            background: #00000098;
        }

        .error {
            color: red;
            margin: 10px 0;
        }

        footer {
            background: #000;
            color: white;
            padding: 40px 30px 20px 30px;
        }

        .footer-container {
            display: flex;
            justify-content: space-between;
            flex-wrap: wrap;
            margin-bottom: 20px;
        }

        .footer-left, .footer-right {
            flex: 1;
        }

        .footer-left p {
            margin: 20px 0;
            margin-left: 80px;
        }

        .footer-right {
            text-align: right;
            margin-right: 80px;
        }

        .footer-right a {
            color: white;
            text-decoration: none;
            display: block;
            margin: 20px 0;
        }

        .footer-right a:hover {
            text-decoration: underline;
            color: #ccc;
        }

        .footer-bottom {
            text-align: center;
            border-top: 1px solid rgba(255,255,255,0.3);
            padding-top: 10px;
            font-size: 14px;
        }
    </style>
</head>
<body>

    <!-- Header (same as homepage) -->
    <header>
        <div class="logo">Library</div>
        <nav>
            <a href="index.php">Home</a>
            <a href="index.php#new-arrivals">Catalog</a>
            <a href="index.php#events">Events</a>
            <a href="index.php#services">Services</a>
            <a href="login.php">Login</a>
        </nav>
    </header>

    <!-- Login Section -->
    <div class="login-container">
        <div class="login-box">
            <h2>Login</h2>
            <p>Access your library account for more!</p>
            <?php if(isset($error)) echo "<div class='error'>$error</div>"; ?>
            <form method="POST">
                <input type="text" name="username" placeholder="Username" required><br>
                <input type="password" name="password" placeholder="Password" required><br>
                <input type="submit" value="Login">
            </form>
            <p>Don't have an account? <a href="register.php" style="color: #9f9f9fff;">Register here</a></p>
        </div>
    </div>

    <!-- Footer (same as homepage) -->
    <footer>
        <div class="footer-container">
            <div class="footer-left">
                <p><strong>Library Address:</strong></p>
                <p>123 Library Street</p>
                <p>City, State, ZIP</p>
                <p>Email: info@library.com</p>
                <p>Phone: +1 234 567 890</p>
            </div>
            <div class="footer-right">
                <a href="index.php">Home</a>
                <a href="index.php#new-arrivals">Catalog</a>
                <a href="index.php#events">Events</a>
                <a href="index.php#services">Services</a>
                <a href="login.php">Login</a>
            </div>
        </div>
        <div class="footer-bottom">
            &copy; 2025 Library Management System
        </div>
    </footer>

</body>
</html>
