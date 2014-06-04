<?php
include_once("php_includes/check_login_status.php");
// If user is already logged in, header that weenis away
if($user_ok == true){
	header("location: user.php?u=".$_SESSION["username"]);
    exit();
}
?><?php
// AJAX CALLS THIS CODE TO EXECUTE
if(isset($_POST["e"])){
	$e = mysqli_real_escape_string($db_conx, $_POST['e']);
	$sql = "SELECT id, username FROM users WHERE email='$e' AND activated='1' LIMIT 1";
	$query = mysqli_query($db_conx, $sql);
	$numrows = mysqli_num_rows($query);
	if($numrows > 0){
		while($row = mysqli_fetch_array($query, MYSQLI_ASSOC)){
			$id = $row["id"];
			$u = $row["username"];
		}
		$emailcut = substr($e, 0, 4);
		$randNum = rand(10000,99999);
		$tempPass = "$emailcut$randNum";
		$hashTempPass = md5($tempPass);
		$sql = "UPDATE useroptions SET temp_pass='$hashTempPass' WHERE username='$u' LIMIT 1";
	    $query = mysqli_query($db_conx, $sql);
		$to = "$e";
		$from = "noreply@cloudbaxa.site90.com";
		$headers ="From: $from\n";
		$headers .= "MIME-Version: 1.0\n";
		$headers .= "Content-type: text/html; charset=iso-8859-1 \n";
		$subject ="Cloudnnect Temporary Password";
		$msg = '<h2>Hello '.$u.'</h2><p>This is an automated message from Cloudnnect. If you did not recently initiate the Forgot Password process, please disregard this email.</p><p>You indicated that you forgot your login password. We can generate a temporary password for you to log in with, then once logged in you can change your password to anything you like.</p><p>After you click the link below your password to login will be:<br /><b>'.$tempPass.'</b></p><p><a href="http://www.yoursite.com/forgot_pass.php?u='.$u.'&p='.$hashTempPass.'">Click here now to apply the temporary password shown below to your account</a></p><p>If you do not click the link in this email, no changes will be made to your account. In order to set your login password to the temporary password you must click the link above.</p>';
		if(mail($to,$subject,$msg,$headers)) {
			echo "success";
			exit();
		} else {
			echo "email_send_failed";
			exit();
		}
    } else {
        echo "no_exist";
    }
    exit();
}
?><?php
// EMAIL LINK CLICK CALLS THIS CODE TO EXECUTE
if(isset($_GET['u']) && isset($_GET['p'])){
	$u = preg_replace('#[^a-z0-9]#i', '', $_GET['u']);
	$temppasshash = preg_replace('#[^a-z0-9]#i', '', $_GET['p']);
	if(strlen($temppasshash) < 10){
		exit();
	}
	$sql = "SELECT id FROM useroptions WHERE username='$u' AND temp_pass='$temppasshash' LIMIT 1";
	$query = mysqli_query($db_conx, $sql);
	$numrows = mysqli_num_rows($query);
	if($numrows == 0){
		header("location: message.php?msg=There is no match for that username with that temporary password in the system. We cannot proceed.");
    	exit();
	} else {
		$row = mysqli_fetch_row($query);
		$id = $row[0];
		$sql = "UPDATE users SET password='$temppasshash' WHERE id='$id' AND username='$u' LIMIT 1";
	    $query = mysqli_query($db_conx, $sql);
		$sql = "UPDATE useroptions SET temp_pass='' WHERE username='$u' LIMIT 1";
	    $query = mysqli_query($db_conx, $sql);
	    header("location: login.php");
        exit();
    }
}
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Forgot Password</title>
<link rel="icon" href="favicon.ico" type="image/x-icon">
<style type="text/css">
#forgotpassform{
	margin-top:24px;	
	font-size:14px;
	color:grey;
}
#forgotpassform > div {
	margin-top: 12px;	
}
#forgotpassform > input {
	width: 250px;
	padding: 3px;
	background: #F3F9DD;
}
#forgotpassbtn {
background-image: -moz-linear-gradient(top, #ffffff, #dbdbdb);
background-image: -webkit-gradient(linear,left top,left bottom,
    color-stop(0, #ffffff),color-stop(1, #dbdbdb));
filter: progid:DXImageTransform.Microsoft.gradient
    (startColorStr='#ffffff', EndColorStr='#dbdbdb');
-ms-filter: "progid:DXImageTransform.Microsoft.gradient
    (startColorStr='#ffffff', EndColorStr='#dbdbdb')";
border: 1px solid #fff;
-moz-box-shadow: 0px 0px 4px rgba(0, 0, 0, 0.4);
-webkit-box-shadow: 0px 0px 4px rgba(0, 0, 0, 0.4);
box-shadow: 0px 0px 4px rgba(0, 0, 0, 0.4);
border-radius: 2px;
-webkit-border-radius: 2px;
-moz-border-radius: 2px;
padding: 2px 15px;
text-decoration: none;
margin-right: 15px;
margin-bottom: 15px;

color: grey;
line-height: 24px;
font-size: 12px;
font-weight: bold;
border: 1px solid #979797;
}
#forgotpassbtn:hover {
background-image: -moz-linear-gradient(top, #ffffff, #eeeeee);
background-image: -webkit-gradient(linear,left top,left bottom,
    color-stop(0, #ffffff),color-stop(1, #eeeeee));
filter: progid:DXImageTransform.Microsoft.gradient
    (startColorStr='#ffffff', EndColorStr='#eeeeee');
-ms-filter: "progid:DXImageTransform.Microsoft.gradient
    (startColorStr='#ffffff', EndColorStr='#eeeeee')";
color: grey;
cursor: pointer;
}
#forgotpassbtn:active {
background-image: -moz-linear-gradient(top, #dbdbdb, #ffffff);
background-image: -webkit-gradient(linear,left top,left bottom,
    color-stop(0, #dbdbdb),color-stop(1, #ffffff));
filter: progid:DXImageTransform.Microsoft.gradient
    (startColorStr='#dbdbdb', EndColorStr='#ffffff');
-ms-filter: "progid:DXImageTransform.Microsoft.gradient
    (startColorStr='#dbdbdb', EndColorStr='#ffffff')";
text-shadow: 0px -1px 0 rgba(255, 255, 255, 0.5);
}

.pageMiddle{
	width: 780px;
	min-height: 800px;
	margin: 100px auto 30px auto;
	background: #fff;
	text-align: center;
	position: relative;
	/*box-shadow: 1px 2px 4px rgba(0,0,0,0.2);*/
	box-shadow: -8px 8px 4px -2px rgba(0,0,0,0.6);
}
.pageMiddle h3{
	padding:20px 30px 20px 30px;
	background-color:#444;
	color: white;
	text-align: center;
}
</style>
<script src="js/main.js"></script>
<script src="js/ajax.js"></script>
<link rel="stylesheet" type="text/css" href="Styles.css">
<script>
function forgotpass(){
	var e = _("email").value;
	if(e == ""){
		_("forgotPassStatus").innerHTML = "Please type your email address first";
	} else {
		_("forgotpassbtn").style.display = "none";
		_("forgotPassStatus").innerHTML = 'please wait ...';
		var ajax = ajaxObj("POST", "forgot_pass.php");
        ajax.onreadystatechange = function() {
	        if(ajaxReturn(ajax) == true) {
				var response = ajax.responseText;
				if(response == "success"){
					_("forgotpassform").innerHTML = '<h4>Check your email inbox in a few minutes</h4>';
				} else if (response == "no_exist"){
					_("forgotPassStatus").innerHTML = "Sorry that email address is not in our system";
				} else if(response == "email_send_failed"){
					_("forgotPassStatus").innerHTML = "Mail function failed to execute";
				} else {
					_("forgotPassStatus").innerHTML = "An unknown error occurred";
				}
	        }
        }
        ajax.send("e="+e);
	}
}
</script>
</head>
<body style="background-image:url('Images/bgGrad.jpg');">
<?php include_once("php_includes/template_pageTop_notLogged.php");  ?>
<div class="pageMiddle">
  <h3>forgot password</h3>
  <form id="forgotpassform" onsubmit="return false;">
    <input id="email" type="text" placeholder="Enter your registered email" onfocus="_('forgotPassStatus').innerHTML='';" maxlength="88">
    <br /><br />
    <button id="forgotpassbtn" onclick="forgotpass()">Generate Temporary Log In Password</button> 
    <p id="forgotPassStatus"></p>
  </form>
</div>
</body>
</html>