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
    <title>My Measurements</title>
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
        <h2 class="title">Body Measurements</h2>
        
        <div class="form-container" style="display: flex; justify-content: center; align-items: center; min-height: 60vh;">
            <div class="form-box" style="width: 100%; max-width: 400px; background: #fff; padding: 20px; border-radius: 10px; box-shadow: 0 4px 15px rgba(0,0,0,0.1);">
                <form method="POST">
                    <label>Chest (cm)</label>
                    <input type="text" name="chest" placeholder="e.g. 95" required>
                    <label>Waist (cm)</label>
                    <input type="text" name="waist" placeholder="e.g. 80" required>
                    <label>Height (cm)</label>
                    <input type="text" name="height" placeholder="e.g. 175" required>
                    <button name="save" class="btn">Save Profile</button>
                </form>

                <?php
                if(isset($_POST['save'])){
                    $chest = $conn->real_escape_string($_POST['chest']);
                    $waist = $conn->real_escape_string($_POST['waist']);
                    $height = $conn->real_escape_string($_POST['height']);
                    
                    $conn->query("INSERT INTO measurements(user_id, chest, waist, height)
                                  VALUES('".$_SESSION['user_id']."', '$chest', '$waist', '$height')");
                    echo "<p style='color:green; text-align:center; margin-top:10px;'>Measurements saved!</p>";
                }
                ?>
            </div>
        </div>
    </div>
</div>

</body>
</html>