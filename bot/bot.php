<?php


	
	
session_start();
date_default_timezone_set("Asia/Tashkent");
$time = date('H:i');
ob_start();
define('API_KEY',"7832943702:AAGQ0xWS5EONaFdFJ89DR6UdLHiPjBGN5YM");
$admin="2114098498";
$bot=bot(getMe)->result->username;


$a=file_get_contents("https://api.telegram.org/bot".API_KEY."/setwebhook?url=".$_SERVER['SERVER_NAME']."".$_SERVER['SCRIPT_NAME']);
echo $a;

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
$stat= mysqli_fetch_assoc(mysqli_query($connect,"SELECT*FROM settings WHERE id= 1"))['users'];
$new = $stat + 1;
mysqli_query($connect,"UPDATE settings SET users = $new WHERE id = 1");
mysqli_query($connect,"INSERT INTO users(`user_id`,`id`,`status`,`balance`,`outing`,`api_key`,`api_status`,`referal`,`refnum`,`ban`) VALUES ('$new','$cid','active','0','0','$key','on','$referal','0','0');");
}
}


/*if(isset($message)){
$get = bot('GetChatMember',[
'chat_id'=>"@Qoracoders_uzb",
'user_id'=>$cid,
]);
$result = $get->result->status;
if($result == "member" or $result == "administrator" or $result == "creator"){
	}else{
		bot('sendMessage',[
		'chat_id'=>$cid,
		'text'=>"🔒 @Qoracoders_uzb <b>ga obuna bo'lmasangiz botdan to'liq foydalana olmaysiz!</b>",
		'parse_mode'=>'html',
		]);
		return false;
	}
}*/


if($botdel){
if($userstatus == "kicked"){
$sql = "UPDATE `users` SET `status` = 'deactive' WHERE `id` = '$botdel_id'";
$result = mysqli_query($connect, $sql);
}
}

if(isset($update)) {
$result = mysqli_query($connect,"SELECT * FROM users WHERE id = $cid$chat_id");
$rew = mysqli_fetch_assoc($result);
}

if(isset($update)) {
$result = mysqli_query($connect,"SELECT * FROM users WHERE id = $cid$chat_id");
$rew = mysqli_fetch_assoc($result);
if($rew['ban']=="1"){
if($cid == $admin){
}else{
exit(); 
}}}

if(isset($update)) {
    $bot_status = @file_get_contents("status.txt");
    $check_id = $cid ? $cid : $chat_id;
    if($bot_status == "off" && $check_id != $admin) {
        if ($text || $data) {
            bot('sendMessage', [
                'chat_id' => $check_id,
                'text' => "<b>🛠 Uzr, botda texnik xizmat ishlari olib borilmoqda. Iltimos birozdan so'ng qayta urinib ko'ring.</b>",
                'parse_mode' => 'html'
            ]);
        }
        exit();
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
[['text'=>"⏪ Orqaga"]],
]
]);

$aort=json_encode([
'resize_keyboard'=>true,
'keyboard'=>[
[['text'=>"🗄️ Boshqaruv"]],
]
]);

if($callback == "yoqot"){
bot('deleteMessage',[
'chat_id'=>$cid,
'message_id'=>$mid,
]);
bot('sendMessage',[
'chat_id'=>$cid,
'text'=>"🖥️ Asosiy menyudasiz",
'parse_mode'=>'html',
'reply_markup'=>$m,
]);
bot('deleteMessage',[
'chat_id'=>$cid,
'message_id'=>$del,
]);
exit();
}

$panel=json_encode([
'resize_keyboard'=>true,
'keyboard'=>[
[['text'=>"⚙️ Asosiy sozlamalar"]],
[['text'=>"🔔 Xabar yuborish"],['text'=>"📊 Statistika"]],
[['text'=>"👤 Foydalanuvchini boshqarish"]],
[['text'=>"🎁 Promokod yaratish"],['text'=>"📋 Promokodlar ro'yxati"]],
[['text'=>"💰 Hisob to'ldirish (ID)"],['text'=>"⚙️ Botni o'chirish/yoqish"]],
[['text'=>"🇺🇿 Valyuta kursi"],['text'=>"⏰ Cron sozlamasi"]],
[['text'=>"⏪ Orqaga"]],
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
[['text'=>"🏆 TOP 100 Balans",'callback_data'=>"preyting"],['text'=>"🏆 Top 100 Referal",'callback_data'=>"treyting"]],
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

if($data =="treyting"){
	$res = mysqli_query($connect,"SELECT * FROM `users`ORDER BY refnum DESC LIMIT 100");
while($roww = mysqli_fetch_assoc($res)){
$id = $roww['id'];
$pul = $roww['balance'];
$member = $roww['refnum'];
$stat = mysqli_num_rows($res);
$top .= "<a href='tg://user?id=$id'>$id</a>  -  <i>$member</i> odam\n";
}
$ids = explode("\n","\n$top");
$soi = substr_count($top,"\n");
$soni = $soi;
foreach($ids as  $id){
$keyboards = [];
$text = "";
for ($i = 1; $i <= $soni; $i++) {
$title = str_replace("\n","",$ids[$i]);
$text .= "<b>$i)</b> ".$ids[$i]."\n";
}
bot('deleteMessage',[
'chat_id'=>$cid2,
'message_id'=>$mid2,
]);
bot('sendMessage',[
'chat_id'=>$cid2,
'text'=>"<b>🏆 TOP-100 referal reytingi:

$text</b>",
'parse_mode'=>'html',
'reply_markup'=>json_encode([
	'inline_keyboard'=>[
[['text'=>"▶️ Orqaga",'callback_data'=>"statis"]]
]
])
]);
exit();
}
}

if($data =="preyting"){
	$res = mysqli_query($connect,"SELECT * FROM `users`ORDER BY balance DESC LIMIT 100");
while($roww = mysqli_fetch_assoc($res)){
$id = $roww['id'];
$pul = $roww['balance'];
$member = $roww['refnum'];
$stat = mysqli_num_rows($res);
$top .= "<a href='tg://user?id=$id'>$id</a> - <i>$pul</i> so'm\n";
}
$ids = explode("\n","\n$top");
$soi = substr_count($top,"\n");
$soni = $soi;
foreach($ids as  $id){
$keyboards = [];
$text = "";
for ($i = 1; $i <= $soni; $i++) {
$title = str_replace("\n","",$ids[$i]);
$text .= "<b>$i)</b> ".$ids[$i]." \n";
}
bot('deleteMessage',[
'chat_id'=>$cid2,
'message_id'=>$mid2,
]);
bot('sendMessage',[
'chat_id'=>$cid2,
'text'=>"<b>TOP-50 balanslar reytingi

$text</b>",
'parse_mode'=>'html',
'reply_markup'=>json_encode([
	'inline_keyboard'=>[
[['text'=>"▶️ Orqaga",'callback_data'=>"statis"]]
]
])
]);
exit();
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
[['text'=>"🛍 Buyurtma berish"]],
[['text'=>"🔐 Mening hisobim"],['text'=>"📱 Hisobni to'ldirish"]],
[['text'=>"🛒 Buyurtma xolati"],['text'=>"☎️ Administrator"]],
[['text'=>"🤝 Hamkorlik dasturi"]],
]]);
$panel2=json_encode([
'resize_keyboard'=>true,
'keyboard'=>[
[['text'=>"💵 Kursni o‘rnatish"],['text'=>"⚖️ Foizni o‘rnatish"]],
[['text'=>"📊 Buyurtmani boshqarish"]],
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
[['text'=>"⏪ Orqaga",callback_data=>"birlamch=exit"]],
]]));
}elseif($res=="tugma"){
edit($chat_id,$message_id,"👉 Sozlama turini tanlang:",json_encode([
inline_keyboard=>[
[['text'=>"📑 Nomini o‘zgartirish",callback_data=>"birlamch=editT"]],
[['text'=>"⏪ Orqaga",callback_data=>"birlamch=exit"]],
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
[['text'=>"⏪ Orqaga",callback_data=>"birlamch=matn"]],
]]));
}elseif($res=="ref"){
edit($chat_id,$mid2,"⚙️ Sozlama turini tanlang:",json_encode([
inline_keyboard=>[
[['text'=>"🎁 Referal tugma xolati",'callback_data'=>"referr=xolati"]],
[['text'=>"🎁 Bonusni o‘zgartirish",'callback_data'=>"referr=edit"]],
[['text'=>"⏪ Orqaga",callback_data=>"birlamch=exit"]],
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
$keyboard2[] = [['text'=>"⏪ Orqaga",callback_data=>"birlamch=exit"]];
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
[['text'=>"⏪ Orqaga",callback_data=>"birlamch=exit"]],
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
[['text'=>"⏪ Orqaga",callback_data=>"birlamch=exit"]],
]));
}
}

