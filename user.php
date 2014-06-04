<?php
include_once("php_includes/check_login_status.php");
// Initialize any variables that the page might echo
$u = "";
$sex = "Male";
$userlevel = "";
$profile_pic = "";
$profile_pic_btn = "";
$avatar_form = "";
$about = "";
$country = "";
$email = "";
$joindate = "";
$lastsession = "";
$totalDownlods = "";
$totalUploads = "";
$totalFollowers = "";
$userRank = "";
// Make sure the _GET username is set, and sanitize it
if(isset($_GET["u"])){
	$u = preg_replace('#[^a-z0-9]#i', '', $_GET['u']);
} else {
    header("location: index.php");
    exit();	
}
// Select the member from the users table
$sql = "SELECT * FROM users WHERE username='$u' AND activated='1' LIMIT 1";
$user_query = mysqli_query($db_conx, $sql);
// Now make sure that user exists in the table
$numrows = mysqli_num_rows($user_query);
if($numrows < 1){
	echo "That user does not exist or is not yet activated, press back";
    exit();	
}
// Check to see if the viewer is the account owner
$isOwner = "no";
if($u == $log_username && $user_ok == true){
	$isOwner = "yes";
	$profile_pic_btn = '<a href="#" onclick="return false;" onmousedown="toggleElement(\'avatar_form\')">Change profile pic</a>';
	$avatar_form  = '<form id="avatar_form" enctype="multipart/form-data" method="post" action="php_parsers/photo_system.php">';
	$avatar_form .=   '<h4 class="insideAvatarForm">Change your profile pic</h4>';
	$avatar_form .=   '<input class="insideAvatarForm" type="file" name="avatar" required>';
	$avatar_form .=   '<input class="insideAvatarForm" type="submit" value="Upload">';
	$avatar_form .= '</form>';
}
// Fetch the user row from the query above
while ($row = mysqli_fetch_array($user_query, MYSQLI_ASSOC)) {
	$profile_id = $row["id"];
	$gender = $row["gender"];
	$country = $row["country"];
	$email = $row["email"];
	$userlevel = $row["userlevel"];
	$userRank = $row["userRank"];
	$avatar = $row["avatar"];
	$about = $row["about"];
	$signup = $row["signup"];
	$lastlogin = $row["lastlogin"];
	$joindate = strftime("%b %d, %Y", strtotime($signup));
	$lastsession = strftime("%b %d, %Y", strtotime($lastlogin));
	$totalDownloads = $row["totalDownloads"];
	$totalUploads = $row["totalUploads"];
}
if($gender == "f"){
	$sex = "Female";
}
//total actual uploads
$sqlCurrUploadsCount = "SELECT COUNT(id) FROM file WHERE owner='$u'";
$queryCurrUploadsCount = mysqli_query($db_conx, $sqlCurrUploadsCount);
$totalCurrUploadsRow = mysqli_fetch_row($queryCurrUploadsCount);
$totalCurrUploads = $totalCurrUploadsRow[0];

//ranks
//color note:
//rookie: #606EBF blue
//contirbutor : #DB8018 brown
//cloudShark: #97C242 green/blue
//cloudPro: #C42D2D red
//legend: 
/*
if($totalCurrUploadsRow[0] >= 0 && $totalCurrUploadsRow[0] <= 4){
	$userRank = "Rookie";
}else if($totalCurrUploadsRow[0] >= 5 && $totalCurrUploadsRow[0] <= 29  ){
	$userRank = "Contributor";
}else if($totalCurrUploadsRow[0] >= 30 && $totalCurrUploadsRow[0] <= 59){
	$userRank = "CloudShark";
}else if($totalCurrUploadsRow[0] >= 60 && $totalCurrUploadsRow[0] <= 99){
	$userRank = "CloudPRO";
}else if($totalCurrUploadsRow[0] >= 100 && $totalCurrUploadsRow[0] <= 499){
	$userRank = "Legend100+";
}else if($totalCurrUploadsRow[0] >= 500 && $totalCurrUploadsRow[0] <= 999){
	$userRank = "Legend500+";
}else if($totalCurrUploadsRow[0] >= 1000){
	$userRank = "Legend1000+";
}
*/
//total followers
$sqlFollowCount = "SELECT COUNT(id) FROM follow WHERE following='$u' AND accepted='1'";
$queryFollowCount = mysqli_query($db_conx, $sqlFollowCount);
$totalFollowersRow = mysqli_fetch_row($queryFollowCount);
$totalFollowers = $totalFollowersRow[0];

