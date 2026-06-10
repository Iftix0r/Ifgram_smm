<?php


	
	
session_start();
date_default_timezone_set("Asia/Tashkent");
$time = date('H:i');
ob_start();
define('API_KEY',"7832943702:AAGQ0xWS5EONaFdFJ89DR6UdLHiPjBGN5YM");
$admin="2114098498";
$bot=bot(getMe)->result->username;

function enc($var,$exception) {
if($var=="encode"){
return base64_encode($exception);
}elseif($var=="decode"){
return base64_decode($exception);
}
}

function keyboard($a=[]){
$d=json_encode([
inline_keyboard=>$a
]);
return $d;
}

function api_query($s){
$qas = array("ssl"=>array("verify_peer"=>false,"verify_peer_name"=>false));
$content = file_get_contents($s, false, stream_context_create($qas));
return $content ? $content : json_encode(['balance'=>" ?"]);
}

require ("../app/controller/sql_connect.php");

	

function arr($p){
global $connect;
$s = mysqli_fetch_assoc(mysqli_query($connect,"SELECT * FROM `providers` WHERE id = $p"));
$data = json_decode(file_get_contents($s['api_url']."?key=".$s['api_key']."&action=services"),1);
$values=[];
$new_arr = [];
$co=0;
foreach($data as $value){

if(!in_array($value['category'], $new_arr)){
$new_arr[] = $value['category'];
$co++;
$values[] =['id'=>$co,'name'=>$value['category']];
}else{
continue;
}
}
$val = ['count'=>$co,'results'=>$values];
return $values ? json_encode($val) : json_encode(["error"=>1]);
}



function bot($method,$datas=[]){
    $url = "https://api.telegram.org/bot".API_KEY."/".$method;
    $ch = curl_init();
    curl_setopt($ch,CURLOPT_URL,$url);
    curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
    curl_setopt($ch,CURLOPT_POSTFIELDS,$datas);
    $res = curl_exec($ch);
    if(curl_error($ch)){
        var_dump(curl_error($ch));
    }else{
        return json_decode($res);
    }
}

function rmdirPro($path){
    $scan = array_diff(scandir($path), ['.','..']);
    foreach($scan as $value){
        if(is_dir("{$path}/{$value}"))
            rmdirPro("{$path}/{$value}");
        else
            @unlink("{$path}/{$value}");
    }
    rmdir($path);
}



function trans($x){
$e = json_decode(file_get_contents("http://translate.googleapis.com/translate_a/single?client=gtx&sl=auto&tl=uz&dt=t&q=".urlencode($x).""),1);
return $e[0][0][0];
}







function number($a){
$form = number_format($a,00,' ',' ');
return $form;
}

function del(){
global $cid,$mid,$chat_id,$message_id;
return bot('deleteMessage',[
'chat_id'=>$chat_id.$cid,
'message_id'=>$message_id.$mid,
]);
}


function edit($id,$mid,$tx,$m){
return bot('editMessageText',[
'chat_id'=>$id,
'message_id'=>$mid,
'text'=>$tx,
'parse_mode'=>"HTML",
'reply_markup'=>$m,
]);
}



function sms($id,$tx,$m){
return bot('sendMessage',[
'chat_id'=>$id,
'text'=>$tx,
'parse_mode'=>"HTML",
'reply_markup'=>$m,
]);
}

function referal($hi){
    $daten = [];
    $rev = [];
    $fayllar = glob("./user/*.*");
    foreach($fayllar as $file){
        if(mb_stripos($file,".users")!==false){
        $value = file_get_contents($file);
        $id = str_replace(["./user/",".users"],["",""],$file);
        $daten[$value] = $id;
        $rev[$id] = $value;
        }
        echo $file;
    }

    asort($rev);
    $reversed = array_reverse($rev);
    for($i=0;$i<$hi;$i+=1){
        $order = $i+1;
        $id = $daten["$reversed[$i]"];
        $ism=bot('getChat',[
        'chat_id'=>$id,
        ])->result->first_name;
        
        $text.= "<b>{$order}</b>. <a href='tg://user?id={$id}'>{$ism}</a> - "."<code>".floor($reversed[$i])."</code>"." <b> ta</b>"."\n";
    }
    return $text;
}


function get($h){
return file_get_contents($h);
}

function put($h,$r){
file_put_contents($h,$r);
}






function joinchat($id){
$array = array("inline_keyboard");
$get = file_get_contents("set/channel");
$ex = explode("\n",$get);
$soni = substr_count($get,"@");
if($get == null){
return true;
}else{
for($i=0;$i<=count($ex)-1;$i++){
$first_line = $ex[$i];
$kanall=str_replace("@","",$first_line);
     $ret = bot("getChatMember",[
         "chat_id"=>$first_line,
         "user_id"=>$id,
         ]);
$stat = $ret->result->status;
         if((($stat=="creator" or $stat=="administrator" or $stat=="member"))){
      $array['inline_keyboard']["$i"][0]['text'] = "✅ ".$first_line;
$array['inline_keyboard']["$i"][0]['url'] = "https://t.me/$kanall";
         }else{
$array['inline_keyboard']["$i"][0]['text'] = "❌ ".$first_line;
$array['inline_keyboard']["$i"][0]['url'] = "https://t.me/$kanall";
$uns = true;
}
}
$array['inline_keyboard']["$i"][0]['text'] = "🔄 Tekshirish";
$array['inline_keyboard']["$i"][0]['callback_data'] = "result";
if($uns == true){
     bot('sendMessage',[
         'chat_id'=>$id,
         'text'=>"⚠️ <b>Botdan foydalanish uchun, quyidagi kanallarga obuna bo'ling:</b>",
'parse_mode'=>html,
'reply_markup'=>json_encode($array),
]);  


}else{
return true;
}
}

}



$update = json_decode(file_get_contents('php://input'));
$message = $update->message;
$edituz = $update->callback_query->message->from->id;
$mesuz = $update->callback_query->message->message_id;
$cid = $message->chat->id;
$cidtyp = $message->chat->type;
$miid = $message->message_id;
$name = $message->chat->first_name;
$user1 = $message->from->username;
$tx = $message->text;
$callback = $update->callback_query;
$mmid = $callback->inline_message_id;
$mes = $callback->message;
$mid = $mes->message_id;
$cmtx = $mes->text;
$mmid = $callback->inline_message_id;
$idd = $callback->message->chat->id;
$cbid = $callback->from->id;
$cbuser = $callback->from->username;
$data = $callback->data;
$ida = $callback->id;
$cqid = $update->callback_query->id;
$qid=$cqid;
$cbins = $callback->chat_instance;
$cbchtyp = $callback->message->chat->type;
$step = file_get_contents("step/$from_id.step");
$update = json_decode(file_get_contents('php://input'));
$message = $update->message;
$mid = $message->message_id;
$msgs = json_decode(file_get_contents('msgs.json'),true);
$data = $update->callback_query->data;
$type = $message->chat->type;
$text = $message->text;
$sd = $message->text;
$uid= $message->from->id;
$gname = $message->chat->title;
$left = $message->left_chat_member;
$new = $message->new_chat_member;
$name = $message->from->first_name;
$bio = $message->from->about;
$repid = $message->reply_to_message->from->id;
$repname = $message->reply_to_message->from->first_name;
$newid = $message->new_chat_member->id;
$leftid = $message->left_chat_member->id;

$botdel = $update->my_chat_member->new_chat_member;
$botdel_id = $update->my_chat_member->from->id;
$userstatus = $botdel->status;

$newname = $message->new_chat_member->first_name;
$leftname = $message->left_chat_member->first_name;
$username = $message->from->username;
$cmid = $update->callback_query->message->message_id;
$cusername = $message->chat->username;
$repmid = $message->reply_to_message->message_id; 
$ccid = $update->callback_query->message->chat->id;
$cuid = $update->callback_query->message->from->id;
$from_id = $message->from->id;
$chat_id = $update->callback_query->message->chat->id;
$message_id = $update->callback_query->message->message_id;
$call = $update->callback_query;
$mes = $call->message;
$data = $call->data;
$qid = $call->id;
$callbackdata = $update->callback_query->data;
$callcid = $mes->chat->id;
$callmid = $mes->message_id;
$callfrid = $call->from->id;
$calluser = $mes->chat->username;
$callfname = $call->from->first_name;
$photo = $message->photo;
$gif = $message->animation;
$video = $message->video;
$music = $message->audio;
$voice = $message->voice;
$sticker = $message->sticker;
$document = $message->document;
$for = $message->forward_from;
$for_id=$for->id;
$contact = $message->contact;
$nomer_id = $contact->user_id;
$nomer_user = $contact->username;
$nomet_name = $contact->first_name;
$nomer_ph = $contact->phone_number;
$cid2=$chat_id;
$mid2=$message_id;
$sana=date("d/m/Y | H:i");

function generate(){
$arr = array('A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','R','S','T','U','V','X','Y','Z','1','2','3','4','5','6','7','8','9','0');
$pass = "";
for($i = 0; $i < 7; $i++){
$index = rand(0, count($arr) - 1);
$pass .= $arr[$index];
}
return $pass;
}

function adduser($cid){
	global $connect;
$result = mysqli_query($connect, "SELECT * FROM users WHERE id = $cid");
$row = mysqli_fetch_assoc($result);
if($row){
}else{
$key = md5(uniqid());
$referal = generate();
$rew = mysqli_num_rows(mysqli_query($connect,"SELECT * FROM users"));
$new =$rew+1;
mysqli_query($connect,"INSERT INTO users(`user_id`,`id`,`status`,`balance`,`outing`,`api_key`,`referal`) VALUES ('$new','$cid','active','0','0','$key','$referal');");
}
}



if($botdel){
if($userstatus == "kicked"){
$sql = "UPDATE `users` SET `status` = 'deactive' WHERE `id` = '$botdel_id'";
$result = mysqli_query($connect, $sql);
}
}


if(isset($update)) {
$result = mysqli_query($connect,"SELECT * FROM users WHERE id = $cid$chat_id");
$rew = mysqli_fetch_assoc($result);
if($rew['status']=="deactive"){
exit();
}
}

if($update){
if(get("status.txt")=="frozen"){
sms($cid.$chat_id,"🥶 Panel vaqtincha muzlatilgan",null);

}
}

$resu = mysqli_query($connect,"SELECT * FROM `settings`");
$setting = mysqli_fetch_assoc($resu);

mkdir("user");
mkdir("set");


$pul=get("user/$chat_id.pul");

$step = get("user/$cid.step");
$stepc = get("user/$chat_id.step");

$ort=json_encode([
'resize_keyboard'=>true,
'keyboard'=>[
[['text'=>"➡️ Orqaga"]],
]
]);

$aort=json_encode([
'resize_keyboard'=>true,
'keyboard'=>[
[['text'=>"🗄️ Boshqaruv"]],
]
]);

$panel=json_encode([
'resize_keyboard'=>true,
'keyboard'=>[
[['text'=>"⚙️ Asosiy sozlamalar"]],
[['text'=>"🔔 Xabar yuborish"],['text'=>"📊 Statistika"]],
[['text'=>"👤 Foydalanuvchini boshqarish"]],
[['text'=>"🇺🇿 Valyuta kursi"],['text'=>"⏰ Cron sozlamasi"]],
[['text'=>"➡️ Orqaga"]],
]]);

if($text=="⏰ Cron sozlamasi" and $cid==$admin){
sms($cid,"
📝 Quyidagi manzillarni cron qiling
<pre>https://".$_SERVER['SERVER_NAME']."".$_SERVER['SCRIPT_NAME']."?update=send</pre> \n- Pochta xabari uchun cron (1 daqiqa)

 <pre>https://".$_SERVER['SERVER_NAME']."".$_SERVER['SCRIPT_NAME']."?update=status</pre>\n- Buyurtma xolati uchun cron (1 daqiqa)

<pre>https://".$_SERVER['SERVER_NAME']."/".str_replace(["/","bot.php"],["",""],$_SERVER['PHP_SELF'])."/update.php</pre> \n- Narxlarni avtomatik yangilash uchun cron (1 daqiqa)
",$panel);

}


if($text=="🗄️ Boshqaruv" and $cid==$admin){
sms($cid,"🖥️ Boshqaruv paneli",$panel);
unlink("user/$cid.step");
exit;
}

if($text=="📊 Statistika" and $cid==$admin){
$stat=0;
$res = mysqli_query($connect, "SELECT * FROM users");
$stat = mysqli_num_rows($res);
$resi = mysqli_query($connect, "SELECT * FROM orders");
$stati = mysqli_num_rows($resi);
$ac =0;
$dc =0;
$pc =0;
$cc =0;
$bc =0;
$fc =0;
$jc =0;
$ppc=0;
$cp=0;
$stati ? $stati = $stati : $stati = "0";
while($hi=mysqli_fetch_assoc($resi)){
if($hi['status']=="Pending") {
$pc++;
}elseif($hi['status']=="Completed"){
$cc++;
}elseif($hi['status']=="Canceled") {
$bc++;
}elseif($hi['status']=="Failed"){
$fc++;
}elseif($hi['status']=="In progress"){
$jc++;
}elseif($hi['status']=="Partial"){
$ppc++;
}elseif($hi['status']=="Processing"){
$cp++;
}
}

while($h=mysqli_fetch_assoc($res)){
if($h['status']=="active") {
$ac++;
}elseif($h['status']=="deactive"){
$dc++;
}
}
$seco=0;
$resit= mysqli_query($connect, "SELECT * FROM services");
$seco = mysqli_num_rows($resit);

sms($cid,"
<b>📊 Statistika</b>
• Jami foydalanuvchilar: $stat ta
• Aktiv foydalanuvchilar: $ac ta
• O'chirilgan foydalanuvchilar: $dc ta

<b>📊 Buyurtmalar</b>
• Jami buyurtmalar: $stati ta
• Bajarilgan buyurtmalar: $cc ta
• Kutilayotgan buyurtmalar: $pc ta
• Jarayondagi buyurtmalar: $jc ta
• Bekor qilingan buyurtmalar: $bc ta
• Muvaffaqiyatsiz buyurtmalar: $fc ta
• Qisman bajarilgan buyurtmalar: $ppc ta
• Qayta ishlangan buyurtmalar: $cp ta

<b>📊 Xizmatlar</b>:
• Barcha xizmatlar: $seco ta
",keyboard([
[['text'=>"♻️ Buyurtmalar xolatini yangilash",'callback_data'=>"update=orders"]],
]));
unlink("user/$cid.step");

}

if((stripos($data,"update=")!==false)){
$resi = mysqli_query($connect, "SELECT * FROM orders");
$stati = mysqli_num_rows($resi);
$ac =0;
$dc =0;
$pc =0;
$cc =0;
$bc =0;
$fc =0;
$jc =0;
$cp =0;
$ppc=0;

$stati ? $stati = $stati : $stati = "0";
while($hi=mysqli_fetch_assoc($resi)){
if($hi['status']=="Pending") {
$pc++;
}elseif($hi['status']=="Completed"){
$cc++;
}elseif($hi['status']=="Canceled") {
$bc++;
}elseif($hi['status']=="Failed"){
$fc++;
}elseif($hi['status']=="In progress"){
$jc++;
}elseif($hi['status']=="Processing"){
$cp++;
}elseif($hi['status']=="Partial"){
$ppc++;
}
}
	
$res = explode("=", $data)[1];
if($res=="orders") {

del();
sms($cid2,"
📊 Buyurtmalar ro'yxati:

• Jami buyurtmalar: $stati ta
• Bajarilgan buyurtmalar: $cc ta
• Kutilayotgan buyurtmalar: $pc ta
• Jarayondagi buyurtmalar: $jc ta
• Bekor qilingan buyurtmalar: $bc ta
• Muvaffaqiyatsiz buyurtmalar: $fc ta
• Qisman bajarilgan buyurtmalar: $ppc ta
• Qayta ishlangan buyurtmalar: $cp ta
",keyboard([
[['text'=>"Kutilayotgan buyurtmalarni yangilash",'callback_data'=>"update=pending"]],
[['text'=>"Jarayondagi buyurtmalarni yangilash",'callback_data'=>"update=In progress"]],
[['text'=>"Qisman bajarilgan buyurtmalarni yangilash",'callback_data'=>"update=partial"]],
[['text'=>"Qayta ishlangan buyurtmalarni yangilash",'callback_data'=>"update=processing"]],
]));
}elseif($res=="pending"){
del();
sms($cid2,"
📊 Buyurtmalar ro'yxati:

• Kutilayotgan buyurtmalar: $pc ta",keyboard([
[['text'=>"Bajarilgan xolatga o‘tkazish",'callback_data'=>"update=new=Pending=Completed"]],
[['text'=>"Jarayondagi xolatga o‘tkazish",'callback_data'=>"update=new=Pending=In progress"]],
[['text'=>"Orqaga",'callback_data'=>"update=orders"]],
]));
}elseif($res=="processing"){
del();
sms($cid2,"
📊 Buyurtmalar ro'yxati:

• qayta ishlangan buyurtmalar: $cp ta",keyboard([
[['text'=>"Bajarilgan xolatga o‘tkazish",'callback_data'=>"update=new=Processing=Completed"]],
[['text'=>"Jarayondagi xolatga o‘tkazish",'callback_data'=>"update=new=Processing=In progress"]],
[['text'=>"Orqaga",'callback_data'=>"update=orders"]],
]));
}elseif($res=="partial"){
del();
sms($cid2,"
📊 Buyurtmalar ro'yxati:

• • Qisman bajarilgan buyurtmalar: $ppc ta",keyboard([
[['text'=>"Bajarilgan xolatga o‘tkazish",'callback_data'=>"update=new=Partial=Completed"]],
[['text'=>"Jarayondagi xolatga o‘tkazish",'callback_data'=>"update=new=Partial=In progress"]],
[['text'=>"Orqaga",'callback_data'=>"update=orders"]],
]));
}elseif($res=="In progress"){
del();
sms($cid2,"
📊 Buyurtmalar ro'yxati:

• Jarayondagi buyurtmalar: $jc ta",keyboard([
[['text'=>"Bajarilgan xolatga o‘tkazish",'callback_data'=>"update=new=In progress=Completed"]],
[['text'=>"Kutilayotgan xolatga o‘tkazish",'callback_data'=>"update=new=In progress=Pending"]],
[['text'=>"Orqaga",'callback_data'=>"update=orders"]],
]));
}elseif($res=="new"){
$out = explode("=",$data)[2];
$inp = explode("=",$data)[3];
$mysqli = mysqli_query($connect, "SELECT * FROM orders WHERE status = '$out'");
while($all = mysqli_fetch_assoc($mysqli)){
$io = $all['order_id'];

$mysa=mysqli_fetch_assoc(mysqli_query($connect,"SELECT * FROM `myorder` WHERE order_id=$io"));
$adm=$mysa['user_id'];

mysqli_query($connect,"UPDATE orders SET status ='$inp' WHERE order_id = $io");
if($inp=="Completed") {
$sav = date("Y.m.d H:i:s");
mysqli_query($connect,"UPDATE myorder SET status='$input', last_check='$sav' WHERE order_id=$io");
}else{
mysqli_query($connect,"UPDATE myorder SET status='$inp' WHERE order_id=$io");
}
if($inp=="Completed"){
sms($adm,"✅ Sizning $io raqamli buyurtmangiz bajarildi",null);
}
}
del();
sms($cid2,"✅ Jarayon tugallandi.",null);
}
}




