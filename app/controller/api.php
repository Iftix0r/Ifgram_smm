<?php


require ('sql_connect.php');

header('Content-type:application/json');
$key = $_REQUEST['key'];
$service = $_REQUEST['service'];
$action = $_REQUEST['action'];
$answer_number = $_REQUEST['answer_number'];
$username = $_REQUEST['username'];
$link = $_REQUEST['link'];
$order = $_REQUEST['order'];
$quantity = $_REQUEST['quantity'];


if($action == "balance"){
$b = mysqli_query($connect,"SELECT * FROM users WHERE api_key = '$key'");
$c = mysqli_num_rows($b);
$res = mysqli_fetch_assoc($b);
if($c){
echo json_encode(['balance'=>$res['balance'],'currency'=>"UZS"]);
}else{
echo json_encode(['error'=>"Incorrect API key"]);
}
exit();
}

if($action == "add") {
$b = mysqli_query($connect,"SELECT * FROM users WHERE api_key = '$key' && status = 'active'");
$c = mysqli_num_rows($b);
$res = mysqli_fetch_assoc($b);
if($c){
$s = mysqli_query($connect,"SELECT * FROM `services` WHERE service_id = '$service' AND service_status='on'");
$cy = mysqli_num_rows($s);
$resy = mysqli_fetch_assoc($s);
if($cy){
if($link){
if($quantity>=$resy['service_min']){
if($quantity<=$resy['service_max']){
$narxi=$resy['service_price']/1000*$quantity;
if($res['balance']>=$narxi){
$api = $resy['api_service'];
$m = mysqli_fetch_assoc(mysqli_query($connect,"SELECT * FROM `providers` WHERE id = '$api'"));
$surl = $m['api_url'];
$skey =$m['api_key'];
$sid = $resy['service_api'];
$j=json_decode(file_get_contents($surl."?key=".$skey."&action=add&service=$sid&link=$link&quantity=$quantity"),1);
$jid=$j['order'];
$jer=$j['error'];
if(empty($jid)){
echo json_encode(['error'=>"Unknown error, please try again later"]);
exit;
}else{
$orders = mysqli_num_rows(mysqli_query($connect,"SELECT * FROM `orders`"));
$order_id =$orders+1;
$ball = $res['balance']-$narxi;
$sav = date("Y.m.d H:i:s");
mysqli_query($connect,"UPDATE `users` SET balance='$ball' WHERE api_key = '$key'");
mysqli_query($connect,"INSERT INTO myorder(`order_id`,`user_id`,`retail`,`status`,`service`,`order_create`,`last_check`) VALUES ('$order_id','$res[id]','$narxi','Pending','$service','$sav','$sav');");
mysqli_query($connect,"INSERT INTO orders(`api_order`,`order_id`,`provider`,`status`) VALUES ('$jid','$order_id','$api','Pending');");
echo json_encode(['order'=>$order_id,'charge'=>$narxi,'currency'=>"UZS"]);
exit;
}

//true
exit;
}else{
		echo json_encode(['error'=>"Not enough funds on balance"]);
		exit;
         }
}else{
		echo json_encode(['error'=>"Quantity less than maximum ".$resy['service_max']]);
		exit;
		 }
}else{
		echo json_encode(['error'=>"Quantity less than minimal ".$resy['service_min']]);
	    exit;
         }
}else{
		echo json_encode(['error'=>"Bad link"]);
		exit;
		 }
}else{
        echo json_encode(['error'=>"Incorrect service ID"]);
        exit;
         }
}else{
         echo json_encode(['error'=>"invalid API key"]);
         exit;
}
}


if($action == "services"){
$b = mysqli_query($connect,"SELECT * FROM users WHERE api_key = '$key'");
$c = mysqli_num_rows($b);
$res = mysqli_fetch_assoc($b);
if($c){
$s = mysqli_query($connect,"SELECT * FROM `services`");
while($a = mysqli_fetch_assoc($s)){
	$cid = $a['category_id'];
	$ca = mysqli_fetch_assoc(mysqli_query($connect,"SELECT * FROM cates WHERE cate_id = $cid"));
	$categ = $ca['category_id'];
	$cas= mysqli_fetch_assoc(mysqli_query($connect,"SELECT * FROM categorys WHERE category_id = $categ"));
$category=base64_decode($cas['category_name'])." ".base64_decode($ca['name']);
	
$name = base64_decode($a['service_name']);
$detail = json_decode($a["api_detail"],true);
$arr [] =['service'=>$a['service_id'],'category'=>$category,"name"=>$name,"rate"=>$a['service_price'],"min"=>$a['service_min'],"max"=>$a['service_max'],"type"=>$a['service_type'],"refill"=>$detail['refill'],"cancel"=>$detail['cancel'],"dripfeed"=>$detail['dripfeed']];
}
echo json_encode($arr,JSON_UNESCAPED_UNICODE);
}else{
echo json_encode(['error'=>"Incorrect API key"]);
}
exit();
}

