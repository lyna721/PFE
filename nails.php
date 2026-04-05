<?php include("config/db.php"); 

if(!isset($_SESSION['user_id'])){
    header("Location: login.php");
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <link rel="stylesheet" href="css/style.css">
    <title>My Nail Designs</title>
</head>
<body>

<div class="container">
    <div class="sidebar">
        <h2>CustomShop</h2>
        <img src="<?php echo $_SESSION['pic']; ?>" width="80" style="border-radius:50%"><br>
        <p><strong><?php echo $_SESSION['name']; ?></strong></p>
        <a href="index.php">Home</a>
        <a href="nails.php">My Nails</a>
        <a href="measurements.php">My Sizes</a>
        <a href="logout.php">Logout</a>
    </div>

    <div class="main">
        <h2 class="title">Nail Customization</h2>

        <div class="form-container" style="display: flex; flex-direction: column; align-items: center; justify-content: center; min-height: 70vh;">
            <div class="form-box" style="width: 100%; max-width: 400px; text-align: center;">
                <p>Upload a photo of your hand or a reference design:</p>
                <form method="POST" enctype="multipart/form-data">
                    <input type="file" name="img" required>
                    <button name="upload" class="btn">Upload Design</button>
                </form>

                <?php
                if(isset($_POST['upload'])){
                    $img_name = time() . $_FILES['img']['name'];
                    $target = "uploads/" . $img_name;
                    
                    if(move_uploaded_file($_FILES['img']['tmp_name'], $target)){
                        $conn->query("INSERT INTO nails(user_id, image) VALUES('".$_SESSION['user_id']."', '$target')");
                        echo "<div style='margin-top:20px;'>";
                        echo "<p>Preview:</p>";
                        echo "<img src='$target' width='250' style='border-radius:15px; border: 3px solid #eee;'>";
                        echo "</div>";
                    }
                }
                ?>
            </div>
        </div>
    </div>
</div>

</body>
</html>