if($text == "🔔 Xabar yuborish" and $cid == $admin){
$result = mysqli_query($connect, "SELECT * FROM `send`");
$row = mysqli_fetch_assoc($result);
if(!$row){
bot('sendMessage',[
'chat_id'=>$cid,
'text'=>"<b>📤 Foydalanuvchilarga yuboriladigan xabarni botga yuboring!</b>",
'parse_mode'=>'html',
'reply_markup'=>$aort
]);
put("user/$cid.step","send");

}else{
bot('sendMessage',[
'chat_id'=>$cid,
'text'=>"<b>📑 Hozirda botda xabar yuborish jarayoni davom etmoqda. Yangi xabar yuborish uchun eski yuborilayotgan xabar barcha foydalanuvchilarga yuborilishini kuting!</b>",
'parse_mode'=>'html',
'reply_markup'=>$panel
]);

}
}

if($step== "send" and $cid==$admin){
$result = mysqli_query($connect, "SELECT * FROM users");
$stat = mysqli_num_rows($result);
$res = mysqli_query($connect,"SELECT * FROM users WHERE user_id = '$stat'");
$row = mysqli_fetch_assoc($res);
$user_id = $row['id'];
$time1 = date('H:i', strtotime('+1 minutes'));
$time2 = date('H:i', strtotime('+2 minutes'));
$tugma = json_encode($update->message->reply_markup);
$reply_markup = base64_encode($tugma);
mysqli_query($connect, "INSERT INTO `send` (`time1`,`time2`,`start_id`,`stop_id`,`admin_id`,`message_id`,`reply_markup`,`step`) VALUES ('$time1','$time2','0','$user_id','$admin','$mid','$reply_markup','send')");
bot('sendMessage',[
'chat_id'=>$admin,
'text'=>"<b>
📋 Saqlandi!
📑 Xabar foydalanuvchilarga $time1 da yuborish boshlanadi!</b>",
'parse_mode'=>'html',
'reply_markup'=>$panel
]);
unlink("user/$cid.step");

}

$result = mysqli_query($connect, "SELECT * FROM `send`"); 
$row = mysqli_fetch_assoc($result);
$sendstep = $row['step'];
if($_GET['update']=="send"){
$row1 = $row['time1'];
$row2 = $row['time2'];
$row3 = $row['time3'];
$row4 = $row['time4'];
$row5 = $row['time5'];
$start_id = $row['start_id'];
$stop_id = $row['stop_id'];
$admin_id = $row['admin_id'];
$mied = $row['message_id'];
$tugma = $row['reply_markup'];
if($tugma == "bnVsbA=="){
$reply_markup = "";
}else{
$reply_markup = base64_decode($tugma);
}
$time1 = date('H:i', strtotime('+1 minutes'));
$time2 = date('H:i', strtotime('+2 minutes'));
$time3 = date('H:i', strtotime('+3 minutes'));
$time4 = date('H:i', strtotime('+4 minutes'));
$time5 = date('H:i', strtotime('+5 minutes'));
$limit = 150;

if($time == $row1 or $time == $row2 or $time == $row3 or $time == $row4 or $time == $row5){
$sql = "SELECT * FROM `users` LIMIT $start_id,$limit";
$res = mysqli_query($connect,$sql);
while($a = mysqli_fetch_assoc($res)){
$id = $a['id'];
if($id == $stop_id){
bot('copyMessage',[
'chat_id'=>$id,
'from_chat_id'=>$admin_id,
'message_id'=>$mied,
'disable_web_page_preview'=>true,
'reply_markup'=>$reply_markup
]);

bot('sendMessage',[
'chat_id'=>$admin_id,
'text'=>"<b>✅ ️Xabar barcha bot foydalanuvchilariga yuborildi!</b>",
'parse_mode'=>'html'
]);
mysqli_query($connect, "DELETE FROM `send`");
exit;
}else{
bot('copyMessage',[
'chat_id'=>$id,
'from_chat_id'=>$admin_id,
'message_id'=>$mied,
'disable_web_page_preview'=>true,
'reply_markup'=>$reply_markup
]);
}
}
mysqli_query($connect, "UPDATE `send` SET `time1` = '$time1'");
mysqli_query($connect, "UPDATE `send` SET `time2` = '$time2'");
mysqli_query($connect, "UPDATE `send` SET `time3` = '$time3'");
mysqli_query($connect, "UPDATE `send` SET `time4` = '$time4'");
mysqli_query($connect, "UPDATE `send` SET `time5` = '$time5'");
$get_id = $start_id + $limit;
mysqli_query($connect, "UPDATE `send` SET `start_id` = '$get_id'");
bot('sendMessage',[
'chat_id'=>$admin_id,
'text'=>"<b>✅ Yuborildi: $get_id</b>",
'parse_mode'=>'html'
]);
}
echo json_encode(["status"=>true,"cron"=>"Sending message"]);
}




$menu=json_encode([
'resize_keyboard'=>true,
'keyboard'=>[
[['text'=>"📦 Buyurtma berish"]],
[['text'=>"🛒 Buyurtmalar"],['text'=>"🗣 Referal"]],
[['text'=>"👔 Kabinet"],['text'=>"💵 Pul kiritish"]],
[['text'=>"📨 Yordam"],['text'=>"*️⃣ Hamkorlik"]],
[['text'=>"📝 Xizmatlar ro‘yxati"]],
]
]);
$panel2=json_encode([
'resize_keyboard'=>true,
'keyboard'=>[
[['text'=>"🛍 Buyurtmalarni sozlash"]],
[['text'=>"💵 Kursni o‘rnatish"],['text'=>"⚖️ Foizni o‘rnatish"]],
[['text'=>"📊 Buyurtmani tekshirish"]],
[['text'=>"📎 Majburiy obunalar"],['text'=>"🔑 API Sozlamalari"]],
[['text'=>"⚙️ Boshqa sozlamalar"]],
[['text'=>"🗄️ Boshqaruv"]],
]]);



if($text=="⚙️ Boshqa sozlamalar" and $cid==$admin){
sms($cid,"⭐ Kerakli bo'limni tanlang:",json_encode([
inline_keyboard=>[
[['text'=>"📑 Matnlar sozlamalari",callback_data=>"birlamch=matn"]],
[['text'=>"💳 Hamyonlar sozlamalari",callback_data=>"birlamch=cards"]],
[['text'=>"💳 Avto tolov sozlamalari",'callback_data'=>"birlamch=autopays"]],
]]));

}

if((stripos($data,"birlamch=")!==false)){
$res=explode("=",$data)[1];
if($res=="matn"){
edit($chat_id,$message_id,"👉 Sozlama turini tanlang:",json_encode([
inline_keyboard=>[
[['text'=>"📑 Nomini o‘zgartirish",callback_data=>"birlamch=editM"]],
[['text'=>"Orqaga",callback_data=>"birlamch=exit"]],
]]));
}elseif($res=="tugma"){
edit($chat_id,$message_id,"👉 Sozlama turini tanlang:",json_encode([
inline_keyboard=>[
[['text'=>"📑 Nomini o‘zgartirish",callback_data=>"birlamch=editT"]],
[['text'=>"Orqaga",callback_data=>"birlamch=exit"]],
]]));
}elseif($res=="exit"){
del();
sms($chat_id,"⭐ Kerakli bo'limni tanlang:",json_encode([
inline_keyboard=>[
[['text'=>"📑 Matnlarni sozlash",callback_data=>"birlamch=matn"]],
//[['text'=>"🎛️ Tugmalarni sozlash",'callback_data'=>"birlamch=tugma"]],
//[['text'=>"🎁 Referal sozlamalari",'callback_data'=>"birlamch=ref"]],
[['text'=>"💳 Hamyonlar sozlamalari",callback_data=>"birlamch=cards"]],
[['text'=>"💳 Avto tolov sozlamalari",'callback_data'=>"birlamch=autopays"]],
]]));
}elseif($res=="editM"){
/*2. Referal uchun matn*/
edit($chat_id,$message_id,"
📑 Kerakli matnni tanlang:

1. /start uchun matn
2. Yangi buyurtma uchun matn
3. Kabinet uchun matn
4. Referal narxi",json_encode([
inline_keyboard=>[
[['text'=>"1",callback_data=>"birlamchi=start"]/*,['text'=>"2",callback_data=>"birlamchi=referal"]*/],
[['text'=>"2",callback_data=>"birlamchi=orders"],['text'=>"3",callback_data=>"birlamchi=kabinet"]],
[['text'=>"4",callback_data=>"birlamchi=referal"]],
[['text'=>"Orqaga",callback_data=>"birlamch=matn"]],
]]));
}elseif($res=="ref"){
edit($chat_id,$mid2,"⚙️ Sozlama turini tanlang:",json_encode([
inline_keyboard=>[
[['text'=>"🎁 Referal tugma xolati",'callback_data'=>"referr=xolati"]],
[['text'=>"🎁 Bonusni o‘zgartirish",'callback_data'=>"referr=edit"]],
[['text'=>"Orqaga",callback_data=>"birlamch=exit"]],
]]));
}elseif($res == "cards"){
del();
$delturi = file_get_contents("set/payments.txt");
$delmore = explode("\n",$delturi);
$delsoni = substr_count($delturi,"\n");
$key=[];
for ($delfor = 1; $delfor <= $delsoni; $delfor++) {
$title=str_replace("\n","",$delmore[$delfor]);
$key[]=["text"=>"$title - ni o'chirish","callback_data"=>"delPayMethod-$title"];
$keyboard2 = array_chunk($key, 1);
$keyboard2[] = [['text'=>"➕ Yangi to'lov tizimi qo'shish",'callback_data'=>"new"]];
$keyboard2[] = [['text'=>"Orqaga",callback_data=>"birlamch=exit"]];
$pay = json_encode([
'inline_keyboard'=>$keyboard2,
]);
}
if($cid2==$admin){
if($delturi == null){
bot('SendMessage',[
	'chat_id'=>$cid2,
	'text'=>"<b>Quyidagilardan birini tanlang:</b>",
	'parse_mode'=>'html',
		'reply_markup'=>json_encode([
'inline_keyboard'=>[
[['text'=>"➕ Yangi to'lov tizimi qo'shish",'callback_data'=>"new"]],
[['text'=>"Orqaga",callback_data=>"birlamch=exit"]],
]
])
]);

}else{
	bot('SendMessage',[
	'chat_id'=>$cid2,
	'text'=>"<b>Quyidagilardan birini tanlang:</b>",
	'parse_mode'=>'html',
		'reply_markup'=>$pay
]);

}
}
}elseif($res=="autopays"){
edit($cid2,$mid2,"👉 Kerakli tolov tizimini tanlang:",keyboard([
[['text'=>"💳 PAYME",'callback_data'=>"autopay=payme"]],
[['text'=>"Orqaga",callback_data=>"birlamch=exit"]],
]));
}
}