if(mb_stripos($data,"autopay=")!==false){
$ex = explode("=",$data)[1];
if($ex=="payme"){
if(empty($setting['payme_id']) or $setting['payme_id']=="null"){
edit($cid2,$mid2,"👉 Kerakli sozlamani tanlang:",keyboard([
[['text'=>"➕ Karta IDsini qo‘shish",'callback_data'=>"autopay=payme_id"]],
[['text'=>"⏪ Orqaga",callback_data=>"birlamch=exit"]],
]));
}else{
edit($cid2,$mid2,"👉 Kerakli sozlamani tanlang

🆔 Hozirgi karta IDsi: ".$setting['payme_id']."",keyboard([
[['text'=>"➕ Karta IDsini o‘zgartirish",'callback_data'=>"autopay=payme_id"]],
[['text'=>"⏪ Orqaga",callback_data=>"birlamch=exit"]],
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
[['text'=>"⏪ Orqaga",callback_data=>"birlamch=exit"]],
]]);
}elseif($m == "off"){
$tx = "❌";
$kb = json_encode([
inline_keyboard=>[
[['text'=>"«✅»",'callback_data'=>"referr=ok=on"]],
[['text'=>"⏪ Orqaga",callback_data=>"birlamch=exit"]],
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
[['text'=>"⏪ Orqaga",callback_data=>"birlamch=exit"]],
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


if($text=="📊 Buyurtmani boshqarish" and joinchat($cid)==1) {
	if($cid == $admin){
$resi = mysqli_query($connect, "SELECT * FROM orders");
$stati = mysqli_num_rows($resi);
sms($cid,"
🔢 Barcha buyurtmalar: $stati ta

➡️ Buyurtma IDsini kiriting:",$aort);
put("user/$cid.step",orders2);
exit;
}}


if($step=="orders2" and $cid==$admin and is_numeric($text)==1){
$amyorder= mysqli_query($connect, "SELECT * FROM myorder WHERE order_id = '$text'");
$myorder = mysqli_fetch_assoc($amyorder);
$aorders = mysqli_query($connect, "SELECT * FROM orders WHERE order_id = '$text'");
$orders = mysqli_fetch_assoc($aorders);
if(!$myorder){
sms($cid,"❌ Buyurtma topilmadi.",$aort);
}else{
$providers= mysqli_fetch_assoc(mysqli_query($connect,"SELECT * FROM `providers` WHERE id = ".$orders['provider']." "));
$apiurl = $providers['api_url'];
$apikey =$providers['api_key'];
$api = json_decode(get("$apiurl?key=$apikey&action=status&order=".$orders['api_order'].""), 1);
$server=str_replace(["/api/adapter/default/index","/api/v1","/api/v2","https://"],["","","",""],$providers['api_url']);
if(($myorder['status']=="Pending") and ($orders['status']=="Pending")) {
$orderstatus = "Bajarilmoqda.";
}elseif(($myorder['status']=="Completed") and ($orders['status']=="Completed")){
$orderstatus = "Bajarilgan.";
}elseif(($myorder['status']=="Canceled") and ($orders['status']=="Canceled")) {
$orderstatus = "Bekor qilingan.";
}elseif(($myorder['status']=="In progress") and ($orders['status']=="In progress")){
$orderstatus = "Jarayonda.";
}elseif(($myorder['status']=="Partial") and ($orders['status']=="Partial")){
$orderstatus = "Qisman bajarilgan.";
}elseif($myorder['status']=="Processing"){
$orderstatus = "Qayta ishlamoqda.";
}
sms($cid,"
<b>Server Orders</b>
<b>*️⃣ Server:</b> $server
<b>🔢 Server Buyurtma IDsi:</b> <code>".$orders['api_order']."</code>
<b>☑️ Server Buyurtma xolati:</b> <code>".$api['status']."</code>

<b>Orders</b>
<b>*️⃣ Server:</b> $server
<b>🔢 Server Buyurtma IDsi:</b> <code>".$orders['api_order']."</code>
<b>☑️ Server Buyurtma xolati:</b> $orderstatus

<b>My Orders</b>
<b>🛍 Buyurtma IDsi:</b> <code>$text</code>
<b>♻️ Buyurtma xolati:</b> $orderstatus
<b>⏰ Buyurtma sanasi:</b> ".$myorder['order_create']."
<b>💰 Buyurtma narxi:</b> ".$myorder['retail']." so'm
<b>👤 Buyurtmachi:</b> <a href='tg://user?id=".$myorder['user_id']."'>".$myorder['user_id']."</a>",json_encode([
	'inline_keyboard'=>[
	[['text'=>"✅ Bajarilgan holatga o'tkazish",'callback_data'=>"status=".$myorder['user_id']."=Completed=$text=".$myorder['retail'].""]],
	[['text'=>"❌ Bekor qilingan holatga o'tkazish",'callback_data'=>"status=".$myorder['user_id']."=Canceled=$text=".$myorder['retail'].""]],
]
	]));
unlink("user/$cid.step");
}
exit;
}

if((stripos($data,"status=")!==false)){
	$user_id = explode("=",$data)[1];
	$order_status = explode("=",$data)[2];
	$order_id = explode("=",$data)[3];
	$order_price = explode("=",$data)[4];
	$sav = date("Y.m.d H:i:s");
	if($order_status=="Completed") {
        del();
	mysqli_query($connect,"UPDATE orders SET status = '$order_status' WHERE order_id = $order_id");
	mysqli_query($connect,"UPDATE myorder SET status='$order_status', last_check='$sav' WHERE order_id=$order_id");
        sms($cid2,"✅ $order_id raqamli buyurtma bajarilgan holatiga o'tkazildi!",null);
	sms($user_id,"✅ Sizning $order_id raqamli buyurtmangiz bajarildi!",null);
	}elseif($order_status=="Canceled"){
        del();
	mysqli_query($connect,"UPDATE orders SET status = '$order_status' WHERE order_id = $order_id");
	mysqli_query($connect,"UPDATE myorder SET status='$order_status', last_check='$sav' WHERE order_id=$order_id");
        sms($cid2,"❌ $order_id raqamli buyurtma bekor qilinga holatiga o'tkazildi!",null);
	sms($user_id,"❌ Sizning $order_id raqamli buyurtmangiz bekor qilindi
	💳 Hisobingizga $order_price so‘m qaytarildi",null);
	$balans= mysqli_fetch_assoc(mysqli_query($connect,"SELECT * FROM users WHERE id = $user_id"));
	$miqdor = $order_price + $balans['balance'];
	mysqli_query($connect,"UPDATE users SET balance=$miqdor WHERE id =$user_id");
	
	}
	
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
[['text'=>"⏪ Orqaga", callback_data=>"api1"]],
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
$keyboard2[]=[['text'=>"⏪ Orqaga",'callback_data'=>"api1"]];
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
$keyboard2[]=[['text'=>"⏪ Orqaga",'callback_data'=>"api1"]];
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

Namuna:</b> <pre>https://gramapi.uz/api/v2</pre>",
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
$keyboard2[]=[['text'=>"⏪ Orqaga",'callback_data'=>"api1"]];
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


if($text == "🤝 Hamkorlik dasturi" and joinchat($cid)==1){
$result = mysqli_query($connect,"SELECT * FROM `users` WHERE id = '$cid'");
$rew = mysqli_fetch_assoc($result);
sms($cid,"

<b>⤵️ Quyidagilardan birini tanlang:</b>

",keyboard([
[['text'=>"🔑 API dan foydalanish",'callback_data'=>"api_key"]],
]));
}

if($data == "hamkorlik_dastur" and joinchat($cid2)==1){
del();
$result = mysqli_query($connect,"SELECT * FROM `users` WHERE id = '$cid2'");
$rew = mysqli_fetch_assoc($result);
sms($cid2,"

<b>⤵️ Quyidagilardan birini tanlang:</b>

",keyboard([
[['text'=>"🔑 API dan foydalanish",'callback_data'=>"api_key"]],
]));
}

if($data == "smmbot"){
del();
$result = mysqli_query($connect,"SELECT * FROM `users` WHERE id = '$cid2'");
$rew = mysqli_fetch_assoc($result);
sms($cid2,"

<b>🤖 Sizga @$bot'dek bot kerak bo'lsa @gramapihelp'ga murojaat qiling!</b>

",keyboard([
[['text'=>"⏪ Orqaga",'callback_data'=>"hamkorlik_dastur"]],
]));
}

if($data == "api_key"){
del();
$result = mysqli_query($connect,"SELECT * FROM `users` WHERE id = '$cid2'");
$rew = mysqli_fetch_assoc($result);
sms($cid2,"

<b>🔑 Sizning API kalitingiz:</b>
<code>".$rew['api_key']."</code>

<b>💵 API balansingiz:</b> ".$rew['balance']." so‘m

",keyboard([
[['text'=>"🔄 APIni yangilash",'callback_data'=>"apidetail=newkey"]],
[['text'=>"*️⃣ Qo‘llanma",'callback_data'=>"apidetail=qoll"]],
[['text'=>"⏪ Orqaga",'callback_data'=>"hamkorlik_dastur"]],
]));
}


if($data == "api_nma"){
del();
$result = mysqli_query($connect,"SELECT * FROM `users` WHERE id = '$cid2'");
$rew = mysqli_fetch_assoc($result);
sms($cid2,"

<b>❓ APi nima?
Botimizdagi xizmatlarni siz ham o'z botingizga yoki saytingizga ulab ishlatishingiz mumkin. Buni ishlatish oson va qulay. Ushbu tizim xavfsizligi taminlanagan. Ko'proq imkoniyat bilan foydalaning. Sizni api kalitingiz agarda boshqalarga ma'lum bo'lsa yangisiga almashtiring. Albatta botga ulash uchun qo'llanma mavjud.</b>

",keyboard([
[['text'=>"⏪ Orqaga",'callback_data'=>"apidetail=qoll"]],
]));
}

if($data == "ogohlantirish"){
del();
$result = mysqli_query($connect,"SELECT * FROM `users` WHERE id = '$cid2'");
$rew = mysqli_fetch_assoc($result);
sms($cid2,"

<b>⚠️ Diqqat APi kalitni begona kishiga bermang. Sizning api kalitiz begonalar qo'liga tushsa tezda api kalitni yangilang. Agarda begonalar qo'liga tushgan apidan berilgan xizmat puli qaytarilmaydi. Bu holat ximoyalangan va sizdan boshqa kishisiz aytmasangiz apini bila olmaydi.</b>

",keyboard([
[['text'=>"⏪ Orqaga",'callback_data'=>"apidetail=qoll"]],
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
✅ Yangi API Kalit.

<code>".$rew['api_key']."</code>

💵 API balansingiz: <b>".$rew['balance']."</b> so‘m
</b>",
'reply_markup'=>keyboard([
[['text'=>"⏪ Orqaga",'callback_data'=>"api_key"]],
])
]);
}elseif($res == "qoll") {
	bot('editMessageText',[
'chat_id'=>$chat_id,
'parse_mode'=>"html",
'message_id'=>$message_id,
'text'=>"
<b>🌐 Saytimiz:</b> ".$_SERVER['HTTP_HOST']."

<b>🔗 API Havola:</b> <code>https://".$_SERVER['HTTP_HOST']."/api/v2</code>
",

'reply_markup'=>keyboard([
[['text'=>"❓ APi nima",'callback_data'=>"api_nma"]],
[['text'=>"⚠️ Ogohlantirish",'callback_data'=>"ogohlantirish"]],
[['text'=>"⏪ Orqaga",'callback_data'=>"api_key"]],
])
]);
}}

$menu_p=json_encode([
'resize_keyboard'=>true,
'keyboard'=>[
[['text'=>"🛍 Buyurtma berish"]],
[['text'=>"🔐 Mening hisobim"],['text'=>"📱 Hisobni to'ldirish"]],
[['text'=>"🛒 Buyurtma xolati"],['text'=>"☎️ Administrator"]],
[['text'=>"🤝 Hamkorlik dasturi"]],
[['text'=>"🗄️ Boshqaruv"]],
]
]);
if($cid==$admin or $chat_id==$admin){
$m=$menu_p;
}else{
$m=$menu;
}



