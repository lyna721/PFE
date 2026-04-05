<?php
$conn = new mysqli("localhost","root","","custom_shop_db");

if($conn->connect_error){
    die("Connection failed");
}

session_start();
?>