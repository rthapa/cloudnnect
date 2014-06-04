<?php
include_once("php_includes/check_login_status.php");
	// If the page requestor is not logged in, usher them away
if($user_ok != true || $log_username == ""){
	header("location: index.php");
    exit();
}
?>
<?php
$profile_id = "";
$avatar = "";
$about = "";
$email = "";
$gender = "";
$country = "";

$profile_pic = "";
$avatar_form = "";

$sql = "SELECT * FROM users WHERE username='$log_username' AND activated='1' LIMIT 1";
$user_query = mysqli_query($db_conx, $sql);

while ($row = mysqli_fetch_array($user_query, MYSQLI_ASSOC)) {
	$username = $row["username"];
	$profile_id = $row["id"];
	$gender = $row["gender"];
	$country = $row["country"];
	$email = $row["email"];
	$avatar = $row["avatar"];
	$about = $row["about"];
}

//set the profile pic form
	$avatar_form  = '<form id="avatar_form" enctype="multipart/form-data" method="post" action="php_parsers/photo_system.php">';
	$avatar_form .=   '<input  id="choose_file" type="file" name="avatar" required>';
	$avatar_form .= '</form>';

	if($avatar != ""){
		$profile_pic = '<img src="user/'.$username.'/'.$avatar.'" alt="'.$username.'">';
	}else{
		$profile_pic  = '<img src="Images/avatardefault.jpg">';
	}
?>
<?php
// Ajax calls this REGISTRATION code to execute
if(isset($_POST["a"])){
	// CONNECT TO THE DATABASE
	include_once("php_includes/db_conx.php");
	// GATHER THE POSTED DATA INTO LOCAL VARIABLES
	/*
	$a = $_POST['a'];
	$e = mysqli_real_escape_string($db_conx, $_POST['e']);
	$g = preg_replace('#[^a-z]#', '', $_POST['g']);
	*/
	$a = $_POST['a'];
	$e = $_POST['e'];
	$g = $_POST['g'];
	// DUPLICATE DATA CHECKS FOR EMAIL
	$sql = "SELECT id FROM users WHERE email='$e' LIMIT 1";
    $query = mysqli_query($db_conx, $sql); 
	$e_check = mysqli_num_rows($query);
	// if user email is re intered in edit let it pass
	$sqlEmail = "SELECT id FROM users WHERE email='$e' AND id ='$profile_id' LIMIT 1";
	$queryEmail = mysqli_query($db_conx, $sqlEmail);
	$user_e_check = mysqli_num_rows($queryEmail);
	// FORM DATA ERROR HANDLING
	if($a == "" || $e == "" || $g == ""){
		echo "The form submission is missing values.";
        exit();
	}else if ($e_check > 0 && $user_e_check == 0){ 
        echo "That email address is already in use in the system";
        exit();
	} else if (strlen($a) > 160){
        echo "about section characters should not be more than 155";
        exit(); 
    }else {
	// END FORM DATA ERROR HANDLING

		// Add user info into the database table for the main site table
		$sql1 = "UPDATE users SET email = '$e' WHERE id = '$profile_id'";
		$sql2 = "UPDATE users SET gender = '$g' WHERE id = '$profile_id'";
		$sql3 = "UPDATE users SET about = '$a' WHERE id = '$profile_id'";
		/*
		$sql = "INSERT INTO users (email, gender, about)       
		        VALUES('$e','$g','$a')";
		       */
		$query = mysqli_query($db_conx, $sql1); 
		$query = mysqli_query($db_conx, $sql2); 
		$query = mysqli_query($db_conx, $sql3); 
		echo "edit_success";
		exit();
	}
	exit();
}
?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="UTF-8">
	<title>Settings</title>
	<link rel="shortcut icon" href="Images/favicon.ico">
	<link rel="stylesheet" type="text/css" href="Styles.css">
	<link rel="stylesheet" type="text/css" href="user-styles.css">
	<link rel="stylesheet" type="text/css" href="tabs_styles.css" />
	<script src="js/main.js"></script>
	<script src="js/ajax.js"></script>
	<script>
		function edit(){
			var a = _("about").value;
			var e = _("email").value;
			var g = _("gender").value;
			var status = _("statusEdit");
			if(a == "" || e == ""|| g == ""){
				_("statusEdit").style.display = "inline-block";
				status.innerHTML = "cannot be empty";
			}else {

				_("signupbtn").style.display = "none";
				status.innerHTML = 'please wait ...';
				var ajax = ajaxObj("POST", "settings.php");
		        ajax.onreadystatechange = function() {
			        if(ajaxReturn(ajax) == true) {
			            if(ajax.responseText != "edit_success"){
			            	_("statusEdit").style.display = "inline-block";
							status.innerHTML = ajax.responseText;
							_("signupbtn").style.display = "block";
						} else {
							window.scrollTo(0,0);
							_("statusEdit").style.display = "inline-block";
							_("statusEdit").innerHTML = "Sucessfully updated";
							_("signupbtn").style.display = "block";
						}
			        }
		        }
		        ajax.send("a="+a+"&e="+e+"&g="+g);
			}
		}

		
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

		<div class="settingsWrapper">
			<div class="title">
			<h4>Edit profile</h4>
			</div>
			<div id="profile_pic_box_settings" >
	  			<?php echo $profile_pic; ?>
	 		</div>
	 		<div id="profile_pic_upload">
	 			<?php echo $avatar_form; ?>
	 		</div>
			<div class="separator">
				<hr class="separator-bevel" />	
			</div>


			<div class="editForm">
				<form class="af-form-edit" name="signupform" id="signupform" onsubmit="return false;">
		

						<div class="af-inner">
						  <label>About</label>
						  <textarea id="about" rows="10" cols="40" id="about" maxlength="160"  placeholder=""><?php echo $about;?></textarea>
						</div>
					
						<div class="af-inner">
						  <label>Email address</label>
						  <input id="email" type="text" maxlength="88" value="<?php echo $email;?>">
						</div>
					
						<div class="af-inner">
						  <label>Gender</label>
						  <select id="gender" class="gender-select">
						  	  <option value="<?php echo $gender;?>"><?php if($gender == "m"){echo 'Male';}else{echo 'Female';}?></option>
							  <option value="m">Male</option>
							  <option value="f">Female</option>
						   </select>
						</div>

					<div class="af-inner">
					<span id="statusEdit">asdf</span>
					</div>

					<div class="separator">
						<hr class="separator-bevel" />	
					</div>
					<div class="reg-button-div saveDiv">
					<input id="signupbtn" onclick="edit()" type="submit" value="Save" />
					</div>
					
				</form>

			</div>
		</div>
		<script>
		//upload button auto after file has been chosen
		document.getElementById("choose_file").onchange = function() {
		    document.getElementById("avatar_form").submit();
		}
		</script>
	</body>
</html>