if($text=="📱 Hisobni to'ldirish" and joinchat($cid)==1){
$ops=get("set/payments.txt");
$s=explode("\n",$ops);
$soni = substr_count($ops,"\n");
for($i=1;$i<=$soni;$i++){
$k[]=['text'=>$s[$i],'callback_data'=>"card=".$s[$i]];
}
$keyboard2=array_chunk($k,2);
$keyboard2[]=[['text'=>"☎️ Admin yordamida",url=>"tg://user?id=$admin"]];
$kb=json_encode([
'inline_keyboard'=>$keyboard2,
]);
if($ops){
sms($cid,"<b>💳 Quyidagi to'lov tizimlaridan birini tanlang:</b>",$kb);
}else{
sms($cid,"<b>⚠️ To'lov tizimlari qo'shilmagan</b>", null);
}}




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
$rew = mysqli_fetch_assoc(mysqli_query($connect,"SELECT * FROM users WHERE id = $text"));
if($rew){
$idi = $rew['id'];
file_put_contents("user/us.id",$idi);
if($rew['ban'] == "0"){
	$bans = "🔔 Banlash";
}
if($rew['ban'] == "1"){
	$bans = "🔕 Bandan olish";
}
else{
    $bans = "🔔 Banlash";
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
<b>Holat: ".$rew['status']."</b>
<b>Balans: ".$rew['balance']." so‘m</b>
<b>Takliflar: ".$rew['refnum']." ta</b>",
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
if($rew['ban'] == "1"){
mysqli_query($connect,"UPDATE users SET ban ='0' WHERE id =$saved");
bot('sendMessage',[
'chat_id'=>$chat_id,
'message_id'=>$message_id,
'text'=>"<b>Foydalanuvchi ($saved) bandan olindi!</b>",
'parse_mode'=>"html",
	'reply_markup'=>$panel,
]);
}else{
mysqli_query($connect,"UPDATE users SET ban='1' WHERE id =$saved");
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
bot('SendMessage',[
'chat_id'=>$admin,
'text'=>"<a href='tg://user?id=$usid'>$usid</a> -> <a href='tg://user?id=$chat_id'>$chat_id</a> ni taklif qildi",
'parse_mode'=>'html',
]);
$p = get("user/$usid.users");
put("user/$usid.users",$p+1);
unlink("user/$chat_id.id");
}
del();
sms($chat_id,"🖥️ Asosiy menyudasiz",$m);

}



if($text == "🛒 Buyurtma xolati" and joinchat($cid)==1){
$key = [];
$sql = "SELECT * FROM myorder WHERE user_id = '$cid' LIMIT 0,50";
$res = mysqli_query($connect,$sql);
while($row = mysqli_fetch_assoc($res)){
if($row['status'] == "Completed"){
$status = "✅";
}
if($row['status'] == "Pending" or $row['status'] == "In progress" or $row['status'] == "Partial"){
$status = "🔄";
}
if($row['status'] == "Processing"){
$status = "🔄";
}
if($row['status'] == "Canceled"){
$status = "⛔️";
}
$k[]=['text'=>"".$row['order_id']." $status",'callback_data'=>"orderstatus-".$row['order_id']];
}
$result = mysqli_query($connect, "SELECT * FROM myorder WHERE user_id = '$cid'");
$row = mysqli_fetch_assoc($result);
if($row){
$keyboard2=array_chunk($k,5);
$keyboard2[]=[['text'=>"⏪",'callback_data'=>"back2-0"],['text'=>"❌",'callback_data'=>"yoqot"],['text'=>"⏩",'callback_data'=>"next2-50"]];
$keyboard2[]=[['text'=>"🔎 ID yordamida qidirish",'callback_data'=>"myorders"]];
$keyboard=json_encode([
'inline_keyboard'=>$keyboard2,
]);
sms($cid,"<b>🛒 Buyurtma xolatiingiz:</b>",$keyboard);
exit();
}else{
sms($cid,"<b>⚠️ Sizda buyurtmalar yo'q</b>",null);
exit();
}
}


if(mb_stripos($data, "next2-")!==false){
$explode = explode("-",$data);
$explode = $explode[1];
$explode1 = $explode + 50;
$key = [];
$sql = "SELECT * FROM myorder WHERE user_id = '$cid2' LIMIT $explode,50";
$res = mysqli_query($connect,$sql);
while($row = mysqli_fetch_assoc($res)){
if($row['status'] == "Completed"){
$status = "✅";
}
if($row['status'] == "Pending" or $row['status'] == "In progress" or $row['status'] == "Partial"){
$status = "🔄";
}
if($row['status'] == "Processing"){
$status = "🔄";
}
if($row['status'] == "Canceled"){
$status = "⛔️";
}
$k[]=['text'=>"".$row['order_id']." $status",'callback_data'=>"orderstatus-".$row['order_id']];
}
$result = mysqli_query($connect, "SELECT * FROM myorder WHERE user_id = '$cid2' LIMIT $explode,50");
$row = mysqli_fetch_assoc($result);
if($row){
$keyboard2=array_chunk($k,5);
$keyboard2[]=[['text'=>"⏪",'callback_data'=>"back2-$explode"],['text'=>"❌",'callback_data'=>"yoqot"],['text'=>"⏩",'callback_data'=>"next2-$explode1"]];
$keyboard2[]=[['text'=>"🔎 ID yordamida qidirish",'callback_data'=>"myorders"]];
$keyboard=json_encode([
'inline_keyboard'=>$keyboard2,
]);
del();
sms($cid2,"<b>🛒 Buyurtma xolatiingiz:</b>",$keyboard);
exit();
}else{
bot('answerCallbackQuery',[
'callback_query_id'=>$qid,
'text'=>"⚠️ Boshqa qator qomadi",
'show_alert'=>true
]);
exit();
}
}

if(mb_stripos($data, "back2-")!==false){
$explode = explode("-",$data);
$explode = $explode[1];
if($explode == "0"){
bot('answerCallbackQuery',[
'callback_query_id'=>$qid,
'text'=>"⚠️ Boshqa qator qomadi",
'show_alert'=>true
]);
exit();
}else{
$explode1 = $explode - 50;
$key = [];
$sql = "SELECT * FROM myorder WHERE user_id = '$cid2' LIMIT $explode1,50";
$res = mysqli_query($connect,$sql);
while($row = mysqli_fetch_assoc($res)){
if($row['status'] == "Completed"){
$status = "✅";
}
if($row['status'] == "Pending" or $row['status'] == "In progress" or $row['status'] == "Partial"){
$status = "🔄";
}
if($row['status'] == "Processing"){
$status = "🔄";
}
if($row['status'] == "Canceled"){
$status = "⛔️";
}
$k[]=['text'=>"".$row['order_id']." $status",'callback_data'=>"orderstatus-".$row['order_id']];
}
$result = mysqli_query($connect, "SELECT * FROM myorder WHERE user_id = '$cid2' LIMIT $explode1,50");
$row = mysqli_fetch_assoc($result);
if($row){
del();
$keyboard2=array_chunk($k,5);
$keyboard2[]=[['text'=>"⏪",'callback_data'=>"back2-$explode1"],['text'=>"❌",'callback_data'=>"yoqot"],['text'=>"⏩",'callback_data'=>"next2-$explode"]];
$keyboard2[]=[['text'=>"🔎 ID yordamida qidirish",'callback_data'=>"myorders"]];
$keyboard=json_encode([
'inline_keyboard'=>$keyboard2,
]);
sms($cid2,"<b>🛒 Buyurtma xolatiingiz:</b>",$keyboard);
exit();
}else{
bot('answerCallbackQuery',[
'callback_query_id'=>$qid,
'text'=>"⚠️ Xatolik:",
'show_alert'=>true
]);
exit();
}
}
}


if(mb_stripos($data, "orderstatus-")!==false){
$service_id=explode("-",$data)[1];
$rew = mysqli_fetch_assoc(mysqli_query($connect,"SELECT * FROM myorder WHERE order_id = $service_id"));

$row = mysqli_fetch_assoc(mysqli_query($connect,"SELECT * FROM `orders` WHERE order_id = $service_id"));
$m = mysqli_fetch_assoc(mysqli_query($connect,"SELECT * FROM `providers` WHERE id = ".$row['provider'].""));
$j=json_decode(get($m['api_url']."?key=".$m['api_key']."&action=status&order=".$row['api_order'].""),1);
$start_count = "".$j['start_count']."";
$qold = "".$j['remains']."";

{
if($rew['status'] == "Completed"){
$status = "✅ Bajarilgan.";
}
if($rew['status'] == "Pending" or $rew['status'] == "In progress" or $rew['status'] == "Partial"){
$status = "🔄 Bajarilmoqda...";
}
if($rew['status'] == "Processing"){
$status = "🔄 Qayta ishlanmoqda.";
}
if($rew['status'] == "Canceled"){
$status = "⛔️ Bekor qilingan.";
}
}

{
if($rew['status'] == "Completed" or $rew['status'] == "Canceled"){
}else{
{
if($j['status'] == null){
}else{
$qoldi = "\n<b>🔢 Qolgan miqdor:</b> $qold ta";
}
}

{
if($j['start_count'] == null){
}else{
$boshlanish = "\n<b>⏩ Boshlash:</b> $start_count ta";
}
}
}
}

bot('sendMessage',[
'chat_id'=>$cid2,
'text'=>"
<b>🆔 ID:</b> <code>$service_id</code>
<b>🔑 Xizmat IDsi:</b> <code>".$rew['service']."</code>
<b>♻️ Holat:</b> $status
<b>⏰ Sana:</b> ".$rew['order_create']."
<b>💰 Narxi:</b> ".$rew['retail']." so'm $boshlanish $qoldi

",
'parse_mode'=>'html',
'reply_markup'=>json_encode([
'inline_keyboard'=>[
[['text'=>"❌",'callback_data'=>"yoqot2"]],
]
])
]);
}


if($data=="myorders" and joinchat($cid2)==1) {
$resi = mysqli_query($connect, "SELECT * FROM orders");
$stati = mysqli_num_rows($resi);
del();
sms($cid2,"🆔 O'zingizga kerak buyurtma ID raqamini yuboring: ",$ort);
put("user/$cid2.step","myorder");
exit;
}

if($step=="myorder" and is_numeric($text)==1){
$orde = mysqli_query($connect, "SELECT * FROM myorder WHERE order_id = '$text'");
$order = mysqli_fetch_assoc($orde);
if(!$order){
sms($cid,"❌ Buyurtma topilmadi.",$m);
}else{
if($order['user_id'] == $cid){
$row = mysqli_fetch_assoc(mysqli_query($connect,"SELECT * FROM orders WHERE order_id = $text"));
$pro = mysqli_fetch_assoc(mysqli_query($connect,"SELECT * FROM providers WHERE id = ".$row['provider'].""));
$j=json_decode(get($pro['api_url']."?key=".$pro['api_key']."&action=status&order=".$row['api_order'].""),1);
$start_count = "".$j['start_count']."";
$qold = "".$j['remains']."";

if($order['status'] == "Completed"){
$status = "✅ Bajarilgan.";
}
if($order['status'] == "Pending" or $order['status'] == "In progress" or $order['status'] == "Partial"){
$status = "🔄 Bajarilmoqda...";
}
if($order['status'] == "Processing"){
$status = "🔄 Qayta ishlanmoqda.";
}
if($order['status'] == "Canceled"){
$status = "⛔️ Bekor qilingan.";
}

{
if($order['status'] == "Completed" or $order['status'] == "Canceled"){
}else{
{
if($j['status'] == null){
}else{
$qoldi = "\n<b>🔢 Qolgan miqdor:</b> $qold ta";
}
}

{
if($j['start_count'] == null){
}else{
$boshlanish = "\n<b>⏩ Boshlash:</b> $start_count ta";
}
}
}
}

sms($cid,"
<b>🆔 ID:</b> <code>$text</code>
<b>♻️ Holat:</b> $status
<b>⏰ Sana:</b> ".$order['order_create']."
<b>💰 Narxi:</b> ".$order['retail']." so'm $boshlanish $qoldi
",$m);
}else{
sms($cid,"⚠️ Bu buyurtma sizga tegishli emas.",$m);
}
unlink("user/$cid.step");
}
exit;
}

if($data== "yoqot2"){
bot('deleteMessage',[
'chat_id'=>$cid2,
'message_id'=>$mid2,
]);
exit();
}

if($data== "yoqot"){
bot('deleteMessage',[
'chat_id'=>$cid2,
'message_id'=>$mid2,
]);
bot('sendMessage',[
'chat_id'=>$cid2,
'text'=>"<b>🖥 Asosiy menyudasiz</b>",
'parse_mode'=>'html',
'reply_markup'=>$m,
]);
exit();
}


if($text=="/start"){
$rew = mysqli_fetch_assoc(mysqli_query($connect,"SELECT * FROM users WHERE id = $cid"));
$start =str_replace(["{name}","{balance}","{time}"],["$name","".$rew['balance']."","$time"],enc("decode",$setting['start']));
sms($cid,$start,$m);

}

if($text && file_exists("promocodes/$text.txt") && !file_exists("promouser/$cid-$text.txt")){
    @mkdir("promouser");
    $amount = file_get_contents("promocodes/$text.txt");
    $rew = mysqli_fetch_assoc(mysqli_query($connect,"SELECT * FROM users WHERE id = $cid"));
    $new_bal = $rew['balance'] + $amount;
    mysqli_query($connect,"UPDATE users SET balance=$new_bal WHERE id=$cid");
    file_put_contents("promouser/$cid-$text.txt", "used");
    bot('sendMessage', [
        'chat_id' => $cid,
        'text' => "<b>🎁 Tabriklaymiz! Siz '$text' promokodini faollashtirdingiz va hisobingizga $amount so'm qo'shildi!</b>",
        'parse_mode' => 'html'
    ]);
} elseif ($text && file_exists("promocodes/$text.txt") && file_exists("promouser/$cid-$text.txt")) {
    bot('sendMessage', [
        'chat_id' => $cid,
        'text' => "<b>⚠️ Siz bu promokoddan foydalanib bo'lgansiz!</b>",
        'parse_mode' => 'html'
    ]);
}

if($text=="⏪ Orqaga" and joinchat($cid)==1){
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

if($text == "⚙️ Botni o'chirish/yoqish" and $cid == $admin){
    $status = file_get_contents("status.txt");
    if($status == "off"){
        file_put_contents("status.txt", "on");
        bot('sendMessage', [
            'chat_id' => $cid,
            'text' => "<b>✅ Bot yoqildi! Endi foydalanuvchilar botdan foydalana oladi.</b>",
            'parse_mode' => 'html'
        ]);
    } else {
        file_put_contents("status.txt", "off");
        bot('sendMessage', [
            'chat_id' => $cid,
            'text' => "<b>🛑 Bot o'chirildi! (Texnik xizmat rejimi). Foydalanuvchilar botdan foydalana olmaydi.</b>",
            'parse_mode' => 'html'
        ]);
    }
}

if($text == "🎁 Promokod yaratish" and $cid == $admin){
    bot('sendMessage', [
        'chat_id' => $cid,
        'text' => "<b>Promokod nomini kiriting:</b>",
        'parse_mode' => 'html',
        'reply_markup' => $ort
    ]);
    file_put_contents("user/$cid.step", "promo_name");
}

if($step == "promo_name" and $cid == $admin and $text != "⏪ Orqaga"){
    file_put_contents("user/promoname.txt", $text);
    bot('sendMessage', [
        'chat_id' => $cid,
        'text' => "<b>Promokod qancha pul berishini kiriting (masalan, 1000):</b>",
        'parse_mode' => 'html',
        'reply_markup' => $ort
    ]);
    file_put_contents("user/$cid.step", "promo_amount");
}

if($step == "promo_amount" and $cid == $admin and $text != "⏪ Orqaga"){
    if(is_numeric($text)){
        @mkdir("promocodes");
        $promo_name = file_get_contents("user/promoname.txt");
        file_put_contents("promocodes/$promo_name.txt", $text);
        bot('sendMessage', [
            'chat_id' => $cid,
            'text' => "<b>✅ '$promo_name' promokodi $text so'm qiymat bilan yaratildi!</b>\n\nFoydalanuvchilarga tarqatishingiz mumkin.",
            'parse_mode' => 'html',
            'reply_markup' => json_decode($panel, true) ? $panel : null
        ]);
        unlink("user/$cid.step");
    } else {
        bot('sendMessage', [
            'chat_id' => $cid,
            'text' => "<b>⚠️ Iltimos, faqat raqam kiriting:</b>",
            'parse_mode' => 'html'
        ]);
    }


// ===== Promokodlar ro'yxati =====
if($text == "📋 Promokodlar ro'yxati" and $cid == $admin){
    @mkdir("promocodes");
    $files = glob("promocodes/*.txt");
    if(empty($files)){
        bot('sendMessage', [
            'chat_id' => $cid,
            'text' => "<b>📋 Hech qanday promokod mavjud emas.</b>",
            'parse_mode' => 'html',
            'reply_markup' => $panel
        ]);
    } else {
        $list = "";
        $kb = [];
        foreach($files as $f){
            $name = basename($f, ".txt");
            $amount = file_get_contents($f);
            $list .= "🎁 <code>$name</code> — <b>$amount so'm</b>\n";
            $kb[] = [['text' => "🗑 $name o'chirish", 'callback_data' => "del_promo=$name"]];
        }
        bot('sendMessage', [
            'chat_id' => $cid,
            'text' => "<b>📋 Mavjud promokodlar:</b>\n\n$list",
            'parse_mode' => 'html',
            'reply_markup' => json_encode(['inline_keyboard' => $kb])
        ]);
    }
}

if(stripos($data, "del_promo=") !== false and $cid2 == $admin){
    $promo_del = explode("=", $data)[1];
    if(file_exists("promocodes/$promo_del.txt")){
        unlink("promocodes/$promo_del.txt");
        $used_files = glob("promouser/*-$promo_del.txt");
        if($used_files){ foreach($used_files as $uf){ @unlink($uf); } }
        bot('answerCallbackQuery', ['callback_query_id'=>$qid,'text'=>"✅ '$promo_del' promokodi o'chirildi!",'show_alert'=>true]);
        bot('deleteMessage', ['chat_id'=>$cid2,'message_id'=>$mid2]);
    }
}

// ===== Hisob to'ldirish (ID orqali) =====
if($text == "💰 Hisob to'ldirish (ID)" and $cid == $admin){
    bot('sendMessage', ['chat_id'=>$cid,'text'=> "<b>👤 Foydalanuvchi Telegram IDsini kiriting:</b>",'parse_mode'=>'html','reply_markup'=>$ort]);
    file_put_contents("user/$cid.step", "manual_topup_id");
}

if($step == "manual_topup_id" and $cid == $admin){
    if(is_numeric($text)){
        $check = mysqli_fetch_assoc(mysqli_query($connect, "SELECT * FROM users WHERE id = $text"));
        if($check){
            file_put_contents("user/$cid.topup_id.txt", $text);
            bot('sendMessage', ['chat_id'=>$cid,'text'=> "<b>💰 Qancha so'm qo'shish kerak?</b>\n\n<i>Foydalanuvchi: <a href='tg://user?id=$text'>$text</a>\nJoriy balansi: ".$check['balance']." so'm</i>",'parse_mode'=>'html','reply_markup'=>$ort]);
            file_put_contents("user/$cid.step", "manual_topup_amount");
        } else {
            bot('sendMessage', ['chat_id'=>$cid,'text'=> "<b>⚠️ Bu IDda foydalanuvchi topilmadi.</b>",'parse_mode'=>'html']);
        }
    } else {
        bot('sendMessage', ['chat_id'=>$cid,'text'=> "<b>⚠️ Faqat raqam (Telegram ID) kiriting:</b>",'parse_mode'=>'html']);
    }
}

if($step == "manual_topup_amount" and $cid == $admin){
    if(is_numeric($text)){
        $target_id = file_get_contents("user/$cid.topup_id.txt");
        $u = mysqli_fetch_assoc(mysqli_query($connect, "SELECT * FROM users WHERE id = $target_id"));
        $new_bal = $u['balance'] + $text;
        $new_out = $u['outing'] + $text;
        mysqli_query($connect, "UPDATE users SET balance=$new_bal, outing=$new_out WHERE id=$target_id");
        bot('sendMessage', ['chat_id'=>$cid,'text'=> "<b>✅ Muvaffaqiyatli!\n\n👤 Foydalanuvchi: <a href='tg://user?id=$target_id'>$target_id</a>\n💵 Qo'shilgan: $text so'm\n🏦 Yangi balans: $new_bal so'm</b>",'parse_mode'=>'html','reply_markup'=>$panel]);
        bot('sendMessage', ['chat_id'=>$target_id,'text'=> "<b>✅ Hisobingizga $text so'm qo'shildi!\n🏦 Yangi balansingiz: $new_bal so'm</b>",'parse_mode'=>'html']);
        @unlink("user/$cid.topup_id.txt");
        unlink("user/$cid.step");
    } else {
        bot('sendMessage', ['chat_id'=>$cid,'text'=> "<b>⚠️ Faqat raqam (miqdor) kiriting:</b>",'parse_mode'=>'html']);
    }
}

}


if($text=="☎️ Administrator" and joinchat($cid)==1){
sms($cid,"<b>📑 Murojaat matnini yozib yuboring.</b>",$ort);
put("user/$cid.step","murojaat");

}

if($step=="murojaat"){
sms($cid,"<b>✅ Murojaatingiz qabul qilindi</b>

<i>Tez orada murojaatingiz ko'rib chiqilib habar berilad.</i>",$m);
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

if($text=="🔐 Mening hisobim" and joinchat($cid)==1){
$rew = mysqli_fetch_assoc(mysqli_query($connect,"SELECT * FROM users WHERE id = $cid"));
$resi = mysqli_query($connect, "SELECT * FROM myorder");
$stati = mysqli_num_rows($resi);
$myorder = "0";
$stati ? $stati = $stati : $stati = "0";
while($hi=mysqli_fetch_assoc($resi)){
if($hi['user_id']==$cid) {
$myorder++;
}
}
$kabinet = "<b>🔎 Sizning ID raqamingiz:</b> <code>$cid</code>

<b>💵 Umumiy balansingiz:</b> ".$rew['balance']." so'm
<b>🗄 Buyurtmalaringiz:</b> $myorder ta

💳 Botga kiritgan pullaringiz: ".$rew['outing']." so'm ";
sms($cid,$kabinet,json_encode([
inline_keyboard=>[
[['text'=>"💳 Hisobni to'ldirish",'callback_data'=>"menu=tolov"]],
]]));

}


if((stripos($data,"menu=")!==false and joinchat($chat_id)==1)){
$res=explode("=",$data)[1];
if($res=="tolov"){
$ops=get("set/payments.txt");
$s=explode("\n",$ops);
$soni = substr_count($ops,"\n");
for($i=1;$i<=$soni;$i++){
$k[]=['text'=>$s[$i],'callback_data'=>"card=".$s[$i]];
}
$keyboard2=array_chunk($k,2);
$keyboard2[]=[['text'=>"☎️ Admin yordamida",url=>"tg://user?id=$admin"]];
$kb=json_encode([
'inline_keyboard'=>$keyboard2,
]);
if($ops){
edit($chat_id,$message_id,"💳 Quyidagi to'lov tizimlaridan birini tanlang:",$kb);
}else{
 bot('answerCallbackQuery',[
		'callback_query_id'=>$qid,
		'text'=>"⚠️ To'lov tizimlari qo'shilmagan",
		'show_alert'=>true,
		]);  
}
}elseif($res=="back"){
$rew = mysqli_fetch_assoc(mysqli_query($connect,"SELECT * FROM users WHERE id = $chat_id"));
del();
$kabinet = "<b>🔎 Sizning ID raqamingiz:</b> <code>$cid2</code>

<b>💵 Umumiy balansingiz:</b> ".$rew['balance']." so'm
<b>🗄 Buyurtmalaringiz:</b> ".$rew['orders']." ta
<b>🔗 Takliflaringiz soni:</b> ".$rew['refnum']." ta

<b>Statusingiz:</b> Oddiy 

💳 Botga kiritgan pullaringiz: ".$rew['outing']." so'm ";
sms($chat_id,"$kabinet",json_encode([
inline_keyboard=>[
[['text'=>"💳 Hisobni to'ldirish",'callback_data'=>"menu=tolov"]],
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

⬇️ Minimal 1 000 so‘m
⬆️ Maksimal 12 000 000 so‘m",$ort);
put("user/$chat_id.step","payme");
}
}
}

if((stripos($data,"card=")!==false)){
$h=explode("=", $data)[1];
$card=get("set/pay/$h/wallet.txt");
$info=get("set/pay/$h/addition.txt");
edit($cid2,$mid2,"
<b>To'lov tizimi:</b> $h

<b>Hamyon:</b> <code>$card</code>
<b>ID:</b> <code>$cid2</code>
   
<b>$info</b>
",json_encode([
'inline_keyboard'=>[
[['text'=>"✅ To'lov qildim",'callback_data'=>"money-$h"]],
[['text'=>"⏪ Orqaga",'callback_data'=>"menu=tolov"]],
]]));
}


if(mb_stripos($data, "money-")!==false){
$ex = explode("-",$data);
$turi = $ex[1];
bot('deleteMessage',[
'chat_id'=>$cid2,
'message_id'=>$mid2,
]);
bot('SendMessage',[
'chat_id'=>$cid2,
'text'=>"💵 <b>To'lov miqdorini kiriting:</b>
	
<i>Minimal: 1 000 so'm</i>",
'parse_mode'=>'html',
'reply_markup'=>$ort,
]);
file_put_contents("user/$cid2.step","oplata-$turi");
exit();
}

if(mb_stripos($step, "oplata-")!==false){
$ex = explode("-",$step);
$turi = $ex[1];
if(is_numeric($text)=="true"){
if($text < 1000){
bot('SendMessage',[
	'chat_id'=>$cid,
	'text'=>"💵 <b>Minimal: 1 000 so'm</b>",
	'parse_mode'=>'html',
]);
exit();
}else{
bot('SendMessage',[
'chat_id'=>$cid,
'text'=>"📝 <b>To'lov chekini (Rasm) yuboring</b>",
'parse_mode'=>'html',
]);
file_put_contents("user/$cid.step","rasm-$text-$turi");
}
exit();
}else{
bot('SendMessage',[
'chat_id'=>$cid,
'text'=>"💵 <b>To'lov miqdorini kiriting:</b>
	
<i>Minimal: 1 000 so'm</i>",
'parse_mode'=>'html',
]);
exit();
}
}

if(mb_stripos($step, "rasm-")!==false){
	$ex = explode("-",$step);
	$miqdor = $ex[1];
        $turi = $ex[2];
bot('forwardMessage',[
'chat_id'=>$admin,
'from_chat_id'=>$cid,
'message_id'=>$mid,
]);
$data = date("Y.m.d H:i:s");
bot('SendMessage',[
'chat_id'=>$admin,
'text'=>"<b>Foydalanuvchi hisobini to'ldirmoqchi!</b>

<b>💳 To'lov tizimi:</b> $turi
<b>👤 Foydalanuvchi:</b> <a href='tg://user?id=$cid'>$cid</a>
<b>💰 To'lov miqdori:</b> $miqdor so'm
<b>⏰ Sana:</b> $data",
'disable_web_page_preview'=>true,
'parse_mode'=>'html',
'reply_markup'=>json_encode([
'inline_keyboard'=>[
[['text'=>"✅",'callback_data'=>"on-$cid-$miqdor"],['text'=>"❌",'callback_data'=>"off-$cid-$miqdor"]],
]
])
]);
bot('sendMessage',[
'chat_id'=>$cid,
'text'=>"✅ <b>Qabul qilindi</b>

<i>To'lov cheki 15-60 daqiqa ichida tekshiriladi!</i>",
'parse_mode'=>'html',
'reply_markup'=>$m,
]);
unlink("user/$cid.step");
exit();
}

if(mb_stripos($data, "on-")!==false){
$ex = explode("-",$data);
$id = $ex[1];
$miqdor = $ex[2];
$ba = mysqli_fetch_assoc(mysqli_query($connect,"SELECT * FROM users WHERE id = $id"));
$a = $ba['balance'] + $miqdor;
$b = $ba['outing'] + $miqdor;
mysqli_query($connect,"UPDATE users SET balance = '$a' WHERE id = $id");
mysqli_query($connect,"UPDATE users SET outing = '$b' WHERE id = $id");
bot('deleteMessage',[
'chat_id'=>$cid2,
'message_id'=>$mid2,
]);
bot('SendMessage',[
'chat_id'=>$id,
'text'=>"✅ <b>To'lovingiz tastiqlandi.</b>
	
<i>Hisobingizga $miqdor so'm qo'shildi</i>",
'parse_mode'=>'html',
]);
bot('SendMessage',[
'chat_id'=>$admin,
'text'=>"➕ <b>Foydalanuvchi (</b>$id<b>) hisobiga $miqdor so'm qo'shildi.</b>",
'parse_mode'=>'html',
]);      
exit();
}

if(mb_stripos($data, "off-")!==false){
$ex = explode("-",$data);
$id = $ex[1];
$miqdor = $ex[2];
bot('editMessageText',[
'chat_id'=>$cid2,
'message_id'=>$mid2,
'text'=>"<b>⚠️ Bekor qilindi.</b>

👤 <b>Foydalanuvchi:</b> <a href='tg://user?id=$id'>$id</a>
💰 <b>To'lov miqdor:</b> $miqdor so'm",
'parse_mode'=>'html',
]);
bot('sendMessage',[
'chat_id'=>$id,
'text'=>"<b>⚠️ To'lovingiz bekor qilindi.</b>",
'parse_mode'=>'html',
]);		
}




if($step=="payme"){
if($text>="1000" and $text<="12000000"){
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
⬇️ Minimal 1 000 so‘m
⬆️ Maksimal 12 000 000 so‘m",$ort);
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



if($text=="🛍 Buyurtma berish" and joinchat($cid)==1){
{
if($cid == $admin){
$n = "➕";
$a = mysqli_query($connect,"SELECT * FROM `categorys`");
}else{
$a = mysqli_query($connect,"SELECT * FROM `categorys` WHERE category_status = 'ON'");
}
}
$c = mysqli_num_rows($a);
while($s = mysqli_fetch_assoc($a)){
$k[]=['text'=>"".enc("decode",$s['category_name']),'callback_data'=>"tanla1=".$s['category_id']];
}
$keyboard2=array_chunk($k,2);
$keyboard2[]=[['text'=>"$n",'callback_data'=>"newFol"]];
$keyboard2[]=[['text'=>"🔎 Qidirish ",'callback_data'=>"order"]];
$kb=json_encode([
'inline_keyboard'=>$keyboard2,
]);
if($c){
bot('sendMessage',[
'chat_id'=>$cid,
'text'=>"<b>Quydagi Ijtimoiy tarmoqlardan birini tanlang.</b>",
'parse_mode'=>'html',
'reply_markup'=>$kb,
]);
exit();
}else{
if($cid == $admin){
bot('sendMessage',[
'chat_id'=>$cid,
'text'=>"<b>Quydagi Ijtimoiy tarmoqlardan birini tanlang.</b>",
'parse_mode'=>'html',
'reply_markup'=>$kb,
]);
}else{
sms($cid,"⚠️ Bu bo'lim qayta tiklanmoqda biroz kuting.",null);
}
exit; 
}
}



if($data=="absd" and joinchat($chat_id)==1){
{
if($chat_id == $admin){
$n = "➕";
$a = mysqli_query($connect,"SELECT * FROM `categorys`");
}else{
$a = mysqli_query($connect,"SELECT * FROM `categorys` WHERE category_status = 'ON'");
}
}
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
$keyboard2=array_chunk($k,2);
$keyboard2[]=[['text'=>"$n",'callback_data'=>"newFol"]];
$keyboard2[]=[['text'=>"🔎 Qidirish",'callback_data'=>"order"]];
$kb=json_encode([
'inline_keyboard'=>$keyboard2,
]);
edit($chat_id,$mid2,"<b>Quydagi Ijtimoiy tarmoqlardan birini tanlang.</b>",$kb);
exit; 
}
}

if($data == "newFol"){
	bot('deleteMessage',[
	'chat_id'=>$chat_id,
	'message_id'=>$message_id,
]);
   bot('sendMessage',[
   'chat_id'=>$chat_id,
   'text'=>"<b>Yangi bo'lim uchun nom yuboring:</b>",
   'parse_mode'=>'html',
   'reply_markup'=>$ort
]);
file_put_contents("user/$chat_id.step",'newFol');

}

if($step == "newFol"){
$res = mysqli_query($connect, "SELECT * FROM `categorys`");
$n = mysqli_fetch_assoc($res);
		bot('SendMessage',[
		'chat_id'=>$cid,
		'text'=>"<b>$text</b> bo'limi qo'shildi!",
		'parse_mode'=>'html',
		'reply_markup'=>$m
]);
$text=enc("encode",$text);
mysqli_query($connect,"INSERT INTO categorys(category_name,category_status) VALUES('$text','ON');");
unlink("user/$cid.step");
bot('sendMessage',[
   'chat_id'=>$cid,
   'text'=>"<b>Yana bo'lim qo'shish uchun ''➕'' tugmasini bosing!</b>",
   'parse_mode'=>'html',
   'reply_markup'=>json_encode([
'inline_keyboard'=>[
[['text'=>"➕",'callback_data'=>"newFol"]],
]
])
]);
}



if((mb_stripos($data,"tanla1=")!==false and joinchat($chat_id)==1)){
$n=explode("=",$data)[1];
$aa = mysqli_fetch_assoc(mysqli_query($connect,"SELECT * FROM categorys WHERE category_id = $n"));
{
if($chat_id == $admin){
$n1 = "📝";
$n2 = "➕";
$n3 = "🗑";
{
if($aa['category_status'] == 'ON'){
$na = "🔒 O'chirish";
$d = "OFF";
}else{
$na = "🔓 Yoqish";
$d = "ON";
}
}
}else{
}
}
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
$keyboard2[]=[['text'=>"$n1",'callback_data'=>"editFolss=$n"],['text'=>"$n2",'callback_data'=>"adFol=$n"],['text'=>"$n3",'callback_data'=>"delFol=$n"]];
$keyboard2[]=[['text'=>"⏪ Orqaga",'callback_data'=>"absd"]];
$kb=json_encode([
'inline_keyboard'=>$keyboard2,
]);
if(!$c){
if($chat_id == $admin){
edit($chat_id,$message_id,"<b>«".enc("decode",$aa['category_name'])."» - tarmoq bo'limlaridan birini tanlang.</b>",$kb);
}else{
bot('answerCallbackQuery',[
	'callback_query_id'=>$qid,
	'text'=>"⚠️ Ushbu tarmq uchun xizmat turlari topilmadi!",
	'show_alert'=>true,
	]);
    }
	}else{
edit($chat_id,$message_id,"<b>«".enc("decode",$aa['category_name'])."» - tarmoq bo'limlaridan birini tanlang.</b>",$kb);
exit; 
}
}



if(mb_stripos($data, "editFolss=")!==false){
	$ex = explode("=",$data)[1];
	bot('deleteMessage',[
	'chat_id'=>$cid2,
	'message_id'=>$mid2,
]);
   bot('sendMessage',[
   'chat_id'=>$cid2,
   'text'=>"<b>Yangi nom kiriting:</b>",
   'parse_mode'=>'html',
   'reply_markup'=>$ort
]);
file_put_contents("user/$cid2.step","editFol=$ex");

}

if((mb_stripos($step,"editFol=")!==false)){
	$ex = explode("=",$step)[1];
if(isset($text)){
$text=enc("encode",$text);
mysqli_query($connect,"UPDATE categorys SET category_name = '$text' WHERE category_id = $ex");
		bot('SendMessage',[
		'chat_id'=>$cid,
		'text'=>"<b>Muvaffaqiyatli o'zgartirildi.</b>",
		'parse_mode'=>'html',
		'reply_markup'=>$m
]);
unlink("user/$cid.step");

}
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
   'text'=>"<b>Yangi ichki bo'lim uchun nom yuboring:</b>",
   'parse_mode'=>'html',
   'reply_markup'=>$aort
]);
file_put_contents("user/$chat_id.step",'newFold');
}


if($step == "newFold"){
		if(isset($text)){
$ci=get("set/c.txt");
		bot('sendMessage',[
		'chat_id'=>$cid,
		'text'=>"<b>$text</b> - nomli ichki bo'lim qo'shildi!",
		'parse_mode'=>'html',
		'reply_markup'=>$m
]);
$to=enc("encode",$text);
mysqli_query($connect,"INSERT INTO cates(`name`,`category_id`) VALUES ('$to','$ci')");
unlink("user/$cid.step");
bot('sendMessage',[
   'chat_id'=>$cid,
   'text'=>"<b>Yana ichki bo'lim qo'shish uchun ''➕'' tugmasini bosing!</b>",
   'parse_mode'=>'html',
   'reply_markup'=>json_encode([
'inline_keyboard'=>[
[['text'=>"➕",'callback_data'=>"adFol=$ci"]],
]
])
]);
}
}


if(mb_stripos($data, "delFol=")!==false){
	$ex = explode("=",$data)[1];
	$c = mysqli_fetch_assoc(mysqli_query($connect,"SELECT * FROM categorys WHERE category_id = $ex"));
	bot('deleteMessage',[
	'chat_id'=>$cid2,
	'message_id'=>$mid2,
]);
   bot('sendMessage',[
   'chat_id'=>$cid2,
   'text'=>"<b>".enc("decode",$c['category_name'])."</b> - bo'limni o'chirishga rizimisiz ?
   
<i>Bo'lim o'chirilsa qayta tiklash imkoni bo'lmaydi, rozi bo'lsangiz ''🗑 O'chirish'' tugmasini bosing!</i>",
   'parse_mode'=>'html',
   'reply_markup'=>json_encode([
'inline_keyboard'=>[
[['text'=>"🗑 O'chirish",'callback_data'=>"delFols=$ex"]],
[['text'=>"⏪ Orqaga",'callback_data'=>"tanla1=$ex"]],
]
])
]);
}

if(mb_stripos($data, "delFols=")!==false){
$ex = explode("=",$data)[1];
$cc = mysqli_fetch_assoc(mysqli_query($connect,"SELECT * FROM categorys WHERE category_id = $ex"));
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
       'text'=>"<b>".enc("decode",$cc['category_name'])."</b> - bo'limi o'chirildi!",
'parse_mode'=>'html',
'reply_markup'=>$m
]);

}

if(mb_stripos($data,"tanla2=")!==false and joinchat($chat_id)==1){
$n=explode("=",$data)[1];
$as=0;
$caid = mysqli_fetch_assoc(mysqli_query($connect,"SELECT * FROM cates WHERE cate_id  = $n"));
{
if($chat_id == $admin){
$nn = "Xizmatlarni yuklab olish";
$n1 = "📝";
$n2 = "➕";
$n3 = "🗑";
$a = mysqli_query($connect,"SELECT * FROM services WHERE category_id = '$n'");
}else{
$a = mysqli_query($connect,"SELECT * FROM services WHERE category_id = '$n' AND service_status = 'on'");
}
}
$c = mysqli_num_rows($a);
while($s = mysqli_fetch_assoc($a)){
$as++;
$narx = $s['service_price'];
$k[]=['text'=>"".base64_decode($s['service_name'])." - $narx so‘m",'callback_data'=>"ordered=".$s['service_id']."=".$n];
}
$keyboard2=array_chunk($k,1);
$adds=json_decode(get("set/sub.json"),1);
$keyboard2[]=[['text'=>"$nn",'callback_data'=>"uplads-$n"]];
$keyboard2[]=[['text'=>"$n1",'callback_data'=>"editFoldm=$n"],['text'=>"$n2",'callback_data'=>"adds-$n"],['text'=>"$n3",'callback_data'=>"delFolm=$n"]];
$keyboard2[]=[['text'=>"⏪ Orqaga",'callback_data'=>"tanla1=".$adds['cate_id']]];
$kb=json_encode([
'inline_keyboard'=>$keyboard2,
]);
if(!$c){
if($chat_id == $admin){
edit($chat_id,$message_id,"<b>«".enc("decode",$caid['name'])."» - bo'lim xizmatlaridan birini tanlang.</b>

<i>⚠️ Buyurtma berishdan oldin xizmat tavsifini o'qib chiqing.</i>",$kb);
}else{
bot('answerCallbackQuery',[
		'callback_query_id'=>$qid,
		'text'=>"⚠️ Ushbu bo'lim uchun xizmatlar topilmadi!",
		'show_alert'=>true,
		]);
    }
}else{
edit($chat_id,$message_id,"<b>«".enc("decode",$caid['name'])."» - bo'lim xizmatlaridan birini tanlang.</b>

<i>⚠️ Buyurtma berishdan oldin xizmat tavsifini o'qib chiqing.</i>",$kb);
exit; 
}
}


if(mb_stripos($data, "editFoldm=")!==false){
	$ex = explode("=",$data)[1];
	bot('deleteMessage',[
	'chat_id'=>$cid2,
	'message_id'=>$mid2,
]);
   bot('sendMessage',[
   'chat_id'=>$cid2,
   'text'=>"<b>Ichki bo'lim uchun yangi nom kiriting:</b>",
   'parse_mode'=>'html',
   'reply_markup'=>$ort
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
		'reply_markup'=>$m
]);
unlink("user/$cid.step");

}

}


if(mb_stripos($data, "delFolm=")!==false){
	$ex = explode("=",$data)[1];
	$c = mysqli_fetch_assoc(mysqli_query($connect,"SELECT * FROM cates WHERE cate_id  = $ex"));
	bot('deleteMessage',[
	'chat_id'=>$cid2,
	'message_id'=>$mid2,
]);
   bot('sendMessage',[
   'chat_id'=>$cid2,
   'text'=>"<b>".enc("decode",$c['name'])."</b> - nomli ichki bo'limni o'chirishga rozimisiz ?
   
<i>Ichki bo'lim o'chirilsa qayta tiklash imkoni bo'lmaydi, rozi bo'lsangiz ''🗑 O'chirish'' tugmasini bosing!</i>",
   'parse_mode'=>'html',
   'reply_markup'=>json_encode([
'inline_keyboard'=>[
[['text'=>"🗑 O'chirish",'callback_data'=>"delFoll=$ex"]],
[['text'=>"⏪ Orqaga",'callback_data'=>"tanla2=$ex"]],
]
])
]);
}





if(mb_stripos($data, "delFoll=")!==false){
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
       'text'=>"<b>$d</b> - nomli ichki bo'lim o'chirildi!",
'parse_mode'=>'html',
'reply_markup'=>$m
]);

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


if(mb_stripos($data, "adds-")!==false){
$pw=explode("-",$data)[1];
file_put_contents("user/$chat_id.cate_id",$pw);
$addss['cate_id'] = $pw;
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
   'reply_markup'=>$ort
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
sms($chat_id,"📝 Xizmat xaqida malumotlar kiriting:

⚠️ Ma'lumot kiritish ni xoxlamasangiz <b>Kiritilmagan</b> tugmasini bosing",json_encode([
'resize_keyboard'=>true,
'keyboard'=>[
[['text'=>"Kiritilmagan"]],
[['text'=>"⏪ Orqaga"]],
]]));
$adds=json_decode(get("set/adds.json"),1);
$adds['api_currency']=$pw;
put("set/adds.json",json_encode($adds,JSON_UNESCAPED_UNICODE));
file_put_contents("user/$chat_id.step",'servis2');
}
}
if(($step=="servis2" and $cid==$admin)){
if(isset($text)){
sms($cid,"💵 Buyurtma narxini yuboring (1000 ta) uchun",$ort);
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
sms($cid,"🆔 Xizmat IDsini yuboring:",$ort);
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
$cate_id = file_get_contents("user/$cid.cate_id");
$service_edit = "true";
mysqli_query($connect,"INSERT INTO services(`service_status`,`service_price`,`service_edit`,`category_id`,`service_api`,`api_service`,`api_currency`,`service_type`,`api_detail`,`service_name`,`service_desc`,`service_min`,`service_max`) VALUES ('on','$service_price','$service_edit','$category_id','$text','$api_service','$api_currency','$type','{\"name\":\"$name\",\"min\":\"$min\",\"max\":\"$max\",\"type\":\"$type\",\"cancel\":\"$cancel\",\"refill\":\"$refill\",\"dripfeed\":\"$dripfeed\"}','$service_name','$service_desc','$min','$max');");

sms($cid,"✅ Yangi xizmat qo'shildi.",$m);
bot('sendMessage',[
   'chat_id'=>$cid,
   'text'=>"<b>Yana xizmat qo'shish uchun ''➕'' tugmasini bosing!</b>",
   'parse_mode'=>'html',
   'reply_markup'=>json_encode([
'inline_keyboard'=>[
[['text'=>"➕",'callback_data'=>"adds-$cate_id"]],
]
])
]);
unlink("user/$cid.cate_id");
}
}

}



