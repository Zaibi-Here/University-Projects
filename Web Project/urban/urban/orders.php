<?php
session_start();
// DB connection
require_once('db.php');

// Check login
if (!isset($_SESSION['user_id'])) {
    die("You must be logged in to view your orders.");
}
$user_id = $_SESSION['user_id'];

// Fetch orders
$stmt = $conn->prepare("SELECT order_id, items, total_price, discounted_price, status, created_at FROM orders WHERE user_id = ? ORDER BY created_at DESC");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$orders = [];
while ($row = $result->fetch_assoc()) {
    $orders[] = $row;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>My Orders – UrbanHarvest</title>
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
      --bg: #f9f9f9;
      --text: #333;
    }
    .mhead{
      color:black;
    }

    * { box-sizing: border-box; margin: 0; padding: 0; }
    body {
      font-family: 'Segoe UI', sans-serif;
      background: var(--bg);
      color: var(--text);
      min-height: 100vh;
      display: flex;
      flex-direction: column;
    }


    .container {
      flex: 1;
      width: 90%;
      max-width: 1000px;
      margin: 2rem auto;
    }

    h1 { margin-bottom: 1rem; color: #FFFFFF; }

    table {
      width: 100%;
      border-collapse: collapse;
      background: var(--light);
      box-shadow: 0 0 10px rgba(0,0,0,0.05);
    }

    th, td {
      padding: 1rem;
      text-align: left;
      border-bottom: 1px solid #ddd;
    }

    th {
      background: #e9f4ec;
      font-weight: bold;
    }

    .status {
      padding: 0.4rem 0.8rem;
      border-radius: 4px;
      font-size: 0.9rem;
      font-weight: bold;
      text-transform: capitalize;
      display: inline-block;
    }

    .pending { background: #fff3cd; color: #856404; }
    .shipped { background: #cce5ff; color: #004085; }
    .delivered { background: #d4edda; color: #155724; }
    .cancelled { background: #f8d7da; color: #721c24; }

    .no-orders {
      text-align: center;
      font-size: 1.2rem;
      color: #888;
      padding: 2rem;
    }

  </style>
</head>
<body>
<?php include 'header.php'; ?>
  
  <div class="container">
    <h1 class="mhead">My Orders</h1>

    <?php if (empty($orders)): ?>
      <div class="no-orders">You haven't placed any orders yet.</div>
    <?php else: ?>
      <table id="orders-table">
        <thead>
          <tr>
            <th>#</th>
            <th>Date</th>
            <th>Items</th>
            <th>Total (₨)</th>
            <th>Status</th>
          </tr>
        </thead>
        <tbody>
          <!-- Rendered by JS -->
        </tbody>
      </table>
    <?php endif; ?>
  </div>
  <?php include 'footer.php'; ?>

  <script>
    // Load header
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
    const orders = <?php echo json_encode($orders); ?>;
    const tbody = document.querySelector('#orders-table tbody');

    function formatDate(dateStr) {
      const date = new Date(dateStr);
      const options = { day: 'numeric', month: 'long', year: 'numeric' };
      return date.toLocaleDateString('en-US', options);
    }

    function formatStatus(status) {
      return status.charAt(0).toUpperCase() + status.slice(1);
    }

    function loadOrders() {
      if (!orders.length || !tbody) return;

      orders.forEach((order, index) => {
        const tr = document.createElement('tr');
        tr.innerHTML = `
          <td>${index + 1}</td>
          <td>${formatDate(order.created_at)}</td>
          <td>${order.items}</td>
          <td>₨${parseFloat(order.discounted_price).toFixed(2)}</td>
          <td><span class="status ${order.status}">${formatStatus(order.status)}</span></td>
        `;
        tbody.appendChild(tr);
      });
    }

    loadOrders();
  </script>
</body>
</html>
