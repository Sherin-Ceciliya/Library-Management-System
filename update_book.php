<?php
// ======= BACKEND SECTION (PHP) =======
if (isset($_GET['action'])) {
    header('Content-Type: application/json');
    $host = "localhost";
    $user = "root"; 
    $pass = ""; 
    $db = "LMS";

    $conn = new mysqli($host, $user, $pass, $db);
    if ($conn->connect_error) {
        die(json_encode(["success" => false, "message" => "Database connection failed"]));
    }

    // --- SEARCH BOOK BY NAME ---
    if ($_GET['action'] === 'search' && isset($_GET['bookname'])) {
        $bookname = $_GET['bookname'];
        $stmt = $conn->prepare("SELECT * FROM book_details WHERE name = ?");
        $stmt->bind_param("s", $bookname);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $book = $result->fetch_assoc();
            echo json_encode(["success" => true, "book" => $book]);
        } else {
            echo json_encode(["success" => false]);
        }
        $stmt->close();
    }

    // --- UPDATE QUANTITY AND INSERT INTO BOOKS TABLE ---
    if ($_GET['action'] === 'update') {
        $data = json_decode(file_get_contents("php://input"), true);
        $bookname = $data['bookId']; // Using book name as key
        $addQty = $data['addQty'];

        // Fetch current quantity
        $stmt = $conn->prepare("SELECT copies_available FROM book_details WHERE name = ?");
        $stmt->bind_param("s", $bookname);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $newQty = $row['copies_available'] + $addQty;

            // Update book_details table
            $update = $conn->prepare("UPDATE book_details SET copies_available = ? WHERE name = ?");
            $update->bind_param("is", $newQty, $bookname);
            $update->execute();

            // Insert new copies into books table
            $insert = $conn->prepare("INSERT INTO books (name) VALUES (?)");
            for ($i = 0; $i < $addQty; $i++) {
                $insert->bind_param("s", $bookname);
                $insert->execute();
            }

            echo json_encode(["success" => true, "newQuantity" => $newQty]);
        } else {
            echo json_encode(["success" => false]);
        }
    }

    $conn->close();
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Update Book Quantity - LMS</title>
  <style>
    /* ====== GLOBAL ====== */
    body {
      font-family: 'Poppins', Arial, sans-serif;
      background: linear-gradient(135deg, #f0f2f5 0%, #e4e8ee 100%);
      margin: 0;
      padding: 0;
    }

    .container {
      width: 420px;
      background: #fff;
      padding: 30px;
      margin: 80px auto;
      box-shadow: 0 8px 25px rgba(0,0,0,0.2);
      border-radius: 15px;
      text-align: center;
      transition: transform 0.3s ease, box-shadow 0.3s ease;
    }

    .container:hover {
      transform: translateY(-5px);
      box-shadow: 0 12px 30px rgba(0,0,0,0.25);
    }

    h1 {
      color: #13357c;
      font-size: 1.8rem;
      margin-bottom: 25px;
    }

    .search-section {
      margin-bottom: 20px;
    }

    input[type="text"], input[type="number"] {
      padding: 12px;
      width: 75%;
      margin: 10px 0;
      border: 1px solid #ccc;
      border-radius: 8px;
      outline: none;
      transition: all 0.3s ease;
      font-size: 14px;
    }

    input[type="text"]:focus, input[type="number"]:focus {
      border-color: #13357c;
      box-shadow: 0 0 6px rgba(19, 53, 124, 0.3);
    }

    button {
      padding: 10px 18px;
      background-color: #000;
      color: white;
      border: none;
      border-radius: 8px;
      cursor: pointer;
      font-size: 14px;
      font-weight: 500;
      letter-spacing: 0.5px;
      transition: all 0.3s ease;
    }

    button:hover {
      background-color: #13357c;
      transform: scale(1.05);
    }

    #bookDetails {
      background: #fafafa;
      border-radius: 10px;
      padding: 20px;
      margin-top: 15px;
      box-shadow: inset 0 0 8px rgba(0,0,0,0.05);
      text-align: left;
    }

    #bookDetails h3 {
      text-align: center;
      color: #13357c;
      margin-bottom: 15px;
    }

    #bookDetails p {
      margin: 8px 0;
      font-size: 14px;
      color: #333;
    }

    .hidden {
      display: none;
    }

    .message {
      margin-top: 20px;
      font-weight: bold;
      transition: opacity 0.3s ease;
    }

    /* ====== HEADER ====== */
    header {
      background: #000;
      color: white;
      padding: 15px 40px;
      display: flex;
      align-items: center;
      justify-content: space-between;
      box-shadow: 0 3px 10px rgba(0,0,0,0.2);
      position: sticky;
      top: 0;
      z-index: 10;
    }

    header .logo {
      font-size: 26px;
      font-weight: 700;
      letter-spacing: 1px;
    }

    nav a {
      color: white;
      text-decoration: none;
      margin: 0 15px;
      font-weight: 500;
      transition: color 0.3s ease;
    }

    nav a:hover {
      text-decoration: underline;
      color: #fd819f;
    }

    /* ====== FOOTER ====== */
    footer {
      background: #000;
      color: white;
      padding: 40px 40px 25px 40px;
      margin-top: 60px;
      box-shadow: 0 -3px 10px rgba(0,0,0,0.2);
    }

    .footer-container {
      display: flex;
      justify-content: space-between;
      flex-wrap: wrap;
      margin-bottom: 25px;
    }

    .footer-left, .footer-right {
      flex: 1;
    }

    .footer-left p {
      margin: 10px 0;
      margin-left: 80px;
      line-height: 1.6;
    }

    .footer-right {
      text-align: right;
      margin-right: 80px;
    }

    .footer-right a {
      color: white;
      text-decoration: none;
      display: block;
      margin: 10px 0;
      transition: all 0.3s ease;
    }

    .footer-right a:hover {
      text-decoration: underline;
      color: #fd819f;
    }

    .footer-bottom {
      text-align: center;
      border-top: 1px solid rgba(255,255,255,0.2);
      padding-top: 15px;
      font-size: 13px;
      opacity: 0.9;
    }
  </style>
