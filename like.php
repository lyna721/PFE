<?php 
include("config/db.php");

if(!isset($_SESSION['user_id'])){
    echo "login_required";
    exit();
}

$id = $conn->real_escape_string($_POST['id']);
$uid = $_SESSION['user_id'];
$uname = $_SESSION['name'];

// Toggle Like
$check = $conn->query("SELECT * FROM likes WHERE product_id='$id' AND user_id='$uid'");

if($check->num_rows == 0) {
    // Add Like
    $conn->query("INSERT INTO likes(product_id,user_id) VALUES('$id','$uid')");
    
    // Create Notification for Seller
    $prod = $conn->query("SELECT user_id, name FROM products WHERE id='$id'")->fetch_assoc();
    $seller_id = $prod['user_id'];
    if($seller_id != $uid) {
        $msg = $uname . " liked your product: " . $prod['name'];
        $conn->query("INSERT INTO notifications (user_id, message) VALUES ('$seller_id', '$msg')");
    }
    echo "liked";
} else {
    // Remove Like
    $conn->query("DELETE FROM likes WHERE product_id='$id' AND user_id='$uid'");
    echo "unliked";
}
?>