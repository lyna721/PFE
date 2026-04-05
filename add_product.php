<?php include("config/db.php"); ?>

<h2>Add Product</h2>

<form method="POST" enctype="multipart/form-data">
    <input type="text" name="name" placeholder="Product name"><br><br>
    <input type="file" name="image"><br><br>
    <button name="submit">Add Product</button>
</form>

<?php
if(isset($_POST['submit'])){

    $name = $_POST['name'];

    $image = $_FILES['image']['name'];
    $tmp = $_FILES['image']['tmp_name'];

    $path = "uploads/" . $image;

    move_uploaded_file($tmp, $path);

    $conn->query("INSERT INTO products (name,image) VALUES ('$name','$path')");

    echo "Product added!";
}
?>