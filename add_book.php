<?php
session_start();
if(!isset($_SESSION['username']) || $_SESSION['role'] != 'librarian') {
    header("Location: login.php");
    exit();
}

include('db_connect.php');

$message = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    $author = $_POST['author'];
    $category = $_POST['category'];
    $quantity = $_POST['quantity'];

    $success = true;

    // Insert summary details into 'bookdetails' table (only once)
    $insertDetails = "INSERT INTO book_details (name, author, copies_available, copies_in_library)
                      VALUES ('$name', '$author',  '$quantity', '$quantity')";
    $resultDetails = mysqli_query($conn, $insertDetails);

    if ($success && $resultDetails) {
        $message = "✅ Book and all $quantity copies added successfully!";
    } else {
        $message = "❌ Error: " . mysqli_error($conn);
    }
    // Insert each copy of the book into 'books' table
    for ($i = 0; $i < $quantity; $i++) {
        $insertBook = "INSERT INTO books (name) VALUES ('$name')";
        $resultBook = mysqli_query($conn, $insertBook);
        if (!$resultBook) {
            $success = false;
            break;
        }
    }

    
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Add Book</title>
    <style>
        
        body {
    font-family: 'Poppins', sans-serif;
    background-color: #fff;
    color: #000;
    margin: 0;
    display: flex;
    flex-direction: column; /* stack header, main, footer */
    min-height: 100vh;
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


        .form-container {
            flex: 1;
    width: 85%;
    max-width: 700px;
    margin: 40px auto;
    background: #fff;
    padding: 40px;
    border-radius: 12px;
    box-shadow: 0 6px 25px rgba(0,0,0,0.1);
        }

        h2 {
            margin-bottom: 25px;
            letter-spacing: 1px;
        }

        input {
            width: 90%;
            padding: 12px;
            margin: 10px 0;
            border: 1px solid #ccc;
            border-radius: 8px;
            background: #fff;
            color: #000;
            font-size: 14px;
        }

        input[readonly] {
            background-color: #eee;
            cursor: not-allowed;
        }

        input:focus {
            outline: none;
            border: 1.5px solid #000;
        }

        button {
            background-color: #000;
            color: #fff;
            border: none;
            padding: 12px;
            border-radius: 8px;
            cursor: pointer;
            transition: 0.3s;
            font-weight: bold;
            width: 95%;
            margin-top: 10px;
        }

        button:hover {
            background-color: #333;
        }

        a {
            display: inline-block;
            margin-top: 15px;
            color: #000;
            text-decoration: none;
            font-weight: 500;
        }

        a:hover {
            text-decoration: underline;
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

    <div class="form-container">
    <h2>Add a New Book</h2>
    <form method="POST" action="">
        <!-- Title -->
        <input type="text" name="name" placeholder="Book Title" required><br>

        <!-- Author -->
        <input type="text" name="author" placeholder="Author" required><br>

        <!-- Category -->
        <input type="text" name="category" placeholder="Category" required><br>

        <!-- ISBN (auto-generated or manually entered) 
        <input type="text" name="isbn" placeholder="ISBN Number" value="<?php echo isset($unique_isbn) ? $unique_isbn : ''; ?>" required><br>
        -->
        <!-- Quantity -->
        <input type="number" name="quantity" placeholder="Quantity" min="1" required><br>

        <!-- Submit Button -->
        <button type="submit" name="add">Add Book</button>
    </form>

    <!-- Display message if exists -->
    <?php if (!empty($message)) : ?>
        <p style="margin-top:10px; color:green; font-weight:bold;"><?php echo $message; ?></p>
    <?php endif; ?>

    <a href="librarian_dashboard.php">← Back to Dashboard</a>
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

</body>
</html>
