<?php
session_start();
include('db_connect.php');

// Handle search or fetch all
if (isset($_GET['query']) || isset($_GET['fetchAll'])) {
    $search = isset($_GET['query']) ? $_GET['query'] : '';

    if ($search != '') {
        $stmt = $conn->prepare("SELECT * FROM book_details WHERE name LIKE ? OR author LIKE ?");
        $likeSearch = "%" . $search . "%";
        $stmt->bind_param("ss", $likeSearch, $likeSearch);
    } else {
        $stmt = $conn->prepare("SELECT * FROM book_details");
    }

    $stmt->execute();
    $result = $stmt->get_result();

    $books = [];
    while ($row = $result->fetch_assoc()) {
        $books[] = $row;
    }

    echo json_encode($books);
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Search Books | Library</title>
<style>
/* ===== General Reset ===== */
* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}

/* ===== Body Layout ===== */
body {
    font-family: Arial, sans-serif;
    background: #f0f4f7;
    color: #333;
    display: flex;
    flex-direction: column;
    min-height: 100vh;
}

/* ===== Header ===== */
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

/* ===== Main Container ===== */
.container {
    flex: 1;
    width: 80%;
    max-width: 1400px;
    margin: 40px auto;
    background: #fff;
    padding: 40px;
    border-radius: 12px;
    box-shadow: 0 6px 25px rgba(0,0,0,0.1);
}

h2 {
    text-align: center;
    color: #333;
    margin-bottom: 30px;
}

/* ===== Search Box ===== */
#searchBox {
    padding: 12px 15px;
    border-radius: 8px;
    border: 1px solid #aaa;
    width: 100%;
    max-width: 400px;
    margin-bottom: 30px;
    display: block;
    margin-left: auto;
    margin-right: auto;
    font-size: 16px;
    outline: none;
    transition: 0.3s;
}
#searchBox:focus {
    border-color: #000;
    box-shadow: 0 0 10px rgba(0,0,0,0.15);
}

/* ===== Table ===== */
.table-wrapper {
    overflow-x: auto;
}

table {
    width: 100%;
    border-collapse: collapse;
    font-size: 16px;
    background: white;
    border-radius: 8px;
    overflow: hidden;
}

th, td {
    padding: 14px 18px;
    text-align: center;
}

th {
    background: #000;
    color: white;
}
tr:nth-child(even) {
    background-color: #f7f9fc;
}
tr:hover {
    background-color: #e6f0ff;
}

/* ===== Borrow Button ===== */
.borrow-btn {
    background-color: #13357c;
    color: white;
    border: none;
    border-radius: 8px;
    padding: 8px 16px;
    font-size: 14px;
    cursor: pointer;
    transition: 0.3s;
}
.borrow-btn:hover {
    background-color: #000;
}

/* ===== Highlight Match ===== */
.highlight {
    background: #fff475;
    font-weight: bold;
}

/* ===== No Results ===== */
.no-results {
    text-align: center;
    font-style: italic;
    color: #555;
    padding: 20px;
}

/* ===== Footer ===== */
footer {
    background: #000;
    color: white;
    padding: 40px 30px 20px 30px;
    margin-top: auto;
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

<!-- ===== Header ===== -->
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

<!-- ===== Main Content ===== -->
<div class="container">
    <h2>Search Books</h2>
    <input type="text" id="searchBox" placeholder="Search by Book Name or Author...">

    <div class="table-wrapper">
        <table id="booksTable">
            <thead>
                <tr>
                    <th>Book Name</th>
                    <th>Author</th>
                    <th>Copies Available</th>
                    <th>Copies In Library</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody></tbody>
        </table>
    </div>
</div>

<!-- ===== Footer ===== -->
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

<script>
function highlightText(text, query) {
    if (!query) return text;
    const regex = new RegExp(`(${query})`, 'gi');
    return text.replace(regex, '<span class="highlight">$1</span>');
}

const tableBody = document.querySelector('#booksTable tbody');
const searchBox = document.getElementById('searchBox');

function fetchBooks(query = '') {
    fetch('search.php?query=' + encodeURIComponent(query))
        .then(response => response.json())
        .then(data => {
            tableBody.innerHTML = '';
            if (data.length === 0) {
                tableBody.innerHTML = '<tr><td colspan="5" class="no-results">No books found</td></tr>';
                return;
            }
            data.forEach(book => {
                const row = `<tr>
                    <td>${highlightText(book.name, query)}</td>
                    <td>${highlightText(book.author, query)}</td>
                    <td>${book.copies_available}</td>
                    <td>${book.copies_in_library}</td>
                    <td>
                        <form action="borrow.php" method="GET">
                            <input type="hidden" name="name" value="${book.name}">
                            <input type="hidden" name="author" value="${book.author}">
                            <input type="hidden" name="copies_available" value="${book.copies_available}">
                            <input type="hidden" name="copies_in_library" value="${book.copies_in_library}">
                            <button type="submit" class="borrow-btn">Borrow</button>
                        </form>
                    </td>
                </tr>`;
                tableBody.innerHTML += row;
            });
        });
}

fetchBooks();

searchBox.addEventListener('input', () => {
    fetchBooks(searchBox.value.trim());
});
</script>

</body>
</html>
