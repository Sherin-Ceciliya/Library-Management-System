<?php
// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$database = "lms";

$conn = new mysqli($servername, $username, $password, $database);
if ($conn->connect_error) die("Connection failed: " . $conn->connect_error);

// Step 1: Fetch all entries with same book name
if (isset($_POST['fetch'])) {
    $name = trim($_POST['name']);

    if (!empty($name)) {
        // Fetch book details (once)
        $sql_details = "SELECT author, copies_available, copies_in_library FROM book_details WHERE name = ?";
        $stmt_details = $conn->prepare($sql_details);
        $stmt_details->bind_param("s", $name);
        $stmt_details->execute();
        $details = $stmt_details->get_result()->fetch_assoc();
        $stmt_details->close();

        // Fetch all entries from books
        $sql_books = "SELECT * FROM books WHERE name = ?";
        $stmt_books = $conn->prepare($sql_books);
        $stmt_books->bind_param("s", $name);
        $stmt_books->execute();
        $result = $stmt_books->get_result();

        if ($result->num_rows > 0) {
            $books = $result->fetch_all(MYSQLI_ASSOC);
        } else {
            $error = "No matching book entries found.";
        }
        $stmt_books->close();
    } else {
        $error = "Please enter a Book Name.";
    }
}

// Step 2: Delete ALL entries for given book name
if (isset($_POST['delete_all'])) {
    $name = trim($_POST['name']);

    if (!empty($name)) {
        $conn->begin_transaction();

        try {
            // Delete from book_details
            // Delete all from books
            $stmt2 = $conn->prepare("DELETE FROM books WHERE name = ?");
            $stmt2->bind_param("s", $name);
            $stmt2->execute();
            $deleted = $stmt2->affected_rows;
            $stmt2->close();
            $stmt1 = $conn->prepare("DELETE FROM book_details WHERE name = ?");
            $stmt1->bind_param("s", $name);
            $stmt1->execute();
            $stmt1->close();

            $conn->commit();

            $success = "All ($deleted) entries for '$name' deleted successfully.";
            header("Refresh:2; url=remove_book.php");
        } catch (Exception $e) {
            $conn->rollback();
            $error = "Error deleting all entries: " . $e->getMessage();
        }
    }
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
        .container { max-width:800px; margin:120px auto 100px auto; background:white; padding:30px; border-radius:10px; box-shadow:0 8px 20px rgba(0,0,0,0.15); text-align:center; }
        input[type=text] { padding:10px; width:80%; margin:10px 0; }
        input[type=submit] { background:#007bff; color:white; border:none; padding:10px 20px; cursor:pointer; border-radius:5px; margin-top:10px; }
        input[type=submit]:hover { background:#0056b3; }
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
        }
        td {
            background-color: #f9f9f9;
            padding: 14px 20px;
            font-size: 16px;
            color: #333333;
        }
        tr:nth-child(even) td { background-color: #f1f5ff; }
        tr:hover td { background-color: #e3f2fd; transition: background-color 0.3s ease; }
        .message { margin:15px 0; font-weight:bold; }
        .success { color:green; }
        .error { color:red; }
        .btn-delete { background:#dc3545; color:white; border:none; padding:10px 18px; border-radius:5px; cursor:pointer; }
        .btn-delete:hover { background:#b02a37; }
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

    <?php if(!isset($books) && !isset($success)): ?>
        <form method="POST" action="">
            <label>Book Name:</label><br>
            <input type="text" name="name" placeholder="Enter Book Name"><br>
            <input type="submit" name="fetch" value="Fetch Book">
        </form>
    <?php endif; ?>

    <?php if(isset($books)): ?>
        <?php if($details): ?>
            <h3>Book Details</h3>
            <table>
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Author</th>
                        <th>Copies Available</th>
                        <th>Copies in Library</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td><?php echo htmlspecialchars($name); ?></td>
                        <td><?php echo htmlspecialchars($details['author']); ?></td>
                        <td><?php echo htmlspecialchars($details['copies_available']); ?></td>
                        <td><?php echo htmlspecialchars($details['copies_in_library']); ?></td>
                    </tr>
                </tbody>
            </table>
        <?php endif; ?>

       
        <form method="POST" action="">
            <input type="hidden" name="name" value="<?php echo htmlspecialchars($name); ?>">
            <input type="submit" name="delete_all" value="Delete All Entries" class="btn-delete">
            <a href="remove_book.php" style="margin-left:15px; color:#007bff;">Cancel</a>
        </form>
    <?php endif; ?>
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
        </div>
    </div>
    <div class="footer-bottom">
        &copy; 2025 Library Management System
    </div>
</footer>

</body>
</html>
