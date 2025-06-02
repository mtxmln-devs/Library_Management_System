<?php
 
$host = 'localhost';
$user = 'root';
$password = '';  
$dbname = 'Book_Management';

$conn = new mysqli($host, $user, $password, $dbname);

session_start();

// Redirect to login if not logged in
if (!isset($_SESSION['UserID'])) {
    header("Location: loginForm.php");
    exit;
}

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$userID = $_SESSION['UserID'];
$loggedInUserName = $_SESSION['UserName'] ?? 'Guest';


$message = "";

 
$sqlUser = "SELECT UserID, Name, Email, Phone, Address FROM User WHERE Name = ?";
$stmtUser = $conn->prepare($sqlUser);
$stmtUser->bind_param("s", $loggedInUserName);
$stmtUser->execute();
$resultUser = $stmtUser->get_result();
$user = $resultUser->fetch_assoc();
$stmtUser->close();

 
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $UserId = intval($_POST['UserId'] ?? 0);
    $bookId = intval($_POST['bookId'] ?? 0);
    $quantity = intval($_POST['quantity'] ?? 0);

    if ($UserId <= 0 || $bookId <= 0 || $quantity <= 0) {
        $message = "Please enter valid UserID, BookID, and Quantity.";
    } else {
         
        $stmt = $conn->prepare("SELECT CopiesAvailable FROM Book WHERE BookID = ?");
        $stmt->bind_param("i", $bookId);
        $stmt->execute();
        $stmt->bind_result($copiesAvailable);
        if ($stmt->fetch()) {
            if ($copiesAvailable >= $quantity) {
                $stmt->close();

                $reservationDate = date('Y-m-d');
                $status = "Reserved";

                 
                $stmt = $conn->prepare("INSERT INTO Reservations (UserID, BookID, ReservationDate, Status) VALUES (?, ?, ?, ?)");
                $stmt->bind_param("iiss", $UserId, $bookId, $reservationDate, $status);
                $stmt->execute();

                if ($stmt->affected_rows > 0) {
                    $stmt->close();

                     
                    $stmt = $conn->prepare("UPDATE Book SET CopiesAvailable = CopiesAvailable - ? WHERE BookID = ?");
                    $stmt->bind_param("ii", $quantity, $bookId);
                    $stmt->execute();

                    $message = "Reservation successful!";
                } else {
                    $message = "Failed to record reservation.";
                }
            } else {
                $message = "Not enough copies available.";
            }
        } else {
            $message = "Book not found.";
        }
        $stmt->close();
    }
}

 
$sql = "SELECT BookID, ISBN, Title, Author, Category, Publisher, CopiesAvailable FROM Book;";
$books = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <title>Library Management System - Reservation</title>
    <link rel="icon" href="logo.png" />
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: Arial, sans-serif;
        }
        
        body {
            background-color: #f5f5f5;
        }
        
        .container {
            display: flex;
            min-height: 100vh;
        }
        
        .sidebar {
            width: 220px;
            background-color: #4a235a;
            color: white;
            padding: 20px;
            min-height: 100vh;
        }
        
        .sidebar .logo {
            display: flex;
            align-items: center;
            margin-bottom: 20px;
        }
        
        .sidebar .logo-icon {
            font-size: 24px;
            margin-right: 10px;
        }
        
        .sidebar .logo-text {
            font-size: 14px;
            font-weight: bold;
        }
        
        .welcome-section {
            display: flex;
            align-items: center;
            margin: 30px 0;
        }
        
        .profile-avatar {
            width: 50px;
            height: 50px;
            background-color: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 10px;
        }
        
        .icon-user {
            font-size: 25px;
        }
        
        .welcome-text h2 {
            font-size: 14px;
            margin-bottom: 5px;
        }
        
        .welcome-text h3 {
            font-size: 20px;
            font-weight: bold;
        }
        
        .menu {
            margin-top: 20px;
        }
        
        .menu-item {
            display: flex;
            align-items: center;
            padding: 20px 15px;
            margin-bottom: 5px;
            cursor: pointer;
            border-radius: 5px;
            color: white;
            text-decoration: none;
        }
        
        .menu-item span:first-child {
            margin-right: 12px; 
        }

        .menu-item:hover {
            background-color: rgba(255, 255, 255, 0.1);
        }
        
        .menu-item.active {
            background-color: rgba(255, 255, 255, 0.2);
        }
        
        .logout {
            position: absolute;
            bottom: 30px;
            left: 35px;
            display: flex;
            align-items: center;
            cursor: pointer;
            font-size: 38px;
            text-decoration: none;
        }
        
        .main-content {
            flex: 1;
        }
        
        .top-bar {
            background-color: #4a235a;
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px 30px;
        }
        
        .search-box {
            flex: 1;
            max-width: 400px;
            position: relative;
        }
        
        .search-box input {
            width: 100%;
            padding: 10px 15px;
            border: none;
            border-radius: 5px;
            outline: none;
        }
        
        .user-profile {
            display: flex;
            align-items: center;
            color: white;
        }
        
        .reservation-content {
            padding: 20px 30px;
        }
        
        .reservation-form {
        background-color: white;
        padding: 20px;
        border-radius: 5px;
        margin-bottom: 20px;
        text-align: center;  
    }
        
    .reservation-form input {
        width: 100%;
        padding: 15px;
        margin-bottom: 10px;
        border: 1px solid #ddd;
        border-color: black;
        border-radius: 4px;
        margin-bottom: 20px;
    }

    .reserve-button {
        background-color: #4a235a;
        color: white;
        border: none;
        padding: 15px 35px;
        border-radius: 5px;
        cursor: pointer;
        display: inline-block;  
    }
        
        .books-table {
            width: 100%;
            border-collapse: collapse;
        }
        
        .books-table th {
            background-color: #4a235a;
            color: white;
            padding: 12px 15px;
            text-align: left;
        }
        
        .books-table td {
            padding: 12px 15px;
            border: 1px solid #ddd;
            background-color: white;
        }
        
        .footer {
            text-align: center;
            margin-top: 130px;
            letter-spacing: 0.7px;
            padding: 15px;
            color: black;
            font-size: 13px;
        }

         
        .icon-home::before {
            content: "🏠";
        }
        .icon-profile::before {
            content: "👤";
        }
        .icon-reservation::before {
            content: "📑";
        }
        .icon-borrowing::before {
            content: "📚";
        }
        .icon-wallet::before {
            content: "💰";
        }
        .icon-logout::before {
            content: "↩️";
        }
        .icon-book::before {
            content: "📖";
        }
        .icon-user::before {
            content: "👤";
        }
    </style>
