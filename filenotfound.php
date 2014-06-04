<?php
include_once("php_includes/check_login_status.php");
?>
<html>
<head>
	<link rel="shortcut icon" href="Images/favicon.ico">
	<link rel="stylesheet" type="text/css" href="Styles.css">
</head>

<body>
<?php
if($log_username != ''){
 include_once("php_includes/template_pageTop.php"); 
}else{
 include_once("php_includes/template_pageTop_notLogged.php"); 
}
?>
<div class="fnotfWrapper">
<h4> oops something went wrong </h4>
</body>
</body>
</html>