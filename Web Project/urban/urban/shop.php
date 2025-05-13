<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
session_start();
require_once('db.php');

// Ensure user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit;
}

// Handle AJAX cart addition
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['item_id']) && isset($_POST['quantity'])) {
    $item_id = intval($_POST['item_id']);
    $quantity = intval($_POST['quantity']);
    $user_id = $_SESSION['user_id'];

    // Check if item exists in cart
    $check_query = "SELECT * FROM cart WHERE user_id = $user_id AND item_id = $item_id";
    $check_result = mysqli_query($conn, $check_query);

    if (mysqli_num_rows($check_result) > 0) {
        $update_query = "UPDATE cart SET quantity = quantity + $quantity WHERE user_id = $user_id AND item_id = $item_id";
        mysqli_query($conn, $update_query);
    } else {
        $insert_query = "INSERT INTO cart (user_id, item_id, quantity, price)
                         SELECT $user_id, id, $quantity, price FROM items WHERE id = $item_id";
        mysqli_query($conn, $insert_query);
    }

    echo "success";
    exit;
}

// Fetch categories
$category_query = "SELECT DISTINCT c.name 
                   FROM categories c
                   INNER JOIN items i ON c.id = i.category_id
                   ORDER BY c.name";
$categories_result = mysqli_query($conn, $category_query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Shop Now - Grocery Store</title>
  <link rel="stylesheet" href="style.css" />
  <link
    href="https://fonts.googleapis.com/css2?family=Poppins&family=Raleway&display=swap"
    rel="stylesheet"
  />

  <style>
    :root {
      --primary: #618264;
      --secondary: #4e7056;
      --accent: #1a1918;
      --text: #333;
      --bg: #f9f9f9;
      --light: #fff;
      --muted: #666;
    }
    * {
      box-sizing: border-box;
      margin: 0;
      padding: 0;
    }
    body {
      font-family: "Segoe UI", sans-serif;
      background: var(--bg);
      color: var(--text);
      line-height: 1.5;
      display: flex;
      flex-direction: column;
      min-height: 100vh;
    }
    a {
      text-decoration: none;
      color: inherit;
    }
    
    .cart-button {
      position: fixed;
      top: 90px;
      right: 20px;
      background-color:rgb(114, 163, 116);
      color: white;
      padding: 12px 18px;
      font-size: 16px;
      border: none;
      border-radius: 8px;
      text-decoration: none;
      z-index: 9999;
      box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
      transition: background-color 0.3s ease;
    }
    .cart-button:hover {
      background-color:rgb(94, 145, 96);
    }

    .container {
      flex: 1;
      width: 90%;
      max-width: 1200px;
      margin: 2rem auto;
    }

    h2 {
      text-align: center;
      margin-bottom: 2rem;
      font-size: 2rem;
    }

    .category {
      margin-bottom: 3rem;
    }

    .category h3 {
      font-size: 1.5rem;
      margin-bottom: 1rem;
    }

    .items {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
      gap: 1.5rem;
    }

    .item {
      background: var(--light);
      border-radius: 8px;
      box-shadow: 0 4px 10px rgba(0, 0, 0, 0.05);
      text-align: center;
      padding: 1rem;
      transition: transform 0.3s, box-shadow 0.3s;
    }

    .item:hover {
      transform: scale(1.03);
      box-shadow: 0 6px 15px rgba(0, 0, 0, 0.1);
    }

    .item img {
      width: 100%;
      height: 180px;
      object-fit: cover;
      border-radius: 5px;
      margin-bottom: 1rem;
    }

    .item p {
      margin: 0.5rem 0;
    }

    .price {
      color: var(--accent);
      font-weight: bold;
    }

    .btn {
      background: var(--accent);
      color: var(--light);
      border: none;
      padding: 0.5rem 1rem;
      margin-top: 0.5rem;
      border-radius: 4px;
      cursor: pointer;
    }

    .btn:hover {
      background: #e65520;
    }

  </style>
</head>
<body>
<?php include 'header.php'; ?>

<a href="cart.php" class="cart-button">🛒 View Cart</a>

<div class="container">
  <h2>Shop by Category</h2>
  <?php while ($category_row = mysqli_fetch_assoc($categories_result)) {
    $category_name = $category_row['name'];
    $item_query = "SELECT i.* 
                   FROM items i
                   INNER JOIN categories c ON i.category_id = c.id
                   WHERE c.name = '" . mysqli_real_escape_string($conn, $category_name) . "'";
    $item_result = mysqli_query($conn, $item_query);

    if (mysqli_num_rows($item_result) > 0): ?>
      <div class="category">
        <h3><?php echo htmlspecialchars($category_name); ?></h3>
        <div class="items">
          <?php while ($item = mysqli_fetch_assoc($item_result)) { ?>
            <div class="item">
              <img src="images/<?php echo htmlspecialchars($item['image']); ?>" alt="<?php echo htmlspecialchars($item['name']); ?>">
              <p><?php echo htmlspecialchars($item['name']); ?></p>
              <p class="price">Rs <?php echo htmlspecialchars($item['price']); ?> / <?php echo htmlspecialchars($item['unit']); ?></p>
              <button class="btn" onclick="addToCart(<?php echo $item['id']; ?>)">Add to Cart</button>
            </div>
          <?php } ?>
        </div>
      </div>
  <?php endif;
  } ?>
</div>
<?php include 'footer.php'; ?>


<script>
  //load header
  fetch('header.html')
  .then(response => response.text())
  .then(data => {
    document.getElementById('header').innerHTML = data;
  });

// Load Footer 
fetch('footer.html')
  .then(response => response.text())
  .then(data => {
    document.getElementById('footer').innerHTML = data;
  });

function addToCart(itemId) {
    const quantity = 1;
    const xhr = new XMLHttpRequest();
    xhr.open('POST', 'shop.php', true);
    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
    xhr.onreadystatechange = function () {
        if (xhr.readyState === 4 && xhr.status === 200) {
            if (xhr.responseText.trim() === 'success') {
                alert("Item added to cart!");
            } else {
                alert("Failed to add item. Try again.");
            }
        }
    };
    xhr.send('item_id=' + itemId + '&quantity=' + quantity);
}
</script>

</body>
</html>
