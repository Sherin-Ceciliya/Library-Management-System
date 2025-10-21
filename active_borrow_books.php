<?php
include('db_connect.php');
session_start();

if(!isset($_SESSION['username']) || $_SESSION['role'] != 'user') {
    header("Location: login.php");
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Active Borrowed Books</title>
    <style>
        /* ===== Global Reset ===== */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

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
            color: #fff;
            text-decoration: none;
            margin: 0 15px;
            font-weight: 500;
            transition: 0.3s;
            position: relative;
        }

        nav a::after {
            content: "";
            position: absolute;
            width: 0%;
            height: 2px;
            bottom: -4px;
            left: 0;
            background-color: #fff;
            transition: 0.3s;
        }

        nav a:hover::after {
            width: 100%;
        }

        /* ===== Content ===== */
        .content {
            flex: 1;
            padding: 60px 20px;
            text-align: center;
            animation: fadeIn 1.5s ease-in-out;
        }

        h1 {
            font-size: 36px;
            margin-bottom: 40px;
            color: #000;
            text-transform: uppercase;
            letter-spacing: 1.5px;
            position: relative;
        }

        h1::after {
            content: "";
            display: block;
            width: 100px;
            height: 4px;
            background: #000;
            margin: 15px auto 0;
            border-radius: 2px;
        }

        /* ===== Search Box ===== */
        .search-container {
            margin-bottom: 25px;
        }

        .search-box {
            padding: 10px 15px;
            width: 60%;
            border-radius: 6px;
            border: 1px solid #ccc;
            outline: none;
            font-size: 16px;
            transition: 0.3s;
        }

        .search-box:focus {
            border-color: #000;
            box-shadow: 0 0 6px rgba(0,0,0,0.2);
        }

        /* ===== Table ===== */
        table {
            width: 85%;
            margin: 40px auto;
            border-collapse: collapse;
            background-color: #fff;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
            transition: 0.3s;
        }

        th, td {
            padding: 15px 10px;
            text-align: center;
        }

        th {
            background-color: #000;
            color: #fff;
            text-transform: uppercase;
            font-size: 14px;
            letter-spacing: 1px;
        }

        td {
            color: #333;
            border-bottom: 1px solid #eee;
        }

        tr:hover {
            background-color: #f9f9f9;
        }

        td[style*='color:red'] {
            font-weight: bold;
        }

        /* ===== Empty State ===== */
        .empty-state {
            text-align: center;
            margin-top: 60px;
            animation: fadeIn 1.5s ease-in;
        }

        .empty-state img {
            width: 140px;
            opacity: 0.8;
            margin-bottom: 20px;
        }

        .empty-state h3 {
            color: #000;
            margin-bottom: 10px;
        }

        .empty-state p {
            color: #555;
            font-size: 15px;
        }

        .empty-state a {
            color: #000;
            text-decoration: underline;
            font-weight: 600;
        }

        /* ===== Button ===== */
        .back-btn {
            display: inline-block;
            margin-top: 40px;
            padding: 12px 24px;
            background: #000;
            color: #fff;
            font-weight: 600;
            border-radius: 6px;
            text-decoration: none;
            box-shadow: 0 4px 8px rgba(0,0,0,0.3);
            transition: 0.3s;
        }

        .back-btn:hover {
            background: #333;
            transform: translateY(-2px);
        }

        /* ===== Footer ===== */
        footer {
            background-color: #000;
            color: #fff;
            padding: 50px 40px 20px;
            text-align: center;
            border-top: 1px solid rgba(255,255,255,0.1);
        }

        footer .footer-container {
            display: flex;
            flex-wrap: wrap;
            justify-content: space-around;
            margin-bottom: 20px;
        }

        footer .footer-left, footer .footer-right {
            flex: 1;
            min-width: 250px;
        }

        footer .footer-left p {
            margin: 10px 0;
            color: #ccc;
        }

        footer .footer-right a {
            color: #fff;
            text-decoration: none;
            display: block;
            margin: 8px 0;
            transition: 0.3s;
        }

        footer .footer-right a:hover {
            color: #ccc;
        }

        .footer-bottom {
            border-top: 1px solid rgba(255,255,255,0.2);
            padding-top: 10px;
            color: #aaa;
            font-size: 14px;
        }

        /* ===== Animations ===== */
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(15px); }
            to { opacity: 1; transform: translateY(0); }
        }
    </style>
</head>
<body>

<header>
    <div class="logo">Library</div>
    <nav>
        <a href="user_dashboard.php">Dashboard</a>
        <a href="index.php#new-arrivals">Catalog</a>
        <a href="index.php#events">Events</a>
        <a href="index.php#services">Services</a>
        <a href="logout.php">Logout</a>
    </nav>
</header>

<div class="content">
    <h1>Active Borrowed Books</h1>

    <!-- Search Bar -->
    <div class="search-container">
        <input type="text" id="searchInput" class="search-box" placeholder="🔍 Search by Book Name or Author..." onkeyup="filterTable()">
    </div>

    <?php
    $username = $_SESSION['username'];

    $sql = "SELECT s.book_id, b.name AS book_name, bd.author, s.issue_date
            FROM status s
            JOIN books b ON s.book_id = b.book_id
            JOIN book_details bd ON b.name = bd.name
            JOIN users u ON s.user_id = u.user_id
            WHERE u.username = ? AND s.status = 'borrow'";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param('s', $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        echo "<table id='booksTable'>";
        echo "<tr><th>Book ID</th><th>Book Name</th><th>Author</th><th>Issue Date</th><th>Return Date</th><th>Fine (₹)</th></tr>";

        while ($row = $result->fetch_assoc()) {
            $issue_date = new DateTime($row['issue_date']);
            $return_date = clone $issue_date;
            $return_date->modify('+30 days');

            $today = new DateTime();
            $days_diff = $today->diff($issue_date)->days;
            $fine = ($days_diff > 30) ? ($days_diff - 30) * 5 : 0;
            $fine_color = ($fine > 0) ? "style='color:red; font-weight:bold;'" : "";

            echo "<tr>";
            echo "<td>{$row['book_id']}</td>";
            echo "<td>{$row['book_name']}</td>";
            echo "<td>{$row['author']}</td>";
            echo "<td>{$issue_date->format('Y-m-d')}</td>";
            echo "<td>{$return_date->format('Y-m-d')}</td>";
            echo "<td $fine_color>" . ($fine > 0 ? '₹'.$fine : 'No Fine') . "</td>";
            echo "</tr>";
        }

        echo "</table>";
    } else {
        echo "<div class='empty-state'>
                <img src='https://cdn-icons-png.flaticon.com/512/4076/4076549.png'/>
                <h3>No active borrowed books right now 📚</h3>
                <p>“A room without books is like a body without a soul.”<br>
                Go ahead, <a href='search.php'>borrow your next adventure!</a></p>
              </div>";
    }

    $stmt->close();
    $conn->close();
    ?>

    <a href="user_dashboard.php" class="back-btn">← Back to Dashboard</a>
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

<!-- ===== JS Search Filter ===== -->
<script>
function filterTable() {
    let input = document.getElementById("searchInput").value.toLowerCase();
    let rows = document.querySelectorAll("#booksTable tr:not(:first-child)");

    rows.forEach(row => {
        let bookName = row.cells[1].innerText.toLowerCase();
        let author = row.cells[2].innerText.toLowerCase();
        row.style.display = (bookName.includes(input) || author.includes(input)) ? "" : "none";
    });
}
</script>

</body>
</html>