if($data=="order") {
del();
sms($cid2,"<b>🆔 O'zingizga kerak bo'lgan xizmat id raqamini yuboring:</b>",$ort);
put("user/$cid2.step","ordered");
}


if($step == "ordered"){
$rew = mysqli_fetch_assoc(mysqli_query($connect,"SELECT * FROM services WHERE service_id = $text"));
if($rew){
bot('deleteMessage',[
'chat_id'=>$cid,
'message_id'=>$del,
]);
$del = bot('sendMessage', [
'chat_id'=>$cid,
'text'=>"<b>Yuklanmoqda...</b>",
'parse_mode'=>'html',
'reply_markup'=>json_encode([
'remove_keyboard'=>true
])
])->result->message_id;
sleep(0.1);
$a = mysqli_query($connect,"SELECT * FROM services WHERE service_id= '$text'");
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
$ab = "<b>⏬ Minimal</b> - $min ta
<b>⏫ Maksimal</b> - $max ta

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
bot('deleteMessage',[
'chat_id'=>$cid,
'message_id'=>$del,
]);
     bot('sendMessage',[
        'chat_id'=>$cid,
       'text'=>"<b>🚀 Xizmat nomi:</b> ".($nam)."

<b>🔑 Xizmat IDsi:</b> <code>$sid</code>
<b>💰 Xizmat narxi (1000x):</b> $narx so‘m

$ab",
'parse_mode'=>'html',
'disable_web_page_preview'=>true,
'reply_markup'=>json_encode([
'inline_keyboard'=>[
[['text'=>"✅ Buyurtma berish",'callback_data'=>"aosdrder=$spi=$min=$max=".$narx."=$type=".$api."=$sid"]],
]
])
]);
sms($cid,"🖥️ Asosiy menyudasiz",$m);
unlink("user/$cid.step"); 
}}else{
bot('SendMessage',[
	'chat_id'=>$cid,
	'text'=>"<b>Siz kiritgan ID bo'yicha hech qanday xizmat topilmadi!</b>

Qayta urinib ko'ring:",
'parse_mode'=>'html',
]);
}
}