if(mb_stripos($data,"autopay=")!==false){
$ex = explode("=",$data)[1];
if($ex=="payme"){
if(empty($setting['payme_id']) or $setting['payme_id']=="null"){
edit($cid2,$mid2,"👉 Kerakli sozlamani tanlang:",keyboard([
[['text'=>"➕ Karta IDsini qo‘shish",'callback_data'=>"autopay=payme_id"]],
[['text'=>"Orqaga",callback_data=>"birlamch=exit"]],
]));
}else{
edit($cid2,$mid2,"👉 Kerakli sozlamani tanlang

🆔 Hozirgi karta IDsi: ".$setting['payme_id']."",keyboard([
[['text'=>"➕ Karta IDsini o‘zgartirish",'callback_data'=>"autopay=payme_id"]],
[['text'=>"Orqaga",callback_data=>"birlamch=exit"]],
]));
}
}elseif($ex=="payme_id") {
del();
bot("sendMediaGroup",[ 
"chat_id"=>$cid2, 
"media"=>json_encode([ 
["type"=>"photo","media" => "https://t.me/s1_kanal/61"], 
["type"=>"photo","media" => "https://t.me/s1_kanal/62"], 
["type"=>"photo","media" => "https://t.me/s1_kanal/63","caption"=>"
1 - «<b>Kartalarim</b>» tugmasini bosing
2 - «<b>Kerakli karta</b>» ni tanlab ustiga bosing
3 - «<b>Havolani ko‘chirib olish</b>» ga bosib linkni saqlab oling va shuyerga kiriting.",'parse_mode'=>html],
]),
]);
sms($cid2,"?? Kartangizning unikal manzilini kiriting

✅ Malumotlaringiz 100% maxfiy saqlanadi.",$aort);
put("user/$cid2.step","%%₹_-#");
}
}
if($step=="%%₹_-#" and $cid==$admin){
if((mb_stripos($text,"https://")!==false) and (mb_stripos($text,"https://payme.")!==false) and (mb_stripos($text,"payme.uz")!==false)){
$card = explode("/",$text)[3];
sms($cid,"✅ O‘zgartirish muvaffaqiyatli amalga oshirildi.",$panel);
mysqli_query($connect,"UPDATE settings SET `payme_id` = '$card' WHERE id = 1");
unlink("user/$cid.step");

}

}






if(mb_stripos($data,"delPayMethod-")!==false){
	$ex = explode("-",$data)[1];
	$delturi = file_get_contents("set/payments.txt");
	$delturi = str_replace("\n".$ex."","",$delturi);
   file_put_contents("set/payments.txt",$delturi);
bot('deleteMessage',[
	'chat_id'=>$cid2,
	'message_id'=>$mid2,
	]);
bot('SendMessage',[
	'chat_id'=>$cid2,
	'text'=>"🗑️ <b>To'lov tizimi o'chirildi!</b>",
		'parse_mode'=>'html',
	'reply_markup'=>$asosiy
]);
rmdirPro("set/pay/$ex");
}

if($data == "new"){
	bot('deleteMessage',[
	'chat_id'=>$cid2,
	'message_id'=>$mid2,
   ]);
   bot('sendMessage',[
   'chat_id'=>$cid2,
   'text'=>"🔠 <b>Yangi to'lov tizimi nomini yuboring:</b>",
   'parse_mode'=>'html',
   'reply_markup'=>$aort
	]);
	file_put_contents("user/$cid2.step",'turi');
	
}

if($step == "turi"){
if($cid==$admin){
if(isset($text)){
put("set/title.txt",$text);
	bot('SendMessage',[
	'chat_id'=>$cid,
	'text'=>"🔢 <b>Ushbu to'lov tizimidagi hamyoningiz raqamini yuboring:</b>",
	'parse_mode'=>'html',
	]);
	file_put_contents("user/$cid.step",'wallet');
	
}
}
}


if($step == "wallet"){
if($cid==$admin){

put("set/wallet.txt",$text);
	bot('SendMessage',[
	'chat_id'=>$cid,
	'text'=>"✅ <b>Ushbu to'lov tizimi orqali hisobni to'ldirish bo'yicha ma'lumotni yuboring:</b>

<i>Misol uchun, \"Ushbu to'lov tizimi orqali pul yuborish jarayonida izoh kirita olmasligingiz mumkin. Ushbu holatda, biz bilan bog'laning.</i>\"",
'parse_mode'=>'html',
	]);
	file_put_contents("user/$cid.step",'addition');
	
}else{
bot('SendMessage',[
'chat_id'=>$cid,
'text'=>"🔢 <b>Faqat raqamlardan foydalaning!</b>",
'parse_mode'=>'html',
]);


}
}

if($step == "addition"){
		if($cid==$admin){
	if(isset($text)){
$ttest=get("set/title.txt");
file_put_contents("set/payments.txt","\n".$ttest,FILE_APPEND);
mkdir("set/pay");
mkdir("set/pay/$ttest");
file_put_contents("set/pay/$ttest/addition.txt","$text");
file_put_contents("set/pay/$ttest/wallet.txt",get("set/wallet.txt"));
	bot('SendMessage',[
	'chat_id'=>$cid,
	'text'=>"✅ <b>$ttest to'lov tizimi qo'shildi!</b>",
	'parse_mode'=>'html',
	'reply_markup'=>$panel,
	]);
	unlink("user/$cid.step");
	
}
}
}


if((stripos($data,"referr=")!==false)){
$res = explode("=",$data)[1];
$fo = explode("=",$data)[2];
if($res=="xolati"){
$m = mysqli_fetch_assoc(mysqli_query($connect,"SELECT * FROM settings WHERE id = 1"))["ref_status"];
if($m == "on"){
$tx = "✅";
$kb = json_encode([
inline_keyboard=>[
[['text'=>"«❌»",'callback_data'=>"referr=ok=off"]],
[['text'=>"Orqaga",callback_data=>"birlamch=exit"]],
]]);
}elseif($m == "off"){
$tx = "❌";
$kb = json_encode([
inline_keyboard=>[
[['text'=>"«✅»",'callback_data'=>"referr=ok=on"]],
[['text'=>"Orqaga",callback_data=>"birlamch=exit"]],
]]);
}
edit($cid2,$mid2,"🎁 Referal tugma xolati: $tx",$kb);
}elseif($res=="ok") {
mysqli_query($connect,"UPDATE settings SET ref_status = '$fo' WHERE id = 1");
$m = mysqli_fetch_assoc(mysqli_query($connect,"SELECT * FROM settings WHERE id = 1"))["ref_status"];
if($m == "on"){
$tx = "✅";
$kb = json_encode([
inline_keyboard=>[
[['text'=>"«❌»",'callback_data'=>"referr=ok=off"]],
[['text'=>"Orqaga",callback_data=>"birlamch=exit"]],
]]);
}elseif($m == "off"){
$tx = "❌";
$kb = json_encode([
inline_keyboard=>[
[['text'=>"«✅»",'callback_data'=>"referr=ok=on"]],
[['text'=>"Orqaga",callback_data=>"birlamch=exit"]],
]]);
}
edit($cid2,$mid2,"🎁 Referal tugma xolati: $tx",$kb);
}elseif($res=="edit") {
$m = mysqli_fetch_assoc(mysqli_query($connect,"SELECT * FROM settings WHERE id = 1"))["bonus"];
del();
sms($cid2,"
🔢 Referal bonus miqdorini kiriting. (raqamlarda)

📝 Hozirgi xolati: $m%",$aort);
put("user/$cid2.step","*##");
}
}
if($step=="*##" and $cid==$admin){
if(is_numeric($text)==1){
mysqli_query($connect,"UPDATE settings SET bonus = '$text' WHERE id = 1");
sms($cid,"✅ O‘zgarish saqlandi",$panel);
unlink("user/$cid.step");

}
}
if((stripos($data,"birlamchi=")!==false)){
$res = explode("=",$data)[1];
if($res=="start"){
$arr = "<code>{balance} </code> - Foydalanuvchi hisobi\n<pre>{name}</pre> - Foydalanuvchi ismi\n<pre>{time} </pre> - Hozirgi vaqt (UTC+5 / UZ)";
}elseif($res=="kabinet") {
$arr ="<pre>{id}</pre> - Foydalanuvchi IDsi\n<pre>{balance}</pre> - Foydalanuvchi hisobi\n<pre>{outing}</pre> - Kiritgan pullar miqdori";
}elseif($res=="referal") {
$arr = "1 ta taklif uchun tolov miqdorini kiriting:";
}elseif($res=="orders") {
$arr ="<pre>{order}</pre> - Buyurtma IDsi (standard)\n<pre>{order_api}</pre> - Buyurtma IDsi (API)";
}
put("bir.txt",$res);
del();
sms($chat_id,"
📝 Yangi matnlarni kiriting.

⚙️ O‘zgaruvchilar:
$arr

📝 Hozirgi matnlar",$aort);
$m  = mysqli_fetch_assoc(mysqli_query($connect,"SELECT * FROM settings WHERE id = 1"))[$res];
sms($chat_id,enc("decode",$m),null);
put("user/$chat_id.step","!?+-");
}
if($step=="!?+-" and $cid==$admin){

$vq = get("bir.txt");
$vo = enc("encode",$text);
mysqli_query($connect,"UPDATE settings SET `$vq` = '$vo' WHERE id = 1");
sms($cid,"✅ O‘zgartirishlar saqlandi",$panel);
unlink("bir.txt");
unlink("user/$cid.step");
exit;
}


if($text=="📊 Buyurtmani tekshirish" and joinchat($cid)==1) {
$resi = mysqli_query($connect, "SELECT * FROM orders");
$stati = mysqli_num_rows($resi);
sms($cid,"
🔢 Barcha buyurtmalar: $stati ta

➡️ Buyurtma IDsini kiriting:",$aort);
put("user/$cid.step",orders2);
exit;
}


if($step=="orders2" and $cid==$admin and is_numeric($text)==1){
$resi = mysqli_query($connect, "SELECT * FROM orders WHERE order_id = '$text'");
$stati = mysqli_fetch_assoc($resi);
if(!$stati){
sms($cid,"❌ Buyurtma topilmadi.",$aort);
}else{
$prv = $stati['provider'];
$a = mysqli_query($connect,"SELECT * FROM providers WHERE id = $prv");
$c = mysqli_fetch_assoc($a);
$prg = $stati['provider'];
$m = mysqli_fetch_assoc(mysqli_query($connect,"SELECT * FROM `providers` WHERE id = '$prg'"));
$surl = $m['api_url'];
$skey =$m['api_key'];

$api = json_decode(get($surl."?key=$skey&action=status&order=".$stati['api_order'].""), 1);
$prtxt=str_replace(["/api/adapter/default/index","/api/v1","/api/v2","https://"],["","","",""],$c['api_url']);
sms($cid,"
*️⃣ Server: $prtxt
🔢 Buyurtma IDsi: <code>".$stati['api_order']."</code>
✅ Buyurtma xolati ($prtxt): <code>".$api['status']."</code>",$panel2);
unlink("user/$cid.step");
}
exit;
}



if($text == "🔑 API Sozlamalari"){
	if($cid == $admin){
	bot('SendMessage',[
	'chat_id'=>$cid,
'text'=>"Quyidagi bo'limlardan birini tanlang:",
	'parse_mode'=>'html',
	'reply_markup'=>json_encode([
	'inline_keyboard'=>[
	[['text'=>"➕ API qo‘shish",'callback_data'=>"api"]],
	[['text'=>"💵 Balansni ko'rish",'callback_data'=>"balans"]],
	[['text'=>"🗑️ O‘chirish",'callback_data'=>"deleteapi"]],
	[['text'=>"📝 Taxrirlash",'callback_data'=>"apio=taxrirlash"]],
]
	])
	]);
	exit;
}
}

if((stripos($data,"apio=")!==false)){
$res=explode("=",$data)[1];
if($res=="taxrirlash") {
edit($cid2,$mid2,"📝 Taxrirlash menyusini tanlang",keyboard([
[['text'=>"🔑 Kalitni o‘zgartirish",'callback_data'=>"apio=kalit"]],
[['text'=>"⬅️ Orqaga", callback_data=>"api1"]],
]));
}elseif($res=="kalit") {
$pr=0;
$prs="";
$a = mysqli_query($connect,"SELECT * FROM providers");
$c = mysqli_num_rows($a);
while($s = mysqli_fetch_assoc($a)){
$pr++;
$prtxt=str_replace(["/api/adapter/default/index","/api/v1","/api/v2","https://"],["","","",""],$s['api_url']);
$prs.="$pr: <b>$prtxt\n</b>";
$k[]=["text"=>$pr,"callback_data"=>"apio=edit=".$s['id']];
}
$keyboard2=array_chunk($k,3);
$keyboard2[]=[['text'=>"Orqaga",'callback_data'=>"api1"]];
$kb=json_encode([inline_keyboard=>$keyboard2]);
if(!$c){
	bot('answerCallbackQuery',[
		'callback_query_id'=>$qid,
		'text'=>"⚠️ Provayderlar topilmadi!",
		'show_alert'=>true,
		]);
	}else{
     bot('editMessageText',[
        'chat_id'=>$chat_id,
       'message_id'=>$message_id,
       'text'=>"Provayderni tanlang:

$prs
",
'parse_mode'=>"HTML",
'reply_markup'=>$kb,
]);

}
}elseif($res=="edit") {
del();
$co=explode("=",$data)[2];
sms($cid2,"🔠 Yangi kalitni kiriting:",$aort);
put("user/$cid2.step","kalitnew=$co");
}
}


if((mb_stripos($step,"kalitnew=")!==false) and $cid==$admin){
sms($cid,"✅ O‘zgartirish muvaffaqiyatli amalga oshirildi.",$panel);
$io = explode("=",$step)[1];
mysqli_query($connect,"UPDATE providers SET api_key = '$text' WHERE id = $io");
unlink("user/$cid.step");

}


if($data == "deleteapi"){
$pr=0;
$prs="";
$a = mysqli_query($connect,"SELECT * FROM providers");
$c = mysqli_num_rows($a);
while($s = mysqli_fetch_assoc($a)){
$pr++;
$prtxt=str_replace(["/api/adapter/default/index","/api/v1","/api/v2","https://"],["","","",""],$s['api_url']);
$prs.="$pr: <b>$prtxt\n</b>";
$k[]=["text"=>$pr,"callback_data"=>"apidel=".$s['id']];
}
$keyboard2=array_chunk($k,3);
$keyboard2[]=[['text'=>"Orqaga",'callback_data'=>"api1"]];
$kb=json_encode([inline_keyboard=>$keyboard2]);
if(!$c){
	bot('answerCallbackQuery',[
		'callback_query_id'=>$qid,
		'text'=>"⚠️ Provayderlar topilmadi!",
		'show_alert'=>true,
		]);
	}else{
     bot('editMessageText',[
        'chat_id'=>$chat_id,
       'message_id'=>$message_id,
       'text'=>"Provayderni tanlang:

$prs
",
'parse_mode'=>"HTML",
'reply_markup'=>$kb,
]);
exit;
}
}

if((stripos($data,"apidel=")!==false)){
$res = explode("=",$data)[1];
del();
mysqli_query($connect,"DELETE FROM providers WHERE id = $res");
mysqli_query($connect,"DELETE FROM services WHERE api_service = $res");
sms($cid2,"🗑️ Provayderni o‘chirish yakunlandi.",null);
}

if($data == "api1"){
	bot('deleteMessage',[
	'chat_id'=>$chat_id,
	'message_id'=>$message_id,
	]);
	bot('SendMessage',[
	'chat_id'=>$chat_id,
'text'=>"Quyidagi bo'limlardan birini tanlang:",
	'parse_mode'=>'html',
	'reply_markup'=>json_encode([
	'inline_keyboard'=>[
	[['text'=>"➕ API qo‘shish",'callback_data'=>"api"]],
	[['text'=>"?? Balansni ko'rish",'callback_data'=>"balans"]],
	[['text'=>"🗑️ O‘chirish",'callback_data'=>"deleteapi"]],
	[['text'=>"📝 Taxrirlash",'callback_data'=>"apio=taxrirlash"]],
]
	])
	]);
	exit;
}

if($data == "api"){
	bot('deleteMessage',[
	'chat_id'=>$chat_id,
	'message_id'=>$message_id,
	]);
	bot('SendMessage',[
	'chat_id'=>$chat_id,
	'text'=>"<b>API manzilini yuboring:

Namuna:</b> <pre>https://apiseen.uz/api/v2</pre>",
	'parse_mode'=>'html',
	'reply_markup'=>$boshqarish,
	]);
	file_put_contents("user/$chat_id.step",'api_url');
	exit;
}

if($step == "api_url"){
	if($cid == $admin){
   if(mb_stripos($text, "https://")!==false){
	if(isset($text)){
	file_put_contents("set/api_url",$text);
	bot('SendMessage',[
	'chat_id'=>$cid,
	'text'=>"$text <b>qabul qilindi!</b>
	
	Endi esa ushbu saytdan olingan API_KEY'ni kiriting:",
'disable_web_page_preview'=>true,
	'parse_mode'=>'html',
	]);
	file_put_contents("user/$cid.step",'api');
	exit;
}
}else{
	bot('SendMessage',[
	'chat_id'=>$cid,
	'text'=>"<b>API manzilini yuboring:

Namuna:</b> <pre>https://apiseen.uz/api/v2</pre>",
	'parse_mode'=>'html',
]);
exit;
}
}
}

if($step == "api"){
	if($cid == $admin){
	if(isset($text)){
$balans = json_decode(file_get_contents(get("set/api_url")."?key=$text&action=balance"),true);
if(isset($balans['error'])){
$admsg="⚠️ Balansni olish imkoni bo'lmadi

Extimol API kalit mavjud emas";
}else{
global $connect;
$admsg="<b>💵 API balansi:</b> ".$balans['balance']." ".$balans['currency']."";
$apc = mysqli_fetch_assoc(mysqli_query($connect,"SELECT * FROM providers"));
$api_url = get("set/api_url");
mysqli_query($connect,"INSERT INTO providers(`api_url`,`api_key`) VALUES ('$api_url','$text')");
}
	bot('SendMessage',[
	'chat_id'=>$cid,
	'text'=>"<b>$admsg</b>",
	'parse_mode'=>'html',
	'reply_markup'=>$asosiy,
	]);
	unlink("user/$cid.step");
	
}
}
}


if($data == "balans"){
$pr=0;
$prs="";
$a = mysqli_query($connect,"SELECT * FROM providers");
$c = mysqli_num_rows($a);
while($s = mysqli_fetch_assoc($a)){
$pr++;
$prtxt=str_replace(["/api/adapter/default/index","/api/v1","/api/v2","https://"],["","","",""],$s['api_url']);
$sa= json_decode(api_query($s['api_url']."?key=".$s['api_key']."&action=balance"));

$prs.="<b>".$pr."</b>: $prtxt - ".$sa->balance." ".$sa->currency." \n";
$k[]=["text"=>$pr,"url"=>$s['api_url']."?key=".$s['api_key']."&action=balance"];
}
$keyboard2=array_chunk($k,3);
$keyboard2[]=[['text'=>"Orqaga",'callback_data'=>"api1"]];
$kb=json_encode([
'inline_keyboard'=>$keyboard2,
]);
if(!$c){
	bot('answerCallbackQuery',[
		'callback_query_id'=>$qid,
		'text'=>"⚠️ Provayderlar topilmadi!",
		'show_alert'=>true,
		]);
	}else{
     bot('editMessageText',[
        'chat_id'=>$chat_id,
       'message_id'=>$message_id,
       'text'=>"Provayderni tanlang:

$prs
",
'parse_mode'=>"HTML",
'reply_markup'=>$kb,
]);

}
}



if($text == "📝 Xizmatlar ro‘yxati"){
sms($cid,"👉 Barcha ta'riflar",keyboard([
[['text'=>"📝 Ta'riflar",'url'=>"https://".$_SERVER['HTTP_HOST']."/services"]],
]));
}

if($text == "*️⃣ Hamkorlik") {
$result = mysqli_query($connect,"SELECT * FROM `users` WHERE id = '$cid'");
$rew = mysqli_fetch_assoc($result);
sms($cid,"
<b>⭐ Sizning API kalitingiz:
<code>".$rew['api_key']."</code>

💵 API hisobi:
<b>".$rew['balance']."</b> so‘m
</b>",keyboard([
[['text'=>"📝 Qo‘llanma",'callback_data'=>"apidetail=qoll"]],
[['text'=>"🔄 APIni yangilash",'callback_data'=>"apidetail=newkey"]],
]));
}

if((stripos($data,"apidetail=")!==false)){
$res = explode("=",$data)[1];
if($res == "newkey"){
$newkey = md5(uniqid());
mysqli_query($connect,"UPDATE users SET api_key = '$newkey' WHERE id = '$chat_id'");
$result = mysqli_query($connect,"SELECT * FROM `users` WHERE id = '$chat_id'");
$rew = mysqli_fetch_assoc($result);
bot('editMessageText',[
'chat_id'=>$chat_id,
'parse_mode'=>"html",
'message_id'=>$message_id,
'text'=>"<b>
✅ API kalit yangilandi.

<code>".$rew['api_key']."</code>

💵 API hisobi:
<b>".$rew['balance']."</b> so‘m
</b>",
'reply_markup'=>keyboard([
[['text'=>"📝 Qo‘llanma",'callback_data'=>"apidetail=qoll"]],
[['text'=>"🔄 APIni yangilash",'callback_data'=>"apidetail=newkey"]],
])
]);
}elseif($res == "qoll") {
	bot('editMessageText',[
'chat_id'=>$chat_id,
'parse_mode'=>"html",
'message_id'=>$message_id,
'text'=>"<b>
❓ APi nima?
Botimizdagi xizmatlarni siz ham o'z botingizga yoki saytingizga ulab ishlatishingiz mumkin. Buni ishlatish oson va qulay. Ushbu tizim xavfsizligi taminlanagan. Ko'proq imkoniyat bilan foydalaning. Sizni api kalitingiz agarda boshqalarga ma'lum bo'lsa yangisiga almashtiring. Albatta botga ulash uchun qo'llanma mavjud.

🔑 APi kalitni ishlatish haqida web saytimiz: ".$_SERVER['HTTP_HOST']."

⚠️ Diqqat APi kalitni begona kishiga bermang. Sizning api kalitiz begonalar qo'liga tushsa tezda api kalitni yangilang. Agarda begonalar qo'liga tushgan apidan berilgan xizmat puli qaytarilmaydi. Bu holat ximoyalangan va sizdan boshqa kishisiz aytmasangiz apini bila olmaydi.
</b>",
'reply_markup'=>keyboard([
[['text'=>"📝 Qo‘llanma",'web_app'=>['url'=>"https://".$_SERVER['HTTP_HOST']."/api"]]],
[['text'=>"🔄 APIni yangilash",'callback_data'=>"apidetail=newkey"]],
])
]);
}
	
	
}


$menu_p=json_encode([
'resize_keyboard'=>true,
'keyboard'=>[
[['text'=>"📦 Buyurtma berish"]],

[['text'=>"🛒 Buyurtmalar"],['text'=>"🗣 Referal"]],
[['text'=>"👔 Kabinet"],['text'=>"💵 Pul kiritish"]],
[['text'=>"📨 Yordam"],['text'=>"*️⃣ Hamkorlik"]],
[['text'=>"📝 Xizmatlar ro‘yxati"]],
[['text'=>"🗄️ Boshqaruv"]],
]
]);
if($cid==$admin or $chat_id==$admin){
$m=$menu_p;
}else{
$m=$menu;
}

if($text == "🛍 Buyurtmalarni sozlash" ){
		bot('sendMessage',[
		'chat_id'=>$cid,
		'text'=>"<b>Quyidagilardan birini tanlang:</b>",
		'parse_mode'=>'html',
		'reply_markup'=>json_encode([
		'inline_keyboard'=>[
		[['text'=>"📂 Bo'limlarni sozlash",'callback_data'=>"bolim"]],
		[['text'=>"📂 Ichki bo'limlarni sozlash",'callback_data'=>"ichki"]],
		[['text'=>"🛍 Xizmatlarni sozlash",'callback_data'=>"xizmat"]]
]
])
]);

}

if($data == "xsetting" ){
del();
		bot('sendMessage',[
		'chat_id'=>$chat_id,
		'text'=>"<b>Quyidagilardan birini tanlang:</b>",
		'parse_mode'=>'html',
		'reply_markup'=>json_encode([
		'inline_keyboard'=>[
		[['text'=>"📂 Bo'limlarni sozlash",'callback_data'=>"bolim"]],
		[['text'=>"📂 Ichki bo'limlarni sozlash",'callback_data'=>"ichki"]],
		[['text'=>"🛍 Xizmatlarni sozlash",'callback_data'=>"xizmat"]]
]
])
]);

}

if($data == "bolim"){
     bot('editMessageText',[
        'chat_id'=>$chat_id,
       'message_id'=>$message_id,
       'text'=>"<b>Quyidagilardan birini tanlang:</b>",
'parse_mode'=>'html',
'reply_markup'=>json_encode([
'inline_keyboard'=>[
[['text'=>"Yangi bo'lim qo'shish",'callback_data'=>"newFol"]],
[['text'=>"Tahrirlash",'callback_data'=>"editFol"]],
[['text'=>"O'chirish",'callback_data'=>"delFol"]],
[['text'=>"Orqaga", 'callback_data'=>"xsetting"]],
]
])
]);
}

if($data == "editFol"){
     bot('editMessageText',[
        'chat_id'=>$cid2,
       'message_id'=>$mid2,
       'text'=>"<b>Quyidagilardan birini tanlang:</b>",
'parse_mode'=>'html',
'reply_markup'=>json_encode([
'inline_keyboard'=>[
[['text'=>"Nomini o'zgartirish",'callback_data'=>"editFols"]],
]
])
]);
}


if($data == "editFols"){
$a = mysqli_query($connect,"SELECT * FROM categorys");
$c = mysqli_num_rows($a);
while($s = mysqli_fetch_assoc($a)){
$k[]=['text'=>enc("decode",$s['category_name']),'callback_data'=>"editFolss-".$s['category_id']];
}

$keyboard2=array_chunk($k,3);
$kb=json_encode([
'inline_keyboard'=>$keyboard2,
]);
if(!$c){
	bot('answerCallbackQuery',[
		'callback_query_id'=>$qid,
		'text'=>"⚠️ Bo'limlar topilmadi!",
		'show_alert'=>true,
		]);
	}else{
     bot('editMessageText',[
        'chat_id'=>$cid2,
       'message_id'=>$mid2,
       'text'=>"<b>Quyidagilardan birini tanlang:</b>",
'parse_mode'=>'html',
'reply_markup'=>$kb
]);
}
}

if(mb_stripos($data, "editFolss-")!==false){
	$ex = explode("-",$data)[1];
	bot('deleteMessage',[
	'chat_id'=>$cid2,
	'message_id'=>$mid2,
]);
   bot('sendMessage',[
   'chat_id'=>$cid2,
   'text'=>"<b>Yangi qiymatni kiriting:</b>",
   'parse_mode'=>'html',
   'reply_markup'=>$aort
]);
file_put_contents("user/$cid2.step","editFol-$ex");

}

if((mb_stripos($step,"editFol-")!==false)){
	$ex = explode("-",$step)[1];
if(isset($text)){
$text=enc("encode",$text);
mysqli_query($connect,"UPDATE categorys SET category_name = '$text' WHERE category_id = $ex");
		bot('SendMessage',[
		'chat_id'=>$cid,
		'text'=>"<b>Muvaffaqiyatli o'zgartirildi.</b>",
		'parse_mode'=>'html',
		'reply_markup'=>$panel2
]);
unlink("user/$cid.step");

}
}



if($data=="delFol"){
$a = mysqli_query($connect,"SELECT * FROM categorys");
$c = mysqli_num_rows($a);
while($s = mysqli_fetch_assoc($a)){
$k[]=['text'=>enc("decode",$s['category_name']),'callback_data'=>"delFols=".$s['category_id']];
}

$keyboard2=array_chunk($k,1);
$kb=json_encode([
'inline_keyboard'=>$keyboard2,
]);
if(!$c){
	bot('answerCallbackQuery',[
		'callback_query_id'=>$qid,
		'text'=>"⚠️ Bo‘limlar topilmadi!",
		'show_alert'=>true,
		]);
	}else{
edit($chat_id,$message_id,"👉 O‘zingizga kerakli tarmoqni tanlang:",$kb);

}
}

if(mb_stripos($data, "delFols=")!==false){
	$ex = explode("=",$data)[1];
	$sd = mysqli_fetch_assoc(mysqli_query($connect,"SELECT * FROM categorys WHERE category_id  = $ex"));
	$cd=$sd['category_id'];
	$d=enc("decode",$sd['category_name']);
$qd = mysqli_fetch_assoc(mysqli_query($connect,"SELECT * FROM cates WHERE category_id  = $ex"));
$sa=$qd['cate_id'];
mysqli_query($connect,"DELETE FROM services WHERE category_id=$sa");
mysqli_query($connect,"DELETE FROM cates WHERE category_id = $cd");
mysqli_query($connect,"DELETE FROM categorys WHERE category_id='$ex'");
     bot('deleteMessage',[
	'chat_id'=>$chat_id,
	'message_id'=>$message_id,
]);
   bot('sendMessage',[
   'chat_id'=>$chat_id,
       'text'=>"Bo'lim olib tashlandi!",
'parse_mode'=>'html',
'reply_markup'=>$panel2
]);

}



if($data == "newFol"){
	bot('deleteMessage',[
	'chat_id'=>$chat_id,
	'message_id'=>$message_id,
]);
   bot('sendMessage',[
   'chat_id'=>$chat_id,
   'text'=>"<b>Yangi bo'lim nomini yuboring:</b>",
   'parse_mode'=>'html',
   'reply_markup'=>$aort
]);
file_put_contents("user/$chat_id.step",'newFol');

}

if($step == "newFol"){
$res = mysqli_query($connect, "SELECT * FROM `categorys`");
$n = mysqli_fetch_assoc($res);
$text=enc("encode",$text);
mysqli_query($connect,"INSERT INTO categorys(category_name,category_status) VALUES('$text','ON');");
		bot('SendMessage',[
		'chat_id'=>$cid,
		'text'=>"Bo'lim qo'shildi!",
		'parse_mode'=>'html',
		'reply_markup'=>$panel2
]);
unlink("user/$cid.step");

}


if($data == "ichki"){
     bot('editMessageText',[
        'chat_id'=>$chat_id,
       'message_id'=>$message_id,
       'text'=>"<b>Quyidagilardan birini tanlang:</b>",
'parse_mode'=>'html',
'reply_markup'=>json_encode([
'inline_keyboard'=>[
[['text'=>"Yangi ichki bo'lim qo'shish",'callback_data'=>"newFold"]],
[['text'=>"Tahrirlash",'callback_data'=>"editFold"]],
[['text'=>"O'chirish",'callback_data'=>"delFold"]],
[['text'=>"Orqaga", 'callback_data'=>"xsetting"]],
]
])
]);
}

if($data == "editFold"){
     bot('editMessageText',[
        'chat_id'=>$cid2,
       'message_id'=>$mid2,
       'text'=>"<b>Quyidagilardan birini tanlang:</b>",
'parse_mode'=>'html',
'reply_markup'=>json_encode([
'inline_keyboard'=>[
[['text'=>"Nomini o'zgartirish",'callback_data'=>"editFolds"]],
]
])
]);
}



if($data == "editFolds"){
$a = mysqli_query($connect,"SELECT * FROM categorys");
while($s = mysqli_fetch_assoc($a)){
$k[]=['text'=>enc("decode",$s['category_name']),'callback_data'=>"editFolds-".$s['category_id']];
}

$keyboard2=array_chunk($k,1);
$kb=json_encode([
'inline_keyboard'=>$keyboard2,
]);
     bot('editMessageText',[
        'chat_id'=>$cid2,
       'message_id'=>$mid2,
       'text'=>"<b>Quyidagilardan birini tanlang:</b>",
'parse_mode'=>'html',
'reply_markup'=>$kb
]);
}

if(mb_stripos($data, "editFolds-")!==false){
$n = explode("-",$data)[1];
$new_arr = [];
$k = [];
$a = mysqli_query($connect,"SELECT * FROM cates WHERE category_id = $n");
$c = mysqli_num_rows($a);
while($s = mysqli_fetch_assoc($a)){
if(!in_array(enc("decode",$s['name']), $new_arr)){
$new_arr[] = enc("decode",$s['name']);
$k[]=['text'=>enc("decode",$s['name']),'callback_data'=>"editFoldm-".$s['cate_id']];
}
}
$keyboard2=array_chunk($k,1);
$kb=json_encode([
'inline_keyboard'=>$keyboard2,
]);
if(!$c){
	bot('answerCallbackQuery',[
		'callback_query_id'=>$qid,
		'text'=>"⚠️ Ushbu bo'lim uchun xizmat turlari topilmadi!",
		'show_alert'=>true,
		]);
	}else{
     bot('editMessageText',[
        'chat_id'=>$cid2,
       'message_id'=>$mid2,
       'text'=>"<b>Quyidagilardan birini tanlang:</b>",
'parse_mode'=>'html',
'reply_markup'=>$kb
]);
}
}

if(mb_stripos($data, "editFoldm-")!==false){
	$ex = explode("-",$data)[1];
	bot('deleteMessage',[
	'chat_id'=>$cid2,
	'message_id'=>$mid2,
]);
   bot('sendMessage',[
   'chat_id'=>$cid2,
   'text'=>"<b>Yangi qiymatni kiriting:</b>",
   'parse_mode'=>'html',
   'reply_markup'=>$boshqarish
]);
file_put_contents("user/$cid2.step","editFoldms-$ex");

}

if(mb_stripos($step, "editFoldms-")!==false){
	$ex = explode("-",$step)[1];
	if(isset($text)){
	$text=enc("encode",$text);
		mysqli_query($connect,"UPDATE cates SET name = '$text' WHERE cate_id = $ex");
		bot('SendMessage',[
		'chat_id'=>$cid,
		'text'=>"<b>Muvaffaqiyatli o'zgartirildi.</b>",
		'parse_mode'=>'html',
		'reply_markup'=>$panel2
]);
unlink("user/$cid.step");

}

}





if($data == "delFold"){
$a = mysqli_query($connect,"SELECT * FROM categorys");
while($s = mysqli_fetch_assoc($a)){
$k[]=['text'=>enc("decode",$s['category_name']),'callback_data'=>"delFolds=".$s['category_id']];
}

$keyboard2=array_chunk($k,3);
$kb=json_encode([
'inline_keyboard'=>$keyboard2,
]);
     bot('editMessageText',[
        'chat_id'=>$cid2,
       'message_id'=>$mid2,
       'text'=>"<b>Quyidagilardan birini tanlang:</b>",
'parse_mode'=>'html',
'reply_markup'=>$kb
]);
}

if(mb_stripos($data, "delFolds=")!==false){
$bolim = explode("=",$data)[1];
$new_arr = [];
$k = [];
$a = mysqli_query($connect,"SELECT * FROM cates WHERE category_id = $bolim");
$c = mysqli_num_rows($a);
while($s = mysqli_fetch_assoc($a)){
if(!in_array(enc("decode",$s['name']), $new_arr)){
$new_arr[] = enc("decode",$s['name']);
$k[]=['text'=>enc("decode",$s['name']),'callback_data'=>"delFolm=".$s['cate_id']];
}
}
$keyboard2=array_chunk($k,1);
$keyboard2[]=[['text'=>"Orqaga",'callback_data'=>"absd"]];
$kb=json_encode([
'inline_keyboard'=>$keyboard2,
]);
if(!$c){
	bot('answerCallbackQuery',[
		'callback_query_id'=>$qid,
		'text'=>"⚠️ Ushbu bo'lim uchun xizmat turlari topilmadi!",
		'show_alert'=>true,
		]);
	}else{
     bot('editMessageText',[
        'chat_id'=>$cid2,
       'message_id'=>$mid2,
     'text'=>"<b>Quyidagilardan birini tanlang:</b>",
'parse_mode'=>'html',
'reply_markup'=>$kb
]);
}
}

if(mb_stripos($data, "delFolm=")!==false){
	$ex = explode("=",$data)[1];

$qd = mysqli_fetch_assoc(mysqli_query($connect,"SELECT * FROM cates WHERE cate_id  = $ex"));
$sa=$qd['cate_id'];
$d = enc("decode",$qd['name']);
mysqli_query($connect,"DELETE FROM services WHERE category_id=$sa");
mysqli_query($connect,"DELETE FROM cates WHERE cate_id=$ex");
     bot('deleteMessage',[
	'chat_id'=>$cid2,
	'message_id'=>$mid2,
]);
   bot('sendMessage',[
   'chat_id'=>$cid2,
       'text'=>"Ichki bo'lim olib tashlandi!",
'parse_mode'=>'html',
'reply_markup'=>$panel2
]);

}


if($data == "newFold"){
$a = mysqli_query($connect,"SELECT * FROM categorys");
while($s = mysqli_fetch_assoc($a)){
$k[]=['text'=>enc("decode",$s['category_name']),'callback_data'=>"adFol=".$s['category_id']];
}

$keyboard2=array_chunk($k,3);
$kb=json_encode([
'inline_keyboard'=>$keyboard2,
]);
     bot('editMessageText',[
        'chat_id'=>$chat_id,
       'message_id'=>$message_id,
       'text'=>"<b>Quyidagilardan birini tanlang:</b>",
'parse_mode'=>'html',
'reply_markup'=>$kb
]);
}


if(mb_stripos($data, "adFol=")!==false){
	$ex = explode("=",$data)[1];
	file_put_contents("set/c.txt",$ex);
	bot('deleteMessage',[
	'chat_id'=>$chat_id,
	'message_id'=>$message_id,
]);
   bot('sendMessage',[
   'chat_id'=>$chat_id,
   'text'=>"<b>Yangi ichki bo'lim nomini yuboring:</b>",
   'parse_mode'=>'html',
   'reply_markup'=>$aort
]);
file_put_contents("user/$chat_id.step",'newFold');

}


if($step == "newFold"){
		if(isset($text)){
$ci=get("set/c.txt");
$to=enc("encode",$text);
mysqli_query($connect,"INSERT INTO cates(`name`,`category_id`) VALUES ('$to','$ci')");
		bot('sendMessage',[
		'chat_id'=>$cid,
		'text'=>"Ichki bo'lim qo'shildi!",
		'parse_mode'=>'html',
		'reply_markup'=>$panel2
]);
unlink("user/$cid.step");

}
}


if($data == "xizmat"){
     bot('editMessageText',[
        'chat_id'=>$chat_id,
       'message_id'=>$message_id,
       'text'=>"<b>Quyidagilardan birini tanlang:</b>",
'parse_mode'=>'html',
'reply_markup'=>json_encode([
'inline_keyboard'=>[
[['text'=>"Yangi xizmat qo'shish",'callback_data'=>"newXiz"]],
[['text'=>"Xizmatlarni yuklab olish",'callback_data'=>"uplXiz"]],
[['text'=>"Tahrirlash",'callback_data'=>"editXiz"]],
[['text'=>"O'chirish",'callback_data'=>"delXiz"]],
[['text'=>"Orqaga", 'callback_data'=>"xsetting"]],
]
])
]);
}

if($data == "uplXiz"){
$a = mysqli_query($connect,"SELECT * FROM categorys");
$c = mysqli_num_rows($a);
while($s = mysqli_fetch_assoc($a)){
$k[]=['text'=>enc("decode",$s['category_name']),'callback_data'=>"uplad=".$s['category_id']];
}
$keyboard2=array_chunk($k,3);
$kb=json_encode([
'inline_keyboard'=>$keyboard2,
]);
if(!$c){
	bot('answerCallbackQuery',[
		'callback_query_id'=>$qid,
		'text'=>"⚠️ Bo‘limlar topilmadi!",
		'show_alert'=>true,
		]);
	}else{
     bot('editMessageText',[
        'chat_id'=>$chat_id,
       'message_id'=>$message_id,
       'text'=>"<b>Quyidagilardan birini tanlang:</b>",
'parse_mode'=>'html',
'reply_markup'=>$kb
]);
}
}


if(mb_stripos($data, "uplad=")!==false){
$n = explode("=",$data)[1];
$upx = json_decode(get("set/upladd.json"),1);
$upx['category_id']=$n;
file_put_contents("set/upladd.json",json_encode($upx,JSON_PRETTY_PRINT));
$new_arr = [];
$k = [];
$a = mysqli_query($connect,"SELECT * FROM cates WHERE category_id = $n");
$c = mysqli_num_rows($a);
while($s = mysqli_fetch_assoc($a)){
if(!in_array(enc("decode",$s['name']), $new_arr)){
$new_arr[] = enc("decode",$s['name']);
$k[]=['text'=>enc("decode",$s['name']),'callback_data'=>"uplads-".$s['cate_id']];
}
}
$keyboard2=array_chunk($k,1);
$keyboard2[]=[['text'=>"Orqaga",'callback_data'=>"uplXiz"]];
$kb=json_encode([
'inline_keyboard'=>$keyboard2,
]);
if(!$c){
	bot('answerCallbackQuery',[
		'callback_query_id'=>$qid,
		'text'=>"⚠️ Ushbu bo'lim uchun xizmat turlari topilmadi!",
		'show_alert'=>true,
		]);
	}else{
bot('editMessageText',[
        'chat_id'=>$chat_id,
       'message_id'=>$message_id,
'text'=>"<b>Quyidagilardan birini tanlang:</b>",
   'parse_mode'=>'html',
   'reply_markup'=>$kb
]);
}
}

if(stripos($data,"uplads-")!==false){
$n = explode("-",$data)[1];
$upx = json_decode(get("set/upladd.json"),1);
$upx['cate_id']=$n;
file_put_contents("set/upladd.json",json_encode($upx,JSON_PRETTY_PRINT));
$pr=0;
$prs="";
$a = mysqli_query($connect,"SELECT * FROM providers");
$c = mysqli_num_rows($a);
while($s = mysqli_fetch_assoc($a)){
$pr++;
$prtxt=str_replace(["/api/adapter/default/index","/api/v1","/api/v2","https://"],["","","",""],$s['api_url']);
$prs.="<b>".$pr."</b>: $prtxt\n";
$k[]=['text'=>$pr,'callback_data'=>"uplprv-".$s['id']];
}
$keyboard2=array_chunk($k,3);
$kb=json_encode([
'inline_keyboard'=>$keyboard2,
]);
if(!$c){

	bot('answerCallbackQuery',[
		'callback_query_id'=>$qid,
		'text'=>"⚠️ Provayderlar topilmadi!",
		'show_alert'=>true,
		]);
	}else{
		del();
     bot('sendMessage',[
        'chat_id'=>$chat_id,
       'text'=>"Provayderni tanlang:
 
$prs",
'parse_mode'=>"HTML",
'reply_markup'=>$kb,
]);

}
}

if(stripos($data,"uplprv-")!==false){
$n = explode("-",$data)[1];
$upx = json_decode(get("set/upladd.json"),1);
$upx['provider']=$n;
file_put_contents("set/upladd.json",json_encode($upx,JSON_PRETTY_PRINT));
edit($chat_id,$message_id,"Provayderning API valyutasini tanlang:",json_encode([
inline_keyboard=>[
[['text'=>"UZS",'callback_data'=>"uplval-UZS-".$upx['provider']]],
[['text'=>"USD",'callback_data'=>"uplval-USD-".$upx['provider']]],
[['text'=>"RUB",'callback_data'=>"uplval-RUB-".$upx['provider']]],
[['text'=>"INR",'callback_data'=>"uplval-INR-".$upx['provider']]],
[['text'=>"TRY",'callback_data'=>"uplval-TRY-".$upx['provider']]],
]]));

}


if(stripos($data,"uplval-")!==false){
$n = explode("-",$data)[1];
$prv = explode("-",$data)[2];
$upx = json_decode(get("set/upladd.json"),1);
$upx['currency']=$n;
file_put_contents("set/upladd.json",json_encode($upx,JSON_PRETTY_PRINT));
$h = json_decode(arr($prv));
$ko=1;
if($h->error) {
bot('answerCallbackQuery',[
		'callback_query_id'=>$qid,
		'text'=>"⚠️ Serverda nosozlik

Qaytadan urining",
		'show_alert'=>true,
		]);
		
		}else{
for($i=0;$i<=22;$i++){
if($h->results[$i]->name){
$arr3 []=['text'=>"".$h->results[$i]->name."",'callback_data'=>"apload=$i=$prv"];
}
}
}
$arr = array_chunk($arr3,1);
$arr[]=[['text'=>"Orqaga",'callback_data'=>"xizmat"],['text'=>"▶️ Keyingi",'callback_data'=>"nexti=next=$prv=$ko=$i"]];
$kb = json_encode([
'inline_keyboard'=>$arr,
]);

edit($chat_id,$message_id,"Kerakli xizmat turini tanlang",$kb);

}

if((stripos($data,"nexti=")!==false)){
$res=explode("=",$data)[1];
$prv=explode("=",$data)[2];
$ko=explode("=", $data)[3];
$kl=explode("=",$data)[4];
$h = json_decode(arr($prv));
$ko=$kl;
if($h->error) {
bot('answerCallbackQuery',[
		'callback_query_id'=>$qid,
		'text'=>"⚠️ Serverda nosozlik

Qaytadan urining",
		'show_alert'=>true,
		]);
		
		}else{
if($res=="next"){
$ma = $kl*2;
for($i=$kl;$i<=$ma;$i++){
$d = $h->results[$i]->name ? $h->results[$i]->name : "";
if($h->results[$i]->name){
$arr3 []=['text'=>$d,'callback_data'=>"apload=$i=$prv"];
}}}

$arr = array_chunk($arr3,1);

$arr[]=[['text'=>"Orqaga",'callback_data'=>"xizmat"],['text'=>"▶️ Keyingi",'callback_data'=>"nexti=next=$prv=$ko=$i"]];
$kb = json_encode([
'inline_keyboard'=>$arr,
]);
edit($chat_id,$message_id,"Kerakli xizmat turini tanlang:",$kb);
exit();
}
}

if((stripos($data,"apload=")!==false)){
$qa = explode("=", $data)[1];
$qa=$qa+1;
$prv=explode("=",$data)[2];
$h = json_decode(arr($prv),1);
if($h['error']){
bot('answerCallbackQuery',[
		'callback_query_id'=>$qid,
		'text'=>"⚠️ Serverda nosozlik
	
Qaytadan urining",
		'show_alert'=>true,
		]);

		}
foreach($h['results'] as $vs){
if($vs['id']==$qa){
$nq = $vs['name'] ? $nq=$vs['name'] : "";
}
}
bot('answerCallbackQuery',[
		'callback_query_id'=>$qid,
		'text'=>"$nq - uchun xizmatlar qidirilmoqda

Iltimos kuting...",
		'show_alert'=>true,
		]);
$upx = json_decode(get("set/upladd.json"),1);
$upx['category']=$nq;
file_put_contents("set/upladd.json",json_encode($upx,JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE));
$s = mysqli_fetch_assoc(mysqli_query($connect,"SELECT * FROM `providers` WHERE id = $prv"));
$j=json_decode(file_get_contents($s['api_url']."?key=".$s['api_key']."&action=services"),1);
$service_count = 0;
$serviceid = 0;
foreach($j as $el){
if($el['category']==$nq){

$service_count++;
$serviceid++;
$name=$el["name"];
$txe = $el['service'];
$min=$el["min"];
$max=$el["max"];
$type=$el['type'];
$service_ide=$el['service'];
$cancel=$el['cancel'] ? 'true':'false';
$dripfeed=$el['dripfeed'] ? 'true':'false';
$refill=$el['refill'] ? 'true':'false';
$k[]=['text'=>($name),'callback_data'=>"couple=".$txe];
}
}
$ko =array_chunk($k,1);
if(empty($service_count)) {
bot('answerCallbackQuery',[
		'callback_query_id'=>$qid,
		'text'=>"⚠️ Serverda nosozlik
	
Qaytadan urining",
		'show_alert'=>true,
		]);

}else{
$ko[]=[['text'=>"✅ Barchasini yuklab olish",'callback_data'=>"allapl=$prv"]];
}
$ko[]=[['text'=>"Orqaga",'callback_data'=>"xizmat"]];
$kb = json_encode([
inline_keyboard=>$ko
]);
edit($chat_id,$message_id,"
$nq

🔢 Xizmatlar soni: $service_count - ta",$kb);
}


if((stripos($data,"allapl=")!==false)){
del();
	$prv=explode("=",$data)[1];
$mas=bot('sendMessage',[
		'chat_id'=>$chat_id,
		'text'=>"📂 Yuklab olish boshlandi!..

🔔 Iltimos kuting.",
		])->result->message_id;
		
		$upx = json_decode(get("set/upladd.json"),1);
		
$s = mysqli_fetch_assoc(mysqli_query($connect,"SELECT * FROM `providers` WHERE id = $prv"));

$j=json_decode(file_get_contents($s['api_url']."?key=".$s['api_key']."&action=services"),1);
if(empty($j)){
edit($cid2,$mas,"⚠️ Serverda nosozlik

Qaytadan urining",null);

}else{
$service_id = mysqli_num_rows(mysqli_query($connect,"SELECT * FROM `services`"));
foreach($j as $el){
if($el['category']==$upx['category']){
$service_id++;
$name=($el["name"]);
$tas = $el['service'];
$min=$el["min"];
$max=$el["max"];
$rate=$el["rate"];
$type=$el['type'];
$cancel=$el['cancel'] ? 'true':'false';
$dripfeed=$el['dripfeed'] ? 'true':'false';
$refill=$el['refill'] ? 'true':'false';

if($upx['currency']=="USD"){
$fr=get("set/usd");
}elseif($upx['currency']=="RUB"){
$fr=get("set/rub");
}elseif($upx['currency']=="INR"){
$fr=get("set/inr");
}elseif($upx['currency']=="TRY"){
$fr=get("set/try");
}elseif($upx['currency']=="UZS"){
$fr = 1;
}

$foiz=mysqli_fetch_assoc(mysqli_query($connect,"SELECT * FROM percent WHERE id = 1"))['percent'];
$rate=$rate*$fr;
$rp=$rate/100;
$rp=$rp*$foiz+$rate;


$service_price = $rp;
$category_id=$upx['cate_id'];
$api_service=$prv; 
$api_currency =$upx['currency']; 
$service_name = base64_encode(mb_convert_encoding(trans($name),"UTF-8","UTF-8"));
$service_desc=null;
$service_edit = "true";
$sq=mysqli_query($connect,"INSERT INTO 
services(`service_status`,`service_edit`,`service_price`,`category_id`,`service_api`,`api_service`,`api_currency`,`service_type`,`api_detail`,`service_name`,`service_desc`,`service_min`,`service_max`) VALUES ('on','$service_edit','$service_price','$category_id','$tas','$api_service','$api_currency','$type','{\"name\":\"$name\",\"min\":\"$min\",\"max\":\"$max\",\"type\":\"$type\",\"cancel\":\"$cancel\",\"refill\":\"$refill\",\"dripfeed\":\"$dripfeed\"}','$service_name','$service_desc','$min','$max');");
}
}

edit($chat_id,$mas,"✅ Yuklab olish jarayoni tugallandi.",null);
unlink("user/$cid2.step");

}
}



if($data == "editXiz"){
     bot('editMessageText',[
        'chat_id'=>$cid2,
       'message_id'=>$mid2,
       'text'=>"<b>Quyidagilardan birini tanlang:</b>",
'parse_mode'=>'html',
'reply_markup'=>json_encode([
'inline_keyboard'=>[
[['text'=>"API xizmat IDsini o'zgartirish",'callback_data'=>"editXizmat-service_api"]],
[['text'=>"Xizmat nomini o'zgartirish",'callback_data'=>"editXizmat-service_name"]],
[['text'=>"Malumotlarni o'zgartirish", 'callback_data'=>"editXizmat-service_desc"]],
[['text'=>"Narxini o‘zgartirish",'callback_data'=>"editXizmat-service_price"]],
[['text'=>".Min buyurtmani o‘zgartirish",'callback_data'=>"editXizmat-service_min"]],
[['text'=>".Max buyurtmani o‘zgartirish",'callback_data'=>"editXizmat-service_max"]],
[['text'=>"Orqaga", 'callback_data'=>"xizmat"]],
]
])
]);
}

if(mb_stripos($data, "editXizmat-")!==false){
$nomi = explode("-",$data)[1];
file_put_contents("user/$cid2.txt",$nomi);
$a = mysqli_query($connect,"SELECT * FROM categorys");
$c = mysqli_num_rows($a);
while($s = mysqli_fetch_assoc($a)){
$k[]=['text'=>enc("decode",$s['category_name']),'callback_data'=>"editXizmats-".$s['category_id']];
}

$keyboard2=array_chunk($k,3);
$keyboard2[]=[['text'=>"Orqaga",'callback_data'=>"editXiz"]];
$kb=json_encode([
'inline_keyboard'=>$keyboard2,
]);
if(!$c){
	bot('answerCallbackQuery',[
		'callback_query_id'=>$qid,
		'text'=>"⚠️ Tarmoqlar topilmadi!",
		'show_alert'=>true,
		]);
	}else{
     bot('editMessageText',[
        'chat_id'=>$cid2,
       'message_id'=>$mid2,
       'text'=>"<b>Quyidagilardan birini tanlang:</b>",
'parse_mode'=>'html',
'reply_markup'=>$kb
]);
}
}


if(mb_stripos($data, "editXizmats-")!==false){
$bolim = explode("-",$data)[1];
$new_arr = [];
$k = [];
$a = mysqli_query($connect,"SELECT * FROM cates WHERE category_id = $bolim");
$c = mysqli_num_rows($a);
while($s = mysqli_fetch_assoc($a)){
if(!in_array(enc("decode",$s['name']), $new_arr)){
$new_arr[] = enc("decode",$s['name']);
$k[]=['text'=>enc("decode",$s['name']),'callback_data'=>"editXt-".$s['cate_id']];
}
}
$keyboard2=array_chunk($k,1);
$keyboard2[]=[['text'=>"Orqaga",'callback_data'=>"editXizmat-$bolim"]];
$kb=json_encode([
'inline_keyboard'=>$keyboard2,
]);
if(!$c){
	bot('answerCallbackQuery',[
		'callback_query_id'=>$qid,
		'text'=>"⚠️ Ushbu bo'lim uchun xizmat turlari topilmadi!",
		'show_alert'=>true,
		]);
	}else{
     bot('editMessageText',[
        'chat_id'=>$cid2,
       'message_id'=>$mid2,
       'text'=>"<b>Quyidagilardan birini tanlang:</b>",
'parse_mode'=>'html',
'reply_markup'=>$kb
]);
}
}


if(mb_stripos($data, "editXt-")!==false){
$n=explode("-",$data)[1];
$as=1;
$a = mysqli_query($connect,"SELECT * FROM services WHERE category_id = '$n'");
$c = mysqli_num_rows($a);
while($s = mysqli_fetch_assoc($a)){
$txts.="<b>".$as."</b>: ".base64_decode($s['service_name'])."\n";
$k[]=['text'=>$as++,'callback_data'=>"editXts-".$s['service_id']];
}
$keyboard2=array_chunk($k,3);
$keyboard2[]=[['text'=>"Orqaga",'callback_data'=>"editXizmats-$n"]];
$kb=json_encode([
'inline_keyboard'=>$keyboard2,
]);
if(!$c){
	bot('answerCallbackQuery',[
		'callback_query_id'=>$qid,
		'text'=>" ⚠️ Ushbu bo'lim uchun xizmatlar topilmadi!",
		'show_alert'=>true,
		]);
	}else{
     bot('editMessageText',[
        'chat_id'=>$cid2,
       'message_id'=>$mid2,
    'text'=>"<b>Quyidagilardan birini tanlang:\n\n$txts</b>",
'parse_mode'=>'html',
'reply_markup'=>$kb
]);
}
}

if(mb_stripos($data, "editXts-")!==false){
	$xiz = explode("-",$data)[1];
	bot('deleteMessage',[
	'chat_id'=>$cid2,
	'message_id'=>$mid2,
]);
   bot('sendMessage',[
   'chat_id'=>$cid2,
   'text'=>"<b>Yangi qiymatni kiriting:</b>",
   'parse_mode'=>'html',
   'reply_markup'=>$boshqarish
]);
file_put_contents("user/$cid2.step","editXizmatlar-$xiz");

}

if(mb_stripos($step, "editXizmatlar-")!==false){
	$xiz = explode("-",$step)[1];
	$ex = file_get_contents("user/$cid.txt");
	if($cid == $admin and isset($text)){
		if($ex=="service_desc"){
		$ex = file_get_contents("user/$cid.txt");
		$vo = base64_encode($text);
		mysqli_query($connect,"UPDATE services SET service_desc='$vo' WHERE service_id = $xiz");
		}elseif($ex=="service_name"){
		$ex = file_get_contents("user/$cid.txt");
		$vo = base64_encode($text);
		mysqli_query($connect,"UPDATE services SET service_name='$vo' WHERE service_id = $xiz");
		}elseif($ex=="service_id"){
		$ex = file_get_contents("user/$cid.txt");
		$vo = $text;
		mysqli_query($connect,"UPDATE services SET service_api='$vo' WHERE service_id = $xiz");
		}elseif($ex=="service_price"){
		$ex = file_get_contents("user/$cid.txt");
		$vo = $text;
		mysqli_query($connect,"UPDATE services SET service_edit='false', service_price='$vo' WHERE service_id = $xiz");
		}elseif($ex=="service_min"){
		$ex = file_get_contents("user/$cid.txt");
		$vo = $text;
		mysqli_query($connect,"UPDATE services SET service_edit='false', service_min='$vo' WHERE service_id = $xiz");
		}elseif($ex=="service_max"){
		$ex = file_get_contents("user/$cid.txt");
		$vo = $text;
		mysqli_query($connect,"UPDATE services SET service_edit='false', service_max='$vo' WHERE service_id = $xiz");
		}
		bot('SendMessage',[
		'chat_id'=>$cid,
		'text'=>"<b> Muvaffaqiyatli o'zgartirildi.</b>",
		'parse_mode'=>'html',
		'reply_markup'=>$panel2
]);
unlink("user/$cid.step");
unlink("user/$cid.txt");

}
}




if($data == "delXiz"){
$a = mysqli_query($connect,"SELECT * FROM categorys");
$c = mysqli_num_rows($a);
while($s = mysqli_fetch_assoc($a)){
$k[]=['text'=>enc("decode",$s['category_name']),'callback_data'=>"deleteXiz-".$s['category_id']];
}
$keyboard2=array_chunk($k,3);
$kb=json_encode([
'inline_keyboard'=>$keyboard2,
]);
if(!$c){
	bot('answerCallbackQuery',[
		'callback_query_id'=>$qid,
		'text'=>"⚠️ Bo‘limlar topilmadi!",
		'show_alert'=>true,
		]);
	}else{
     bot('editMessageText',[
        'chat_id'=>$chat_id,
       'message_id'=>$message_id,
       'text'=>"<b>Quyidagilardan birini tanlang:</b>",
'parse_mode'=>'html',
'reply_markup'=>$kb
]);

}
}

if(mb_stripos($data, "deleteXiz-")!==false){
	$n = explode("-",$data)[1];
   file_put_contents("set/c.txt",$ex);
$new_arr = [];
$k = [];
$a = mysqli_query($connect,"SELECT * FROM cates WHERE category_id = $n");
$c = mysqli_num_rows($a);
while($s = mysqli_fetch_assoc($a)){
if(!in_array(enc("decode",$s['name']), $new_arr)){
$new_arr[] = enc("decode",$s['name']);
$k[]=['text'=>enc("decode",$s['name']),'callback_data'=>"delx-".$s['cate_id']];
}
}
$keyboard2=array_chunk($k,1);
$keyboard2[]=[['text'=>"Orqaga",'callback_data'=>"newXiz"]];
$kb=json_encode([
'inline_keyboard'=>$keyboard2,
]);
if(!$c){
	bot('answerCallbackQuery',[
		'callback_query_id'=>$qid,
		'text'=>"⚠️ Ushbu bo'lim uchun xizmat turlari topilmadi!",
		'show_alert'=>true,
		]);
	}else{
bot('editMessageText',[
        'chat_id'=>$chat_id,
       'message_id'=>$message_id,
'text'=>"<b>Quyidagilardan birini tanlang:</b>",
   'parse_mode'=>'html',
   'reply_markup'=>$kb
]);
}
}

if(mb_stripos($data, "delx-")!==false){
	$n=explode("-",$data)[1];
$as=0;
$a = mysqli_query($connect,"SELECT * FROM services WHERE category_id = '$n'");
$c = mysqli_num_rows($a);
while($s = mysqli_fetch_assoc($a)){
$as++;
$narx = $s['service_price'];
$txts.="<b>".$as."</b>: ".base64_decode($s['service_name'])." $narx - so‘m\n";

$k[]=['text'=>$as,'callback_data'=>"delmat-".$s['service_id']];
}
$keyboard2=array_chunk($k,5);
$kb=json_encode([
'inline_keyboard'=>$keyboard2,
]);
if(!$c){
	bot('answerCallbackQuery',[
		'callback_query_id'=>$qid,
		'text'=>"⚠️ Ushbu bo'lim uchun xizmatlar topilmadi!",
		'show_alert'=>true,
		]);
	}else{
edit($chat_id,$message_id,"
👉 O‘zingizga kerakli xizmatni tanlang:

$txts",$kb);

}
}

if(mb_stripos($data, "delmat-")!==false){
$ichki = explode("-",$data)[1];
mysqli_query($connect,"DELETE FROM services WHERE service_id = $ichki");
     bot('deleteMessage',[
	'chat_id'=>$cid2,
	'message_id'=>$mid2,
]);
   bot('sendMessage',[
   'chat_id'=>$cid2,
       'text'=>"Xizmat o‘chirildi!",
'parse_mode'=>'html',
'reply_markup'=>$panel
]);

}







if($data == "newXiz"){
$a = mysqli_query($connect,"SELECT * FROM categorys");
$c = mysqli_num_rows($a);
while($s = mysqli_fetch_assoc($a)){
$k[]=['text'=>enc("decode",$s['category_name']),'callback_data'=>"add=".$s['category_id']];
}
$keyboard2=array_chunk($k,3);
$kb=json_encode([
'inline_keyboard'=>$keyboard2,
]);
if(!$c){
	bot('answerCallbackQuery',[
		'callback_query_id'=>$qid,
		'text'=>"⚠️ Bo‘limlar topilmadi!",
		'show_alert'=>true,
		]);
	}else{
     bot('editMessageText',[
        'chat_id'=>$chat_id,
       'message_id'=>$message_id,
       'text'=>"<b>Quyidagilardan birini tanlang:</b>",
'parse_mode'=>'html',
'reply_markup'=>$kb
]);
}
}


if(mb_stripos($data, "add=")!==false){
$n = explode("=",$data)[1];
file_put_contents("set/c.txt",$n);
$new_arr = [];
$k = [];
$a = mysqli_query($connect,"SELECT * FROM cates WHERE category_id = $n");
$c = mysqli_num_rows($a);
while($s = mysqli_fetch_assoc($a)){
if(!in_array(enc("decode",$s['name']), $new_arr)){
$new_arr[] = enc("decode",$s['name']);
$k[]=['text'=>enc("decode",$s['name']),'callback_data'=>"adds-".$s['cate_id']];
}
}
$keyboard2=array_chunk($k,1);
$keyboard2[]=[['text'=>"Orqaga",'callback_data'=>"newXiz"]];
$kb=json_encode([
'inline_keyboard'=>$keyboard2,
]);
if(!$c){
	bot('answerCallbackQuery',[
		'callback_query_id'=>$qid,
		'text'=>"⚠️ Ushbu bo'lim uchun xizmat turlari topilmadi!",
		'show_alert'=>true,
		]);
	}else{
bot('editMessageText',[
        'chat_id'=>$chat_id,
       'message_id'=>$message_id,
'text'=>"<b>Quyidagilardan birini tanlang:</b>",
   'parse_mode'=>'html',
   'reply_markup'=>$kb
]);
}
}

if(mb_stripos($data, "adds-")!==false){
$pw=explode("-",$data)[1];
$adds=json_decode(get("set/adds.json"),1);
$adds['cate_id']=$pw;
$a = mysqli_query($connect,"SELECT * FROM providers");
$c = mysqli_num_rows($a);
if(!$c){
bot('answerCallbackQuery',[
		'callback_query_id'=>$qid,
		'text'=>"⚠️ Provayderlar topilmadi!",
		'show_alert'=>true,
		]);
	}else{
$adds['category_id']=file_get_contents("set/c.txt");
put("set/adds.json",json_encode($adds,JSON_UNESCAPED_UNICODE));
	bot('deleteMessage',[
	'chat_id'=>$chat_id,
	'message_id'=>$message_id,
]);
   bot('sendMessage',[
   'chat_id'=>$chat_id,
   'text'=>"<b>Yangi xizmat nomini yuboring:</b>",
   'parse_mode'=>'html',
   'reply_markup'=>$aort
]);
file_put_contents("user/$chat_id.step",'servisw');

}
}
if($step == "servisw"){
$pr=0;
$prs="";
$a = mysqli_query($connect,"SELECT * FROM providers");
$c = mysqli_num_rows($a);
while($s = mysqli_fetch_assoc($a)){
$pr++;
$prtxt=str_replace(["/api/v1","/api/v2","https://"],["","",""],$s['api_url']);
$prs.="<b>".$pr."</b>: $prtxt\n";
$k[]=['text'=>$pr,'callback_data'=>"checkC-".$s['id']];
}
$keyboard2=array_chunk($k,3);
$kb=json_encode([
'inline_keyboard'=>$keyboard2,
]);
if(!$c){
	bot('sendMessage',[
		chat_id=>$cid,
		'text'=>"⚠️ Provayderlar topilmadi!",
		]);
	}else{
     bot('sendMessage',[
        'chat_id'=>$cid,
       'text'=>"Provayderni tanlang:
 
$prs",
'parse_mode'=>"HTML",
'reply_markup'=>$kb,
]);

put("set/adds.json.name",$text);
file_put_contents("user/$cid.step","servis0");

}
}

if((stripos($data,"checkC-")!==false and $stepc=="servis0" and $chat_id==$admin)){
$pw=explode("-",$data)[1];
sms($chat_id,"Provayderning API xizmatlari bolimida korsatilgan valyutani tanlang:",json_encode([
'inline_keyboard'=>[
[['text'=>"UZS ",'callback_data'=>"checkP-UZS"]],
[['text'=>"USD ",'callback_data'=>"checkP-USD"]],
[['text'=>"RUB ",'callback_data'=>"checkP-RUB"]],
[['text'=>"INR ",'callback_data'=>"checkP-INR"]],
[['text'=>"TRY ",'callback_data'=>"checkP-TRY"]],
]]));
$adds=json_decode(get("set/adds.json"),1);
$adds['api_service']=$pw;
put("set/adds.json",json_encode($adds,JSON_UNESCAPED_UNICODE));
file_put_contents("user/$chat_id.step",'servis1');
}

if((stripos($data,"checkP-")!==false and  $stepc=="servis1" and $chat_id==$admin)){
$pw=explode("-",$data)[1];
if(isset($data)){
del();
sms($chat_id,"📝 Xizmat xaqida malumotlar kiriting:

⚠️ Ma'lumot kiritish ni xoxlamasangiz <b>Kiritilmagan</b> tugmasini bosing",json_encode([
'resize_keyboard'=>true,
'keyboard'=>[
[['text'=>"Kiritilmagan"]],
[['text'=>"🗄️ Boshqaruv"]],
]]));
$adds=json_decode(get("set/adds.json"),1);
$adds['api_currency']=$pw;
put("set/adds.json",json_encode($adds,JSON_UNESCAPED_UNICODE));
file_put_contents("user/$chat_id.step",'servis2');
}
}
if(($step=="servis2" and $cid==$admin)){
if(isset($text)){
sms($cid,"💵 Buyurtma narxini yuboring (1000 ta) uchun",$aort);
if($text=="Kiritilmagan"){
put("set/adds.json.desc","");
}else{
put("set/adds.json.desc",$text);
}
file_put_contents("user/$cid.step",'servis3');
}

}


if(($step=="servis3" and $cid==$admin)){
if(is_numeric($text)){
sms($cid,"🆔 Xizmat IDsini yuboring:",$aort);
$adds=json_decode(get("set/adds.json"),1);
$adds['service_price']=$text;
put("set/adds.json",json_encode($adds,JSON_UNESCAPED_UNICODE));
file_put_contents("user/$cid.step",'servisID');
}

}


if($step=="servisID"){
if(is_numeric($text)){
$pw = json_decode(get("set/adds.json"));
$cure = $pw->api_service;
$ap = mysqli_fetch_assoc(mysqli_query($connect,"SELECT * FROM providers WHERE id = $cure"));
$surl=$ap['api_url'];
$skey=$ap['api_key'];
$j=json_decode(get($surl."?key=".$skey."&action=services"), true);
foreach($j as $el){
if($el['service']=="$text"){
$name=$el["name"];
$min=$el["min"];
$max=$el["max"];
$rate=$el["rate"];
$rate=$el["rate"];
$type=$el['type'];
$tas = $el['service'];
$cancel=$el['cancel'] ? 'true':'false';
$dripfeed=$el['dripfeed'] ? 'true':'false';
$refill=$el['refill'] ? 'true':'false';
break;
}
}


if(empty($min) and empty($max)){
sms($cid,"
🔕 Noma'lum xatolik yuz berdi.

Qaytadan xizmat IDsini yuboring:",null);
}else{
$category_id=$pw->cate_id;
$service_price = $pw->service_price;
$api_service=$pw->api_service; 
$api_currency =$pw->api_currency; 
$service_name = base64_encode(mb_convert_encoding(get("set/adds.json.name"),"UTF-8","UTF-8"));
$service_desc = base64_encode(get("set/adds.json.desc"));
$service_edit = "true";
mysqli_query($connect,"INSERT INTO services(`service_status`,`service_price`,`service_edit`,`category_id`,`service_api`,`api_service`,`api_currency`,`service_type`,`api_detail`,`service_name`,`service_desc`,`service_min`,`service_max`) VALUES ('on','$service_price','$service_edit','$category_id','$text','$api_service','$api_currency','$type','{\"name\":\"$name\",\"min\":\"$min\",\"max\":\"$max\",\"type\":\"$type\",\"cancel\":\"$cancel\",\"refill\":\"$refill\",\"dripfeed\":\"$dripfeed\"}','$service_name','$service_desc','$min','$max');");

sms($cid,"✅ Yangi xizmat qo'shildi.",$panel2);
}
}

}




if($text=="💵 Pul kiritish" and joinchat($cid)==1){
$ops=get("set/payments.txt");
$s=explode("\n",$ops);
$soni = substr_count($ops,"\n");
for($i=1;$i<=$soni;$i++){
$k[]=['text'=>$s[$i],'callback_data'=>"payBot=".$s[$i]];
}
$keyboard2=array_chunk($k,2);
$keyboard2[]=[['text'=>"💳 PAYME",'callback_data'=>"menu=PAYME"]];
$kb=json_encode([
'inline_keyboard'=>$keyboard2,
]);
sms($cid,"💳 Kerakli tolov tizimini tanlang:",$kb);

}

if($text=="⚙️ Asosiy sozlamalar" and $cid==$admin){
sms($cid,$text,$panel2);

}

if($text=="💵 Kursni o‘rnatish" and $cid==$admin){
sms($cid,"👉 Kerakli valyutasi tanlang:",json_encode([
'inline_keyboard'=>[
[['text'=>"AQSH dollari ($)",'callback_data'=>"course=usd"]],
[['text'=>"Rossiya rubli (₽)",'callback_data'=>"course=rub"]],
[['text'=>"Hindston rupiysi (₹)",'callback_data'=>"course=inr"]],
[['text'=>"Turkiya lirasi (₺)",'callback_data'=>"course=try"]],
]]));

}

if((stripos($data,"course=")!==false)){
$val=explode("=",$data)[1];
if(get("set/".$val."")){
$VAL=get("set/".$val);
}else{
$VAL=0;
}
del();
sms($chat_id,"
1 - ".strtoupper($val)." narxini kiriting:

♻️ Joriy narx: ".$VAL." so‘m",$aort);
put("user/$chat_id.step","course=$val");
}

if((mb_stripos($step,"course=")!==false and is_numeric($text))){
$val=explode("=",$step)[1];
put("set/".$val,"$text");
sms($cid,"
✅ 1 - ".strtoupper($val)." narxi $text so‘mga o‘zgardi",$panel);
unlink("user/$cid.step");

}

if($text == "🗣 Referal" and joinchat($cid)==1) {
$result = mysqli_query($connect, "SELECT * FROM users WHERE id = $cid");
$row = mysqli_fetch_assoc($result);
$myid = $row['user_id'];
sms($cid,"
Sizning referal havolangiz:

https://t.me/$bot?start=user$myid

Sizga har bir taklif qilgan referalingiz uchun ".enc("decode",$setting['referal'])." so'm beriladi.

👤ID raqam: $myid",json_encode([
inline_keyboard=>[
//[['text'=>"🗣 Referal konkurs",'callback_data'=>"konkurs"]],
]]));
}
if($data == "konkurs" and joinchat($chat_id)==1){
edit($cid2,$mid2,referal(10),null);
}

if($text=="⚖️ Foizni o‘rnatish" and $cid==$admin){
$m = mysqli_fetch_assoc(mysqli_query($connect,"SELECT * FROM percent WHERE id = 1"))['percent'];
$m ? $m : 0;
sms($cid,"
⭐ Bot xizmatlari uchun foizni kiriting

♻️ Joriy foiz: $m%",$aort);
put("user/$cid.step","updFoiz");

}

if($step=="updFoiz"){
if(is_numeric($text)){
mysqli_query($connect,"UPDATE percent SET percent = '$text' WHERE id = 1");
sms($cid,"✅ O‘zgartirish muvaffaqiyatli bajarildi.",$panel);
}
put("user/$cid.step","");

}

$saved = file_get_contents("user/us.id");

if($text == "👤 Foydalanuvchini boshqarish"){
if($cid == $admin){
	bot('SendMessage',[
	'chat_id'=>$cid,
	'text'=>"<b>Kerakli foydalanuvchining ID raqamini kiriting:</b>",
	'parse_mode'=>'html',
	'reply_markup'=>$aort,
	]);
file_put_contents("user/$cid.step",'iD');
}

}

if($step == "iD"){
if($cid == $admin){
$rew = mysqli_fetch_assoc(mysqli_query($connect,"SELECT * FROM users WHERE user_id = $text"));
if($rew){
$idi = $rew['id'];
file_put_contents("user/us.id",$idi);
$pul = $rew['balance'];
$ban = $rew['status'];
if($ban == "active"){
	$bans = "🔔 Banlash";
}
if($ban == "deactive"){
	$bans = "🔕 Bandan olish";
}

bot('SendMessage',[
'chat_id'=>$cid,
'text'=>"<b>Qidirilmoqda...</b>",
'parse_mode'=>'html',
]);
bot('editMessageText',[
        'chat_id'=>$cid,
        'message_id'=>$mid + 1,
        'text'=>"<b>Qidirilmoqda...</b>",
       'parse_mode'=>'html',
]);
bot('editMessageText',[
      'chat_id'=>$cid,
     'message_id'=>$mid + 1,
'text'=>"<b>Foydalanuvchi topildi!

ID:</b> <a href='tg://user?id=$idi'>$text</a>
<b>Balans: $pul so‘m</b>",
'parse_mode'=>'html',
'reply_markup'=>json_encode([
	'inline_keyboard'=>[
[['text'=>"$bans",'callback_data'=>"ban"]],
[['text'=>"➕ Pul qo'shish",'callback_data'=>"plus"],['text'=>"➖ Pul ayirish",'callback_data'=>"minus"]],
]
])
]);
unlink("user/$cid.step");
}else{
bot('SendMessage',[
	'chat_id'=>$cid,
	'text'=>"<b>Foydalanuvchi topilmadi.</b>

Qayta urinib ko'ring:",
'parse_mode'=>'html',
]);
}
}

}

if($data == "plus"){
bot('sendMessage',[
'chat_id'=>$chat_id,
'message_id'=>$message_id,
'text'=>"<a href='tg://user?id=$saved'>$saved</a> <b>ning hisobiga qancha pul qo'shmoqchisiz?</b>",
'parse_mode'=>"html",
	'reply_markup'=>$aort,
]);
file_put_contents("user/$chat_id.step",'plus');

}

if($step == "plus"){
if($cid == $admin){
if(is_numeric($text)=="true"){
bot('sendMessage',[
'chat_id'=>$saved,
'text'=>"<b>Adminlar tomonidan hisobingiz $text so‘m to'ldirildi</b>",
'parse_mode'=>"html",
'reply_markup'=>$menu,
]);
bot('sendMessage',[
'chat_id'=>$cid,
'text'=>"<b>Foydalanuvchi hisobiga $text so‘m qo'shildi!</b>",
'parse_mode'=>"html",
'reply_markup'=>$panel,
]);
$rew = mysqli_fetch_assoc(mysqli_query($connect,"SELECT * FROM users WHERE id = $saved"));
$miqdor = $text+$rew['balance'];
$p2 =$text+$rew['outing'];
mysqli_query($connect,"UPDATE users SET balance=$miqdor, outing=$p2 WHERE id =$saved");
unlink("user/$cid.step");
}else{
bot('SendMessage',[
'chat_id'=>$cid,
'text'=>"<b>Faqat raqamlardan foydalaning!</b>",
'parse_mode'=>'html',
'protect_content'=>true,
]);

}
}

}

if($data == "minus"){
bot('sendMessage',[
'chat_id'=>$chat_id,
'message_id'=>$message_id,
'text'=>"<a href='tg://user?id=$saved'>$saved</a> <b>ning hisobidan qancha pul ayirmoqchisiz?</b>",
'parse_mode'=>"html",
	'reply_markup'=>$aort,
]);
file_put_contents("user/$chat_id.step",'minus');

}

if($step == "minus"){
if($cid == $admin){
if(is_numeric($text)=="true"){
bot('sendMessage',[
'chat_id'=>$saved,
'text'=>"<b>Adminlar tomonidan hisobingizdan $text so‘m olindi.</b>",
'parse_mode'=>"html",
'reply_markup'=>$menu,
]);
bot('sendMessage',[
'chat_id'=>$cid,
'text'=>"<b>Foydalanuvchi hisobidan $text so‘m olindi!</b>",
'parse_mode'=>"html",
'reply_markup'=>$panel,
]);
$rew = mysqli_fetch_assoc(mysqli_query($connect,"SELECT * FROM users WHERE id = $saved"));
$miqdor =$rew['balance'] - $text;
$p2 =$rew['outing'] - $text;
mysqli_query($connect,"UPDATE users SET balance=$miqdor, outing=$p2 WHERE id =$saved");
unlink("user/$cid.step");
}else{
bot('SendMessage',[
'chat_id'=>$cid,
'text'=>"<b>Faqat raqamlardan foydalaning!</b>",
'parse_mode'=>'html',
'protect_content'=>true,
]);
}
}

}

if($data=="ban"){
$rew = mysqli_fetch_assoc(mysqli_query($connect,"SELECT * FROM users WHERE id = $saved"));
if($admin!=$saved){
if($rew['status'] == "deactive"){
mysqli_query($connect,"UPDATE users SET status='active' WHERE id =$saved");
bot('sendMessage',[
'chat_id'=>$chat_id,
'message_id'=>$message_id,
'text'=>"<b>Foydalanuvchi ($saved) bandan olindi!</b>",
'parse_mode'=>"html",
	'reply_markup'=>$panel,
]);
}else{
mysqli_query($connect,"UPDATE users SET status='deactive' WHERE id =$saved");
bot('sendMessage',[
'chat_id'=>$chat_id,
'message_id'=>$message_id,
'text'=>"<b>Foydalanuvchi ($saved) banlandi!</b>",
'parse_mode'=>"html",
	'reply_markup'=>$panel,
]);
}
}else{
bot('answerCallbackQuery',[
'callback_query_id'=>$qid,
'text'=>"Bloklash mumkin emas!",
'show_alert'=>true,
]);
}

}


if($data=="result" and joinchat($chat_id)==1){
if(joinchat($chat_id)==1){
	$usid = get("user/$chat_id.id");
$pul = mysqli_fetch_assoc(mysqli_query($connect,"SELECT*FROM users WHERE id=$usid"))['balance'];
$a = $pul+enc("decode",$setting['referal']);
mysqli_query($connect,"UPDATE users SET balance = $a WHERE id = $usid");
$text = "
<a href='tg://user?id=$chat_id'>✅ Foydalanuvchi</a> <b> botimizdan foydalanib boshladi!</b>

Hisobingizga ".enc("decode",$setting['referal'])." so‘m qo'shildi!";
sms($usid,"$text",$m);
$p = get("user/$usid.users");
put("user/$usid.users",$p+1);
unlink("user/$chat_id.id");
}
del();
sms($chat_id,"🖥️ Asosiy menyudasiz",$m);

}



if($text=="🛒 Buyurtmalar" and joinchat($cid)==1) {
$rew = mysqli_fetch_assoc(mysqli_query($connect,"SELECT * FROM myorder WHERE user_id = $cid"));
if(!$rew){
sms($cid,"🤷‍♂️ Sizda xechqanday buyurtma topilmadi.",null);

}else{
$rew = mysqli_query($connect,"SELECT * FROM myorder WHERE user_id = $cid");
while($my=mysqli_fetch_assoc($rew)){
$k[]=['text'=>$my['order_id']];
}
$keyboard2=array_chunk($k,3);
$keyboard2[]=[['text'=>"➡️ Orqaga"]];
$keyboard=json_encode([
'resize_keyboard'=>true,
'keyboard'=>
$keyboard2,
]);
sms($cid,"👉 Barcha buyurtmalaringiz:",$keyboard);
put("user/$cid.step",orders);

}
}

if($step=="orders" and is_numeric($text) and joinchat($cid)==1) {
$rew = mysqli_fetch_assoc(mysqli_query($connect,"SELECT * FROM orders WHERE order_id = $text"));
$ori =$rew['api_order'];
$prov =$rew['provider'];
$ap = mysqli_fetch_assoc(mysqli_query($connect,"SELECT * FROM providers WHERE id = $prov"));
$ourl=$ap['api_url'];
$okey=$ap['api_key'];
$s=json_decode(get($ourl."?key=".$okey."&action=status&order=$ori"),1);
$err=$s['error'];
$response=$rew['status'];
if($response=="Completed") {
   $status="bajarilgan";
   }
   if($response=="In progress") {
   $status="bajarilmoqda";
   }
   if($response=="Partial"){
   $status="qayta ishlanmoqda";
   }
   if($response=="Pending"){
  $status="bajarilmoqda";
  }
  if($response=="Processing"){
  $status="bajarilmoqda";
  }
  if($response=="Canceled"){
  $status="bekor qilingan";
  }
if(!$rew or $err){
sms($cid,"❌ Buyurtma topilmadi!",$m);
unlink("user/$cid.step");
}else{
sms($cid,"
✅ Buyurtma xolati: $status",$m);
unlink("user/$cid.step");
}

}



if($text=="/start" and joinchat($cid)==1){
$rew = mysqli_fetch_assoc(mysqli_query($connect,"SELECT * FROM users WHERE id = $cid"));
$start =str_replace(["{name}","{balance}","{time}"],["$name","".$rew['balance']."","$time"],enc("decode",$setting['start']));
sms($cid,$start,$m);

}

if($text=="➡️ Orqaga" and joinchat($cid)==1){
sms($cid,"🖥️ Asosiy menyudasiz",$m);
unlink("user/$cid.step");
exit();
}


if($text=="🇺🇿 Valyuta kursi" and $cid==$admin){
$json3=json_decode(file_get_contents("https://cbu.uz/uz/arkhiv-kursov-valyut/json/"),1);
foreach($json3 as $json4){
if($json4['Ccy']=="USD"){
$usd=$json4['Rate'];
break;
}
}
foreach($json3 as $json4){
if($json4['Ccy']=="RUB"){
$rub=$json4['Rate'];
break;
}
}
foreach($json3 as $json4){
if($json4['Ccy']=="INR"){
$inr=$json4['Rate'];
break;
}
}
foreach($json3 as $json4){
if($json4['Ccy']=="TRY"){
$try=$json4['Rate'];
break;
}
}

sms($cid,"<b> 
1 $(USD) - $usd UZS
1 ₽(RUB) - $rub UZS
1 ₹(INR) - $inr UZS
1 ₺(TRY) - $try UZS
</b>",$panel);

}


if($text=="📨 Yordam" and joinchat($cid)==1) {
sms($cid,"
⭐ Bizga savollaringiz bormi?

📑 Murojaat matnini yozib yuboring.
",$ort);
put("user/$cid.step","murojaat");

}

if($step=="murojaat"){
sms($cid,"✅ Murojaatingiz qabul qilindi",$m);
bot('copyMessage',[
chat_id=>$admin,
from_chat_id=>$cid,
'message_id'=>$mid,
'reply_markup'=>json_encode([
inline_keyboard=>[
[['text'=>"👁️ Ko‘rish",url=>"tg://user?id=$cid"]],
[['text'=>"📑 Javob yozish",'callback_data'=>"javob=$cid"]],
]
]),
]);
put("user/$cid.step","");

}
/*
if($text == "/otkazchi") {
	sms($cid,"Boshlandi",null);
$us = get("users.txt");
$a = explode("\n",$us);
$co = substr_count($us,"\n");
for($i = 1;$i<=$co;$i++){
adduser($a[$i]);
}
sms($cid,"Tugadi",null);
}*/

if((stripos($data,"javob=")!==false)){
$ida = explode("=", $data)[1];
sms($admin,"$ida Foydalanuvchiga yuboriladigan xabaringizni kiriting.",$ort);
put("user/$cid2.step","ticket=$ida");

}
if((mb_stripos($step,"ticket=")!==false) and ($cid==$admin)){
$ida = explode("=",$step)[1];
$if = bot('copyMessage',[
chat_id=>$ida,
from_chat_id=>$admin,
'message_id'=>$mid,
]);

if($if->ok == 1){
sms($cid,"✅ Xabar yuborildi",$panel);
}else{
sms($cid,"❌ Xabar yuborilmari, extimol botni bloklagan.",$panel);
}
unlink("user/$cid.step");

}

if($text=="👔 Kabinet" and joinchat($cid)==1) {
$rew = mysqli_fetch_assoc(mysqli_query($connect,"SELECT * FROM users WHERE id = $cid"));
$kabinet =str_replace(["{outing}","{balance}","{id}"],["".$rew['outing']."","".$rew['balance']."",$rew['user_id']],enc("decode",$setting['kabinet']));
sms($cid,$kabinet,json_encode([
inline_keyboard=>[
[['text'=>"💵 Pul kiritish",'callback_data'=>"menu=tolov"]],
]]));

}

if((stripos($data,"menu=")!==false and joinchat($chat_id)==1)){
$res=explode("=",$data)[1];
if($res=="tolov"){
$ops=get("set/payments.txt");
$s=explode("\n",$ops);
$soni = substr_count($ops,"\n");
for($i=1;$i<=$soni;$i++){
$k[]=['text'=>$s[$i],'callback_data'=>"payBot=".$s[$i]];
}
$keyboard2=array_chunk($k,2);
$keyboard2[]=[['text'=>"💳 PAYME",'callback_data'=>"menu=PAYME"]];
$keyboard2[]=[['text'=>"➡️ Orqaga",'callback_data'=>"menu=back"]];
$kb=json_encode([
'inline_keyboard'=>$keyboard2,
]);
edit($chat_id,$message_id,"💳 Kerakli tolov tizimini tanlang:",$kb);

}elseif($res=="back"){
$rew = mysqli_fetch_assoc(mysqli_query($connect,"SELECT * FROM users WHERE id = $chat_id"));
del();
$kabinet =str_replace(["{outing}","{balance}","{id}"],["".$rew['outing']."","".$rew['balance']."","$cid"],enc("decode",$setting['kabinet']));
sms($chat_id,"$kabinet",json_encode([
inline_keyboard=>[
[['text'=>"💵 Pul kiritish",'callback_data'=>"menu=tolov"]],
]]));

}elseif($res=="PAYME") {
if(empty($setting['payme_id']) or $setting['payme_id']=="null" or $setting['payme_id']=="NULL"){
bot('answerCallbackQuery',[
'callback_query_id'=>$cqid,
'text'=>"⚠️ Ushbu tolov tizimidagi kerakli malumotlar yetishmaydi",
'show_alert'=>true,
]);
}else{
del();
sms($chat_id,"
💵 To‘lov miqdorini kiriting:

⬇️ Minimal 10000 so‘m
⬆️ Maksimal 12000000 so‘m",$ort);
put("user/$chat_id.step","payme");
}
}
}

if((stripos($data,"payBot=")!==false)){
$h=explode("=", $data)[1];
$card=get("set/pay/$h/wallet.txt");
$info=get("set/pay/$h/addition.txt");
edit($cid2,$mid2,"
To'lov tizimi: $h

Hamyon: $card
Izoh: $cid2
   
$info
",json_encode([
'inline_keyboard'=>[
[['text'=>"✅ To‘lov qildim",'callback_data'=>"tolovqldm"]],
[['text'=>"➡️ Orqaga",'callback_data'=>"menu=tolov"]],
]]));
}

if($data == "tolovqldm") {

sms($chat_id,"💳 To‘lov cheki yoki rasmini yuboring",$ort);
put("user/$chat_id.step","tolovqldm");
}

if($step=="tolovqldm"){
sms($cid,"✅ Hisobni to‘ldirish arizangiz qabul qilindi.",$m);
file_put_contents("user/us.id",$cid);
if($text){
bot('forwardMessage',[
'chat_id'=>$admin,
'message_id'=>$mid,
'from_chat_id'=>$cid,
]);
sms($admin,"👤 Kerakli tugmani tanlang",json_encode([
'inline_keyboard'=>[
[['text'=>"Pul qo‘shish",'callback_data'=>'plus']],
]
]));
}elseif($message->photo){
bot('forwardMessage',[
'chat_id'=>$admin,
'message_id'=>$mid,
'from_chat_id'=>$cid,
]);
sms($admin,"👤 Kerakli tugmani tanlang",json_encode([
'inline_keyboard'=>[
[['text'=>"Pul qo‘shish",'callback_data'=>'plus']],
]
]));


}
unlink("user/$cid.step");
}




if($step=="payme"){
if($text>="10000" and $text<="12000000"){
$checkout=json_decode(file_get_contents("https://".$_SERVER['HTTP_HOST']."/payme.php?action=create&card=".$setting['payme_id']."&sum=$text&desc=@$bot"),true);
$checkout=$checkout['_result']['_details']['_pay_url'];  
$checkid=str_replace("https://checkout.paycom.uz/",'',$checkout);
sms($cid,"💵 To‘lov miqdori: $text so‘m",json_encode([
'inline_keyboard'=>[
[['text'=>"💵 To‘lovga o‘tish",'url'=>"$checkout"]],
[['text'=>"💵 Shuyerda to‘lash",'web_app'=>['url'=>"$checkout"]]],
[['text'=>"✅ Tekshirish",'callback_data'=>"checkout=$checkid=$text"]],
]]));
sms($cid,"🖥️ Asosiy menyudasiz",$menu);
exit; 
unlink("user/$cid.step");
}else{
sms($cid,"
⬇️ Minimal 10000 so‘m
⬆️ Maksimal 12000000 so‘m",$ort);
exit; 
}
}


if((stripos($data,"checkout=")!==false and joinchat($chat_id)==1)){
$checkid=explode("=",$data)[1];
$plus=explode("=",$data)[2];
$checkids=file_get_contents("payments.txt");
if(mb_stripos($checkids,$checkid)!==false){
bot('answerCallbackQuery',[
'callback_query_id'=>$cqid,
'text'=>"⚠️ To‘lov bajarilgan.",
'show_alert'=>true,
]);

}else{
$js=json_decode(file_get_contents("https://".$_SERVER['HTTP_HOST']."/payme.php?id=$checkid&action=info"),true);
$pay_time=$js['mess'];
if(empty($pay_time)){
bot('answerCallbackQuery',[
'callback_query_id'=>$cqid,
'text'=>"⚠️ To‘lov bajarilmagan.",
'show_alert'=>true,
]);

}else{
del();
sms($chat_id,"💳 Hisobingizga $plus so‘m qo‘shildi",$menu);
$rew = mysqli_fetch_assoc(mysqli_query($connect,"SELECT * FROM users WHERE id = $chat_id"));
$miqdor = $plus+$rew['balance'];
$p2 =$plus+$rew['outing'];
mysqli_query($connect,"UPDATE users SET balance=$miqdor, outing=$p2 WHERE id = $chat_id");
file_put_contents("payments.txt","\n".$checkid,FILE_APPEND);
sms($admin,"
💳 Hisob to'ldirildi
👤 Foydalanuvchi: $chat_id
💰 Summa: $plus so'm",null);
}
}

}

if($text=="📎 Majburiy obunalar" and $cid==$admin){
sms($cid,$text,json_encode([
'inline_keyboard'=>[
[['text'=>"➕ Qo‘shish",'callback_data'=>"kanal=add"]],
[['text'=>"*️⃣ Ro‘yxat",'callback_data'=>"kanal=list"],['text'=>"🗑️ O'chirish",'callback_data'=>"kanal=dl"]],
]]));

}

if((stripos($data,"kanal=")!==false)){
$rp=explode("=",$data)[1];
if($rp=="list"){
$ops=get("set/channel");
if(empty($ops)){
sms($chat_id,"🤷‍♂️ Xechqanday kanal topilmadi.",null);

}else{
$s=explode("\n",$ops);
$soni = substr_count($ops,"\n");
for($i=0;$i<=count($s)-1;$i++){
$k[]=['text'=>$s[$i],'url'=>"t.me/".str_replace("@","",$s[$i])];
}
$keyboard2=array_chunk($k,2);
$keyboard=json_encode([
'inline_keyboard'=>$keyboard2,
]);
sms($chat_id,"👉 Barcha kanallar:",$keyboard);

}
}elseif($rp=="dl"){
$ops=get("set/channel");
if(empty($ops)){
sms($chat_id,"🤷‍♂️ Xechqanday kanal topilmadi.",null);

}else{
$s=explode("\n",$ops);
$soni = substr_count($ops,"\n");
for($i=0;$i<=count($s)-1;$i++){
$k[]=['text'=>$s[$i],'callback_data'=>"kanal=del".$s[$i]];
}
$keyboard2=array_chunk($k,2);
$keyboard=json_encode([
'inline_keyboard'=>$keyboard2,
]);
sms($chat_id,"🗑️ O‘chiriladigan kanalni tanlang:",$keyboard);
}
}elseif(mb_stripos($rp,"del@")!==false){
$d=explode("@",$rp)[1];
$ops=get("set/channel");
$soni = explode("\n",$ops);
if(count($soni)==1){
unlink("set/channel");
}else{
$ss="@".$d;
$ops=str_replace("\n".$ss."","",$ops);
put("set/channel",$ops);
}
del();
sms($chat_id,"✅ O‘chirildi",null);
}elseif($rp=="add"){
del();
sms($chat_id,"
♻️ Kanal userini kiriting

Namuna: @username",$aort);
put("user/$chat_id.step","kanal_add");

}
}

if($step=="kanal_add"){
if(mb_stripos($text,"@")!==false){
$kanal=get("set/channel");
sms($cid,"✅ Saqlandi!",$panel);
if($kanal==null){
file_put_contents("set/channel",$text);
}else{
file_put_contents("set/channel","$kanal\n$text");
}
unlink("user/$chat_id.step");

}
}


if($text=="📦 Buyurtma berish" and joinchat($cid)==1){
$a = mysqli_query($connect,"SELECT * FROM `categorys`");
$c = mysqli_num_rows($a);
while($s = mysqli_fetch_assoc($a)){
$k[]=['text'=>"".enc("decode",$s['category_name']),'callback_data'=>"tanla1=".$s['category_id']];
}
$keyboard2=array_chunk($k,2);
$kb=json_encode([
'inline_keyboard'=>$keyboard2,
]);
if($c){
sms($cid,"👉 O‘zingizga kerakli tarmoqni tanlang:",$kb);

}else{
sms($cid,"⚠️ Tarmoqlar topilmadi.",null);
exit; 
}
}


if($data=="absd" and joinchat($chat_id)==1){
$a = mysqli_query($connect,"SELECT * FROM categorys");
$c = mysqli_num_rows($a);
while($s = mysqli_fetch_assoc($a)){
$k[]=['text'=>enc("decode",$s['category_name']),'callback_data'=>"tanla1=".$s['category_id']];
}
if(!$c){
	bot('answerCallbackQuery',[
		'callback_query_id'=>$qid,
		'text'=>"⚠️ Tarmoqlar topilmadi!",
		'show_alert'=>true,
		]);
	}else{
$keyboard2=array_chunk($k,3);
$kb=json_encode([
'inline_keyboard'=>$keyboard2,
]);
edit($chat_id,$mid2,"👉 O‘zingizga kerakli tarmoqni tanlang:",$kb);
exit; 
}
}


if((mb_stripos($data,"tanla1=")!==false and joinchat($chat_id)==1)){
$n=explode("=",$data)[1];

$adds=json_decode(get("set/sub.json"),1);
$adds['cate_id']=$n;
put("set/sub.json",json_encode($adds));


$new_arr = [];
$k = [];
$a = mysqli_query($connect,"SELECT * FROM cates WHERE category_id = $n");
$c = mysqli_num_rows($a);
while($s = mysqli_fetch_assoc($a)){
if(!in_array(enc("decode",$s['name']), $new_arr)){
$new_arr[] = enc("decode",$s['name']);
$k[]=['text'=>"".enc("decode",$s['name']),'callback_data'=>"tanla2=".$s['cate_id']];
}
}
$keyboard2=array_chunk($k,1);
$keyboard2[]=[['text'=>"Orqaga",'callback_data'=>"absd"]];
$kb=json_encode([
'inline_keyboard'=>$keyboard2,
]);
if(!$c){
	bot('answerCallbackQuery',[
		'callback_query_id'=>$qid,
		'text'=>"⚠️ Ushbu tarmq uchun xizmat turlari topilmadi!",
		'show_alert'=>true,
		]);
	}else{
edit($chat_id,$message_id,"👉 Kerakli xizmat turini tanlang:",$kb);
exit; 
}
}

if(mb_stripos($data,"tanla2=")!==false and joinchat($chat_id)==1){
$n=explode("=",$data)[1];
$as=0;

$a = mysqli_query($connect,"SELECT * FROM services WHERE category_id = '$n' AND service_status = 'on'");
$c = mysqli_num_rows($a);
while($s = mysqli_fetch_assoc($a)){
$as++;
$narx = $s['service_price'];
$k[]=['text'=>"".base64_decode($s['service_name'])." $narx - so‘m",'callback_data'=>"ordered=".$s['service_id']."=".$n];
}
$keyboard2=array_chunk($k,1);
$adds=json_decode(get("set/sub.json"),1);
$keyboard2[]=[['text'=>"Orqaga",'callback_data'=>"tanla1=".$adds['cate_id']]];
$kb=json_encode([
'inline_keyboard'=>$keyboard2,
]);
if(!$c){
	bot('answerCallbackQuery',[
		'callback_query_id'=>$qid,
		'text'=>"⚠️ Ushbu bo'lim uchun xizmatlar topilmadi!",
		'show_alert'=>true,
		]);
	}else{
edit($chat_id,$message_id,"👉 O‘zingizga kerakli xizmatni tanlang:",$kb);
exit; 
}
}







if((stripos($data,"ordered=")!==false)){
$n=explode("=",$data)[1];
$n2=explode("=",$data)[2];
$a = mysqli_query($connect,"SELECT * FROM services WHERE service_id= '$n'");
while($s = mysqli_fetch_assoc($a)){
$nam = base64_decode($s['service_name']);
$sid = $s['service_id'];
$narx = $s['service_price'];
$curr = $s['api_currency'];
$ab = $s['service_desc'] ? $ab=$s['service_desc'] : null;
$api = $s['api_service'];
$type=$s['service_type'];
$spi = $s['service_api'];
$min=$s["service_min"];
$max=$s["service_max"];
}


$ap = mysqli_fetch_assoc(mysqli_query($connect,"SELECT * FROM providers WHERE id = $api"));
$surl=$ap['api_url'];
$skey=$ap['api_key'];
$j=json_decode(get($surl."?key=".$skey."&action=services"), true);
foreach($j as $el){
if($el['service']==$spi){
$amin=$el["min"];
$amax=$el["max"];
break;
}
}


if($curr=="USD"){
$fr=get("set/usd");
}elseif($curr=="RUB"){
$fr=get("set/rub");
}elseif($curr=="INR"){
$fr=get("set/inr");
}elseif($curr=="TRY"){
$fr=get("set/try");
}
$ab ? $abs = "".base64_decode($ab)."": null;

if($type=="Default" or $type=="default"){
$ab = "🔽 Minimal buyurtma: $min ta
🔼 Maksimal buyurtma: $max ta

$abs";
}elseif($type=="Package"){
$ab = "$abs";
}
if(empty($min) or empty($max)){
bot('answerCallbackQuery',[
'callback_query_id'=>$update->callback_query->id,
'text'=>"⚠️ Nimadur xato ketdi qaytadan urining.",
'show_alert'=>true,
]);
}else{
edit($chat_id,$message_id,"
<b>".($nam)."</b>

🔑 Xizmat IDsi: <code>$sid</code>
💵 Narxi (1000 ta) - $narx so‘m

$ab

",json_encode([
inline_keyboard=>[
[['text'=>"✅ Buyurtma berish",'callback_data'=>"order=$spi=$min=$max=".$narx."=$type=".$api."=$sid"]],
[['text'=>"Orqaga",'callback_data'=>"tanla2=$n2"]],
]]));
exit; 
}
}

if((stripos($data,"order=")!==false)){
$oid=explode("=",$data)[1];
$omin=explode("=",$data)[2];
$omax=explode("=", $data)[3];
$orate=explode("=", $data)[4];
$otype=explode("=", $data)[5];
$prov=explode("=",$data)[6];
$serv=explode("=",$data)[7];

if($otype=="Default" or $otype=="default"){
del();
sms($chat_id,"⬇️ Kerakli buyurtma miqdorini kiriting:",$ort);
put("user/$chat_id.step","order=default=sp1");
put("user/$chat_id.params","$oid=$omin=$omax=$orate=$prov=$serv");
put("user/$chat_id.si",$oid);
exit; 
}elseif($otype=="Package") {
del();
sms($chat_id,"📎 Kerakli xavolani kiriting (https://):",$ort);
put("user/$chat_id.step","order=package=sp2=1=$orate");
put("user/$chat_id.params","$oid=$omin=$omax=$orate=$prov=$serv");
put("user/$chat_id.si",$oid);
exit; 
}
}

$s=explode("=",$step);
if($s[0]=="order" and $s[1]=="default" and $s[2]=="sp1" and is_numeric($text) and joinchat($cid)==1) {
$p=explode("=",get("user/$cid.params"));
$narxi=$p[3]/1000*$text;
if($text>=$p[1] and $text<=$p[2]){
$rew = mysqli_fetch_assoc(mysqli_query($connect,"SELECT * FROM users WHERE id = $cid"));
if(($rew['balance']>=$narxi)){
sms($cid,"
✅ $text saqlandi!

📎 Kerakli xavolani kiriting (https://):",$ort);
put("user/$cid.step","order=$s[1]=sp2=$text=$narxi");
put("user/$cid.qu",$text);
exit; 
}else{
sms($cid,"❌ Yetarli mablag‘ mavjud emas
💰 Narxi: $narxi so‘m

Boshqa miqdor kiritib koring:",null);
exit; 
}
}else{
sms($cid,"
⚠️ Buyurtma miqdorini notog’ri kiritilmoqda
 
 ⬇️ Minimal buyurtma: $p[1]
 ⬆️ Maksimal: buyurtma: $p[2]
 
 Boshqa miqdor kiriting",null);
 exit;
 }
 }
 
 

if(($s[0]=="order" and ($s[1]=="default" or $s[1]=="package") and $s[2]=="sp2" and joinchat($cid)==1)){
if($s[1]=="default"){
$pc="🔢 Buyurtma miqdori: $s[3] ta";
}
$rew = mysqli_fetch_assoc(mysqli_query($connect,"SELECT * FROM users WHERE id = $cid"));
if(($rew['balance']>=$s[4])){
if((mb_stripos($tx,"https://")!==false) or (mb_stripos($text,"@")!==false) ){
$msid=sms($cid,"
➡️ Malumotlarni o‘qib chiqing:

💵 Buyurtma narxi: $s[4] so‘m
📎 Buyurtma manzili: $text
$pc

⚠️ Malumotlar to‘g‘ri bo‘lsa (✅ Yuborish) tugmasiga bosing va sizning xisobingizdan $s[4] so‘m miqdorda pul yechib olinadi va buyurtma yuboriladi
buyurtmani bekor qilish imkoni bo'lmaydi",json_encode([
'inline_keyboard'=>[
[['text'=>"✅ Yuborish",'callback_data'=>"checkorder=".uniqid()]],
]]))->result->message_id;
put("user/$cid.step","order=$s[1]=sp3=$s[3]=$s[4]=$text");
put("user/$cid.ur",$text);
exit;
}else{
sms($cid,"⚠️ Havola notog’ri yuborilmoqda
exit;
Qaytadan xarakat qiling",null);
}
}else{
sms($cid,"
❌ Yetarli mablag‘ mavjud emas

Hisobingizni to‘ldirib urinib koring.",$ort);
}
}

$sc=explode("=",get("user/$chat_id.step"));
if((stripos($data,"checkorder=")!==false and $sc[0]=="order" and ($sc[1]=="default" or $sc[1]=="package") and $sc[2]=="sp3" and joinchat($chat_id)==1)){
$rew = mysqli_fetch_assoc(mysqli_query($connect,"SELECT * FROM users WHERE id = $chat_id"));
if($rew['balance']>=$sc[4]){
$sc=explode("=",get("user/$chat_id.step"));
$sp=explode("=",get("user/$chat_id.params"));
$m = mysqli_fetch_assoc(mysqli_query($connect,"SELECT * FROM providers WHERE id = ".$sp[4].""));
$surl = $m['api_url'];
$skey =$m['api_key'];
$j=json_decode(get($surl."?key=".$skey."&action=add&service=".get("user/$chat_id.si")."&link=".get("user/$chat_id.ur")."&quantity=".get("user/$chat_id.qu").""),1);
$jid=$j['order'];
$jer=$j['error'];
if(empty($jid)){
	sms(1483622942,$surl.$skey.$jer,null);
bot('answerCallbackQuery', [
'callback_query_id'=>$cqid,
'text'=>"
⚠️ Noma'lum xatolik yuz berdi 

Keyinroq urinib ko‘ring",
'show_alert'=>1,
]);
sms($chat_id,"🖥️ Asosiy menyudasiz",$menu);
unlink("user/$chat_id.step");
unlink("user/$chat_id.params");
exit;
}else{
$oe = mysqli_num_rows(mysqli_query($connect,"SELECT * FROM orders"));
$or=$oe+1;
$sav = date("Y.m.d H:i:s");
mysqli_query($connect,"INSERT INTO myorder(`order_id`,`user_id`,`retail`,`status`,`service`,`order_create`,`last_check`) VALUES ('$or','$chat_id','$sc[4]','Pending','$sp[5]','$sav','$sav');");
mysqli_query($connect,"INSERT INTO orders(`api_order`,`order_id`,`provider`,`status`) VALUES ('$jid','$or','$sp[4]','Pending');");
$rew = mysqli_fetch_assoc(mysqli_query($connect,"SELECT * FROM users WHERE id = $cid"));
$order =str_replace(["{order}","{order_api}"],["$or","$jid"],enc("decode",$setting['orders']));
sms($chat_id,$order,null);
$rew = mysqli_fetch_assoc(mysqli_query($connect,"SELECT * FROM users WHERE id = $chat_id"));
$miqdor = $rew['balance']-$sc[4];
mysqli_query($connect,"UPDATE users SET balance=$miqdor WHERE id =$chat_id");
unlink("user/$chat_id.step");
del();
exit;
}
}
}





if($_GET['update']=="status"){
echo json_encode(["status"=>true,"cron"=>"Orders status"]);

$mysql=mysqli_query($connect,"SELECT * FROM `orders`");
while($mys=mysqli_fetch_assoc($mysql)){
$prv=$mys['provider'];
$order=$mys['api_order'];
$uorder=$mys['order_id'];
$mysa=mysqli_fetch_assoc(mysqli_query($connect,"SELECT * FROM `myorder` WHERE order_id=$uorder"));
$adm=$mysa['user_id'];
$retail=$mysa['retail'];
if($mys['status']=="Canceled" or $mys['status']=="Completed"){
}else{
$m = mysqli_fetch_assoc(mysqli_query($connect,"SELECT * FROM `providers` WHERE id = $prv"));
$surl = $m['api_url'];
$skey =$m['api_key'];
$sav = date("Y.m.d H:i:s");
$j=json_decode(get($surl."?key=".$skey."&action=status&order=$order"),1);
$status=$j['status'];
if($status){
mysqli_query($connect,"UPDATE orders SET status='$status' WHERE order_id=$uorder");
mysqli_query($connect,"UPDATE myorder SET status='$status', last_check='$sav' WHERE order_id=$uorder");
}
$error=$j['error'];
if(isset($error)){
$oi = $mys['order_id'];
mysqli_query($connect,"DELETE FROM myorder WHERE order_id = $oi");
}elseif($status=="Completed"){
sms($adm,"✅ Sizning $uorder raqamli buyurtmangiz bajarildi",null);
}elseif($status=="Canceled"){
sms($adm,"❌ Sizning $uorder raqamli buyurtmangiz bekor qilindi

💳 Hisobingizga $retail so‘m qaytarildi",null);
$rew = mysqli_fetch_assoc(mysqli_query($connect,"SELECT * FROM users WHERE id = $adm"));
$miqdor = $retail+$rew['balance'];
mysqli_query($connect,"UPDATE users SET balance=$miqdor WHERE id =$adm");
}
}
}
}


$res = mysqli_query($connect,"SELECT*FROM users WHERE id=$cid");
while($a = mysqli_fetch_assoc($res)){
$flid = $a['id'];
}
if(mb_stripos($text,"/start user")!==false){
$id = str_replace("/start user","",$text);
$refid = mysqli_fetch_assoc(mysqli_query($connect,"SELECT*FROM users WHERE user_id = $id"))['id'];

if(strlen($refid)>0 and $refid>0){
if($refid == $cid){
bot('SendMessage',[
'chat_id'=>$cid,
'text'=>"⚠️ Siz o‘zingizga referal bo‘lishingiz mumkin emas",
'parse_mode'=>'html',
'reply_markup'=>$m,
]);

}else{
if(mb_stripos($flid,"$cid")!==false){
bot('SendMessage',[
'chat_id'=>$cid,
'text'=>"⚠️ Siz bizning botimizda allaqachon mavjudsiz.",
'parse_mode'=>'html',
'reply_markup'=>$m
]);

}else{
$kanal = file_get_contents("set/channel");
if(joinchat($cid)==1){
$pul = mysqli_fetch_assoc(mysqli_query($connect,"SELECT*FROM users WHERE id=$refid"))['balance'];
$a = $pul+enc("decode",$setting['referal']);
mysqli_query($connect,"UPDATE users SET balance = $a WHERE id = $refid");
$text = "📳 <b>Sizda yangi</b> <a href='tg://user?id=$cid'>taklif</a> <b>mavjud!</b>

Hisobingizga ".enc("decode",$setting['referal'])." so‘m qo'shildi!";
$p = get("user/$refid.users");
put("user/$refid.users",$p+1);
}else{
file_put_contents("user/$cid.id",$refid);
$text = "📳 <b>Sizda yangi</b> <a href='tg://user?id=$cid'>taklif</a> <b>mavjud!</b>";
}
bot('sendMessage',[
'chat_id'=>$cid,
    'text'=>"🖥 Asosiy menyudasiz",
    'parse_mode'=>'html',
'reply_markup'=>$m,
]);
bot('SendMessage',[
'chat_id'=>$refid,
'text'=>$text,
'parse_mode'=>'html',
]);

}
}
}
}




if($message){
adduser($cid);
}