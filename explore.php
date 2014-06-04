<?php
include_once("php_includes/check_login_status.php");

$sql = "SELECT * FROM file";
$query = mysqli_query($db_conx, $sql);
$statusnumrows = mysqli_num_rows($query);
?>
<!DOCTYPE html>
<html>
<head>
	<title>Explore</title>
	<link rel="shortcut icon" href="Images/favicon.ico">
	<link rel="stylesheet" type="text/css" href="Styles.css">
	<link rel="stylesheet" type="text/css" href="user-styles.css">
	<link rel="stylesheet" type="text/css" href="feedStyle.css">
	<script src="js/main.js"></script>
	<script src="js/ajax.js"></script>
	<script type="text/javascript">
	</script>
</head>
<body>
<?php
if($log_username != ''){
 include_once("php_includes/template_pageTop.php"); 
}else{
 include_once("php_includes/template_pageTop_notLogged.php"); 
}
?>

<div class="exploreWrapper">
	<div class="explore-main">
		<div class="popular-div">
			<h5>Popular</h5>
		</div>
		<div class="popular-div">
			<h5>Popular</h5>
		</div>
	</div>
	<?php include_once("rightBarContent.php"); ?>
</div>
</body>
</html>
