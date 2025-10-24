<?php
session_start();
include('db_connect.php');

// Ensure user logged in
if (!isset($_SESSION['username']) || $_SESSION['role'] != 'user') {
    header("Location: login.php");
    exit();
}

$username = $_SESSION['username'];

// Fetch user_id
$stmtUser = $conn->prepare("SELECT user_id FROM users WHERE username = ? LIMIT 1");
$stmtUser->bind_param("s", $username);
$stmtUser->execute();
$resUser = $stmtUser->get_result();
if ($resUser && $uRow = $resUser->fetch_assoc()) {
    $user_id = $uRow['user_id'];
} else {
    die("User not found.");
}
$stmtUser->close();

// Get book info from GET
$book_name = $_GET['name'] ?? '';
$author = $_GET['author'] ?? '';

if ($book_name == '') {
    header("Location: search.php");
    exit();
}

// Fetch book details
$stmtBook = $conn->prepare("SELECT name, author, copies_available, copies_in_library FROM book_details WHERE name = ? AND author = ? LIMIT 1");
$stmtBook->bind_param("ss", $book_name, $author);
$stmtBook->execute();
$resBook = $stmtBook->get_result();
if ($resBook && $book = $resBook->fetch_assoc()) {
    $copies_available = $book['copies_available'];
    $copies_in_library = $book['copies_in_library'];
} else {
    die("Book not found.");
}
$stmtBook->close();

// Dates
$issue_date = date('Y-m-d');
$end_date = date('Y-m-d', strtotime('+30 days')); // renamed return_date to end_date

// Handle borrow POST
$success = false;
$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['borrow'])) {
    if ($copies_available <= 0) $errors[] = "No copies available.";

    if (empty($errors)) {
        $conn->begin_transaction();
        try {
            // Get available book_id
            $stmtBookId = $conn->prepare("SELECT book_id FROM books WHERE name = ? AND book_id NOT IN (SELECT book_id FROM status WHERE status='borrow') LIMIT 1");
            $stmtBookId->bind_param("s", $book_name);
            $stmtBookId->execute();
            $resId = $stmtBookId->get_result();
            if ($resId && $rowId = $resId->fetch_assoc()) {
                $book_id = $rowId['book_id'];
            } else {
                throw new Exception("No available book copy.");
            }
            $stmtBookId->close();

            // Insert into status
            $stmtStatus = $conn->prepare("INSERT INTO status (book_id, user_id, issue_date, status) VALUES (?, ?, ?, 'borrow')");
            $stmtStatus->bind_param("iss", $book_id, $user_id, $issue_date);
            $stmtStatus->execute();
            $stmtStatus->close();

            // Update book_details
            $stmtUpd = $conn->prepare("UPDATE book_details SET copies_available = copies_available - 1 WHERE name = ? AND author = ?");
            $stmtUpd->bind_param("ss", $book_name, $author);
            $stmtUpd->execute();
            $stmtUpd->close();

            $conn->commit();
            $success = true;
        } catch (Exception $e) {
            $conn->rollback();
            $errors[] = $e->getMessage();
        }
    }
}
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Borrow Book | Library</title>
<style>
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
header .logo { font-size: 24px; font-weight: bold; }
nav a { color: white; text-decoration: none; margin: 0 15px; font-weight: 500; }
nav a:hover { text-decoration: underline; }

/* ===== Main Container ===== */
.container {
    flex: 1;
    width: 85%;
    max-width: 700px;
    margin: 40px auto;
    background: #fff;
    padding: 40px;
    border-radius: 12px;
    box-shadow: 0 6px 25px rgba(0,0,0,0.1);
}

