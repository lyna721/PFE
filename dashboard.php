<?php 
include("config/db.php"); 
if(!isset($_SESSION['user_id']) || $_SESSION['role'] != 'seller') { header("Location: login.php"); exit(); }

$user_id = $_SESSION['user_id'];
// Fetch orders for this seller
$orders = $conn->query("SELECT * FROM orders WHERE seller_id = '$user_id' ORDER BY created_at DESC");
?>
<!DOCTYPE html>
<html>
<head>
    <link rel="stylesheet" href="css/style.css">
    <style>
        .main-content { margin-left: 280px; padding: 40px; }
        .order-card { background: white; padding: 20px; border-radius: 10px; margin-bottom: 15px; box-shadow: 0 2px 5px rgba(0,0,0,0.1); }
    </style>
</head>
<body>
<div class="container">
    <div class="sidebar">
        <h2>Seller Panel</h2>
        <a href="index.php">🏠 Home Feed</a>
        <a href="create_product.php">✨ Create New Design</a>
        <a href="dashboard.php" class="active">📦 My Orders</a>
        <hr style="border: 0.5px solid #444; margin: 15px 0;">
        <a href="logout.php">🚪 Logout</a>
    </div>

    <div class="main-content">
        <h1>Incoming Orders</h1>
        <?php if($orders->num_rows > 0): ?>
            <?php while($row = $orders->fetch_assoc()): ?>
                <div class="order-card">
                    <p><strong>Order ID:</strong> #<?php echo $row['id']; ?></p>
                    <p><strong>Status:</strong> <?php echo $row['status']; ?></p>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <div class="order-card">
                <p>No orders have been placed yet.</p>
            </div>
        <?php endif; ?>
    </div>
</div>
</body>
</html>