<?php
$server = $_SERVER['HTTP_HOST'];
$first_route  = explode('?',$_SERVER["REQUEST_URI"]);
$gets         = explode('&',$first_route[1]);
  foreach ($gets as $get) {
    $get = explode('=',$get);
    $_GET[$get[0]]  = $get[1];
  }
$routes       = array_filter( explode('/',$first_route[0]) );

if( SUBFOLDER === true ){
array_shift($routes);
$route = $routes;
  }else {
    foreach ($routes as $index => $value):
      $route[$index-1] = $value;
    endforeach;
  }
  if($route == null){
  	include("app/controller/index.php");
 }elseif($route[0]=="api" and $route[1]=="v2") {
 	include("app/controller/api.php");
 }elseif($route[0]=="services"){
 	include("app/controller/services.php");
 }elseif($route[0]=="orders"){
include("app/controller/orders.php");
}elseif($route[0]=="api"){
include("app/controller/api2.php");
}else{
include("app/controller/404.php");
}
