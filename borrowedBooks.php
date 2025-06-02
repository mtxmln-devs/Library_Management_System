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

// Handle return action
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['return_id'])) {
    $recordIdToReturn = $_POST['return_id'];
    $currentDate = date('Y-m-d');
    
    $updateSql = "UPDATE BookRecords SET ReturnDate = ?, Status = 'Returned' WHERE RecordID = ? AND UserID = ?";
    $stmt = $conn->prepare($updateSql);
    $stmt->bind_param("sii", $currentDate, $recordIdToReturn, $userID);
    $stmt->execute();
    $stmt->close();
}

$sqlUser = "SELECT UserID, Name, Email, Phone, Address FROM User WHERE Name = ?";
$stmtUser = $conn->prepare($sqlUser);
$stmtUser->bind_param("s", $loggedInUserName);
$stmtUser->execute();
$resultUser = $stmtUser->get_result();
$user = $resultUser->fetch_assoc();
$stmtUser->close();

$userId = $userID;

$sql = "SELECT br.RecordID, b.BookID, b.Title, br.BorrowDate, br.DueDate, br.ReturnDate, br.Status
        FROM BookRecords br
        JOIN Book b ON br.BookID = b.BookID
        WHERE br.UserID = $userID";

$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>Borrowed Books - Library Management System</title>
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

        .borrowed-books-content {
            padding: 20px 30px;
        }

        .page-title {
            font-size: 24px;
            font-weight: bold;
            margin-bottom: 20px;
        }

        .books-table {
            width: 100%;
            height: 350px;
            border-collapse: collapse;
            margin-bottom: 10px;
        }

        .books-table th {
            background-color: #4a235a;
            color: white;
            padding: 12px 15px;
            text-align: left;
            font-weight: bold;
        }

        .books-table td {
            padding: 12px 15px;
            border: 1px solid #ddd;
            background-color: white;
        }

        .books-table tr:hover td {
            background-color: #f9f9f9;
        }

        .footer {
            text-align: center;
            margin-top: 140px;
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
        .icon-search::before { content: "🔍"; }
        .icon-user::before { content: "👤"; }

        button {
            background-color: #4a235a;
            color: white;
            border: none;
            padding: 5px 10px;
            border-radius: 4px;
            cursor: pointer;
            margin-left: 10px;
        }

        button:hover {
            background-color: #6c3483;
        }
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
                <div class="profile-avatar">
                    <span class="icon-user"></span>
                </div>
                <div class="welcome-text">
                    <h2>Welcome,</h2>
                    <h3><?php echo htmlspecialchars($loggedInUserName); ?>!</h3>
                </div>
            </div>

            <div class="menu">
                <a href="home.php" class="menu-item active"><span class="icon-home"></span><span>Home</span></a>
                <a href="profile.php" class="menu-item"><span class="icon-profile"></span><span>Profile</span></a>
                <a href="reserveForm.php" class="menu-item"><span class="icon-reservation"></span><span>Reservation</span></a>
                <a href="borrowForm.php" class="menu-item"><span class="icon-borrowing"></span><span>Borrowing</span></a>
                <a href="wallet.php" class="menu-item"><span class="icon-wallet"></span><span>My Wallet</span></a>
            </div>

            <a href="index.php" class="logout"><span class="icon-logout"></span></a>
        </div>

        <div class="main-content">
            <div class="top-bar">
                <div class="search-box">
                    <form method="get" action="search.php">
                        <input type="text" name="q" placeholder="Search books...">
                    </form>
                </div>
                <div class="user-profile">
                    <span><?php echo htmlspecialchars($loggedInUserName); ?></span>
                </div>
            </div>

            <div class="borrowed-books-content">
                <h1 class="page-title">Borrowed Books</h1>
                <table class="books-table">
    <thead>
        <tr>
            <th>RecordID</th>
            <th>BookID</th>
            <th>Title</th>
            <th>Borrow Date</th>
            <th>Due Date</th>
            <th>Return Date</th>
            <th>Status</th>
            <th>Action</th> <!-- New Action column -->
        </tr>
    </thead>
    <tbody>
        <?php
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                echo "<tr>
                    <td>{$row['RecordID']}</td>
                    <td>{$row['BookID']}</td>
                    <td>{$row['Title']}</td>
                    <td>{$row['BorrowDate']}</td>
                    <td>{$row['DueDate']}</td>
                    <td>{$row['ReturnDate']}</td>
                    <td>{$row['Status']}</td>
                    <td>";
                
                if ($row['Status'] !== 'Returned') {
                    echo "<form method='POST' style='display:inline;'>
                            <input type='hidden' name='return_id' value='{$row['RecordID']}' />
                            <button type='submit' onclick='return confirm(\"Return this book?\")'>Return</button>
                          </form>";
                } else {
                    echo "—"; // Placeholder when already returned
                }

                echo "</td></tr>";
            }
        } else {
            echo "<tr><td colspan='8' style='text-align:center;'>No borrowed books found.</td></tr>";
        }
        ?>
    </tbody>
</table>

            </div>

            <div class="footer">All Rights Reserved 2025 | Library Management System</div>
        </div>
    </div>
</body>
</html>
<?php
$conn->close();
?>
