<?php
$host = "localhost";
$username = "root";
$password = "";
$database = "book_management";

$conn = new mysqli($host, $username, $password, $database);

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

$sqlUser = "SELECT UserID, Name, Email, Phone, Address FROM user WHERE Name = ?";
$stmtUser = $conn->prepare($sqlUser);
$stmtUser->bind_param("s", $loggedInUserName);
$stmtUser->execute();
$resultUser = $stmtUser->get_result();
$user = $resultUser->fetch_assoc();
$stmtUser->close();

if ($user) {
    $userId = $user['UserID'];
} else {
    die("User not found.");
}

$message = "";

$stmtWallet = $conn->prepare("SELECT WalletID, Balance FROM wallet WHERE UserID = ?");
$stmtWallet->bind_param("i", $userId);
$stmtWallet->execute();
$resultWallet = $stmtWallet->get_result();

if ($resultWallet->num_rows > 0) {
    $wallet = $resultWallet->fetch_assoc();
    $walletId = $wallet['WalletID'];
    $walletBalance = floatval($wallet['Balance']);
} else {
    $stmtInsertWallet = $conn->prepare("INSERT INTO wallet (UserID, Balance) VALUES (?, 0.00)");
    $stmtInsertWallet->bind_param("i", $userId);
    $stmtInsertWallet->execute();
    $walletId = $conn->insert_id;
    $walletBalance = 0.00;
    $stmtInsertWallet->close();
}
$stmtWallet->close();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['cashInAmount'])) {
    $amount = floatval($_POST['cashInAmount']);
    if ($amount > 0) {
        $stmtUpdateWallet = $conn->prepare("UPDATE wallet SET Balance = Balance + ? WHERE WalletID = ?");
        $stmtUpdateWallet->bind_param("di", $amount, $walletId);
        $stmtUpdateWallet->execute();
        $stmtUpdateWallet->close();

        $walletBalance += $amount;
        $message .= "Cash in successful. ₱" . number_format($amount, 2) . " added.<br>";
    } else {
        $message = "Invalid amount entered.";
    }
}

if (isset($_GET['action']) && $_GET['action'] === 'payfine' && isset($_GET['fine_id']) && isset($_GET['amount'])) {
    $fineId = (int)$_GET['fine_id'];
    $amount = (float)$_GET['amount'];

    if ($walletBalance >= $amount) {
        $walletBalance -= $amount;
        $stmtUpdateWallet = $conn->prepare("UPDATE wallet SET Balance = ? WHERE WalletID = ?");
        $stmtUpdateWallet->bind_param("di", $walletBalance, $walletId);
        $stmtUpdateWallet->execute();
        $stmtUpdateWallet->close();

        $stmtPayFine = $conn->prepare("UPDATE fines SET Paid = 1, PaymentDate = NOW() WHERE FineID = ? AND UserID = ?");
        $stmtPayFine->bind_param("ii", $fineId, $userId);
        $stmtPayFine->execute();
        $stmtPayFine->close();

        header("Location: finesBooks.php?status=paid");
        exit;
    } else {
        header("Location: finesBooks.php?status=insufficient");
        exit;
    }
}

$conn->close();
?>




<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Library Management System - My Wallet</title>
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
        .wallet-content {
            padding: 20px 30px;
        }
        .wallet-title {
            font-size: 24px;
            font-weight: bold;
            margin-bottom: 20px;
        }
        .balance-container {
            border: 1px solid #ddd;
            border-radius: 5px;
            background-color: white;
            padding: 0;
            margin-bottom: 30px;
            max-width: 600px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .balance-label {
            background-color: #4a235a;
            color: white;
            padding: 12px 15px;
            font-weight: bold;
            display: inline-block;
            border-top-left-radius: 4px;
            border-bottom-left-radius: 4px;
        }
        .balance-amount {
            font-size: 18px;
            font-weight: bold;
            padding: 0 20px;
            flex-grow: 1;
        }
        .cash-in-btn {
            background-color: #4a235a;
            color: white;
            border: none;
            padding: 10px 15px;
            cursor: pointer;
            margin: 5px;
            border-radius: 3px;
            font-size: 14px;
        }
        .peso-sign {
            font-weight: normal;
        }
        .footer {
            text-align: center;
            margin-top: 50px;
            letter-spacing: 0.7px;
            padding: 15px;
            color: black;
            font-size: 13px;
            position: absolute;
            bottom: 0;
            width: calc(100% - 220px);
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
        .icon-edit::before {
            content: "✏️";
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
            <a href="reserveForm.php" class="menu-item">
                <span class="icon-reservation"></span>
                <span>Reservation</span>
            </a>
            <a href="borrowForm.php" class="menu-item">
                <span class="icon-borrowing"></span>
                <span>Borrowing</span>
            </a>
            <a href="Wallet.php" class="menu-item active">
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

        <div class="wallet-content">
            <h1 class="wallet-title">My Wallet</h1>
            <?php if (!empty($message)): ?>
            <div style="margin-bottom: 20px; padding: 10px 15px; border-left: 5px solid green; background: #e8f5e9; color: #2e7d32; font-size: 14px; max-width: 600px;">
                <?= $message ?>
            </div>
            <?php endif; ?>

            <form method="POST" action="wallet.php">
                <div class="balance-container">
                    <div class="balance-label">Available Balance</div>
                    <div class="balance-amount"><span class="peso-sign">₱</span> <?= number_format($walletBalance, 2) ?></div>
                    <input type="number" name="cashInAmount" placeholder="Enter amount" style="width: 120px; padding: 5px;" step="0.01" min="1" required>
                    <button class="cash-in-btn" type="submit">Cash In</button>
                </div>
            </form>
        </div>
        <div class="footer">
            All Rights Reserved 2025 | Library Management System
        </div>

       
