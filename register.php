<?php include("config/db.php"); ?>
<!DOCTYPE html>
<html>
<head><link rel="stylesheet" href="css/style.css"></head>
<body>
<div class="container">
    <div class="main">
        <div class="form-container">
            <div class="form-box">
                <h2>Join CustomShop</h2>
                <form method="POST">
                    <input type="text" name="name" placeholder="Full Name" required>
                    <input type="email" name="email" placeholder="Email Address" required>
                    <input type="password" name="pass" placeholder="Password" required>
                    <select name="role">
                        <option value="customer">Customer</option>
                        <option value="seller">Seller</option>
                    </select>
                    <button name="reg_btn" class="btn">Register</button>
                </form>

                <?php
                if(isset($_POST['reg_btn'])){
                    $email = $conn->real_escape_string($_POST['email']);
                    
                    // Check if email exists
                    $check = $conn->query("SELECT id FROM users WHERE email='$email'");
                    if($check->num_rows > 0){
                        echo "<p style='color:red;'>Error: This email is already taken.</p>";
                    } else {
                        $name = $conn->real_escape_string($_POST['name']);
                        $pass = password_hash($_POST['pass'], PASSWORD_DEFAULT);
                        $role = $_POST['role'];
                        
                        $conn->query("INSERT INTO users(name,email,password,role) VALUES('$name','$email','$pass','$role')");
                        echo "<p style='color:green;'>Success! You can now login.</p>";
                    }
                }
                ?>
            </div>
        </div>
    </div>
</div>
</body>
</html>