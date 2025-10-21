<?php
session_start();
if(!isset($_SESSION['username']) || $_SESSION['role'] != 'librarian') {
    header("Location: login.php");
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Librarian Dashboard</title>
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

        /* Main Dashboard Container */
        .dashboard-container {
            flex: 1; /* Take remaining space */
            max-width: 1000px;
            margin: 40px auto;
            padding: 20px;
            text-align: center;
        }

        .dashboard-container h1 {
            color: #333;
            margin-bottom: 40px;
        }

        /* Cards Layout */
        .cards {
            display: flex;
            flex-wrap: nowrap;
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
            margin-top: 20px;
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
        <h1>Welcome Librarian, <?php echo $_SESSION['username']; ?>!</h1>
        <div class="cards">
            <div class="card">
                <img src="./images/add_book.png" alt="Add Book">
                <h3>Add Book</h3>
                <p>Add new books to the library catalog and manage inventory.</p>
                <a href="add_book.php">Go</a>
            </div>

            <div class="card">
                <img src="./images/update_book.png" alt="Update Book">
                <h3>Update Book</h3>
                <p>Edit details of existing books including title, author, and availability.</p>
                <a href="#">Go</a>
            </div>

            <div class="card">
                <img src="./images/remove_book.png" alt="Remove Book">
                <h3>Remove Book</h3>
                <p>Remove books that are outdated or no longer available in the library.</p>
                <a href="#">Go</a>
            </div>

            <div class="card">
                <img src="./images/view_borrow.png" alt="View Borrowed Books">
                <h3>Return Books</h3>
                <p>See all books currently borrowed by users and manage returns.</p>
                <a href="return.php">Go</a>
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
