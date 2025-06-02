<?php
$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $Name = $_POST['Name'];
    $Email = $_POST['Email'];
    $Phone = $_POST['Phone'];
    $Address = $_POST['Address'];
    
  $conn = new mysqli('localhost', 'root', '', 'Book_Management');

    if ($conn->connect_error) {
        $message = "Connection failed: " . $conn->connect_error;
    } else {
        // Remove UserID from the insert and use only 4 values and placeholders
        $stmt = $conn->prepare("INSERT INTO `User` (Name, Email, Phone, Address) VALUES (?, ?, ?, ?)");

        if (!$stmt) {
            $message = "Prepare failed: " . $conn->error;
        } else {
            $stmt->bind_param("ssss", $Name, $Email, $Phone, $Address);
            if ($stmt->execute()) {
                $message = "Registration Successful!";
            } else {
                $message = "Error: " . $stmt->error;
            }

            $stmt->close();
        }
        $conn->close();
    }
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Library Management System - Sign Up</title>
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

    input[type="text"], input[type="email"], input[type="tel"] {
      display: block;
      width: 300px;
      margin: 15px auto;
      padding: 12px;
      border: none;
      border-radius: 5px;
      background-color: #ddd;
      font-family: 'Cal Sans', sans-serif;
    }

    .signup-link {
      color: white;
      font-size: 0.9rem;
      margin-top: 10px;
      margin-bottom: 30px;
    }

    .signup-link a {
      color: black;
      font-weight: bold;
      margin-left: 5px;
    }

    .login-button {
      background-color: #e1e1e1;
      color: black;
      border: none;
      padding: 10px 40px;
      font-size: 1rem;
      border-radius: 5px;
      cursor: pointer;
      font-weight: bold;
      font-family: 'Cal Sans', sans-serif;
      margin-left: 5px;
    }

    .login-button:hover {
      background-color: #ccc;
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
    <h1>Sign Up</h1>
    <form method="post">
      <div class="form-group">
        <label for="name">Name</label>
        <input type="text" id="name" name="Name" required />
      </div>
      <div class="form-group">
        <label for="email">Email</label>
        <input type="email" id="email" name="Email" required />
      </div>
      <div class="form-group">
        <label for="phone">Phone</label>
        <input type="tel" id="phone" name="Phone" required />
      </div>
      <div class="form-group">
        <label for="address">Address</label>
        <input type="text" id="address" name="Address" required />
      </div>

      <div class="signup-link">
        Already have an account? <a href="loginForm.php">Log In</a>
      </div>

      <input type="submit" class="login-button" value="Sign Up">
    </form>

    <?php if (!empty($message)) : ?>
      <div class="message"><?= htmlspecialchars($message) ?></div>
    <?php endif; ?>
  </div>
</body>
</html>
