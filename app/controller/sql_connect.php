<?php
ob_start();
define("DB_SERVER","localhost"); 
define("DB_USERNAME","ifgram"); 
define("DB_PASSWORD","Iftixor2006"); 
define("DB_NAME","ifgram");
$site_url = $_SERVER['HTTP_HOST'];
$connect = mysqli_connect(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);
mysqli_set_charset($connect,"utf8mb4");
$bot = "ifgrambot";


$th = mysqli_query($connect,"SELECT * FROM `settings` WHERE id = 1");
$row = mysqli_fetch_assoc($th);
$theme = $row['site_theme'];
$style =$row['site_style'];
?>