/* ===== Headings ===== */
h2 { text-align: center; color: #13357c; margin-bottom: 30px; }

/* ===== Info Row ===== */
.info-row { display: flex; justify-content: space-around; margin-bottom: 20px; }
.stat { background: #f7f9fc; padding: 10px 15px; border-radius: 8px; font-weight: 700; }

/* ===== Form ===== */
.form-row { margin-bottom: 15px; }
label { display: block; margin-bottom: 5px; font-weight: 600; }
input[type="text"], input[type="date"] {
    width: 100%;
    padding: 10px;
    border-radius: 8px;
    border: 1px solid #aaa;
    outline: none;
}
input[readonly] { background: #f5f5f5; }

/* ===== Buttons ===== */
.buttons-row {
    display: flex;
    gap: 15px;
    justify-content: center;
    margin-top: 10px;
}
.primary-btn {
    flex: 1;
    background: #13357c;
    color: #fff;
    padding: 12px 0;
    border-radius: 10px;
    border: none;
    font-size: 16px;
    font-weight: 600;
    cursor: pointer;
    transition: 0.3s;
}
.primary-btn:hover { background: #0d245f; }
.primary-btn[disabled] { opacity: 0.6; cursor: not-allowed; }
.cancel-btn {
    flex: 1;
    display: inline-block;
    text-align: center;
    padding: 12px 0;
    background: #f0f4f7;
    border-radius: 10px;
    color: #13357c;
    font-weight: 600;
    text-decoration: none;
    transition: 0.3s;
}
.cancel-btn:hover { background: #e0e7f0; }

/* ===== Messages ===== */
.success-box { background: #e6ffed; color: #114b2a; padding: 12px; border-radius: 8px; margin-bottom: 15px; }
.error-box { background: #fff0f0; color: #7a1313; padding: 12px; border-radius: 8px; margin-bottom: 15px; }

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

<div class="container">
<?php if($success): ?>
    <div class="success-box">Borrow successful! Book reserved.</div>
    <h2><?php echo htmlspecialchars($book_name); ?></h2>
    <p><strong>Author:</strong> <?php echo htmlspecialchars($author); ?></p>
    <p><strong>Book ID:</strong> <?php echo $book_id; ?></p>
    <p><strong>Borrower:</strong> <?php echo htmlspecialchars($username); ?></p>
    <p><strong>Issue Date:</strong> <?php echo $issue_date; ?></p>
    <p><strong>End Date:</strong> <?php echo $end_date; ?></p>
    <p style="margin-top:15px;"><a href="search.php" class="cancel-btn">Back to catalogue</a></p>
<?php else: ?>
    <?php if(!empty($errors)): ?>
        <div class="error-box"><?php foreach($errors as $err) echo htmlspecialchars($err)."<br>"; ?></div>
    <?php endif; ?>
    <h2><?php echo htmlspecialchars($book_name); ?></h2>
    <p><strong>Author:</strong> <?php echo htmlspecialchars($author); ?></p>
    <div class="info-row">
        <div class="stat">Copies available: <?php echo $copies_available; ?></div>
        <div class="stat">Total copies: <?php echo $copies_in_library; ?></div>
    </div>
    <form method="POST" class="borrow-form">
        <div class="form-row">
            <label>Name</label>
            <input type="text" value="<?php echo htmlspecialchars($username); ?>" readonly>
        </div>
        <div class="form-row">
            <label>Issue Date</label>
            <input type="date" value="<?php echo $issue_date; ?>" readonly>
        </div>
        <div class="form-row">
            <label>End Date</label>
            <input type="date" value="<?php echo $end_date; ?>" readonly>
        </div>
        <div class="form-row buttons-row">
            <button type="submit" name="borrow" class="primary-btn" <?php echo ($copies_available<=0?'disabled':''); ?>>
                <?php echo ($copies_available<=0?'Not Available':'Borrow'); ?>
            </button>
            <a href="search.php" class="cancel-btn">Cancel</a>
        </div>
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
            <a href="logout.php">Logout</a>
        </div>
    </div>
    <div class="footer-bottom">
        &copy; <?php echo date("Y"); ?> Library Management System
    </div>
</footer>

</body>
</html>
