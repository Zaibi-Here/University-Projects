<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
?>
<?php
// Database connection (runs first when page is accessed)
$host = 'localhost';
$user = 'root';
$pass = ''; // Leave empty if no password in XAMPP
$db   = 'urban';

$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
  die("Database Connection Failed: " . $conn->connect_error);
}

// Handle form POST
$response = "";
if ($_SERVER["REQUEST_METHOD"] === "POST") {
  $fullname = trim($_POST['fullname']);
  $email = trim($_POST['email']);
  $password = $_POST['password'];
  $confirm = $_POST['confirm'];
  $address = trim($_POST['address']);
  $city = trim($_POST['city']);
  $zipcode = trim($_POST['zipcode']);

  // Validation
  if (empty($fullname) || empty($email) || empty($password) || empty($confirm) || empty($address) || empty($city) || empty($zipcode)) {
    $response = "All fields are required.";
  } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $response = "Invalid email format.";
  } elseif (strlen($password) < 6) {
    $response = "Password must be at least 6 characters.";
  } elseif ($password !== $confirm) {
    $response = "Passwords do not match.";
  } else {
    // Secure password
    $hashed = password_hash($password, PASSWORD_DEFAULT);

    // Insert user data
    $stmt = $conn->prepare("INSERT INTO users (full_name, email, password, address, city, zipcode) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssss", $fullname, $email, $hashed, $address, $city, $zipcode);

    if ($stmt->execute()) {
      // After successful registration, redirect to login.php
      header("Location: index.php ");
      exit(); // Make sure the script ends here after redirect
    } else {
      if ($conn->errno === 1062) {
        $response = "Email already registered.";
      } else {
        $response = "Error: " . $conn->error;
      }
    }

    $stmt->close();
  }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>User Registration – UrbanHarvest</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <style>
    :root {
      --primary: #618264;
      --secondary: #4e7056;
      --accent: #ff6b35;
      --light: #fff;
      --bg: #f9f9f9;
      --text: #333;
    }

    * { box-sizing: border-box; margin: 0; padding: 0; }
    body {
      font-family: 'Segoe UI', sans-serif;
      background: var(--bg);
      color: var(--text);
      display: flex;
      justify-content: center;
      align-items: center;
      height: 100vh;
    }

    .register-container {
      background: var(--light);
      padding: 2rem;
      border-radius: 10px;
      box-shadow: 0 0 20px rgba(0,0,0,0.1);
      width: 100%;
      max-width: 400px;
    }

    h2 {
      text-align: center;
      color: var(--primary);
      margin-bottom: 1rem;
    }

    form {
      display: flex;
      flex-direction: column;
    }

    label {
      margin-bottom: 0.3rem;
      font-weight: bold;
    }

    input {
      padding: 0.6rem;
      margin-bottom: 1rem;
      border: 1px solid #ccc;
      border-radius: 5px;
    }

    .password-toggle {
      display: flex;
      align-items: center;
      margin-bottom: 1rem;
    }

    .password-toggle input[type="checkbox"] {
      margin-right: 0.5rem;
    }

    .btn {
      background: var(--accent);
      color: var(--light);
      padding: 0.7rem;
      border: none;
      border-radius: 5px;
      cursor: pointer;
      font-size: 1rem;
      transition: background 0.3s;
    }

    .btn:hover {
      background: #e65520;
    }

    .message {
      margin-top: 1rem;
      text-align: center;
      font-weight: bold;
    }

    .message.success {
      color: green;
    }

    .message.error {
      color: red;
    }
  </style>
</head>
<body>

  <div class="register-container">
    <h2>User Registration</h2>
    <form method="POST" action="">
      <label for="fullname">Full Name</label>
      <input type="text" name="fullname" id="fullname" required />

      <label for="email">Email</label>
      <input type="email" name="email" id="email" required />

      <label for="password">Password</label>
      <input type="password" name="password" id="password" required />

      <label for="confirm">Confirm Password</label>
      <input type="password" name="confirm" id="confirm" required />

      <label for="address">Address</label>
      <input type="text" name="address" id="address" required />

      <label for="city">City</label>
      <input type="text" name="city" id="city" required />

      <label for="zipcode">Zipcode</label>
      <input type="text" name="zipcode" id="zipcode" required />

      <div class="password-toggle">
        <input type="checkbox" id="show-passwords" />
        <label for="show-passwords">Show Passwords</label>
      </div>

      <button type="submit" class="btn">Register</button>

      <?php if (!empty($response)): ?>
        <div class="message <?php echo (strpos($response, 'successful') !== false) ? 'success' : 'error'; ?>">
          <?php echo htmlspecialchars($response); ?>
        </div>
      <?php endif; ?>
    </form>
  </div>

  <script>
    const password = document.getElementById('password');
    const confirm = document.getElementById('confirm');
    const showPasswords = document.getElementById('show-passwords');

    showPasswords.addEventListener('change', () => {
      const type = showPasswords.checked ? 'text' : 'password';
      password.type = type;
      confirm.type = type;
    });
  </script>

</body>
</html>
