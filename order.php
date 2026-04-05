<?php include("config/db.php"); 

// Block anyone not logged in OR anyone who is a seller
if(!isset($_SESSION['user_id']) || $_SESSION['role'] != "customer"){
    header("Location: index.php"); 
    exit();
}

$product_id = $_GET['id'] ?? 0;
?>
<!DOCTYPE html>
<html>
<head><link rel="stylesheet" href="css/style.css"></head>
<body>
<div class="container">
    <div class="sidebar">
        <h2>CustomShop</h2>
        <a href="index.php">Back to Feed</a>
    </div>

    <div class="main">
        <div class="form-container">
            <div class="form-box">
                <h2>Complete Your Order</h2>
                <form method="POST">
                    <input type="text" name="phone" placeholder="Phone Number" required>
                    <textarea name="location" placeholder="Delivery Address" required></textarea>
                    <button name="submit_order" class="btn">Place Order</button>
                </form>

                <?php
                if(isset($_POST['submit_order'])){
                    $phone = $conn->real_escape_string($_POST['phone']);
                    $loc = $conn->real_escape_string($_POST['location']);
                    $uid = $_SESSION['user_id'];

                    $conn->query("INSERT INTO orders (product_id, user_id, phone, location) 
                                  VALUES ('$product_id', '$uid', '$phone', '$loc')");
                    echo "<p style='color:green;'>Ordered Successfully!</p>";
                }
                ?>
            </div>
        </div>
    </div>
</div>
$product_info = $conn->query("SELECT user_id, name FROM products WHERE id='$product_id'")->fetch_assoc();
$seller_id = $product_info['user_id'];
$conn->query("INSERT INTO notifications (user_id, message) VALUES ('$seller_id', 'New Order! Someone just bought: ".$product_info['name']."')");
</body>
</html>