//
$profile_pic = '<img src="user/'.$u.'/'.$avatar.'" alt="'.$u.'">';
if($avatar == NULL){
	$profile_pic = '<img src="Images/avatardefault.jpg" alt="'.$u.'">';
}
?>

<?php
// Friend and Follow logic from here ------------------------------------------------------->
$isFriend = false;
$isFollowing = false;
$isFollowPending = false;
$ownerBlockViewer = false;
$viewerBlockOwner = false;
if($u != $log_username && $user_ok == true){
	$friend_check = "SELECT id FROM friends WHERE user1='$log_username' AND user2='$u' AND accepted='1' OR user1='$u' AND user2='$log_username' AND accepted='1' LIMIT 1";
	if(mysqli_num_rows(mysqli_query($db_conx, $friend_check)) > 0){
        $isFriend = true;
    }
	$block_check1 = "SELECT id FROM blockedusers WHERE blocker='$u' AND blockee='$log_username' LIMIT 1";
	if(mysqli_num_rows(mysqli_query($db_conx, $block_check1)) > 0){
        $ownerBlockViewer = true;
    }
	$block_check2 = "SELECT id FROM blockedusers WHERE blocker='$log_username' AND blockee='$u' LIMIT 1";
	if(mysqli_num_rows(mysqli_query($db_conx, $block_check2)) > 0){
        $viewerBlockOwner = true;
    }
    $follow_check = "SELECT id FROM follow WHERE follower='$log_username' AND following='$u' AND accepted='1' LIMIT 1";
    $follow_approve = "SELECT id FROM follow WHERE follower='$log_username' AND following='$u' AND accepted='0' LIMIT 1";
    if(mysqli_num_rows(mysqli_query($db_conx, $follow_check)) > 0){
    	$isFollowing = true;
    }
    if(mysqli_num_rows(mysqli_query($db_conx, $follow_approve)) > 0){
    	$isFollowPending = true;
    }
}
?><?php 
$follow_btn = '';
$friend_button = '<button disabled>Request As Friend</button>';
$block_button = '<button disabled>Block User</button>';
$follow_status = 'disabled';
$follow_text = 'follow';
$follow_onclick = "followToggle('follow', '$u', 'followBtn')";
//logic for follow button
if($isFollowing == true){
	$follow_text = 'following';
	$follow_onclick = "followToggle('unfollow', '$u', 'follow')";
	$follow_status = '';
	$follow_btn = '<button '.$follow_status.' id="follow" onclick="'.$follow_onclick.'">'.$follow_text.'</button>';
}else if($isFollowPending == true){
	$follow_text = 'Follow pending';
	$follow_status = '';
	$follow_onclick = "followToggle('unfollow', '$u', 'follow')";
	$follow_btn = '<button '.$follow_status.' id="follow" onclick="'.$follow_onclick.'">'.$follow_text.'</button>';
}else if($u == $log_username){
	//$follow_text = 'Edit Profile';
	$follow_status = '';
	$follow_btn = '<button '.$follow_status.' id="follow" onclick="window.location.href=\'settings.php\'">Edit profile</button>';
}else{
	$follow_status = '';
	$follow_btn = '<button '.$follow_status.' id="follow" onclick="'.$follow_onclick.'">'.$follow_text.'</button>';
}

//if not logged in
if($log_username == ''){
	$follow_status = '';
	$follow_btn = '<button '.$follow_status.' id="follow" onclick="window.location.href=\'index.php\'">Follow</button>';
}

