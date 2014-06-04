<?php
include_once("php_includes/check_login_status.php");
if($user_ok == true){
	header("location: user.php?u=".$_SESSION["username"]);
	exit();
}
?>
<?php
// AJAX CALLS THIS LOGIN CODE TO EXECUTE
if(isset($_POST["e"])){
	// CONNECT TO THE DATABASE
	include_once("php_includes/db_conx.php");
	// GATHER THE POSTED DATA INTO LOCAL VARIABLES AND SANITIZE
	$e = mysqli_real_escape_string($db_conx, $_POST['e']);
	$p = md5($_POST['p']);
	// GET USER IP ADDRESS
    $ip = preg_replace('#[^0-9.]#', '', getenv('REMOTE_ADDR'));
	// FORM DATA ERROR HANDLING
	if($e == "" || $p == ""){
		echo "login_failed";
        exit();
	} else {
	// END FORM DATA ERROR HANDLING
		$sql = "SELECT id, username, password FROM users WHERE email='$e' AND activated='1' LIMIT 1";
        $query = mysqli_query($db_conx, $sql);
        $row = mysqli_fetch_row($query);
		$db_id = $row[0];
		$db_username = $row[1];
        $db_pass_str = $row[2];
		if($p != $db_pass_str){
			echo "login_failed";
            exit();
		} else {
			// CREATE THEIR SESSIONS AND COOKIES
			$_SESSION['userid'] = $db_id;
			$_SESSION['username'] = $db_username;
			$_SESSION['password'] = $db_pass_str;
			setcookie("id", $db_id, strtotime( '+30 days' ), "/", "", "", TRUE);
			setcookie("user", $db_username, strtotime( '+30 days' ), "/", "", "", TRUE);
    		setcookie("pass", $db_pass_str, strtotime( '+30 days' ), "/", "", "", TRUE); 
			// UPDATE THEIR "IP" AND "LASTLOGIN" FIELDS
			$sql = "UPDATE users SET ip='$ip', lastlogin=now() WHERE username='$db_username' LIMIT 1";
            $query = mysqli_query($db_conx, $sql);
			echo $db_username;
		    exit();
		}
	}
	exit();
}
?>
<!DOCTYPE html>
<html lang="en">
	<head>
		<!--
		# Author: Rabi Thapa
		# Description: Prototype HTML for cloudBox website.
		-->
		<title> Cloud Box</title>
		<meta charset="utf-8">
		
		<link rel="shortcut icon" href="Images/favicon.ico">
		<link rel="stylesheet" type="text/css" href="Styles.css">
		
		<script src="js/main.js"></script>
		<script src="js/ajax.js"></script>
		<script type="text/javascript">
			function toggleOverlay(){
				var overlay = document.getElementById('overlay');
				var specialBox = document.getElementById('specialBox');
				overlay.style.opacity = .8;
				if(overlay.style.display == "block"){
					overlay.style.display = "none";
					specialBox.style.display = "none";
				} else {
					overlay.style.display = "block";
					specialBox.style.display = "block";
				}
			}
		</script>
		<script>
			function emptyElement(x){
				_(x).innerHTML = "";
			}
			function login(){
				var e = _("email").value;
				var p = _("password").value;
				if(e == "" || p == ""){
					_("status-login").innerHTML = "Fill out all of the form data";
				} else {
					_("loginbtn").style.display = "none";
					_("status-login").innerHTML = 'please wait ...';
					var ajax = ajaxObj("POST", "index.php");
			        ajax.onreadystatechange = function() {
				        if(ajaxReturn(ajax) == true) {
				            if(ajax.responseText == "login_failed"){
								_("status-login").innerHTML = "Login unsuccessful, please try again.";
								_("loginbtn").style.display = "block";
							} else {
								window.location = "user.php?u="+ajax.responseText;
							}
				        }
			        }
			        ajax.send("e="+e+"&p="+p);
				}
			}
		</script>
	</head>
	<body>
	<nav class="navigation-wrapper"> <!--Navigation-->
	
		<div class="navigation">
				
				<div class="logo">
					<a href="index.html"><img src="Images/cloudBoxLogo.png" alt="cloudbaxa-logo" height="40" width="50"></a>
				</div>
				<!--
				<ul class="menu">
					<li><a href="#" id="active">Home</a></li>
					<li><a href="#">Login</a></li>
					<li><a href="register.html">Register</a></li>
					<li><a href="#">Support</a></li>
				</ul>
				-->
				<div class="nav-right">
						<div class="cl-effect-1">
							<a href="register.php"><h1>Sign up</h1></a>
							<!--<h4><a href="register.php">Sign up</a></h4>-->
						</div>
				</div>
		</div> 
	</nav> 
	<!--Navigation END-->
							

	<div class="login-new">
		<div class="container">					
			<section class="main">
				<div class="bannerLogo">
					<img src="Images/cloudboxoBannerLogo.png" alt="cloudbaxa-logo" height="180" width="180">
				</div>
				<form class="form-login clearfix" id="loginform" onsubmit="return false;">
					<div class="login-status"> 
				   		 <p id="status-login">Oops wrong email or password. Please try again.</p>
					</div>
				    <p>
				        <input type="text" id="email" maxlength="88" placeholder="Email">
				        <input type="password" id="password" maxlength="100" placeholder="Password"> 
				    </p>
				    
				    <button type="submit" id="loginbtn" onclick="login()">
				    	<i class="icon-arrow-right"><img src="Images/cloudBoxLogoDark.png"></i>
				    	<span onclick="login()">Log in</span>
				    </button>    
				    
				    <h4><a href="#">forgot your password?</a></h4>
				    <h4><a href="register.php">Sign up for new account?</a></h4>
				</form>​​​​
			</section>
			<svg id="clouds-login-fail" xmlns="http://www.w3.org/2000/svg" version="1.1" width="100%" height="100" viewBox="0 0 100 100" preserveAspectRatio="none">
				<path d="M-5 100 Q 0 20 5 100 Z
						 M0 100 Q 5 0 10 100
						 M5 100 Q 10 30 15 100
						 M10 100 Q 15 10 20 100
						 M15 100 Q 20 30 25 100
						 M20 100 Q 25 -10 30 100
						 M25 100 Q 30 10 35 100
						 M30 100 Q 35 30 40 100
						 M35 100 Q 40 10 45 100
						 M40 100 Q 45 50 50 100
						 M45 100 Q 50 20 55 100
						 M50 100 Q 55 40 60 100
						 M55 100 Q 60 60 65 100
						 M60 100 Q 65 50 70 100
						 M65 100 Q 70 20 75 100
						 M70 100 Q 75 45 80 100
						 M75 100 Q 80 30 85 100
						 M80 100 Q 85 20 90 100
						 M85 100 Q 90 50 95 100
						 M90 100 Q 95 25 100 100
						 M95 100 Q 100 15 105 100 Z">
				</path>
			</svg>
        </div>
	</div>						<!--Top Banner Wrapper END-->
							<!--Second Banner end-->
	<div class="what-cloudboxo">
		<img src="Images/whatcloud.png" alt="cloudboxo-what" height="40" width="60">
		<h4> What is Cloudbaxa?</h4>
	</div>
	<div class="separator">
		<hr class="separator-bevel" />	
	</div>
		
	<div class="offerBanner">		<!--Second Banner Wrapper-->
		<div class="cloud-promo">
			<img src="Images/cloudConnect.png">
			<h4>Connect with people</h4>
			<p>Find people with similar taste who loves to share their stuff to the cloud.</p>
		</div>
		
		<div class="cloud-promo">
			<img src="Images/cloudDownload.png">
			<h4>Swift Uploads and Downloads</h4>
			<p>Download or upload files to the cloud so your friends and fans can excess it.</p>
		</div>
		
		<div class="cloud-promo">
			<img src="Images/cloudHobby.png">
			<h4>Your personality</h4>
			<p>Use the cloud to create a profile that suits your personality. Show off your product, art, blog, music or video with the cloud.</p>
		</div>
		
		<div class="cloud-promo">
			<img src="Images/cloudPhone.png">
			<h4>Cloud access</h4>
			<p>Either you own a smart phone, notebook or a tablet, now you can access cloudbaxa from any device.</p>
		</div>
	</div>							<!--Second Banner end-->

	<?php include_once("php_includes/page_bottom.php"); ?>

	<div class="footerTest">		
	</div>
	
	<div class="md-overlay"></div>
	<script src="js/classie.js"></script>
	<script src="js/modalEffects.js"></script>
</body>
</html>