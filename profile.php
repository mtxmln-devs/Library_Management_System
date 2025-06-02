<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "Book_Management";

 
$conn = new mysqli($servername, $username, $password, $dbname);

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


 
$sqlUser = "SELECT UserID, Name, Email, Phone, Address FROM User WHERE Name = ?";
$stmtUser = $conn->prepare($sqlUser);
$stmtUser->bind_param("s", $loggedInUserName);
$stmtUser->execute();
$resultUser = $stmtUser->get_result();
$user = $resultUser->fetch_assoc();
$stmtUser->close();

 
if ($user) {
    $sqlRecords = "SELECT BookRecords.RecordID, Book.BookID, Book.Title AS BookTitle, BookRecords.BorrowDate, BookRecords.DueDate, BookRecords.ReturnDate, BookRecords.Status 
                           FROM BookRecords 
                           INNER JOIN Book ON BookRecords.BookID = Book.BookID
                           WHERE BookRecords.UserID = ?";
    $stmtRecords = $conn->prepare($sqlRecords);
    $stmtRecords->bind_param("i", $user['UserID']);
    $stmtRecords->execute();
    $resultRecords = $stmtRecords->get_result();
    $records = $resultRecords->fetch_all(MYSQLI_ASSOC);
    $stmtRecords->close();
} else {
    $records = [];
}

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
                .profile-content {
                    padding: 20px 30px;
                }
                .profile-header {
                    display: flex;
                    margin-bottom: 30px;
                }
                .large-avatar {
                    width: 120px;
                    height: 120px;
                    background-color: #e0e0e0;
                    border-radius: 50%;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    margin-right: 30px;
                    position: relative;
                }
                .edit-icon {
                    position: absolute;
                    bottom: 5px;
                    right: 5px;
                    background-color: white;
                    width: 25px;
                    height: 25px;
                    border-radius: 50%;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    cursor: pointer;
                    transition: transform 0.2s ease;
                    text-decoration: none;
                }
                .edit-icon:hover {
                    transform: scale(1.1);
                }
                .profile-info {
                    padding-top: 10px;
                }
                .profile-info h2 {
                    font-size: 24px;
                    margin-bottom: 10px;
                }
                .profile-info p {
                    font-size: 14px;
                    color: #555;
                    margin-bottom: 5px;
                }
                .records-section h3 {
                    font-size: 20px;
                    margin-bottom: 15px;
                }
                .records-table {
                    width: 100%;
                    border-collapse: collapse;
                }
                .records-table th {
                    background-color: #4a235a;
                    color: white;
                    padding: 15px 15px;
                    text-align: left;
                }
                .records-table td {
                    padding: 25px 15px;
                    border: 1px solid #ddd;
                    background-color: white;
                }
                .footer {
                    text-align: center;
                    margin-top: 50px;
                    letter-spacing: 0.7px;
                    padding: 15px;
                    color: black;
                    font-size: 13px;
                }
                .icon-home::before { content: "🏠"; }
                .icon-profile::before { content: "👤"; }
                .icon-reservation::before { content: "📑"; }
                .icon-borrowing::before { content: "📚"; }
                .icon-wallet::before { content: "💰"; }
                .icon-logout::before { content: "↩️"; }
                .icon-book::before { content: "📖"; }
                .icon-user::before { content: "👤"; }
                .icon-edit::before { content: "✏️"; }
    </style>
</head>
<body>
    <div class="container">
        <div class="sidebar">
            <div class="logo">
                <span class="icon-book logo-icon"></span>
                <div class="logo-text">LIBRARY<br>MANAGEMENT SYSTEM</div>
            </div>
            <div class="welcome-section">
                <div class="profile-avatar"><span class="icon-user"></span></div>
                <div class="welcome-text">
                    <h2>Welcome,</h2>
                    <h3><?php echo htmlspecialchars($loggedInUserName); ?>!</h3>
                </div>
            </div>
            <div class="menu">
                <a href="home.php
                " class="menu-item"><span class="icon-home"></span><span>Home</span></a>
                <a href="profile.php" class="menu-item active"><span class="icon-profile"></span><span>Profile</span></a>
                <a href="reserveForm.php" class="menu-item"><span class="icon-reservation"></span><span>Reservation</span></a>
                <a href="borrowForm.php" class="menu-item"><span class="icon-borrowing"></span><span>Borrowing</span></a>
                <a href="wallet.php" class="menu-item"><span class="icon-wallet"></span><span>My Wallet</span></a>
            </div>
            <a href="index.php" class="logout"><span class="icon-logout"></span></a>
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

            <div class="profile-content">
                <div class="profile-header">
                    <div class="large-avatar">
                        <a href="editAcc.php" class="edit-icon">
                            <span class="icon-edit"></span>
                        </a>

                    </div>
                    <div class="profile-info">
                        <h2><?php echo htmlspecialchars($user['Name'] ?? ''); ?></h2>
                        <p>UserID: <?php echo htmlspecialchars($user['UserID'] ?? ''); ?></p>
                        <p>Email: <?php echo htmlspecialchars($user['Email'] ?? ''); ?></p>
                        <p>Phone: <?php echo htmlspecialchars($user['Phone'] ?? ''); ?></p>
                        <p>Address: <?php echo htmlspecialchars($user['Address'] ?? ''); ?></p>
                    </div>
                </div>

                <div class="records-section">
                    <h3>Records</h3>
                    <table class="records-table">
                        <thead>
                            <tr>
                                <th>RecordID</th>
                                <th>BookID</th>
                                <th>Book Title</th>
                                <th>Borrow Date</th>
                                <th>Due Date</th>
                                <th>Return Date</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody id="recordsBody">
                            <?php if (empty($records)): ?>
                                <tr>
                                    <td colspan="7" style="text-align: center;">No records found.</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($records as $record): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($record['RecordID']); ?></td>
                                        <td><?php echo htmlspecialchars($record['BookID']); ?></td>
                                        <td><?php echo htmlspecialchars($record['BookTitle']); ?></td>
                                        <td><?php echo htmlspecialchars($record['BorrowDate']); ?></td>
                                        <td><?php echo htmlspecialchars($record['DueDate']); ?></td>
                                        <td><?php echo htmlspecialchars($record['ReturnDate'] ?? 'N/A'); ?></td>
                                        <td><?php echo htmlspecialchars($record['Status']); ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="footer">All Rights Reserved 2025 | Library Management System</div>
        </div>
    </div>
</body>
</html>