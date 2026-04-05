<?php include("config/db.php"); ?>

<!DOCTYPE html>
<html>
<head>
<link rel="stylesheet" href="css/style.css">
<title>Login</title>
</head>
<body>

<div class="container">

<div class="sidebar">
<h2>CustomShop</h2>
<a href="index.php">Home</a>
<a href="login.php">Login</a>
<a href="register.php">Register</a>
</div>

<div class="main">

<h2 class="title">Login</h2>

<div class="form-container">
<div class="form-box">

<form method="POST">
<input type="email" name="email" placeholder="Email">
<input type="password" name="password" placeholder="Password">
<button name="login">Login</button>
</form>

<?php
if(isset($_POST['login'])){
$email = $_POST['email'];
$password = $_POST['password'];

$res = $conn->query("SELECT * FROM users WHERE email='$email' AND password='$password'");

if($res->num_rows > 0){
$user = $res->fetch_assoc();

$_SESSION['user_id'] = $user['id'];
$_SESSION['role'] = $user['role'];
$_SESSION['name'] = $user['name'];
$_SESSION['pic'] = $user['profile_pic'];

header("Location: index.php");
exit();
}else{
echo "Wrong login";
}
}
?>

</div>
</div>

</div>
</div>

</body>
</html>