<?php
session_start();
$error = "";

$host = 'localhost';
$user = 'root';
$pass = ''; // Leave empty if no password in XAMPP
$db   = 'urban';

$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
  $email = trim($_POST['email']);
  $password = trim($_POST['password']);

  // Check for hardcoded admin
  if ($email === 'admin' && $password === 'admin123') {
    $_SESSION['admin'] = true;
    header("Location: admin.php");
    exit();
  }

  // Normal user login flow
  $stmt = $conn->prepare("SELECT id, full_name, password FROM users WHERE email = ?");
  $stmt->bind_param("s", $email);
  $stmt->execute();
  $stmt->store_result();

  if ($stmt->num_rows > 0) {
    $stmt->bind_result($id, $name, $hashed_password);
    $stmt->fetch();

    if (password_verify($password, $hashed_password)) {
      $_SESSION['user_id'] = $id;
      $_SESSION['user_name'] = $name;
      header("Location: Home.html");
      exit();
    } else {
      $error = "Invalid email or password.";
    }
  } else {
    $error = "Invalid email or password.";
  }

  $stmt->close();
}
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Welcome – UrbanHarvest</title>
  <style>
    :root {
      --primary: #618264;
      --secondary: #4e7056;
      --accent: #ff6b35;
      --light: #fff;
      --text: #333;
      --bg: #f9f9f9;
    }
    * { box-sizing: border-box; margin: 0; padding: 0; }
    body {
      font-family: "Segoe UI", sans-serif;
      background: var(--bg);
      display: flex;
      flex-direction: column;
      min-height: 100vh;
      color: var(--text);
    }
    header {
      background: var(--primary);
      color: var(--light);
      padding: 1rem;
      text-align: center;
    }
    header .logo {
      font-size: 1.8rem;
      font-weight: bold;
    }
    .login-container {
      flex: 1;
      display: flex;
      justify-content: center;
      align-items: center;
      padding: 2rem;
    }
    .login-box {
      background: var(--light);
      padding: 2rem;
      border-radius: 8px;
      box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
      width: 100%;
      max-width: 400px;
    }
    .login-box h2 {
      text-align: center;
      margin-bottom: 1.5rem;
      color: var(--primary);
    }
    .form-group {
      margin-bottom: 1rem;
    }
    .form-group label {
      display: block;
      margin-bottom: 0.5rem;
      font-weight: bold;
    }
    .form-group input {
      width: 100%;
      padding: 0.5rem;
      border: 1px solid #ccc;
      border-radius: 4px;
    }
    .btn-login, .btn-register {
      width: 100%;
      padding: 0.75rem;
      background: var(--accent);
      color: var(--light);
      border: none;
      border-radius: 4px;
      font-size: 1rem;
      cursor: pointer;
      margin-top: 0.75rem;
    }
    .btn-login:hover, .btn-register:hover {
      background: #e65520;
    }
    .error {
      color: red;
      font-size: 0.9rem;
      margin-top: 0.5rem;
      text-align: center;
    }
    footer {
      background: var(--secondary);
      color: var(--light);
      padding: 1.5rem;
      text-align: center;
    }
  </style>
</head>
<body>
  <header>
    <div class="logo">UrbanHarvest</div>
  </header>

  <div class="login-container">
    <div class="login-box">
      <h2>Login to UrbanHarvest</h2>
      <form method="POST" action="">
        <div class="form-group">
          <label for="email">Email</label>
          <input type="text" name="email" id="email" required />
        </div>
        <div class="form-group">
          <label for="password">Password</label>
          <input type="password" name="password" id="password" required />
        </div>
        <button class="btn-login" type="submit">Login</button>
      </form>

      <form action="register.php" method="get">
        <button class="btn-register" type="submit">Register</button>
      </form>

      <?php if ($error): ?>
        <div class="error"><?php echo $error; ?></div>
      <?php endif; ?>
    </div>
  </div>

  <footer>&copy; 2025 UrbanHarvest. All Rights Reserved.</footer>
</body>
</html>