if((stripos($data,"ordered=")!==false)){
$n=explode("=",$data)[1];
$n2=explode("=",$data)[2];
$aa = mysqli_fetch_assoc(mysqli_query($connect,"SELECT * FROM services WHERE service_id = $n"));
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
$ab = "<b>⏬ Minimal</b> - $min ta
<b>⏫ Maksimal</b> - $max ta

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
{
if($chat_id == $admin){
$nnn = "📝";
$nnnn = "🗑";
}else{
}
}
     bot('editMessageText',[
        'chat_id'=>$chat_id,
       'message_id'=>$message_id,
       'text'=>"<b>🚀 Xizmat nomi:</b> ".($nam)."

<b>🔑 Xizmat IDsi:</b> <code>$sid</code>
<b>💰 Xizmat narxi (1000x):</b> $narx so‘m

$ab",
'parse_mode'=>'html',
'disable_web_page_preview'=>true,
'reply_markup'=>json_encode([
'inline_keyboard'=>[
[['text'=>"$nnn",'callback_data'=>"edits=$sid=$n2"],['text'=>"$nnnn",'callback_data'=>"delxiz=$sid"]],
[['text'=>"✅ Buyurtma berish",'callback_data'=>"aosdrder=$spi=$min=$max=".$narx."=$type=".$api."=$sid"]],
[['text'=>"⏪ Orqaga",'callback_data'=>"tanla2=$n2"]],
]
])
]);
exit;
}
}


