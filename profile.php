<?php include("config/db.php"); 
if(!isset($_SESSION['user_id'])) { header("Location: login.php"); exit(); }
$user = $conn->query("SELECT * FROM users WHERE id='".$_SESSION['user_id']."'")->fetch_assoc();
?>
<!DOCTYPE html>
<html>
<head><link rel="stylesheet" href="css/style.css"></head>
<body>
<div class="container">
    <div class="sidebar">
        <a href="index.php">← Back</a>
    </div>
    <div class="main">
        <div class="form-container">
            <div class="form-box">
                <h2>Profile Settings</h2>
                <form method="POST" enctype="multipart/form-data">
                    <img src="<?php echo $user['profile_pic']; ?>" width="80" style="border-radius:50%"><br>
                    <input type="text" name="name" value="<?php echo $user['name']; ?>">
                    <textarea name="bio" placeholder="Bio..."><?php echo $user['bio']; ?></textarea>
                    <input type="file" name="pic">
                    <button name="save" class="btn">Save Profile</button>
                </form>
                <?php
                if(isset($_POST['save'])){
                    $n = $_POST['name'];
                    $b = $_POST['bio'];
                    $sql = "UPDATE users SET name='$n', bio='$b'";
                    if($_FILES['pic']['name']){
                        $p = "uploads/".time().$_FILES['pic']['name'];
                        move_uploaded_file($_FILES['pic']['tmp_name'], $p);
                        $sql .= ", profile_pic='$p'";
                        $_SESSION['pic'] = $p;
                    }
                    $sql .= " WHERE id='".$_SESSION['user_id']."'";
                    $conn->query($sql);
                    $_SESSION['name'] = $n;
                    header("Location: profile.php");
                }
                ?>
            </div>
        </div>
    </div>
</div>
</body>
</html>