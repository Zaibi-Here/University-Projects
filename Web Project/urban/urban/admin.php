<?php
session_start();
require_once('db.php');

// Ensure user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit;
}

$message = '';

// Handle status update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_status'])) {
    $order_id = $_POST['order_id'];
    $status = $_POST['status'];
    $stmt = $conn->prepare("UPDATE orders SET status = ? WHERE order_id = ?");
    $stmt->bind_param("si", $status, $order_id);
    $stmt->execute();
    $stmt->close();
}

// Handle item CRUD operations
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['item_action'])) {
    $action = $_POST['item_action'];
    $item_id = $_POST['item_id'] ?? null;
    $name = $_POST['name'] ?? '';
    $price = $_POST['price'] ?? 0;
    $unit = $_POST['unit'] ?? '';
    $category_id = $_POST['category_id'] ?? 0;

    // Image handling
    $image_path = null;
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $upload_dir = "images/";
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0755, true);
        }
        $image_path = $upload_dir . basename($_FILES["image"]["name"]);
        move_uploaded_file($_FILES["image"]["tmp_name"], $image_path);
    }

    if ($action === 'create') {
        $stmt = $conn->prepare("INSERT INTO items (name, price, unit, category_id, image) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("sdsis", $name, $price, $unit, $category_id, $image_path);
        $stmt->execute();
        $stmt->close();
        $message = "Item added successfully!";
    } elseif ($action === 'update' && $item_id) {
        if ($image_path) {
            $stmt = $conn->prepare("UPDATE items SET name = ?, price = ?, unit = ?, category_id = ?, image = ? WHERE id = ?");
            $stmt->bind_param("sdsisi", $name, $price, $unit, $category_id, $image_path, $item_id);
        } else {
            $stmt = $conn->prepare("UPDATE items SET name = ?, price = ?, unit = ?, category_id = ? WHERE id = ?");
            $stmt->bind_param("sdsii", $name, $price, $unit, $category_id, $item_id);
        }
        $stmt->execute();
        $stmt->close();
        $message = "Item updated successfully!";
    } elseif ($action === 'delete' && $item_id) {
        $stmt = $conn->prepare("DELETE FROM items WHERE id = ?");
        $stmt->bind_param("i", $item_id);
        $stmt->execute();
        $stmt->close();
        $message = "Item deleted.";
    }
}

// Fetch categories
$categories_result = $conn->query("SELECT * FROM categories");
$categories = [];
while ($row = $categories_result->fetch_assoc()) {
    $categories[] = $row;
}

// Handle category filter
$filter_category = $_GET['category_id'] ?? '';
$category_filter_query = $filter_category ? "WHERE items.category_id = " . intval($filter_category) : "";

// Fetch orders
$orders_result = $conn->query("SELECT * FROM orders ORDER BY created_at DESC");

