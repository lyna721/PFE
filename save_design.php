<?php
include("config/db.php");
if(!isset($_SESSION['user_id'])) { exit("Login required"); }

$user_id = $_SESSION['user_id'];
$name = $conn->real_escape_string($_POST['name']);
$price = $conn->real_escape_string($_POST['price']);
$category = $conn->real_escape_string($_POST['category']);
$imgData = $_POST['image'] ?? '';

if(empty($imgData)) { exit("No image data received"); }

$imgData = str_replace('data:image/png;base64,', '', $imgData);
$imgData = str_replace(' ', '+', $imgData);
$data = base64_decode($imgData);

$fileName = "design_" . time() . ".png";
$filePath = "uploads/" . $fileName;

if(file_put_contents($filePath, $data)){
    $sql = "INSERT INTO products (user_id, name, image, price, category, status) 
            VALUES ('$user_id', '$name', '$filePath', '$price', '$category', 'Active')";
    
    if($conn->query($sql)){
        echo "success";
    } else {
        echo $conn->error;
    }
} else {
    echo "Failed to save image file.";
}
?>