<?php
require('PHPMailer/class.phpmailer.php');
include("PHPMailer/PHPMailerAutoload.php");

$mail = new PHPMailer();
$name=$_POST['name'];
$phone =$_POST['phone']; 
$email =$_POST['email'];
$message_field =$_POST['message']; 
$formtitle =$_POST['formtitle'];
$company =$_POST['company'];
$country =$_POST['country'];
$goods =$_POST['goods']; 
$goodsArray = json_decode($goods, true);  
$mail->SetFrom('webmaster@sintoms.com', 'Sintoms');
$mail->CharSet = "UTF-8";
$mail->AddAddress('info@sintoms.com');	 
$mail->Subject = $formtitle;              
$message="";
$message .="<table width='100%' border='0' cellspacing='0' cellpadding='8' bordercolor='#CCCCCC' style='font-size:18px;'>";
if($name){
	$message .="<tr><td><strong>Имя</strong></td><td>".$name."</td></tr>";
}
if($phone){
	$message .="<tr><td><strong>Номер телефона</strong></td><td>".$phone."</td></tr>";
}
if($email){
	$message .="<tr><td><strong>E-mail</strong></td><td>".$email."</td></tr>";
}
if($message_field){
	$message .="<tr><td><strong>Сообщение</strong></td><td>".$message_field."</td></tr>";
}
if($_POST['page']){
	$message .="<tr><td><strong>Страница</strong></td><td>".$_POST['page']."</td></tr>";
}
if($_POST['score']){
	$message .="<tr><td><strong>Рейтинг</strong></td><td>".$_POST['score']."</td></tr>";
}
if($_POST['company']){
	$message .="<tr><td><strong>Компания</strong></td><td>".$_POST['company']."</td></tr>";
}
if($_POST['country']){
	$message .="<tr><td><strong>Страна</strong></td><td>".$_POST['country']."</td></tr>";
}
if($goods){
	foreach ($goodsArray as $key => $value) {
		$message .="<tr><td><strong>".$value['title']."</strong></td><td>".$value['count']."</td></tr>";
	}
}
$message .="</table>";
$mail->Body=''.$message;
if(is_array($_FILES)) {
    foreach ($_FILES["attachmentFile"]["name"] as $k => $v) {
        $mail->AddAttachment( $_FILES["attachmentFile"]["tmp_name"][$k], $_FILES["attachmentFile"]["name"][$k] );
    }
}
$mail->IsHTML(true);

if(!$mail->Send()) {
	echo "<p>Ошибка в отправке почты.</p>";
} else {
	echo "<div class='thank'>Ваше обращение успешно отправлено!</div>";
}
?>