// Fetch items
$items_result = $conn->query("SELECT items.*, categories.name AS category_name FROM items LEFT JOIN categories ON items.category_id = categories.id $category_filter_query ORDER BY items.id DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard - UrbanHarvest</title>
    <style>
        :root {
            --primary: #618264;
            --accept: #4caf50;
            --reject: #f44336;
            --bg: #f4f4f4;
            --white: #fff;
        }
        body {
            font-family: Arial, sans-serif;
            background: var(--bg);
            color: #333;
            padding: 2rem;
        }
        h1, h2, h3 {
            text-align: center;
        }
        .section {
            margin-bottom: 3rem;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 1.5rem;
        }
        th, td {
            padding: 0.8rem;
            border: 1px solid #ddd;
        }
        th {
            background-color: var(--primary);
            color: white;
        }
        button {
            padding: 0.5rem 1rem;
            border: none;
            border-radius: 4px;
            color: white;
            cursor: pointer;
        }
        .accept {
            background-color: var(--accept);
        }
        .reject {
            background-color: var(--reject);
        }
        .form-control {
            width: 100%;
            padding: 0.7rem;
            margin-bottom: 1rem;
        }
        .item-form {
            max-width: 600px;
            margin: 0 auto;
            background: var(--white);
            padding: 2rem;
            border-radius: 8px;
        }
        .item-image {
            width: 60px;
            height: auto;
        }
        .message {
            background-color: #d4edda;
            color: #155724;
            padding: 1rem;
            margin: 1rem auto;
            max-width: 600px;
            border: 1px solid #c3e6cb;
            border-radius: 4px;
            text-align: center;
        }
        .category-filter {
            text-align: center;
            margin-bottom: 2rem;
        }
    </style>
</head>
<body>

<h1>Admin Dashboard - UrbanHarvest</h1>

<?php if ($message): ?>
    <div class="message"><?= $message ?></div>
<?php endif; ?>

<div class="section">
    <h2>Orders</h2>
    <?php if ($orders_result->num_rows > 0): ?>
        <table>
            <tr>
                <th>Order ID</th>
                <th>User ID</th>
                <th>Name</th>
                <th>Items</th>
                <th>Total Price</th>
                <th>Status</th>
                <th>Created At</th>
                <th>Update</th>
            </tr>
            <?php while ($order = $orders_result->fetch_assoc()): ?>
                <tr>
                    <td><?= $order['order_id'] ?></td>
                    <td><?= $order['user_id'] ?></td>
                    <td><?= $order['name'] ?></td>
                    <td><?= $order['items'] ?></td>
                    <td>₨<?= $order['total_price'] ?></td>
                    <td><?= ucfirst($order['status']) ?></td>
                    <td><?= $order['created_at'] ?></td>
                    <td>
                        <form method="post">
                            <input type="hidden" name="order_id" value="<?= $order['order_id'] ?>">
                            <select name="status" class="form-control">
                                <option value="pending" <?= $order['status'] === 'pending' ? 'selected' : '' ?>>Pending</option>
                                <option value="accepted" <?= $order['status'] === 'accepted' ? 'selected' : '' ?>>Accepted</option>
                                <option value="rejected" <?= $order['status'] === 'rejected' ? 'selected' : '' ?>>Rejected</option>
                            </select>
                            <button type="submit" name="update_status" class="accept">Update</button>
                        </form>
                    </td>
                </tr>
            <?php endwhile; ?>
        </table>
    <?php else: ?>
        <p>No orders found.</p>
    <?php endif; ?>
</div>

<div class="section">
    <h2>Manage Items</h2>

    <div class="item-form">
        <h3>Add New Item</h3>
        <form method="post" enctype="multipart/form-data">
            <input type="hidden" name="item_action" value="create">
            <input type="text" name="name" placeholder="Item Name" class="form-control" required>
            <input type="number" step="0.01" name="price" placeholder="Price" class="form-control" required>
            <input type="text" name="unit" placeholder="Unit (e.g. kg, dozen)" class="form-control" required>
            <select name="category_id" class="form-control" required>
                <option value="">Select Category</option>
                <?php foreach ($categories as $category): ?>
                    <option value="<?= $category['id'] ?>"><?= $category['name'] ?></option>
                <?php endforeach; ?>
            </select>
            <input type="file" name="image" accept="image/*" class="form-control" required>
            <button type="submit" class="accept">Add Item</button>
        </form>
    </div>

    <div class="category-filter">
        <form method="get">
            <select name="category_id" onchange="this.form.submit()">
                <option value="">Filter by Category</option>
                <?php foreach ($categories as $category): ?>
                    <option value="<?= $category['id'] ?>" <?= $filter_category == $category['id'] ? 'selected' : '' ?>>
                        <?= $category['name'] ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </form>
    </div>

    <?php if ($items_result->num_rows > 0): ?>
        <table>
            <tr>
                <th>ID</th>
                <th>Image</th>
                <th>Name</th>
                <th>Price</th>
                <th>Unit</th>
                <th>Category</th>
                <th>Actions</th>
            </tr>
            <?php while ($item = $items_result->fetch_assoc()): ?>
                <tr>
                    <td><?= $item['id'] ?></td>
                    <td>
                        <?php if ($item['image']): ?>
                            <img src="<?= $item['image'] ?>" class="item-image" alt="Item Image">
                        <?php endif; ?>
                    </td>
                    <td><?= $item['name'] ?></td>
                    <td>₨<?= $item['price'] ?></td>
                    <td><?= $item['unit'] ?></td>
                    <td><?= $item['category_name'] ?></td>
                    <td>
                        <!-- Update -->
                        <form method="post" enctype="multipart/form-data" style="display:inline-block;">
                            <input type="hidden" name="item_action" value="update">
                            <input type="hidden" name="item_id" value="<?= $item['id'] ?>">
                            <input type="text" name="name" value="<?= $item['name'] ?>" class="form-control" required>
                            <input type="number" step="0.01" name="price" value="<?= $item['price'] ?>" class="form-control" required>
                            <input type="text" name="unit" value="<?= $item['unit'] ?>" class="form-control" required>
                            <select name="category_id" class="form-control" required>
                                <?php foreach ($categories as $category): ?>
                                    <option value="<?= $category['id'] ?>" <?= $item['category_id'] == $category['id'] ? 'selected' : '' ?>><?= $category['name'] ?></option>
                                <?php endforeach; ?>
                            </select>
                            <input type="file" name="image" class="form-control" accept="image/*">
                            <button type="submit" class="accept">Update</button>
                        </form>
                        <!-- Delete -->
                        <form method="post" style="display:inline-block;">
                            <input type="hidden" name="item_action" value="delete">
                            <input type="hidden" name="item_id" value="<?= $item['id'] ?>">
                            <button type="submit" class="reject" onclick="return confirm('Are you sure?')">Delete</button>
                        </form>
                    </td>
                </tr>
            <?php endwhile; ?>
        </table>
    <?php else: ?>
        <p>No items found for the selected category.</p>
    <?php endif; ?>
</div>
</body>
</html>

<?php $conn->close(); ?>