if($action=="orders"){
$b = mysqli_query($connect,"SELECT * FROM users WHERE api_key = '$key'");
$c = mysqli_num_rows($b);
$res = mysqli_fetch_assoc($b);
if($c){
$id =$res['id'];
$q = mysqli_query($connect,"SELECT * FROM myorder WHERE user_id = $id");

while($d = mysqli_fetch_assoc($q)){
$h[]=['customer'=>$d['user_id'],'service'=>$d['service'],'order'=>$d['order_id'],'status'=>$d['status'],'charge'=>$d['retail'],'currency'=>"UZS"];

}
echo $h ? json_encode($h,JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) : json_encode(['error'=>"No orders"]);
}else{
echo json_encode(array('error'=>"Incorrect API Key"));
}

exit;
}

if($action=="status"){
$b = mysqli_query($connect,"SELECT * FROM `users` WHERE api_key = '$key'");
$c = mysqli_num_rows($b);
$res = mysqli_fetch_assoc($b);

if($c){
if(is_string($_GET['orders'])){
$order_ids = explode(',', $_GET['orders']);
if(is_array($order_ids)){
$data = [];

$id =$res['id'];
foreach($order_ids as $order_id){
if(!is_numeric($order_id)){
$data[$order_id]=array('error'=>$order_id." is not a number");
}else{
$rew = mysqli_fetch_assoc(mysqli_query($connect,"SELECT * FROM `myorder` WHERE order_id = $order_id AND user_id = $id"));


$resi = mysqli_query($connect, "SELECT * FROM orders WHERE order_id = $order_id");
$stati = mysqli_fetch_assoc($resi);
$prv = $stati['provider'];
$a = mysqli_query($connect,"SELECT * FROM providers WHERE id = $prv");
$c = mysqli_fetch_assoc($a);
$prg = $stati['provider'];
$m = mysqli_fetch_assoc(mysqli_query($connect,"SELECT * FROM `providers` WHERE id = '$prg'"));
$surl = $m['api_url'];
$skey =$m['api_key'];
$api = json_decode(file_get_contents($surl."?key=$skey&action=status&order=".$stati['api_order'].""), 1);



if(empty($rew)){
$data[$order_id]=array('error'=>"Incorrect order ID");
}else{
$data[$order_id]=array('order'=>$order_id,'status'=>$rew['status'],'start_count'=>$api['start_count'] ? $api['start_count'] : "0",'remains'=>$api['remains'] ? $api['remains'] : "0",'charge'=>$rew['retail'],'currency'=>"UZS");
}
}
}
}
}else{
if(is_string($_GET['order'])){
$id =$res['id'];
$rew = mysqli_fetch_assoc(mysqli_query($connect,"SELECT * FROM `myorder` WHERE order_id = $order AND user_id = $id"));
if(empty($rew)){
$data=array('error'=>"Incorrect order ID");
}else{
	
$resi = mysqli_query($connect, "SELECT * FROM orders WHERE order_id = $order");
$stati = mysqli_fetch_assoc($resi);
$prv = $stati['provider'];
$a = mysqli_query($connect,"SELECT * FROM providers WHERE id = $prv");
$c = mysqli_fetch_assoc($a);
$prg = $stati['provider'];
$m = mysqli_fetch_assoc(mysqli_query($connect,"SELECT * FROM `providers` WHERE id = '$prg'"));
$surl = $m['api_url'];
$skey =$m['api_key'];
$api = json_decode(file_get_contents($surl."?key=$skey&action=status&order=".$stati['api_order'].""), 1);

	
$data=array('order'=>$order,'status'=>$rew['status'],'start_count'=>$api['start_count'] ? $api['start_count'] : "0",'remains'=>$api['remains'] ? $api['remains'] : "0",'charge'=>$rew['retail'],'currency'=>"UZS");
}
}
}
}else{
echo json_encode(['error'=>"Incorrect API key"]);
exit;
}
echo $data ? json_encode($data,JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) : json_encode(['error'=>"Invalid order ID"]);
exit;
}


echo json_encode(['error'=>"Incorrect request"]);