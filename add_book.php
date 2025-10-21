<?php
include 'db_connect.php'; // Connect to database

// Generate a unique ISBN automatically
date_default_timezone_set('Asia/Kolkata');
$unique_isbn = "ISBN" . date("YmdHis") . rand(100, 999); // Example: ISBN20251020143045987

if (isset($_POST['add'])) {
    $book_title = $_POST['title'];
    $author = $_POST['author'];
    $category = $_POST['category'];
    $isbn = $_POST['isbn'];
    $quantity = $_POST['quantity'];

    // Insert query
    $query = "INSERT INTO books (title, author, category, isbn, quantity) 
              VALUES ('$book_title', '$author', '$category', '$isbn', '$quantity')";
    $result = mysqli_query($conn, $query);

    if ($result) {
        echo "<script>alert('✅ Book added successfully!');</script>";
    } else {
        echo "<script>alert('❌ Error adding book.');</script>";
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
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }

        .form-container {
            background-color: #f9f9f9;
            color: #000;
            padding: 40px;
            border-radius: 15px;
            width: 380px;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.15);
            text-align: center;
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
    </style>
</head>
<body>

    <div class="form-container">
        <h2>Add a New Book</h2>
        <form method="POST" action="">
            <input type="text" name="title" placeholder="Book Title" required><br>
            <input type="text" name="author" placeholder="Author" required><br>
            <input type="text" name="category" placeholder="Category" required><br>
            <input type="text" name="isbn" value="<?php echo $unique_isbn; ?>" readonly><br>
            <input type="number" name="quantity" placeholder="Quantity" required><br>
            <button type="submit" name="add">Add Book</button>
        </form>

        <a href="librarian_dashboard.php">← Back to Dashboard</a>
    </div>

</body>
</html>