// LOGIC FOR FRIEND BUTTON
if($isFriend == true){
	$friend_button = '<button onclick="friendToggle(\'unfriend\',\''.$u.'\',\'friendBtn\')">Unfriend</button>';
} else if($user_ok == true && $u != $log_username && $ownerBlockViewer == false){
	$friend_button = '<button onclick="friendToggle(\'friend\',\''.$u.'\',\'friendBtn\')">Request As Friend</button>';
}
// LOGIC FOR BLOCK BUTTON
if($viewerBlockOwner == true){
	$block_button = '<button onclick="blockToggle(\'unblock\',\''.$u.'\',\'blockBtn\')">Unblock User</button>';
} else if($user_ok == true && $u != $log_username){
	$block_button = '<button onclick="blockToggle(\'block\',\''.$u.'\',\'blockBtn\')">Block User</button>';
}
?><?php
$friendsHTML = '';
$friends_view_all_link = '';
$sql = "SELECT COUNT(id) FROM friends WHERE user1='$u' AND accepted='1' OR user2='$u' AND accepted='1'";
$query = mysqli_query($db_conx, $sql);
$query_count = mysqli_fetch_row($query);
$friend_count = $query_count[0];
if($friend_count < 1){
	$friendsHTML = $u." has no friends yet";
} else {
	$max = 18;
	$all_friends = array();
	$sql = "SELECT user1 FROM friends WHERE user2='$u' AND accepted='1' ORDER BY RAND() LIMIT $max";
	$query = mysqli_query($db_conx, $sql);
	while ($row = mysqli_fetch_array($query, MYSQLI_ASSOC)) {
		array_push($all_friends, $row["user1"]);
	}
	$sql = "SELECT user2 FROM friends WHERE user1='$u' AND accepted='1' ORDER BY RAND() LIMIT $max";
	$query = mysqli_query($db_conx, $sql);
	while ($row = mysqli_fetch_array($query, MYSQLI_ASSOC)) {
		array_push($all_friends, $row["user2"]);
	}
	$friendArrayCount = count($all_friends);
	if($friendArrayCount > $max){
		array_splice($all_friends, $max);
	}
	if($friend_count > $max){
		$friends_view_all_link = '<a href="view_friends.php?u='.$u.'">view all</a>';
	}
	$orLogic = '';
	foreach($all_friends as $key => $user){
			$orLogic .= "username='$user' OR ";
	}
	$orLogic = chop($orLogic, "OR ");
	$sql = "SELECT username, avatar FROM users WHERE $orLogic";
	$query = mysqli_query($db_conx, $sql);
	while($row = mysqli_fetch_array($query, MYSQLI_ASSOC)) {
		$friend_username = $row["username"];
		$friend_avatar = $row["avatar"];
		if($friend_avatar != ""){
			$friend_pic = 'user/'.$friend_username.'/'.$friend_avatar.'';
		} else {
			$friend_pic = 'Images/avatardefault.jpg';
		}
		$friendsHTML .= '<a href="user.php?u='.$friend_username.'"><img class="friendpics" src="'.$friend_pic.'" alt="'.$friend_username.'" title="'.$friend_username.'"></a>';
	}
}
?><?php 
$coverpic = "";
$sql = "SELECT filename FROM photos WHERE user='$u' ORDER BY RAND() LIMIT 1";
$query = mysqli_query($db_conx, $sql);
if(mysqli_num_rows($query) > 0){
	$row = mysqli_fetch_row($query);
	$filename = $row[0];
	$coverpic = '<img src="user/'.$u.'/'.$filename.'" alt="pic">';
}
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title><?php echo $u; ?></title>
<link rel="shortcut icon" href="Images/favicon.ico">
<link rel="stylesheet" type="text/css" href="Styles.css">
<link rel="stylesheet" type="text/css" href="user-styles.css">
<link rel="stylesheet" type="text/css" href="tabs_styles.css" />
<script src="js/main.js"></script>
<script src="js/ajax.js"></script>
<script type="text/javascript">
function friendToggle(type,user,elem){
	var conf = confirm("Press OK to confirm the '"+type+"' action for user <?php echo $u; ?>.");
	if(conf != true){
		return false;
	}
	_(elem).innerHTML = 'please wait ...';
	var ajax = ajaxObj("POST", "php_parsers/friend_system.php");
	ajax.onreadystatechange = function() {
		if(ajaxReturn(ajax) == true) {
			if(ajax.responseText == "friend_request_sent"){
				_(elem).innerHTML = 'Friend Request Sent';
			} else if(ajax.responseText == "unfriend_ok"){
				_(elem).innerHTML = '<button onclick="friendToggle(\'friend\',\'<?php echo $u; ?>\',\'friendBtn\')">Request As Friend</button>';
			} else {
				alert(ajax.responseText);
				_(elem).innerHTML = 'Try later';
			}
		}
	}
	ajax.send("type="+type+"&user="+user);
}
function followToggle(type,user,elem){
	var conf = confirm("Press OK to confirm the '"+type+"' action for user <?php echo $u; ?>.");
	if(conf != true){
		return false;
	}
	_('follow').innerHTML = 'please wait..';
	var ajax = ajaxObj("POST", "php_parsers/follow_system.php");
	ajax.onreadystatechange = function() {
		if(ajaxReturn(ajax) == true) {
			if(ajax.responseText == "follow_request_sent"){
				//_(elem).innerHTML = 'Follow pending';
				_(elem).innerHTML = '<button <?php echo $follow_status; ?> id="follow" onclick="followToggle(\'unfollow\', \'<?php echo $u; ?>\', \'followBtn\')">Follow pending</button>';
			} else if(ajax.responseText == "unfollow_ok"){
				//_(elem).innerHTML = 'follow';
				location.reload();
			} else {
				alert(ajax.responseText);
				_('follow').innerHTML = 'Try later';
			}
		}
	}
	ajax.send("type="+type+"&user="+user);
}
function blockToggle(type,blockee,elem){
	var conf = confirm("Press OK to confirm the '"+type+"' action on user <?php echo $u; ?>.");
	if(conf != true){
		return false;
	}
	var elem = document.getElementById(elem);
	elem.innerHTML = 'please wait ...';
	var ajax = ajaxObj("POST", "php_parsers/block_system.php");
	ajax.onreadystatechange = function() {
		if(ajaxReturn(ajax) == true) {
			if(ajax.responseText == "blocked_ok"){
				elem.innerHTML = '<button onclick="blockToggle(\'unblock\',\'<?php echo $u; ?>\',\'blockBtn\')">Unblock User</button>';
			} else if(ajax.responseText == "unblocked_ok"){
				elem.innerHTML = '<button onclick="blockToggle(\'block\',\'<?php echo $u; ?>\',\'blockBtn\')">Block User</button>';
			} else {
				alert(ajax.responseText);
				elem.innerHTML = 'Try again later';
			}
		}
	}
	ajax.send("type="+type+"&blockee="+blockee);
}



