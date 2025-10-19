<?php
session_start();
include('db_connect.php');

// --- Handle return action ---
if (isset($_POST['return']) && isset($_POST['book_id'])) {
    $book_id = $_POST['book_id'];
    $user_id = $_POST['user_id'];

    // 1. Update status table
    $update = "UPDATE status 
               SET status='return', return_date=CURDATE() 
               WHERE book_id='$book_id' AND user_id='$user_id'";
    $conn->query($update);

    // 2. Increase copies_available by 1 in book_details table
    $update_copies = "UPDATE book_details 
                      SET copies_available = copies_available + 1 
                      WHERE name = (SELECT name FROM books WHERE book_id='$book_id')";
    $conn->query($update_copies);
}


// --- Handle user filter ---
$filter_user = "";
if (isset($_POST['go']) && !empty($_POST['search_user'])) {
    $filter_user = $conn->real_escape_string($_POST['search_user']);
    $query = "SELECT s.user_id, s.book_id, b.name AS book_name, s.issue_date 
              FROM status s 
              JOIN books b ON s.book_id = b.book_id 
              WHERE s.status='borrow' AND s.user_id='$filter_user'";
} else {
    $query = "SELECT s.user_id, s.book_id, b.name AS book_name, s.issue_date 
              FROM status s 
              JOIN books b ON s.book_id = b.book_id 
              WHERE s.status='borrow'";
}

$result = $conn->query($query);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Return Books</title>
    <style>
        /* General Page Layout */
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

        /* Main Container */
        .container {
            flex: 1;
            width:80%;
            max-width: 1800px;
            margin: 40px auto;
            padding: 40px;
            background: #f1f4f6ff;
            border-radius: 12px;
            box-shadow: 0 8px 25px rgba(0,0,0,0.15);
        }

        h2 {
            color: #333;
            text-align: center;
            margin-bottom: 40px;
        }

        /* Form */
        form.search-form {
            display: flex;
            align-items: center;
            justify-content: flex-start;
            margin-bottom: 30px;
            gap: 15px;
        }

        label {
            font-weight: bold;
            font-size: 18px;
        }

        input[type="text"] {
            padding: 12px 15px;
            border: 1px solid #000;
            border-radius: 8px;
            font-size: 16px;
            width: 250px;
        }

        button {
            background-color: #000;
            color: white;
            border: none;
            padding: 12px 25px;
            border-radius: 8px;
            cursor: pointer;
            font-weight: bold;
            font-size: 16px;
            transition: background 0.3s;
        }

        button:hover {
            background-color: #333;
        }

        /* Table */
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            font-size: 18px;
        }

        th {
            background-color: #000;
            color: white;
            padding: 20px;
            text-align: center;
        }

        td {
            background: #f6f8faff;
            color: #000;
            text-align: center;
            padding: 20px;
            border-bottom: 1px solid #ddd;
        }

        tr:hover td {
            background-color: #e0e0e0;
            transition: 0.3s;
        }

        .no-data {
            text-align: center;
            color: red;
            font-weight: bold;
            padding: 30px;
            font-size: 18px;
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

        /* Return button inside table */
        table button {
            padding: 10px 20px;
            border-radius: 6px;
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

<!-- Content -->
<div class="container">
    <h2>Return Books</h2>

    <form method="POST" class="search-form">
        <input type="text" name="search_user" placeholder="User ID" value="<?php echo $filter_user; ?>">
        <button type="submit" name="go">Go</button>
    </form>

    <?php
    if ($result->num_rows > 0) {
        echo "<table>";
        echo "<tr><th>User ID</th><th>Book ID</th><th>Book Name</th><th>Issue Date</th><th>Fine</th><th>Action</th></tr>";

        while ($row = $result->fetch_assoc()) {
            $issue_date = new DateTime($row['issue_date']);
            $today = new DateTime();
            $days_diff = $today->diff($issue_date)->days;
            $fine = ($days_diff > 30) ? ($days_diff - 30) * 5 : 0;

            echo "<tr>
                <td>{$row['user_id']}</td>
                <td>{$row['book_id']}</td>
                <td>{$row['book_name']}</td>
                <td>{$row['issue_date']}</td>
                <td>₹{$fine}</td>
                <td>
                    <form method='POST' style='margin:0;'>
                        <input type='hidden' name='book_id' value='{$row['book_id']}'>
                        <input type='hidden' name='user_id' value='{$row['user_id']}'>
                        <button type='submit' name='return'>Return</button>
                    </form>
                </td>
            </tr>";
        }
        echo "</table>";
    } else {
        echo "<div class='no-data'>No available rows.</div>";
    }

    $conn->close();
    ?>
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
        &copy; <?php echo date("Y"); ?> Library Management System
    </div>
</footer>

</body>
</html>
