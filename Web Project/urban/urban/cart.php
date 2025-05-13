<?php
// DB Connection
session_start();
require_once('db.php');

// Get user_id
if (!isset($_SESSION['user_id'])) die("User not logged in.");
$user_id = $_SESSION['user_id'];

// Fetch user info
$user_query = $conn->prepare("SELECT full_name, email, address, city, zipcode FROM users WHERE id = ?");
$user_query->bind_param("i", $user_id);
$user_query->execute();
$user_info = $user_query->get_result()->fetch_assoc();

// Handle quantity update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_qty'])) {
    $item_id = $_POST['item_id'];
    $new_qty = max(1, intval($_POST['quantity']));

    $stmt = $conn->prepare("SELECT price FROM items WHERE id = ?");
    $stmt->bind_param("i", $item_id);
    $stmt->execute();
    $price = $stmt->get_result()->fetch_assoc()['price'];

    $total_price = $price * $new_qty;

    $update = $conn->prepare("UPDATE cart SET quantity = ? WHERE user_id = ? AND item_id = ?");
    $update->bind_param("iii", $new_qty, $user_id, $item_id);
    $update->execute();
    

    header("Location: cart.php");
    exit;
}

// Handle place order
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['place_order'])) {
    $discount_code = strtoupper(trim($_POST['discount_code']));
    $discount_percent = match($discount_code) {
        "SAVE10" => 0.1,
        "SAVE20" => 0.2,
        "SAVE30" => 0.3,
        default => 0,
    };

    $cart_result = $conn->query("SELECT c.quantity, c.total_price, i.name FROM cart c JOIN items i ON c.item_id = i.id WHERE c.user_id = $user_id");
    $items = [];
    $total_price = 0;
    while ($row = $cart_result->fetch_assoc()) {
        $items[] = "{$row['name']} (x{$row['quantity']})";
        $total_price += $row['total_price'];
    }

    if (empty($items)) {
        echo "<script>alert('Cart is empty!');</script>";
    } else {
        $items_str = implode(", ", $items);
        $discounted_price = $total_price - ($total_price * $discount_percent);
        $created_at = date("Y-m-d H:i:s");

        $insert = $conn->prepare("INSERT INTO orders (user_id, name, email, address, city, zipcode, items, total_price, discount_code, discounted_price, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $insert->bind_param(
            "issssssddss",
            $user_id,
            $user_info['full_name'],
            $user_info['email'],
            $user_info['address'],
            $user_info['city'],
            $user_info['zipcode'],
            $items_str,
            $total_price,
            $discount_code,
            $discounted_price,
            $created_at
        );
        $insert->execute();

        $conn->query("DELETE FROM cart WHERE user_id = $user_id");

        echo "<script>alert('Order placed successfully!'); window.location='cart.php';</script>";
        exit;
    }
}

// Fetch cart items
$query = $conn->prepare("SELECT c.item_id, c.quantity, c.price, c.total_price, i.name FROM cart c JOIN items i ON c.item_id = i.id WHERE c.user_id = ?");
$query->bind_param("i", $user_id);
$query->execute();
$cart_result = $query->get_result();
$cart_items = $cart_result->fetch_all(MYSQLI_ASSOC);
$total_price = array_sum(array_column($cart_items, 'total_price'));
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Your Cart - Urban Harvest</title>
    <link rel="stylesheet" href="style.css" />
  <link
    href="https://fonts.googleapis.com/css2?family=Poppins&family=Raleway&display=swap"
    rel="stylesheet"
  />
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
            color: var(--text);
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }


        .container {
            flex: 1;
            width: 90%;
            max-width: 1000px;
            margin: 2rem auto;
        }

        h1 { margin-bottom: 1rem; }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 1rem;
        }

        th, td {
            border: 1px solid #ccc;
            padding: 0.75rem;
            text-align: left;
        }

        th {
            background: #e8f4fd;
        }

        .btn {
            background: var(--accent);
            color: white;
            padding: 0.4rem 0.8rem;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }

        .btn:hover {
            background: #e65520;
        }

        input[type="number"], input[type="text"] {
            padding: 0.3rem;
            border-radius: 4px;
            border: 1px solid #ccc;
            width: 60px;
        }

        .total {
            font-weight: bold;
            text-align: right;
        }

        form.discount-form {
            display: flex;
            justify-content: flex-end;
            gap: 0.5rem;
            align-items: center;
        }

    </style>
</head>
<body>

<?php include 'header.php'; ?>

<div class="container">
    <h1>Your Cart</h1>
    <table>
        <tr>
            <th>Item</th>
            <th>Price</th>
            <th>Quantity</th>
            <th>Subtotal</th>
            <th>Action</th>
        </tr>
        <?php foreach ($cart_items as $item): ?>
        <tr>
            <td><?= htmlspecialchars($item['name']) ?></td>
            <td>₨<?= number_format($item['price'], 2) ?></td>
            <td>
                <form method="POST">
                    <input type="hidden" name="item_id" value="<?= $item['item_id'] ?>">
                    <input type="number" name="quantity" value="<?= $item['quantity'] ?>" min="1">
                    <button type="submit" name="update_qty" class="btn">Update</button>
                </form>
            </td>
            <td>₨<?= number_format($item['total_price'], 2) ?></td>
            <td></td>
        </tr>
        <?php endforeach; ?>
        <tr class="total">
            <td colspan="3">Total:</td>
            <td colspan="2">₨<?= number_format($total_price, 2) ?></td>
        </tr>
    </table>

    <form method="POST" class="discount-form">
        <label>Discount Code:</label>
        <input type="text" name="discount_code" placeholder="e.g. SAVE10">
        <button type="submit" name="place_order" class="btn">Place Order</button>
    </form>
</div>

<?php include 'footer.php'; ?>

<script>
    //load header
  fetch('header.php')
  .then(response => response.text())
  .then(data => {
    document.getElementById('header').innerHTML = data;
  });

// Load Footer
fetch('footer.php')
  .then(response => response.text())
  .then(data => {
    document.getElementById('footer').innerHTML = data;
  });
 </script> 
</body>
</html>
