<?php
session_start();
if(!isset($_SESSION['username']) || $_SESSION['role'] != 'user') {
    header("Location: login.php");
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>User Dashboard</title>
    <style>
        /* Reset & Body */
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            background: #f0f4f7;
        }

        /* Header */
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

        /* Main Content */
        .dashboard-container {
            flex: 1; /* Take remaining space */
            max-width: 1000px;
            margin-left: auto;
            margin-right: auto;
            margin-bottom: 200px;
            margin-top: 100px;
            padding: 20px;
            text-align: center;
        }

        .dashboard-container h1 {
            color: #333;
            margin-bottom: 40px;
        }

        .cards {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 30px;
        }

        .card {
            background: white;
            border-radius: 12px;
            width: 220px;
            padding: 20px;
            box-shadow: 0 8px 20px rgba(0,0,0,0.15);
            text-align: center;
            transition: 0.3s;
        }

        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 12px 25px rgba(0,0,0,0.25);
        }

        .card img {
            width: 80px;
            height: 80px;
            object-fit: contain;
            margin-bottom: 15px;
        }

        .card h3 {
            margin-bottom: 10px;
            color: #2575fc;
        }

        .card p {
            font-size: 14px;
            color: #555;
            margin-bottom: 35px;
        }

        .card a {
            text-decoration: none;
            color: white;
            background: #2575fc;
            padding: 8px 15px;
            border-radius: 6px;
            font-weight: bold;
            transition: 0.3s;
        }

        .card a:hover {
            background: #6a11cb;
        }

        /* Footer */
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

    <!-- Header -->
    <header>
        <div class="logo">Library</div>
        <nav>
            <a href="index.php">Home</a>
            <a href="index.php#new-arrivals">Catalog</a>
            <a href="index.php#events">Events</a>
            <a href="index.php#services">Services</a>
            <a href="logout.php">Logout</a>
        </nav>
    </header>

    <!-- Dashboard Section -->
    <div class="dashboard-container">
        <h1>Welcome, <?php echo $_SESSION['username']; ?>!</h1>
        <div class="cards">
            <div class="card">
                <img src="./images/book.png" alt="Search Book">
                <h3>Search Book</h3>
                <p>Find any book in our library catalog quickly and easily.</p>
                <a href="search.php">Go</a>
            </div>

            <div class="card">
                <img src="./images/view_borrow.png" alt="View Borrowed Books">
                <h3>View Borrowed Books</h3>
                <p>Check books you have currently borrowed.</p>
                <a href="active_borrow_books.php">Go</a>
            </div>

            <div class="card">
                <img src="./images/book (1).png" alt="Borrow Book">
                <h3>Library Activity Log</h3>
                <p>Track all your borrowed, returned, and reading activities in one place.</p>
                <a href="user_history.php">Go</a>
            </div>
        </div>
    </div>

    <!-- Footer -->
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
                <a href="logout.php">Logout</a>
            </div>
        </div>
        <div class="footer-bottom">
            &copy; 2025 Library Management System
        </div>
    </footer>

</body>
</html>
