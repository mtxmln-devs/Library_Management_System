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

$message = "";


$sqlUser = "SELECT UserID, Name, Email, Phone, Address FROM User WHERE Name = ?";
$stmtUser = $conn->prepare($sqlUser);
$stmtUser->bind_param("s", $loggedInUserName);
$stmtUser->execute();
$resultUser = $stmtUser->get_result();
$user = $resultUser->fetch_assoc();
$stmtUser->close();

$userId = $userID;
 
$sql = "SELECT f.FineID, f.RecordID, f.FineAmount, 
               CASE WHEN f.Paid = 1 THEN 'Paid' ELSE 'Unpaid' END AS Remarks,
               f.PaymentDate
        FROM fines f
        WHERE f.UserID = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $userId);
$stmt->execute();
$result = $stmt->get_result();

$fines = [];
while ($row = $result->fetch_assoc()) {
    $fines[] = $row;
}
$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Library Management System</title>
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
        
        .icon-user{
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
            margin-right: 10px;
            font-size: 18px;
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
            color: #777;
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
        
        .fines-content {
            padding: 20px 30px;
        }
        
        .fines-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }
        
        .page-title {
            font-size: 24px;
            font-weight: bold;
            margin-bottom: 20px;
        }
        
        .pay-button {
            background-color: #4a235a;
            color: white;
            border: none;
            padding: 8px 20px;
            border-radius: 5px;
            cursor: pointer;
            font-weight: bold;
        }
        
        .pay-button:hover {
            background-color: #5e2d73;
        }
        
        .fines-table {
            width: 100%;
            height: 350px;
            border-collapse: collapse;
            margin-bottom: 30px;
        }
        
        .fines-table th {
            background-color: #4a235a;
            color: white;
            padding: 12px 15px;
            text-align: left;
            font-weight: bold;
        }
        
        .fines-table td {
            padding: 12px 15px;
            border: 1px solid #ddd;
            background-color: white;
        }
        
        .fines-table tr:hover td {
            background-color: #f9f9f9;
        }
        
        .footer {
            text-align: center;
            margin-top: 100px;
            letter-spacing: 0.7px;
            padding: 15px;
            color: black;
            font-size: 13px;
        }

        .pay-button {
            text-decoration: none;
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
        .icon-search::before {
            content: "🔍";
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

    <div class="main-content">
        <div class="fines-header">
            <h1 class="page-title">Fines</h1>
        </div>

        <?php if (isset($_GET['status'])): ?>
            <div style="margin-bottom: 15px; color: <?= $_GET['status'] === 'paid' ? 'green' : 'red' ?>;">
                <?= $_GET['status'] === 'paid' ? 'Fine paid successfully.' : 'Insufficient balance in wallet.' ?>
            </div>
        <?php endif; ?>


        <table class="fines-table">
            <thead>
                <tr>
                    <th>FineID</th>
                    <th>RecordID</th>
                    <th>Amount</th>
                    <th>Remarks</th>
                    <th>Payment Date</th>
                    <th>Payment</th>
                </tr>
            </thead>
            <tbody>
                <?php if (count($fines) > 0): ?>
                    <?php foreach ($fines as $fine): ?>
                        <tr>
                            <td><?= htmlspecialchars($fine['FineID']) ?></td>
                            <td><?= htmlspecialchars($fine['RecordID']) ?></td>
                            <td>₱<?= number_format($fine['FineAmount'], 2) ?></td>
                            <td><?= htmlspecialchars($fine['Remarks']) ?></td>
                            <td><?= $fine['PaymentDate'] ? htmlspecialchars($fine['PaymentDate']) : 'N/A' ?></td>
                            <td>
                                <?php if ($fine['Remarks'] === 'Unpaid'): ?>
                                    <a 
                                        href="wallet.php?action=payfine&fine_id=<?= $fine['FineID'] ?>&amount=<?= $fine['FineAmount'] ?>" 
                                        class="pay-button"
                                    >
                                        Pay
                                    </a>
                                <?php else: ?>
                                    <span style="color: green; font-weight: bold;">✔</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr><td colspan="6" style="text-align: center;">No fines found.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>

        <div class="footer">
                All Rights Reserved 2025 | Library Management System
            </div>
    </div>
</div>
</body>
</html>