if(mb_stripos($data, "edits=")!==false){
	$service_id = explode("=",$data)[1];
	$category_id = explode("=",$data)[2];
	$c = mysqli_fetch_assoc(mysqli_query($connect,"SELECT * FROM services WHERE service_id = $service_id"));
	$p = mysqli_fetch_assoc(mysqli_query($connect,"SELECT * FROM providers WHERE id = $c[api_service]"));
	
	if($c['service_desc'] == null){
	 $service_desc = "Kiritilmagan";
	}else{
	 $service_desc = enc("decode",$c['service_desc']);   
	}
	bot('deleteMessage',[
	'chat_id'=>$cid2,
	'message_id'=>$mid2,
]);
   bot('sendMessage',[
   'chat_id'=>$cid2,
   'text'=>"<b>1. Nom:</b> ".enc("decode",$c['service_name'])."
   
<b>2. Narx:</b> ".$c['service_price']." so'm
<b>3. Minimal:</b> ".$c['service_min']." ta
<b>4. Maksimal:</b> ".$c['service_max']." ta
<b>5. Provider:</b> <code>".$p['api_url']."</code>
<b>6. Tavsif:</b> $service_desc

<b>API dagi SERVIS ID:</b> <code>".$c['service_api']." </code>",
   'parse_mode'=>'html',
   'disable_web_page_preview'=>true,
   'reply_markup'=>json_encode([
'inline_keyboard'=>[
[['text'=>"📝  Nom",'callback_data'=>"editservice=service_name=$service_id=$category_id"],['text'=>"📝  Narxi",'callback_data'=>"editservice=service_price=$service_id=$category_id"]],
[['text'=>"📝  Minimal",'callback_data'=>"editservice=service_min=$service_id=$category_id"],['text'=>"📝  Maksimal",'callback_data'=>"editservice=service_max=$service_id=$category_id"]],
[['text'=>"📝  Tavsif",'callback_data'=>"editservice=service_desc=$service_id=$category_id"]],
[['text'=>"📝  Provider",'callback_data'=>"editservispro=$service_id=$category_id"]],
[['text'=>"📝  API dagi SERVIS ID",'callback_data'=>"editservice_api=$service_id=$category_id"]],
[['text'=>"⏪ Orqaga",'callback_data'=>"ordered=$service_id=$category_id"]],
]
])
]);
unlink("user/$cid.step");
}


