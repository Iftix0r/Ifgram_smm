<?php


require ('sql_connect.php');

$s = mysqli_query($connect,"SELECT * FROM `services`");
while($a = mysqli_fetch_assoc($s)){
$service = $a['service_id'];
$e = mysqli_query($connect,"SELECT * FROM myorder WHERE status='Completed' AND service='$service'");
$b = mysqli_fetch_assoc($e);
$d[]=$b?$b:;
}
echo json_encode($d,JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);