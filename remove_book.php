<?php
// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$database = "lms";

$conn = new mysqli($servername, $username, $password, $database);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Step 1: Fetch book details for confirmation
if (isset($_POST['fetch'])) {
    $book_id = trim($_POST['book_id']);
    $name = trim($_POST['name']);

    if (!empty($book_id)) {
        $sql = "SELECT b.book_id, b.name, d.author, d.copies_available, d.copies_in_library 
                FROM books b 
                JOIN book_details d ON b.name = d.name 
                WHERE b.book_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $book_id);
    } elseif (!empty($name)) {
        $sql = "SELECT b.book_id, b.name, d.author, d.copies_available, d.copies_in_library 
                FROM books b 
                JOIN book_details d ON b.name = d.name 
                WHERE b.name = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $name);
    } else {
        $error = "Please enter either Book ID or Name.";
    }

    if (!isset($error)) {
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $book = $result->fetch_assoc();
        } else {
            $error = "No matching book found.";
        }
        $stmt->close();
    }
}

// Step 2: Delete book after confirmation
if (isset($_POST['confirm'])) {
    $book_id = trim($_POST['book_id']);
    $name = trim($_POST['name']);

    // Delete from book_details first
    $sql1 = "DELETE FROM book_details WHERE name = ?";
    $stmt1 = $conn->prepare($sql1);
    $stmt1->bind_param("s", $name);
    $stmt1->execute();
    $stmt1->close();

    // Then delete from books
    $sql2 = "DELETE FROM books WHERE book_id = ?";
    $stmt2 = $conn->prepare($sql2);
    $stmt2->bind_param("i", $book_id);

    if ($stmt2->execute()) {
        if ($stmt2->affected_rows > 0) {
            $success = "Book removed successfully.";
            // Automatically refresh back to initial form after 2 seconds
            header("Refresh:2; url=remove_book.php");
        } else {
            $error = "Book not found.";
        }
    } else {
        $error = "Error deleting book: " . $conn->error;
    }

    $stmt2->close();
}

$conn->close();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Remove Book</title>
    <style>
        body { font-family: Arial, sans-serif; margin:0; min-height:100vh; background:#f0f4f7; }
        header { background: #000; color: white; padding: 15px 30px; display:flex; justify-content: space-between; align-items:center; }
        header .logo { font-size: 24px; font-weight: bold; }
        nav a { color:white; text-decoration:none; margin:0 15px; font-weight:500; }
        nav a:hover { text-decoration:underline; }
        .container { max-width:600px; margin:120px auto 100px auto; background:white; padding:30px; border-radius:10px; box-shadow:0 8px 20px rgba(0,0,0,0.15); text-align:center; }
        input[type=text] { padding:10px; width:80%; margin:10px 0; }
        input[type=submit] { background:#007bff; color:white; border:none; padding:10px 20px; cursor:pointer; border-radius:5px; margin-top:10px; }
        input[type=submit]:hover { background:#0056b3; }
        table { margin:20px auto; border-collapse:collapse; }
        th, td { padding:10px 20px; border:1px solid #ccc; }
        .message { margin:15px 0; font-weight:bold; }
        .success { color:green; }
        .error { color:red; }
        footer { background:#000; color:white; padding:40px 30px 20px 30px; }
        .footer-container { display:flex; justify-content:space-between; flex-wrap:wrap; margin-bottom:20px; }
        .footer-left, .footer-right { flex:1; }
        .footer-left p { margin:20px 0; margin-left:80px; }
        .footer-right { text-align:right; margin-right:80px; }
        .footer-right a { color:white; text-decoration:none; display:block; margin:20px 0; }
        .footer-right a:hover { text-decoration:underline; color:#ccc; }
        .footer-bottom { text-align:center; border-top:1px solid rgba(255,255,255,0.3); padding-top:10px; font-size:14px; }
    </style>
</head>
<body>

<header>
    <div class="logo">Library</div>
    <nav>
        <a href="index.php">Home</a>
        <a href="index.php#new-arrivals">Catalog</a>
        <a href="index.php#events">Events</a>
        <a href="index.php#services">Services</a>
    </nav>
</header>

<div class="container">
    <h2>Remove Book from Library</h2>

    <?php if(isset($error)) echo "<div class='message error'>$error</div>"; ?>
    <?php if(isset($success)) echo "<div class='message success'>$success</div>"; ?>

    <?php if(!isset($book) && !isset($success)): ?>
        <form method="POST" action="">
            <label>Book ID:</label><br>
            <input type="text" name="book_id" placeholder="Enter Book ID"><br>
            <b>OR</b><br>
            <label>Book Name:</label><br>
            <input type="text" name="name" placeholder="Enter Book Name"><br>
            <input type="submit" name="fetch" value="Fetch Book">
        </form>
    <?php endif; ?>

    <?php if(isset($book)): ?>
        <h3>Confirm Deletion</h3>
        <table>
    <thead>
        <tr>
            <th>Book ID</th>
            <th>Name</th>
            <th>Author</th>
            <th>Copies Available</th>
            <th>Copies in Library</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td><?php echo htmlspecialchars($book['book_id']); ?></td>
            <td><?php echo htmlspecialchars($book['name']); ?></td>
            <td><?php echo htmlspecialchars($book['author']); ?></td>
            <td><?php echo htmlspecialchars($book['copies_available']); ?></td>
            <td><?php echo htmlspecialchars($book['copies_in_library']); ?></td>
        </tr>
    </tbody>
</table>

        <form method="POST" action="">
            <input type="hidden" name="book_id" value="<?php echo htmlspecialchars($book['book_id']); ?>">
            <input type="hidden" name="name" value="<?php echo htmlspecialchars($book['name']); ?>">
            <input type="submit" name="confirm" value="Yes, Delete Book">
            <a href="remove_book.php" style="margin-left:15px; color:#007bff;">Cancel</a>
        </form>
    <?php endif; ?>
</div>
<style>
    table {
    width: 100%;
    border-collapse: separate;
    border-spacing: 0;
    margin: 25px 0;
    border-radius: 12px;
    overflow: hidden;
    box-shadow: 0 4px 15px rgba(0,0,0,0.1);
    background-color: #ffffff;
}

th {
    background: #0d47a1;
    color: #ffffff;
    text-align: left;
    padding: 14px 20px;
    font-size: 16px;
    letter-spacing: 0.3px;
    width: 40%;
}

td {
    background-color: #f9f9f9;
    padding: 14px 20px;
    font-size: 16px;
    color: #333333;
}

tr:nth-child(even) td {
    background-color: #f1f5ff;
}

tr:hover td {
    background-color: #e3f2fd;
    transition: background-color 0.3s ease;
}

table th:first-child {
    border-top-left-radius: 12px;
}

table tr:last-child td:first-child {
    border-bottom-left-radius: 12px;
}

table tr:last-child td:last-child {
    border-bottom-right-radius: 12px;
}

    </style>

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
        </div>
    </div>
    <div class="footer-bottom">
        &copy; 2025 Library Management System
    </div>
</footer>

</body>
</html>