</head>
<body>
<div class="container">
    <div class="sidebar">
         
        <div class="logo">
            <span class="icon-book logo-icon"></span>
            <div class="logo-text">
                LIBRARY<br>MANAGEMENT SYSTEM
            </div>
        </div>
        <div class="welcome-section">
            <div class="profile-avatar">
                <span class="icon-user"></span>
            </div>
            <div class="welcome-text">
                <h2>Welcome,</h2>
                <h3><?php echo htmlspecialchars($loggedInUserName); ?>!</h3>
            </div>
        </div>
        <div class="menu">
            <a href="home.php" class="menu-item">
                <span class="icon-home"></span>
                <span>Home</span>
            </a>
            <a href="profile.php" class="menu-item">
                <span class="icon-profile"></span>
                <span>Profile</span>
            </a>
            <a href="reserveForm.php" class="menu-item active">
                <span class="icon-reservation"></span>
                <span>Reservation</span>
            </a>
            <a href="borrowForm.php" class="menu-item">
                <span class="icon-borrowing"></span>
                <span>Borrowing</span>
            </a>
            <a href="wallet.php" class="menu-item">
                <span class="icon-wallet"></span>
                <span>My Wallet</span>
            </a>
        </div>
        <a href="index.php" class="logout">
            <span class="icon-logout"></span>
        </a>
    </div>

    <div class="main-content">
        <div class="top-bar">
            <div class="search-box">
                <form method="get" action="search.php" class="search-box">
                    <input type="text" name="q" placeholder="Search books...">
                </form>
            </div>
            <div class="user-profile">
                <span><?php echo htmlspecialchars($loggedInUserName); ?></span>
            </div>
        </div>

        <div class="reservation-content">
            <div class="reservation-form">
                <?php if ($message): ?>
                    <p style="color: red; font-weight: bold;"><?php echo htmlspecialchars($message) ?></p>
                <?php endif; ?>
                <form method="post" action="">
                    <input type="number" name="UserId" placeholder="UserID" required />
                    <input type="number" name="bookId" placeholder="BookID" required />
                    <input type="number" name="quantity" placeholder="Quantity" min="1" required />
                    <button type="submit" class="reserve-button">RESERVE</button>
                </form>
            </div>

            <table class="books-table">
                <thead>
                    <tr>
                        <th>BookID</th>
                        <th>ISBN</th>
                        <th>Title</th>
                        <th>Author</th>
                        <th>Category</th>
                        <th>Publisher</th>
                        <th>Copies</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $books->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['BookID']) ?></td>
                        <td><?php echo htmlspecialchars($row['ISBN']) ?></td>
                        <td><?php echo htmlspecialchars($row['Title']) ?></td>
                        <td><?php echo htmlspecialchars($row['Author']) ?></td>
                        <td><?php echo htmlspecialchars($row['Category']) ?></td>
                        <td><?php echo htmlspecialchars($row['Publisher']) ?></td>
                        <td><?php echo htmlspecialchars($row['CopiesAvailable']) ?></td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>

        <div class="footer">
            All Rights Reserved 2025 | Library Management System
        </div>
    </div>
</div>
</body>
</html>

<?php
$conn->close();
?>