</head>
<body>
     <!-- Header -->
    <header>
        <div class="logo">Library</div>
        <nav>
            <a href="#">Home</a>
            <a href="#new-arrivals">Catalog</a>
            <a href="#events">Events</a>
            <a href="#services">Services</a>
            <a href="logout.php">Logout</a>
        </nav>
    </header>

  <div class="container">
    <h1>📚 Update Quantity</h1>
    <div class="search-section">
      <input type="text" id="searchBook" placeholder="Enter book name">
      <button onclick="searchBook()">Search</button>
    </div>

    <div id="bookDetails" class="hidden">
      <h3>Book Details</h3>
      <p><strong>Book Name:</strong> <span id="bookId"></span></p>
      <p><strong>Author:</strong> <span id="bookAuthor"></span></p>
      <p><strong>Copies Available:</strong> <span id="bookQty"></span></p>
      <p><strong>Copies in Library:</strong> <span id="bookLib"></span></p>

      <input type="number" id="addQty" placeholder="Enter quantity to add" min="1">
      <button onclick="updateQuantity()">Update Quantity</button>
    </div>

    <div id="message" class="message"></div>
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
                <a href="#">Home</a>
                <a href="#new-arrivals">Catalog</a>
                <a href="#events">Events</a>
                <a href="#services">Services</a>
                <a href="login.php">Login</a>
            </div>
        </div>
        <div class="footer-bottom">
            &copy; 2025 Library Management System
        </div>
    </footer>

  <script>
    function searchBook() {
      const name = document.getElementById('searchBook').value.trim();
      const msg = document.getElementById('message');
      msg.textContent = "";

      if (name === "") {
        msg.textContent = "Please enter a book name!";
        msg.style.color = "red";
        return;
      }

      fetch(`update_book.php?action=search&bookname=${encodeURIComponent(name)}`)
        .then(response => response.json())
        .then(data => {
          if (data.success) {
            document.getElementById('bookDetails').classList.remove('hidden');
            document.getElementById('bookId').textContent = data.book.name;
            document.getElementById('bookAuthor').textContent = data.book.author;
            document.getElementById('bookQty').textContent = data.book.copies_available;
            document.getElementById('bookLib').textContent = data.book.copies_in_library;
          } else {
            msg.textContent = "Book not found!";
            msg.style.color = "red";
            document.getElementById('bookDetails').classList.add('hidden');
          }
        })
        .catch(error => console.error("Error:", error));
    }

    function updateQuantity() {
      const id = document.getElementById('bookId').textContent;
      const addQty = parseInt(document.getElementById('addQty').value);
      const msg = document.getElementById('message');

      if (isNaN(addQty) || addQty <= 0) {
        msg.textContent = "Please enter a valid quantity to add.";
        msg.style.color = "red";
        return;
      }

      fetch('update_book.php?action=update', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ bookId: id, addQty: addQty })
      })
      .then(response => response.json())
      .then(data => {
        if (data.success) {
          document.getElementById('bookQty').textContent = data.newQuantity;
          msg.textContent = "✅ Quantity successfully updated!";
          msg.style.color = "green";
          document.getElementById('addQty').value = "";
        } else {
          msg.textContent = "Error updating quantity.";
          msg.style.color = "red";
        }
      })
      .catch(error => console.error("Error:", error));
    }
  </script>
</body>
</html>
