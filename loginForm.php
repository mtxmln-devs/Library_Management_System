<?php
session_start();
$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $inputName = $_POST['Name'];
    $inputEmail = $_POST['Email'];

    $conn = new mysqli('localhost', 'root', '', 'Book_Management');

    if ($conn->connect_error) {
        $message = "Connection failed: " . $conn->connect_error;
    } else {
        $stmt = $conn->prepare("SELECT * FROM `User` WHERE Name = ? AND Email = ?");
        $stmt->bind_param("ss", $inputName, $inputEmail);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 1) {
            $user = $result->fetch_assoc();
            $_SESSION['UserID'] = $user['UserID'];
            $_SESSION['UserName'] = $user['Name'];

            header("Location: home.php");
            exit;
        } else {
            $message = "❌ Invalid credentials. Please try again.";
        }

        $stmt->close();
        $conn->close();
    }
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Library Login</title>
  <link rel="icon" href="logo.png" />

  <link href="https://fonts.googleapis.com/css2?family=Cal+Sans&display=swap" rel="stylesheet">
  <style>
    body {
      margin: 0;
      font-family: 'Cal Sans', sans-serif;
      background-color: #3b0a50;
      display: flex;
      justify-content: center;
      align-items: center;
      height: 100vh;
    }

    .login-container {
      text-align: center;
      color: white;
    }

    h1 {
      font-size: 2.5rem;
      margin-bottom: 2rem;
      letter-spacing: 4px;
    }

    input[type="text"], input[type="email"] {
      display: block;
      width: 300px;
      margin: 15px auto;
      padding: 12px;
      border: none;
      border-radius: 5px;
      background-color: #ddd;
      font-family: 'Cal Sans', sans-serif;
    }

    .login-button {
      background-color: #e1e1e1;
      color: black;
      border: none;
      padding: 10px 40px;
      font-size: 1rem;
      border-radius: 5px;
      cursor: pointer;
      margin-top: 20px;
      font-weight: bold;
    }

    .login-button:hover {
      background-color: #ccc;
    }

    .signup-link {
      margin-top: 10px;
      font-size: 0.9rem;
    }

    .signup-link a {
      color: #f1f1f1;
      font-weight: bold;
    }

    .message {
      margin-top: 20px;
      font-size: 1rem;
      color: #f0f0f0;
    }
  </style>
</head>
<body>
  <div class="login-container">
    <h1>Log In</h1>
    <form method="post">
      <input type="text" name="Name" placeholder="Name" required />
      <input type="email" name="Email" placeholder="Email" required />

      <div class="signup-link">
        Don't have an account? <a href="signup.php">Sign Up</a>
      </div>

      <input type="submit" class="login-button" value="Log In">
    </form>

    <?php if (!empty($message)) : ?>
      <div class="message"><?= htmlspecialchars($message) ?></div>
    <?php endif; ?>
  </div>
</body>
</html>
