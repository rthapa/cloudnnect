<?php
include_once("php_includes/check_login_status.php");
$message = "";
$msg = preg_replace('#[^a-z 0-9.:_()]#i', '', $_GET['msg']);
if($msg == "activation_failure"){
	$message = '<h2>Activation Error</h2> Sorry there seems to have been an issue activating your account at this time. We have already notified ourselves of this issue and we will contact you via email when we have identified the issue.';
} else if($msg == "activation_success"){
	$message = '<h2>Activation Success</h2> Your account is now activated. <a href="index.php">Click here to log in</a>';
} else if ($msg == "'"){ 
	$message = '<h2>Sad old trick .. please go back.</h2>';
} else {
	$message = $msg;
}
?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="UTF-8">
	<title>Feeds</title>
	<link rel="shortcut icon" href="Images/favicon.ico">
	<link rel="stylesheet" type="text/css" href="Styles.css">
	<link rel="stylesheet" type="text/css" href="user-styles.css">
	<link rel="stylesheet" type="text/css" href="feedStyle.css" />
	<script src="js/main.js"></script>
	<script src="js/ajax.js"></script>
</head>
<body>
	<?php
		if($log_username != ''){
		 include_once("php_includes/template_pageTop.php"); 
		}else{
		 include_once("php_includes/template_pageTop_notLogged.php"); 
		}
	?>
	<div class="searchWrapper">
			<div><?php echo $message; ?></div>
	</div>
</body>