<?php 
session_start();
if(isset($_SESSION['user'])){
    header("Location: tong-quan.php");
    exit();
}
include('index.html'); 
?>