<?php
// It is important for any file that includes this file, to have
// check_login_status.php included at its very top.
$envelope = '<img src="Images/note_dead.jpg" width="22" height="12" alt="Notes" title="This envelope is for logged in members">';
$loginLink = '<a href="login.php">Log In</a> &nbsp; | &nbsp; <a href="signup.php">Sign Up</a>';
if($user_ok == true) {
	$sql = "SELECT notescheck FROM users WHERE username='$log_username' LIMIT 1";
	$query = mysqli_query($db_conx, $sql);
	$row = mysqli_fetch_row($query);
	$notescheck = $row[0];
	$sql = "SELECT id FROM notifications WHERE username='$log_username' AND date_time > '$notescheck' LIMIT 1";
	$query = mysqli_query($db_conx, $sql);
	$numrows = mysqli_num_rows($query);
	//for notification number use $numrows = $count; maybe
		if ($numrows == 0) {
		$envelope = '<a href="notifications.php" title="Your notifications and friend requests"><img src="Images/notifyNotActive.png" width="38" height="23" alt="Notes"></a>';
		} else {
		$envelope = '<a href="notifications.php" title="You have new notifications"><img src="Images/notifyActive.png" width="42" height="25" alt="Notes"></a>';
		}
		$loginLink = '<a href="user.php?u='.$log_username.'">'.$log_username.'</a>';
		$logoutLink = '<a href="logout.php">Log Out</a>';
		$dropDownLogo = '<img src="Images/dropDownLogo.png">';
		
}
?>
<?php
//for banner profile pic
$logged_user = $_SESSION['username'];
$sqlLoggedAvatar = "SELECT avatar FROM users WHERE username = '$logged_user' LIMIT 1";
$logged_query = mysqli_query($db_conx, $sqlLoggedAvatar);
while ($row = mysqli_fetch_array($logged_query, MYSQLI_ASSOC)) {
$logged_avatar = $row["avatar"];
}
//check if user has avatar uploaded, if not default avatar
if($logged_avatar==null){
	$profile_pic_banner = '<img src="Images/avatardefault.jpg" alt="'.$logged_user.'" >';
}else{
	$profile_pic_banner = '<img src="user/'.$logged_user.'/'.$logged_avatar.'" alt="'.$logged_user.'" >';
}

?>
<script>
function toggleNavPanel(x){
    var panel = document.getElementById(x), navarrow = document.getElementById("navarrow"), maxH="120px";
    if(panel.style.height == maxH){
        panel.style.height = "0px";
    } else {
        panel.style.height = maxH;
    }
}
</script>
<div class="navigation-wrapper">
	<div class="navigation">
	<div class="logo">
			<a href="feed.php">
				<img src="Images/cloudBoxLogo.png" alt="cloudbaxa-logo" height="40" width="50">
			</a>
			 
	</div>

	 <form method="GET" action="search.php" id="search">
			<input name="search" type="text" size="40" placeholder="Search..." />
	 </form>

	<div class="nav-right">
					 <!--&nbsp; &nbsp; -->
		<div class="ppTopDrop" onclick="toggleNavPanel('dropOptions');">
			<div id="dropOptions" class="dropOptions">
				<a href="user.php?u=<?php echo $log_username; ?>"><h4>View profile</h4></a>
				<a href="settings.php"><h4>Edit Profile</h4></a>
				<a href="user_uploads.php?uid=<?php echo $log_id;?>"><h4>My Uploads</h4></a>
				<a href="logout.php"><h4>Logout</h4></a>
			</div>
		</div>
		<!--
		<div class="ppTop">
			<?php echo $logoutLink; ?>
			
		</div>
		-->

		<div class="ppTopPic">
			<a href="user.php?u=<?php echo $log_username; ?>">
			<?php echo $profile_pic_banner; ?>
			</a>
		</div>
		 <div class="ppTopName">
			<a id="ppTopUsername" href="user.php?u=<?php echo $log_username; ?>">
			<?php echo $log_username; ?>
			</a>
		</div>
		<div class="ppTopNotify">
			<?php echo $envelope; ?>
		</div> 
		<div class="ppTopUpload">
			<a href="upload.php"><h5>upload</h5></a>
		</div>     
	</div>
</div>
</div>
