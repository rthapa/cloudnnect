<?php
include_once("php_includes/check_login_status.php");
require_once("php_includes/feed-query.php");
	// If the page requestor is not logged in, usher them away
if($user_ok != true || $log_username == ""){
	header("location: http://localhost/cloudbaxa/index.php");
    exit();
}
?>
<?php
$totalGroupsCount = 0;
$totalLiked = 0;
$totalUploads = 0;
//logged in users detail
// Select the member from the users table
$sqlLogUser = "SELECT * FROM users WHERE username='$log_username' AND activated='1' LIMIT 1";
$user_query = mysqli_query($db_conx, $sqlLogUser);
$numrows = mysqli_num_rows($user_query);
while ($row = mysqli_fetch_array($user_query, MYSQLI_ASSOC)) {
	$profile_id = $row["id"];
	$gender = $row["gender"];
	$country = $row["country"];
	$email = $row["email"];
	$userlevel = $row["userlevel"];
	$avatar = $row["avatar"];
	$about = $row["about"];
	$signup = $row["signup"];
	$lastlogin = $row["lastlogin"];
	$joindate = strftime("%b %d, %Y", strtotime($signup));
	$lastsession = strftime("%b %d, %Y", strtotime($lastlogin));
	$totalDownloads = $row["totalDownloads"];
	$totalUploads = $row["totalUploads"];
}

//return follow and follower count
//total followers
$sqlFollowCount = "SELECT COUNT(id) FROM follow WHERE following='$log_username' AND accepted='1'";
$queryFollowCount = mysqli_query($db_conx, $sqlFollowCount);
$totalFollowersRow = mysqli_fetch_row($queryFollowCount);
$totalFollowers = $totalFollowersRow[0];

//total following
$sqlFollowingCount = "SELECT COUNT(id) FROM follow WHERE follower='$log_username' AND accepted='1'";
$queryFollowingCount = mysqli_query($db_conx, $sqlFollowingCount);
$totalFollowingRow = mysqli_fetch_row($queryFollowingCount);
$totalFollowing = $totalFollowingRow[0];

//grouplist
$sqlGroup = "SELECT * FROM groups WHERE creator='$log_username' ORDER BY createDate DESC LIMIT 10";
$queryGroup = mysqli_query($db_conx, $sqlGroup);
$groupNumrows = mysqli_num_rows($queryGroup);
$groupList = "";
while ($row = mysqli_fetch_array($queryGroup, MYSQLI_ASSOC)) {
	$groupId = $row["groupId"];
	$groupTitle = $row["groupTitle"];
	$groupAvatar = $row["groupAvatar"];
	$privacy = $row["privacy"];
	$creator = $row["creator"];
	$createDate = $row["createDate"];

	$groupList .= '<li><a href="group.php?gid='.$groupId.'"><img src="Images/cloudConnect.png" width="32px" height="22px">'.$groupTitle.'</a></li>';

	//total group count 
$sqlGroupsCount = "SELECT COUNT(groupId) FROM groups WHERE creator='$log_username'";
$queryGroupsCount = mysqli_query($db_conx, $sqlGroupsCount);
$totalGroupsRow = mysqli_fetch_row($queryGroupsCount);
$totalGroupsCount = $totalGroupsRow[0];

}
//other group that the user is in
//grouplist
$sqlMyGroup = "SELECT * FROM usersgroups WHERE userId='$log_id' ORDER BY createDate DESC LIMIT 10";
$queryMyGroup = mysqli_query($db_conx, $sqlMyGroup);
$statusnumrows = mysqli_num_rows($queryMyGroup);
while ($row1 = mysqli_fetch_array($queryMyGroup, MYSQLI_ASSOC)) {
	$ugId = $row1["ugId"];
	$userId = $row1["userId"];
	$groupIdOther = $row1["groupId"];
	$createDate = $row1["createDate"];

	//fetch the group title
	$sqlGroupTitle = "SELECT * FROM groups WHERE groupId='$groupIdOther' LIMIT 1";
	$queryGroupTitle = mysqli_query($db_conx, $sqlGroupTitle);
	$statusnumrows = mysqli_num_rows($queryGroupTitle);
	while ($row3 = mysqli_fetch_array($queryGroupTitle, MYSQLI_ASSOC)) {
	$groupTitleOther = $row3["groupTitle"];
	}
	$groupList .= '<li><a href="group.php?gid='.$groupIdOther.'"><img src="Images/cloudConnect.png" width="32px" height="22px">'.$groupTitleOther.'</a></li>'; 
}


//totalLikes by logged in user
$sqlLikedCount = "SELECT COUNT(id) FROM likedislike WHERE username='$log_username' AND type='like'";
$queryLikedCount = mysqli_query($db_conx, $sqlLikedCount);
$totalLikedRow = mysqli_fetch_row($queryLikedCount);
$totalLiked = $totalLikedRow[0];

//totalUploads in the database by this user
$sqlUploadCount = "SELECT COUNT(id) FROM file WHERE owner='$log_username'";
$queryUploadCount = mysqli_query($db_conx, $sqlUploadCount);
$totalUploadRow = mysqli_fetch_row($queryUploadCount);
$totalUploads = $totalUploadRow[0];
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

		<div class="feedWrapper">
			<div class="leftbarContent">
				<h5 class="accountHeading"><span>Account</span></h5>
				<ul class="loggedUserDetail">
					<li><a href="user.php?u=<?php echo $log_username ?>"><?php echo $log_username; ?></a><br/></li>
					<li><a href="settings.php"><img src="Images/editLogoSized.png">Edit Profile</a></li>
					<li><a href=""><img src="Images/peopleNoBg.png" width="30px" height="22px">Followers (<?php echo $totalFollowers;?>)</a></li>
					<li><a href=""><img src="Images/peopleNoBg.png" width="30px" height="22px">Following (<?php echo $totalFollowing;?>)</a></li>
					<li><a href="user_uploads.php?uid=<?php echo $log_id;?>"><img src="Images/uploadLogoSized.png" width="30px" height="22px">Uploads (<?php echo $totalUploads;?>)</a></li>
				</ul>
				<h5 class="accountHeading"><span>My Groups (<?php echo $totalGroupsCount;?>)</span></h5>
				<ul class="loggedUserDetail">
					<?php echo $groupList;?>
				</ul>
				<h5 class="accountHeading"><span>My likes (<?php echo $totalLiked;?>)</span></h5>
				<ul class="loggedUserDetail">
				</ul>
			</div>
			<?php include_once("rightBarContent.php"); ?>
			<div class="feedContent">
				<?php 
				$query = new QUERY;
				$query->do_news_feed($profile_id); 
				?>
			</div>
			<!--- include here right bar?? -->
			<div class="clearfix"></div>
		</div>
	</body>
</html>