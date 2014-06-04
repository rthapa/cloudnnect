<?php
session_start();
// If user is logged in, header them away
if(isset($_SESSION["username"])){
	header("location: message.php?msg=You are already logged in");
    exit();
}
?>
<?php
// Ajax calls this NAME CHECK code to execute
if(isset($_POST["usernamecheck"])){ //IF AXAJ requests the 'usernamecheck' via post do this
	include_once("php_includes/db_conx.php");
	$username = preg_replace('#[^a-z0-9]#i', '', $_POST['usernamecheck']);
	$sql = "SELECT id FROM users WHERE username='$username' LIMIT 1";
    $query = mysqli_query($db_conx, $sql); 
    $uname_check = mysqli_num_rows($query);
    if (strlen($username) < 3 || strlen($username) > 16) {
	    echo '<strong style="color:#F00;">3 - 16 characters please</strong>';
	    exit();
    }
	if (is_numeric($username[0])) {
	    echo '<strong style="color:#F00;">must begin with a letter</strong>';
	    exit();
    }
    if ($uname_check < 1) {
	    echo '<strong style="color:#009900;">username available</strong>';
	    exit();
    } else {
	    echo '<strong style="color:#F00;">username taken</strong>';
	    exit();
    }
}
?>
<?php
// Ajax calls this REGISTRATION code to execute
if(isset($_POST["u"])){
	// CONNECT TO THE DATABASE
	include_once("php_includes/db_conx.php");
	// GATHER THE POSTED DATA INTO LOCAL VARIABLES
	$u = preg_replace('#[^a-z0-9]#i', '', $_POST['u']);
	$e = mysqli_real_escape_string($db_conx, $_POST['e']);
	$p = $_POST['p'];
	$g = preg_replace('#[^a-z]#', '', $_POST['g']);
	$c = preg_replace('#[^a-z ]#i', '', $_POST['c']);
	$dob = preg_replace('#[^0-9.]#', '', $_POST['dob']);
	// GET USER IP ADDRESS
    $ip = preg_replace('#[^0-9.]#', '', getenv('REMOTE_ADDR'));
	// DUPLICATE DATA CHECKS FOR USERNAME AND EMAIL
	$sql = "SELECT id FROM users WHERE username='$u' LIMIT 1";
    $query = mysqli_query($db_conx, $sql); 
	$u_check = mysqli_num_rows($query);
	// -------------------------------------------
	$sql = "SELECT id FROM users WHERE email='$e' LIMIT 1";
    $query = mysqli_query($db_conx, $sql); 
	$e_check = mysqli_num_rows($query);
	// FORM DATA ERROR HANDLING
	if($u == "" || $e == "" || $p == "" || $g == "" || $c == "" ||$dob == ""){
		echo "The form submission is missing values.";
        exit();
	} else if ($u_check > 0){ 
        echo "The username you entered is alreay taken";
        exit();
	} else if ($e_check > 0){ 
        echo "That email address is already in use in the system";
        exit();
	} else if (strlen($u) < 3 || strlen($u) > 16) {
        echo "Username must be between 3 and 16 characters";
        exit(); 
    } else if (is_numeric($u[0])) {
        echo 'Username cannot begin with a number';
        exit();
    } else {
	// END FORM DATA ERROR HANDLING
	    // Begin Insertion of data into the database
		// Hash the password and apply your own mysterious unique salt
		/*
		$cryptpass = crypt($p);
		include_once ("php_includes/randStrGen.php");
		$p_hash = randStrGen(20)."$cryptpass".randStrGen(20);
		*/
		$p_hash = md5($p);
		// Add user info into the database table for the main site table
		$sql = "INSERT INTO users (username, email, password, gender, dob, country, ip, signup, lastlogin, notescheck, activated)       
		        VALUES('$u','$e','$p_hash','$g', '$dob', '$c','$ip',now(),now(),now(), '1')";
		$query = mysqli_query($db_conx, $sql); 
		$uid = mysqli_insert_id($db_conx);
		// Establish their row in the useroptions table
		$sql = "INSERT INTO useroptions (id, username, background) VALUES ('$uid','$u','original')";
		$query = mysqli_query($db_conx, $sql);
		// Create directory(folder) to hold each user's files(pics, MP3s, etc.)
		if (!file_exists("user/$u")) {
			mkdir("user/$u", 0755);
		}
		// Email the user their activation link
		$to = "$e";							 
		$from = "no-reply@cloudnnect.com";
		$subject = 'Clounnect Account Activation';
		
		$message = '<!DOCTYPE html><html><head><meta charset="UTF-8"><title>Cloudnnect Message</title></head><body style="margin:0px; font-family:Tahoma, Geneva, sans-serif;"><div style="padding:10px; background:#333; font-size:24px; color:#CCC;"><a href="http://cloudnnect.com"><img src="Images/cloudBoxLogo.png" width="36" height="30" alt="cloudbaxa" style="border:none; float:left;"></a>Cloudbaxa Account Activation</div><div style="padding:24px; font-size:17px;">Hello '.$u.',<br /><br />Click the link below to activate your account when ready:<br /><br /><a href="http://cloudbaxa.site90.com/activation.php?id='.$uid.'&u='.$u.'&e='.$e.'&p='.$p_hash.'">Click here to activate your account now</a><br /><br />Login after successful activation using your:<br />* E-mail Address: <b>'.$e.'</b></div></body></html>';
		
		$headers = "From: $from\n";
        $headers .= "MIME-Version: 1.0\n";
        $headers .= "Content-type: text/html; charset=iso-8859-1\n";
		if(mail($to, $subject, $message, $headers));
		echo "signup_success";
		exit();
	}
	exit();
}
?>
<!DOCTYPE html>
<html lang="en">

	<head>
		<title>Register</title>
		<link rel="shortcut icon" href="Images/favicon.ico">
		<link rel="stylesheet" type="text/css" href="Styles.css">
		<script src="js/main.js"></script>
	<script src="js/ajax.js"></script>
	<script>
		function restrict(elem){
			var tf = _(elem);
			var rx = new RegExp;
			if(elem == "email"){
				rx = /[' "]/gi;
			} else if(elem == "username"){
				rx = /[^a-z0-9]/gi;
			}
			tf.value = tf.value.replace(rx, "");
		}
		function emptyElement(x){
			_(x).style.display = "none";
			_(x).innerHTML = "";
		}
		function checkusername(){
			var u = _("username").value;
			if(u != ""){
				_("unamestatus").innerHTML = 'checking ...';
				var ajax = ajaxObj("POST", "register.php");
		        ajax.onreadystatechange = function() {
			        if(ajaxReturn(ajax) == true) {
			        	_("unamestatus").style.display = "inline";
			            _("unamestatus").innerHTML = ajax.responseText;
			        }
		        }
		        ajax.send("usernamecheck="+u);
			}
		}

		function signup(){
			var u = _("username").value;
			var e = _("email").value;
			var p1 = _("pass1").value;
			var p2 = _("pass2").value;
			//var c = _("country").value;
			var g = _("gender").value;
			var c = _("country").value;
			var dob = _("dob").value;
			var status = _("status");
			if(u == "" || e == "" || p1 == "" || p2 == "" || g == "" || c=="" || dob==""){
				_("status").style.display = "inline-block";
				status.innerHTML = "Fill out all of the form data";
			} else if(p1 != p2){
				_("status").style.display = "inline-block";
				status.innerHTML = "Your password fields do not match";
			} else {
				_("signupbtn").style.display = "none";
				status.innerHTML = 'please wait ...';
				var ajax = ajaxObj("POST", "register.php");
		        ajax.onreadystatechange = function() {
			        if(ajaxReturn(ajax) == true) {
			            if(ajax.responseText != "signup_success"){
							status.innerHTML = ajax.responseText;
							_("signupbtn").style.display = "block";
						} else {
							window.scrollTo(0,0);
							/*
							_("signupform").innerHTML = "OK "+u+", check your email inbox and junk mail box at <u>"+e+"</u> in a moment to complete the sign up process by activating your account. You will not be able to do anything on the site until you successfully activate your account.";
							*/
							_("signupform").innerHTML = "This site is on test so no activation is required. </br> You can log in now.";					
						}
			        }
		        }
		        ajax.send("u="+u+"&e="+e+"&p="+p1+"&g="+g+"&c="+c+"&dob="+dob);
			}
		}
	</script>
	</head>
	
	<body style="background-image:url('Images/bgGrad.jpg');">
		<nav class="navigation-wrapper"> <!--Navigation-->
	
		<div class="navigation">
				
				<div class="logo">
					<a href="index.php"><img src="Images/cloudBoxLogo.png" alt="cloudbaxa-logo" height="40" width="50"></a>
				</div>

				<form method="GET" action="search.php" id="search">
						<input name="search" type="text" size="40" placeholder="Search..." />
				 </form>
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
							<a href="index.php"><h1>Log in</h1></a>
							<!--<h4><a href="register.php">Sign up</a></h4>-->
						</div>
				</div>
		</div> 
		</nav>
		<!--Navigation END-->
		<section class="af-wrapper">
	            <h3>Register</h3>		
				<form class="af-form" name="signupform" id="signupform" onsubmit="return false;">
					
					
						<div class="af-inner">
							<label>Username</label>
							<input type="text" onblur="checkusername()" onkeyup="restrict('username')" maxlength="16" id="username" placeholder="Your first name">
							<span id="unamestatus"></span>
						</div>
				
						<div class="af-inner">
						  <label>Email address</label>
						  <input id="email" type="text" onfocus="emptyElement('status')" onkeyup="restrict('email')" maxlength="88" placeholder="yourmail@mail.com">
						</div>
					
						<div class="af-inner">
						  <label>Password</label>
						  <input id="pass1" type="password" onfocus="emptyElement('status')" maxlength="100" placeholder="eg: Qol,Kl45k">
						</div>


						<div class="af-inner">
						  <label>Re-type Password</label>
						  <input id="pass2" type="password" onfocus="emptyElement('status')" maxlength="100" placeholder="eg: Qol,Kl45k">
						</div>
					
						<div class="af-inner">
						  <label>Gender</label>
						  <select id="gender" onfocus="emptyElement('status')" class="gender-select">
						  	  <option value=""></option>
							  <option value="m">Male</option>
							  <option value="f">Female</option>
						   </select>
						</div>

						<div class="af-inner">
							<label>Country</label>
							 <?php include_once("php_includes/template_country_list.php"); ?>	
						</div>

						<div class="af-inner">
							<label>Birth Date</label>
							<input type="date" name="birthdate" id="dob" placeholder="MM/DD/YYYY">
						</div>

					<div class="af-inner">
					<span id="status"></span>
					</div>

					<div class="reg-button-div">
					<input id="signupbtn" onclick="signup()" type="submit" value="Sign up" />
					</div>
					
				</form>
		</section>
		<?php include_once("php_includes/page_bottom.php");?>
	</body>
	
</html>