</script>
</head>
<body >
<?php
if($log_username != ''){
 include_once("php_includes/template_pageTop.php"); 
}else{
 include_once("php_includes/template_pageTop_notLogged.php"); 
}
?>

	

  <!--
  <div id="photo_showcase" onclick="window.location = 'photos.php?u=<?php echo $u; ?>';" title="view <?php echo $u; ?>&#39;s photo galleries">
    <?php echo $coverpic; ?>
  </div> -->
<div class="bodyWrapper">

	<div class="user-about-wrapper">
	  <div id="profile_pic_box" >
	  	<!--
	  	<?php echo $profile_pic_btn; ?><?php echo $avatar_form; ?>
	  	-->
	  	<?php echo $profile_pic; ?>
	  </div>
	  <h4><?php echo $u; ?></h4>
	  <p id="rank"><?php echo $userRank;?></p>
	  <p><?php echo $about; ?></p>
	  <!--<h2><?php echo $u; ?></h2>-->
	  <!--
	  <div class="profile-stats">
	  	<div>
	  		<h3>24</h3>
	  		<h4>Follwers</h4>
	  	</div>
	  	<div>
	  		<h3>27</h3>
	  		<h4>Following</h4>
	  	</div>
	  </div>
		-->
	   <div class="user-about">
	  	  <a href="#"><span id="followBtn"><?php echo $follow_btn;?></span></a>
	  	  <div>
		  	  <div class="stats"><img src="Images/connectLogo.png "><h3><?php echo $totalFollowers;?></h3></div>
		  	  <div class="stats"><img src="Images/downloadLogo.png "><h3><?php echo $totalDownloads;?></h3></div>
		  	  <div class="stats"><a href="user_uploads.php?uid=<?php echo $profile_id;?>"><img src="Images/uploadLogo.png "><h3><?php echo $totalCurrUploads;?></h3></a></div>
	  	  </div>
	  	  <!--
		  <p>Is the viwer owner aswell: <b><?php echo $isOwner; ?></b></p>
		  <p>Mood: # </p>
		  <p>Gender: <?php echo $sex; ?></p>
		  <p>Country: <?php echo $country; ?></p>
		  <p>User Level: <?php echo $userlevel; ?></p>
		  <p>Join Date: <?php echo $joindate; ?></p>
		  <p>Last Session: <?php echo $lastsession; ?></p>
		  <a href="#"><h4 id="follow">Youtube</h4></a>
		  <a href="#"><h4 id="follow">Facebook</h4></a>
		  
		  <p>fr btn: <span id="friendBtn"><?php echo $friend_button; ?></br></span> <?php echo $u." has ".$friend_count." friends"; ?> <?php echo $friends_view_all_link; ?></p>
		  <p>Block Button: <span id="blockBtn"><?php echo $block_button; ?></span></p>
		  <p><?php echo $friendsHTML; ?></p>
			-->
	  </div>
	</div>

	<div class="main-body">
		<div class="profile-nav">
			<a href="upload.php"><h4><img src="Images/arrowUp.png" width="20" height="20">Upload</h4></a>
		</div>

		<!--<div class="statusDiv"> -->
			<!--template_status.php" -->
		<?php include_once("template_uploads.php"); ?>
	</div>
</div>
</body>
</html>