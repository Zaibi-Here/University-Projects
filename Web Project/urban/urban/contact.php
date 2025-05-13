<?php
session_start();
require_once('db.php');

// Ensure user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit;
}

// Initialize variables
$name = $email = $phone = $message = '';
$form_message = '';
$message_class = '';

// Process form
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Sanitize inputs
    $name = trim(htmlspecialchars($_POST['name'] ?? ''));
    $email = trim(htmlspecialchars($_POST['email'] ?? ''));
    $phone = trim(htmlspecialchars($_POST['phone'] ?? ''));
    $message = trim(htmlspecialchars($_POST['message'] ?? ''));

    // Validate inputs
    $errors = [];

    if (empty($name)) $errors[] = "Name is required.";
    if (empty($email)) {
        $errors[] = "Email is required.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Invalid email format.";
    }
    if (empty($phone)) $errors[] = "Phone number is required.";
    if (empty($message)) $errors[] = "Message is required.";

    // If no errors, insert into DB
    if (empty($errors)) {
        $stmt = $conn->prepare("INSERT INTO contact_messages (name, email, phone, message) VALUES (?, ?, ?, ?)");
        if ($stmt) {
            $stmt->bind_param("ssss", $name, $email, $phone, $message);
            if ($stmt->execute()) {
                $form_message = "Thank you for your message, $name! We'll get back to you soon.";
                $message_class = 'success';
                $name = $email = $phone = $message = '';
            } else {
                $form_message = "Failed to save your message. Please try again later.";
                $message_class = 'error';
            }
            $stmt->close();
        } else {
            $form_message = "Database error: unable to prepare statement.";
            $message_class = 'error';
        }
    } else {
        $form_message = implode('<br>', $errors);
        $message_class = 'error';
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Contact Us - UrbanHarvest</title>
    <link rel="stylesheet" href="style.css">
    <link
      href="https://fonts.googleapis.com/css2?family=Poppins&family=Raleway&display=swap"
      rel="stylesheet"
    />
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body, input, textarea { font-family: 'Raleway', sans-serif; }
        h1 { font-family: 'Poppins', sans-serif; font-size: 2rem; color: #FFFFFF; text-align: center; margin: 20px 0; }
        main { max-width: 800px; margin: auto; padding: 20px; }
        section { background: #fff; padding: 25px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.05); }
        .form-group { margin-bottom: 15px; }
        label { font-weight: bold; display: block; margin-bottom: 5px; }
        input, textarea {
            width: 100%; padding: 10px; border: 1px solid #ccc;
            border-radius: 5px; font-size: 1rem;
        }
        .btn {
            display: inline-block; background: #618264; color: #fff;
            border: none; padding: 12px 25px; border-radius: 5px;
            cursor: pointer; font-weight: bold; margin-top: 15px;
        }
        .btn:hover { background: #4e7056; }
        .message {
            margin: 15px 0; padding: 15px; border-radius: 5px; text-align: center;
        }
        .success { background: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
        .error { background: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
    </style>
</head>
<body>

<?php include 'header.php'; ?>

<main>
    <section>
        <h1>Contact Us</h1>
        <p style="text-align:center;">Have any questions? We'd love to hear from you!</p>

        <?php if (!empty($form_message)): ?>
            <div class="message <?= $message_class ?>">
                <?= $form_message ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="contact.php">
            <div class="form-group">
                <label for="name">Full Name:</label>
                <input type="text" id="name" name="name" required value="<?= htmlspecialchars($name) ?>">
            </div>

            <div class="form-group">
                <label for="email">Email Address:</label>
                <input type="email" id="email" name="email" required value="<?= htmlspecialchars($email) ?>">
            </div>

            <div class="form-group">
                <label for="phone">Phone Number:</label>
                <input type="tel" id="phone" name="phone" required value="<?= htmlspecialchars($phone) ?>">
            </div>

            <div class="form-group">
                <label for="message">Your Message:</label>
                <textarea id="message" name="message" required><?= htmlspecialchars($message) ?></textarea>
            </div>

            <button type="submit" class="btn">Send Message</button>
        </form>
    </section>
</main>

<?php include 'footer.php'; ?>

</body>
</html>
