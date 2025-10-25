<?php
include('db_connect.php');
session_start();

if (!isset($_SESSION['username']) || $_SESSION['role'] != 'user') {
    header("Location: login.php");
    exit();
}

$username = $_SESSION['username'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>User History</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: Arial, sans-serif;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            background: #f0f4f7;
        }

        /* Header */
        header {
            background: #000;
            color: #fff;
            padding: 15px 30px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        header .logo { font-size: 24px; font-weight: bold; }

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

        nav a:hover::after { width: 100%; }

        /* Content */
        .content {
            flex: 1;
            padding: 60px 20px;
            text-align: center;
            animation: fadeIn 1.2s ease-in-out;
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

        .search-container { margin-bottom: 25px; }

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

        table {
            width: 85%;
            margin: 40px auto;
            border-collapse: collapse;
            background-color: #fff;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
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

        tr:hover { background-color: #f9f9f9; }

        .status-returned {
            color: green;
            font-weight: bold;
        }

        .status-borrowed {
            color: orange;
            font-weight: bold;
        }

        .empty-state {
            text-align: center;
            margin-top: 60px;
            animation: fadeIn 1.2s ease-in;
        }

        .empty-state img {
            width: 140px;
            opacity: 0.8;
            margin-bottom: 20px;
        }

        .empty-state h3 { color: #000; margin-bottom: 10px; }
        .empty-state p { color: #555; font-size: 15px; }

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

        footer {
            background-color: #000;
            color: #fff;
            padding: 50px 40px 20px;
            text-align: center;
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

        footer .footer-right a:hover { color: #ccc; }

        .footer-bottom {
            border-top: 1px solid rgba(255,255,255,0.2);
            padding-top: 10px;
            color: #aaa;
            font-size: 14px;
        }

        .time-ago {
            color: #777;
            font-size: 13px;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }
    </style>
</head>
<body>

<header>
    <div class="logo">Library</div>
    <nav>
        <a href="user_dashboard.php">Dashboard</a>
        <a href="user_borrow.php">Borrow</a>
        <a href="search.php">Search</a>
        <a href="user_active_books.php">Active Borrowed</a>
        <a href="user_history.php" style="text-decoration: underline;">History</a>
        <a href="logout.php">Logout</a>
    </nav>
</header>

<div class="content">
    <h1>User Activity History</h1>

    <div class="search-container">
        <input type="text" id="searchInput" class="search-box" placeholder="🔍 Search by Book Name or Author..." onkeyup="filterTable()">
    </div>

    <?php
    function timeAgo($datetime) {
        $time = strtotime($datetime);
        $diff = time() - $time;
        if ($diff < 60) return "just now";
        elseif ($diff < 3600) return floor($diff / 60) . " min ago";
        elseif ($diff < 86400) return floor($diff / 3600) . " hrs ago";
        elseif ($diff < 2592000) return floor($diff / 86400) . " days ago";
        elseif ($diff < 31536000) return floor($diff / 2592000) . " months ago";
        else return floor($diff / 31536000) . " years ago";
    }

    $sql = "SELECT s.book_id, b.name AS book_name, bd.author, s.issue_date, s.return_date, s.status
            FROM status s
            JOIN books b ON s.book_id = b.book_id
            JOIN book_details bd ON b.name = bd.name
            JOIN users u ON s.user_id = u.user_id
            WHERE u.username = ?
            ORDER BY s.issue_date DESC";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param('s', $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        echo "<table id='historyTable'>";
        echo "<tr><th>Book ID</th><th>Book Name</th><th>Author</th><th>Issue Date</th><th>Return Date</th><th>Status</th><th>Activity Time</th></tr>";

        while ($row = $result->fetch_assoc()) {
            $return_display = $row['return_date'] ? $row['return_date'] : 'Not Returned';
            $status_class = ($row['status'] == 'returned') ? 'status-returned' : 'status-borrowed';
            $activity_time = $row['return_date'] ? $row['return_date'] : $row['issue_date'];
            $timeago = timeAgo($activity_time);

            echo "<tr>";
            echo "<td>{$row['book_id']}</td>";
            echo "<td>{$row['book_name']}</td>";
            echo "<td>{$row['author']}</td>";
            echo "<td>{$row['issue_date']}</td>";
            echo "<td>$return_display</td>";
            echo "<td class='$status_class'>" . ucfirst($row['status']) . "</td>";
            echo "<td class='time-ago'>$timeago</td>";
            echo "</tr>";
        }

        echo "</table>";
    } else {
        echo "<div class='empty-state'>
                <img src='https://cdn-icons-png.flaticon.com/512/4076/4076549.png'/>
                <h3>No history records yet 📖</h3>
                <p>Start borrowing books to see your history here!</p>
              </div>";
    }

    $stmt->close();
    $conn->close();
    ?>

    <a href="user_dashboard.php" class="back-btn">← Back to Dashboard</a>
</div>

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

<script>
function filterTable() {
    let input = document.getElementById("searchInput").value.toLowerCase();
    let rows = document.querySelectorAll("#historyTable tr:not(:first-child)");
    rows.forEach(row => {
        let bookName = row.cells[1].innerText.toLowerCase();
        let author = row.cells[2].innerText.toLowerCase();
        row.style.display = (bookName.includes(input) || author.includes(input)) ? "" : "none";
    });
}
</script>

</body>
</html>