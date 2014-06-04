<?php
$to = "rthapa90gmail.com";
$subject = "HTML email";

$message = "Testing php mail";

// Always set content-type when sending HTML email
$headers = "MIME-Version: 1.0" . "\r\n";
$headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";

// More headers
$headers .= 'From: <no-reply@cloudnnect.com>' . "\r\n";

$sent = mail($to,$subject,$message,$headers);
if($sent){
	echo 'success';
}else{
	echo 'failed';
}
?>