if(mb_stripos($data, "editservice_api=")!==false){
	$s_id = explode("=",$data)[1];
	$c_id = explode("=",$data)[2];
	bot('deleteMessage',[
	'chat_id'=>$cid2,
	'message_id'=>$mid2,
]);
   bot('sendMessage',[
   'chat_id'=>$cid2,
   'text'=>"<b>Yangi qiymatni kiriting:</b>",
   'parse_mode'=>'html',
   	'reply_markup'=>json_encode([
'inline_keyboard'=>[
[['text'=>"⏪ Orqaga",'callback_data'=>"edits=$s_id=$c_id"]],
]
])
]);
file_put_contents("user/$cid2.step","editXizma_t_id-$s_id-$c_id");

}



if(mb_stripos($step, "editXizma_t_id-")!==false){
	$xiz = explode("-",$step)[1];
	$caid = explode("-",$step)[2];
	if(is_numeric($text)){
	bot('SendMessage',[
	'chat_id'=>$cid,
	'text'=>"<b>Muvaffaqiyatli o'zgartirildi.</b>",
	'parse_mode'=>'html',
    'reply_markup'=>json_encode([
    'inline_keyboard'=>[
    [['text'=>"⏪ Orqaga",'callback_data'=>"edits=$xiz=$caid"]],
    ]
    ])
    ]);
    unlink("user/$cid.step");
	mysqli_query($connect,"UPDATE services SET service_api='$text' WHERE service_id = $xiz");
	$providers_id = mysqli_fetch_assoc(mysqli_query($connect,"SELECT * FROM services WHERE service_id = $xiz"))['api_service'];
	$ap = mysqli_fetch_assoc(mysqli_query($connect,"SELECT * FROM providers WHERE id = $providers_id"));
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
   	mysqli_query($connect,"UPDATE services SET api_detail='{\"name\":\"$name\",\"min\":\"$min\",\"max\":\"$max\",\"type\":\"$type\",\"cancel\":\"$cancel\",\"refill\":\"$refill\",\"dripfeed\":\"$dripfeed\"}' WHERE service_id = $xiz");
    mysqli_query($connect,"UPDATE services SET service_api='$text' WHERE service_id = $xiz");
	}else{
	bot('SendMessage',[
	'chat_id'=>$cid,
	'text'=>"<b>⚠️ ID raqam yuboring.</b>",
	'parse_mode'=>'html',
    'reply_markup'=>json_encode([
    'inline_keyboard'=>[
    [['text'=>"⏪ Orqaga",'callback_data'=>"edits=$xiz=$caid"]],
    ]
    ])
    ]);
	}
}

//// mysqli_query($connect,"UPDATE services SET service_api='$vo' WHERE service_id = $xiz");

if(mb_stripos($data, "editservispro=")!==false){
$s_id = explode("=",$data)[1];
$c_id = explode("=",$data)[2];
$pr=0;
$prs="";
$a = mysqli_query($connect,"SELECT * FROM providers");
$c = mysqli_num_rows($a);
while($s = mysqli_fetch_assoc($a)){
$pr++;
$prtxt=str_replace(["/api/v1","/api/v2","https://"],["","",""],$s['api_url']);
$prs.="<b>".$pr."</b>: $prtxt\n";
$k[]=['text'=>$pr,'callback_data'=>"editprovider-$s_id-$c_id-".$s['id']];
}
$keyboard2=array_chunk($k,4);
$keyboard2[]=[['text'=>"⏪ Orqaga",'callback_data'=>"edits=$s_id=$c_id"]];
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
        'chat_id'=>$cid2,
       'text'=>"Provayderni tanlang:
 
$prs",
'parse_mode'=>"HTML",
'reply_markup'=>$kb,
]);
}
}

if((stripos($data,"editprovider-")!==false and $cid2==$admin)){
$s_id=explode("-",$data)[1];
$c_id=explode("-",$data)[2];
$p_id=explode("-",$data)[3];
mysqli_query($connect,"UPDATE services SET api_service='$p_id' WHERE service_id = $s_id");
del();
bot('SendMessage',[
	'chat_id'=>$cid2,
	'text'=>"<b> Muvaffaqiyatli o'zgartirildi.</b>",
	'parse_mode'=>'html',
	'reply_markup'=>json_encode([
'inline_keyboard'=>[
[['text'=>"⏪ Orqaga",'callback_data'=>"edits=$s_id=$c_id"]],
]
])
]);
}


if(mb_stripos($data, "editservice=")!==false){
    $s = explode("=",$data)[1];
	$s_id = explode("=",$data)[2];
	$c_id = explode("=",$data)[3];
	bot('deleteMessage',[
	'chat_id'=>$cid2,
	'message_id'=>$mid2,
]);
   bot('sendMessage',[
   'chat_id'=>$cid2,
   'text'=>"<b>Yangi qiymatni kiriting:</b>",
   'parse_mode'=>'html',
   'reply_markup'=>json_encode([
    'inline_keyboard'=>[
    [['text'=>"⏪ Orqaga",'callback_data'=>"edits=$s_id=$c_id"]],
    ]
    ])
]);
file_put_contents("user/$cid2.step","editXizmatid-$s_id-$s-$c_id");

}





