<?php
session_start();

if (!isset($_SESSION['UserID'])) {
    header("Location: loginForm.php");
    exit;
}

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "Book_Management";

$conn = new mysqli($servername, $username, $password, $dbname);

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

// Fetch user data
if ($_SERVER["REQUEST_METHOD"] === "GET") {
    $sql = "SELECT Name, Email, Phone, Address FROM User WHERE UserID = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $userID);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();
    $stmt->close();
}

// Handle update form submission
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $name = $_POST['Name'];
    $email = $_POST['Email'];
    $phone = $_POST['Phone'];
    $address = $_POST['Address'];

    $sqlUpdate = "UPDATE User SET Name = ?, Email = ?, Phone = ?, Address = ? WHERE UserID = ?";
    $stmt = $conn->prepare($sqlUpdate);
    $stmt->bind_param("ssssi", $name, $email, $phone, $address, $userID);
    if ($stmt->execute()) {
        $message = "Profile updated successfully.";
        $_SESSION['UserName'] = $name;
        header("Location: profile.php");
        exit;
    } else {
        $message = "Error updating profile.";
    }
    $stmt->close();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Edit Account</title>
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
                .menu1 {
                    background-color: white;
                    padding: 30px;
                    margin: 30px;
                    border-radius: 10px;
                    box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
                }

                .menu1 h2 {
                    color: #4a235a;
                    margin-bottom: 20px;
                    font-size: 24px;
                    border-bottom: 2px solid #4a235a;
                    padding-bottom: 10px;
                }

                .menu1 form label {
                    display: block;
                    margin-bottom: 8px;
                    font-weight: bold;
                    color: #4a235a;
                }

                .menu1 form input[type="text"],
                .menu1 form input[type="email"],
                .menu1 form textarea {
                    width: 100%;
                    padding: 10px 12px;
                    margin-bottom: 20px;
                    border: 1px solid #ccc;
                    border-radius: 5px;
                    outline: none;
                    font-size: 14px;
                }

                .menu1 form input[type="text"]:focus,
                .menu1 form input[type="email"]:focus,
                .menu1 form textarea:focus {
                    border-color: #4a235a;
                    box-shadow: 0 0 5px rgba(74, 35, 90, 0.3);
                }

                .form-buttons {
                    display: flex;
                    justify-content: flex-start;
                    gap: 15px;
                    align-items: center;
                }

                .form-buttons input[type="submit"] {
                    background-color: #4a235a;
                    color: white;
                    padding: 10px 20px;
                    border: none;
                    font-weight: bold;
                    border-radius: 5px;
                    cursor: pointer;
                    transition: background-color 0.2s ease;
                }

                .form-buttons input[type="submit"]:hover {
                    background-color: #3a1d47;
                }

                .form-buttons a {
                    color: #4a235a;
                    text-decoration: none;
                    font-weight: bold;
                    padding: 10px 20px;
                    border: 1px solid #4a235a;
                    border-radius: 5px;
                    transition: all 0.2s ease;
                }

                .form-buttons a:hover {
                    background-color: #4a235a;
                    color: white;
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

            <div class="menu1">
            <h2>Edit Account</h2>
            <form method="POST">
                <label for="Name">Name:</label>
                <input type="text" id="Name" name="Name" value="<?= htmlspecialchars($user['Name'] ?? '') ?>" required>

                <label for="Email">Email:</label>
                <input type="email" id="Email" name="Email" value="<?= htmlspecialchars($user['Email'] ?? '') ?>" required>

                <label for="Phone">Phone:</label>
                <input type="text" id="Phone" name="Phone" value="<?= htmlspecialchars($user['Phone'] ?? '') ?>">

                <label for="Address">Address:</label>
                <textarea id="Address" name="Address" rows="3"><?= htmlspecialchars($user['Address'] ?? '') ?></textarea>

                <div class="form-buttons">
                <input type="submit" value="Update">
                <a href="profile.php">Cancel</a>
                </div>
            </form>

    <?php if ($message): ?>
        <div class="message"><?= htmlspecialchars($message) ?></div>
    <?php endif; ?>
    </div>

</body>
</html>
