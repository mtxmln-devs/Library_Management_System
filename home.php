<?php
session_start();

// Redirect to login if not logged in
if (!isset($_SESSION['UserID'])) {
    header("Location: loginForm.php");
    exit;
}

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "Book_Management";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$userID = $_SESSION['UserID'];
$loggedInUserName = $_SESSION['UserName'] ?? 'Guest';

$borrowedCount = 0;
$reservedCount = 0;
$returnedCount = 0;
$finesCount = 0;

// Borrowed Books Count
$sqlBorrowed = "SELECT COUNT(*) FROM BookRecords WHERE UserID = ? AND Status = 'Borrowed'";
$stmtBorrowed = $conn->prepare($sqlBorrowed);
$stmtBorrowed->bind_param("i", $userID);
$stmtBorrowed->execute();
$stmtBorrowed->bind_result($borrowedCount);
$stmtBorrowed->fetch();
$stmtBorrowed->close();

// Reserved Books Count
$sqlReserved = "SELECT COUNT(*) FROM Reservations WHERE UserID = ? AND Status = 'Reserved'";
$stmtReserved = $conn->prepare($sqlReserved);
$stmtReserved->bind_param("i", $userID);
$stmtReserved->execute();
$stmtReserved->bind_result($reservedCount);
$stmtReserved->fetch();
$stmtReserved->close();

// Returned Books Count
$sqlReturned = "SELECT COUNT(*) FROM BookRecords WHERE UserID = ? AND Status = 'Returned'";
$stmtReturned = $conn->prepare($sqlReturned);
$stmtReturned->bind_param("i", $userID);
$stmtReturned->execute();
$stmtReturned->bind_result($returnedCount);
$stmtReturned->fetch();
$stmtReturned->close();

// Fines Count
$sqlFines = "SELECT COUNT(*) FROM Fines WHERE UserID = ? AND Paid = 0";
$stmtFines = $conn->prepare($sqlFines);
$stmtFines->bind_param("i", $userID);
$stmtFines->execute();
$stmtFines->bind_result($finesCount);
$stmtFines->fetch();
$stmtFines->close();

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Library Management System</title>
    <link rel="icon" href="logo.png" />
    <style>
        * {
            margin: 0; padding: 0; box-sizing: border-box;
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
            position: relative;
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
        .menu-item i {
            font-size: 18px;
        }
        .logout {
            position: absolute;
            bottom: 30px;
            left: 35px;
            display: flex;
            align-items: center;
            font-size: 38px;
            color: white;
            text-decoration: none;
        }
        .logout i {
            margin-right: 10px;
            font-size: 18px;
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
        .search-icon {
            position: absolute;
            top: 50%;
            left: 10px;
            transform: translateY(-50%);
        }
        .user-profile {
            display: flex;
            align-items: center;
            color: white;
        }
        .user-avatar {
            width: 40px;
            height: 40px;
            background-color: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 10px;
        }
        .dashboard {
            padding: 30px;
        }
        .stats-container {
            display: flex;
            gap: 30px;
            flex-wrap: wrap;
        }
        .stat-card {
            flex: 1;
            color: white;
            padding: 20px;
            border-radius: 15px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            min-height: 170px;
            text-decoration: none;
        }
        .stat-card.borrowed {
            background-color: #c0392b;
        }
        .stat-card.reserved {
            background-color: #2980b9;
        }
        .stat-card.returned {
            background-color: #e67e22;
        }
        .stat-card.fines {
            background-color: #27ae60;
        }
        .stat-value {
            font-size: 36px;
            font-weight: bold;
            margin-bottom: 10px;
        }
        .stat-label {
            font-size: 16px;
        }
        .footer {
            text-align: center;
            margin-top: 360px;
            letter-spacing: 0.7px;
            padding: 15px;
            color: black;
            font-size: 13px;
        }
        .icon-home::before { content: "🏠"; }
        .icon-profile::before { content: "👤"; }
        .icon-reservation::before { content: "📑"; }
        .icon-borrowing::before { content: "📚"; }
        .icon-logout::before { content: "↩️"; }
        .icon-wallet::before { content: "💰"; }
        .icon-book::before { content: "📖"; }
        .icon-search::before { content: "🔍"; }
        .icon-user::before { content: "👤"; }
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
                <a href="home.php" class="menu-item active">
                    <span class="icon-home"></span>
                    <span>Home</span>
                </a>
                <a href="profile.php" class="menu-item">
                    <span class="icon-profile"></span>
                    <span>Profile</span>
                </a>
                <a href="reserveForm.php" class="menu-item">
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

            <div class="dashboard">
                <div class="stats-container">
                    <a href="borrowedBooks.php" class="stat-card borrowed">
                        <div class="stat-value"><?php echo htmlspecialchars($borrowedCount); ?></div>
                        <div class="stat-label">Borrowed Books</div>
                    </a>
                    <a href="reservedBooks.php" class="stat-card reserved">
                        <div class="stat-value"><?php echo htmlspecialchars($reservedCount); ?></div>
                        <div class="stat-label">Reserved Books</div>
                    </a>
                    <a href="returnedBooks.php" class="stat-card returned">
                        <div class="stat-value"><?php echo htmlspecialchars($returnedCount); ?></div>
                        <div class="stat-label">Returned Books</div>
                    </a>
                    <a href="finesBooks.php" class="stat-card fines">
                        <div class="stat-value"><?php echo htmlspecialchars($finesCount); ?></div>
                        <div class="stat-label">Fines</div>
                    </a>
                </div>
            </div>

            <div class="footer">
                All Rights Reserved 2025 | Library Management System
            </div>
        </div>
    </div>
</body>
</html>