if(mb_stripos($step, "editXizmatid-")!==false){
	$xiz = explode("-",$step)[1];
	$ex = explode("-",$step)[2];
	$caid = explode("-",$step)[3];
	if($cid == $admin and isset($text)){
	bot('SendMessage',[
	'chat_id'=>$cid,
	'text'=>"<b> Muvaffaqiyatli o'zgartirildi.</b>",
	'parse_mode'=>'html',
    'reply_markup'=>json_encode([
    'inline_keyboard'=>[
    [['text'=>"⏪ Orqaga",'callback_data'=>"edits=$xiz=$caid"]],
    ]
    ])
    ]);
	if($ex=="service_desc"){
	$vo = base64_encode($text);
	mysqli_query($connect,"UPDATE services SET service_desc='$vo' WHERE service_id = $xiz");
	}elseif($ex=="service_name"){
	$vo = base64_encode($text);
	mysqli_query($connect,"UPDATE services SET service_name='$vo' WHERE service_id = $xiz");
	}elseif($ex=="service_price"){
	$vo = $text;
	mysqli_query($connect,"UPDATE services SET service_edit='false', service_price='$vo' WHERE service_id = $xiz");
	}elseif($ex=="service_min"){
	$vo = $text;
	mysqli_query($connect,"UPDATE services SET service_edit='false', service_min='$vo' WHERE service_id = $xiz");
	}elseif($ex=="service_max"){
	$vo = $text;
	mysqli_query($connect,"UPDATE services SET service_edit='false', service_max='$vo' WHERE service_id = $xiz");
	}
unlink("user/$cid.step");
}
}


if(mb_stripos($step, "editXizmatlar-")!==false){
	$xiz = explode("-",$step)[1];
	$ex = explode("-",$step)[2];
	$caid = explode("-",$step)[3];
	if($cid == $admin and isset($text)){
	bot('SendMessage',[
	'chat_id'=>$cid,
	'text'=>"<b> Muvaffaqiyatli o'zgartirildi.</b>",
	'parse_mode'=>'html',
    'reply_markup'=>json_encode([
'inline_keyboard'=>[
[['text'=>"⏪ Orqaga",'callback_data'=>"edits=$xiz=$caid"]],
]
])
]);
	   if($ex=="service_desc"){
		$vo = base64_encode($text);
		mysqli_query($connect,"UPDATE services SET service_desc='$vo' WHERE service_id = $xiz");
		}elseif($ex=="service_name"){
		$vo = base64_encode($text);
		mysqli_query($connect,"UPDATE services SET service_name='$vo' WHERE service_id = $xiz");
		}elseif($ex=="service_id"){
		$vo = $text;
		mysqli_query($connect,"UPDATE services SET service_api='$vo' WHERE service_id = $xiz");
		}elseif($ex=="service_price"){
		$vo = $text;
		mysqli_query($connect,"UPDATE services SET service_edit='false', service_price='$vo' WHERE service_id = $xiz");
		}elseif($ex=="service_min"){
		$vo = $text;
		mysqli_query($connect,"UPDATE services SET service_edit='false', service_min='$vo' WHERE service_id = $xiz");
		}elseif($ex=="service_max"){
		$vo = $text;
		mysqli_query($connect,"UPDATE services SET service_edit='false', service_max='$vo' WHERE service_id = $xiz");
		}
unlink("user/$cid.step");
}
}


if(mb_stripos($data, "delxiz=")!==false){
	$ex = explode("=",$data)[1];
	$ex2 = explode("=",$data)[1];
	$c = mysqli_fetch_assoc(mysqli_query($connect,"SELECT * FROM services WHERE service_id = $ex"));
	bot('deleteMessage',[
	'chat_id'=>$cid2,
	'message_id'=>$mid2,
]);
   bot('sendMessage',[
   'chat_id'=>$cid2,
   'text'=>"<b>".enc("decode",$c['service_name'])."</b> - xizmatini o'chirishga rizimisiz ?
   
<i>Xizmat o'chirilsa qayta tiklash imkoni bo'lmaydi, rozi bo'lsangiz ''🗑 O'chirish'' tugmasini bosing!</i>",
   'parse_mode'=>'html',
   'reply_markup'=>json_encode([
'inline_keyboard'=>[
[['text'=>"🗑 O'chirish",'callback_data'=>"delmat-$ex"]],
[['text'=>"⏪ Orqaga",'callback_data'=>"ordered=$ex=$ex2"]],
]
])
]);
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
'reply_markup'=>$m
]);

}

if((stripos($data,"aosdrder=")!==false)){
del();
$oid=explode("=",$data)[1];
$omin=explode("=",$data)[2];
$omax=explode("=", $data)[3];
$orate=explode("=", $data)[4];
$otype=explode("=", $data)[5];
$prov=explode("=",$data)[6];
$serv=explode("=",$data)[7];
if($otype=="Default" or $otype=="default"){
sms($chat_id,"<b>Kerakli buyurtma miqdorini kiriting:</b>

⏬ Minimal -  $omin
⏫ Maksimal - $omax",$ort);
put("user/$chat_id.step","order=default=sp1");
put("user/$chat_id.params","$oid=$omin=$omax=$orate=$prov=$serv");
put("user/$chat_id.si",$oid);
exit; 
}elseif($otype=="Package") {
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
sms($cid,"⛔️ Yetarli mablag‘ mavjud emas

💰 Narxi: $narxi so‘m
🔢 Buyurtma miqdori: $text ta

Boshqa miqdor kiritib koring:",null);
exit; 
}
}else{
sms($cid,"
⚠️ Buyurtma miqdorini notog’ri kiritilmoqda
 
 ⏬ Minimal -  $p[1]
 ⏫ Maksimal - $p[2]
 
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
$rew = mysqli_fetch_assoc(mysqli_query($connect,"SELECT * FROM users WHERE id = $cid"));
$row = mysqli_fetch_assoc($result);
$qoladi = $rew[balance] - $s[4];
$msid=sms($cid,"
➡️ Malumotlarni o‘qib chiqing:

💵 Balansingiz: ".$rew[balance]." so'm

💰 Buyurtma narxi: $s[4] so‘m
💸 Balansingizda: $qoladi so'm qoladi 

📎 Buyurtma havolasi: $text
$pc


⚠️ Malumotlar to‘g‘ri bo‘lsa (✅ Buyurtma berish) tugmasiga bosing va sizning xisobingizdan $s[4] so‘m miqdorda pul yechib olinadi va buyurtma yuboriladi
buyurtmani bekor qilish imkoni bo'lmaydi",json_encode([
'inline_keyboard'=>[
[['text'=>"✅ Buyurtma berish",'callback_data'=>"ccheckorderr=".uniqid()]],
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

Hisobingizni to‘ldirib qayta urinib koring.",$ort);
}
}

$sc=explode("=",get("user/$chat_id.step"));
if((stripos($data,"ccheckorderr=")!==false and $sc[0]=="order" and ($sc[1]=="default" or $sc[1]=="package") and $sc[2]=="sp3" and joinchat($chat_id)==1)){
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
del();
if(empty($jid)){
sms("@gramapi_errors","⚠️ Xatolik yuz berdi  

<b>🛍 Xizmat IDsi:</b> <code>".$sp[5]."</code>
<b>👤 Buyurtmachi:</b> <code>$cqid</code>",null);
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
$orders=mysqli_fetch_assoc(mysqli_query($connect,"SELECT * FROM `settings` WHERE id=1"))['ordercount'];
$or =$orders+1;
mysqli_query($connect,"UPDATE settings SET ordercount='$or' WHERE id=1");
$sav = date("Y.m.d H:i:s");
mysqli_query($connect,"INSERT INTO myorder(`order_id`,`user_id`,`retail`,`status`,`service`,`order_create`,`last_check`) VALUES ('$or','$chat_id','$sc[4]','Pending','$sp[5]','$sav','$sav');");
mysqli_query($connect,"INSERT INTO orders(`api_order`,`order_id`,`provider`,`status`) VALUES ('$jid','$or','$sp[4]','Pending');");
mysqli_query($connect,"INSERT INTO neworder(`order_id`,`api_order_id`,`provider`,`user_id`,`retail`,`status`,`service`,`order_create`,`last_check`) VALUES ('$or','$jid','$sp[4]','$chat_id','$sc[4]','Pending','$sp[5]','$sav','$sav');");
$rew = mysqli_fetch_assoc(mysqli_query($connect,"SELECT * FROM users WHERE id = $cid"));
$order = "<b>✅ Buyurtma qabul qilindi!</b>

<b>Buyurtma ID si:</b> <code>$or</code>";
sms($chat_id,$order,null);

$rew = mysqli_fetch_assoc(mysqli_query($connect,"SELECT * FROM users WHERE id = $chat_id"));
$miqdor = $rew['balance']-$sc[4];
mysqli_query($connect,"UPDATE users SET balance=$miqdor WHERE id =$chat_id");
sms("@gramapi_orders","<b>🆕 BOT | Yangi buyurtma:</b> <code>$or</code>

<b>🛍 Xizmat IDsi:</b> <code>".$sp[5]."</code>
<b>💰 Buyurtma narxi:</b> ".$sc[4]." so'm
<b>👤 Buyurtmachi:</b> <a href='tg://user?id=$chat_id'>$chat_id</a>
<b>⏺ Oldingi balansi:</b> ".$rew['balance']."  so'm
<b>➡️ Yangi balansi:</b> $miqdor so'm",null);
exit;
}
}
}


if($_GET['update']=="status"){
echo json_encode(["status"=>true,"cron"=>"Orders status"]);
$mysql=mysqli_query($connect,"SELECT * FROM `neworder`");
while($mys=mysqli_fetch_assoc($mysql)){
$prv=$mys['provider'];
$order=$mys['api_order_id'];
$uorder=$mys['order_id'];
$api_order = $mys['api_order_id'];
$service = $mys['service'];
$order_create = $mys['order_create'];
$mysa=mysqli_fetch_assoc(mysqli_query($connect,"SELECT * FROM `neworder` WHERE order_id=$uorder"));
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
if($status == "Completed"){
mysqli_query($connect,"UPDATE orders SET status='Completed' WHERE order_id=$uorder");
mysqli_query($connect,"UPDATE myorder SET status='Completed', last_check='$sav' WHERE order_id=$uorder");
mysqli_query($connect,"DELETE FROM neworder WHERE order_id = $uorder");
}elseif ($status == "Canceled"){
mysqli_query($connect,"UPDATE orders SET status='Canceled' WHERE order_id=$uorder");
mysqli_query($connect,"UPDATE myorder SET status='Canceled', last_check='$sav' WHERE order_id=$uorder");
mysqli_query($connect,"DELETE FROM neworder WHERE order_id = $uorder");
}}
$error=$j['error'];
if(isset($error)){
$oi = $mys['order_id'];
mysqli_query($connect,"DELETE FROM myorder WHERE order_id = $oi");
}elseif($status=="Completed"){
sms($adm,"✅ Sizning $uorder raqamli buyurtmangiz bajarildi",null);
}elseif($status=="Canceled"){
sms($adm,"⚠️ Sizning $uorder raqamli buyurtmangiz bekor qilindi

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