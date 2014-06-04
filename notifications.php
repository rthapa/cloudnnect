
<?php
include_once("php_includes/check_login_status.php");
// If the page requestor is not logged in, usher them away
if($user_ok != true || $log_username == ""){
	header("location: index.php");
    exit();
}
$notification_list = "";
$sql = "SELECT * FROM notifications WHERE username LIKE BINARY '$log_username' ORDER BY date_time DESC";
$query = mysqli_query($db_conx, $sql);
$numrows = mysqli_num_rows($query);
if($numrows < 1){
	$notification_list = "No other notifications";
} else {
	while ($row = mysqli_fetch_array($query, MYSQLI_ASSOC)) {
		$noteid = $row["id"];
		$initiator = $row["initiator"];
		$app = $row["app"];
		$note = $row["note"];
		$date_time = $row["date_time"];
		$date_time = strftime("%b %d, %Y", strtotime($date_time));
		$notification_list .= "<p class='notify-para'><a href='user.php?u=$initiator'>$initiator</a> | $app<br />$note</p>";
	}
}
mysqli_query($db_conx, "UPDATE users SET notescheck=now() WHERE username='$log_username' LIMIT 1");
?><?php
$follow_requests = "";
$sql = "SELECT * FROM follow WHERE following='$log_username' AND accepted='0' ORDER BY datemade ASC";
$query = mysqli_query($db_conx, $sql);
$numrows = mysqli_num_rows($query);
if($numrows < 1){
	$follow_requests = '<h3>No follow requests</h3>';
} else {
	while ($row = mysqli_fetch_array($query, MYSQLI_ASSOC)) {
		$reqID = $row["id"];
		$user1 = $row["follower"];
		$datemade = $row["datemade"];
		$datemade = strftime("%B %d", strtotime($datemade));
		$thumbquery = mysqli_query($db_conx, "SELECT avatar FROM users WHERE username='$user1' LIMIT 1");
		$thumbrow = mysqli_fetch_row($thumbquery);
		$user1avatar = $thumbrow[0];
		$user1pic = '<img src="user/'.$user1.'/'.$user1avatar.'" alt="'.$user1.'" class="user_pic">';
		if($user1avatar == NULL){
			$user1pic = '<img src="images/avatardefault.jpg" alt="'.$user1.'" class="user_pic">';
		}
		$follow_requests .= '<div id="friendreq_'.$reqID.'" class="friendrequests">';
		$follow_requests .= '<a href="user.php?u='.$user1.'">'.$user1pic.'</a>';
		$follow_requests .= '<div class="user_info" id="user_info_'.$reqID.'"><a href="user.php?u='.$user1.'">'.$user1.'</a> wants to follow you.<br />'.$datemade.'<br />';
		$follow_requests .= '<button id="followAcceptBtn" onclick="followReqHandler(\'accept\',\''.$reqID.'\',\''.$user1.'\',\'user_info_'.$reqID.'\')">accept</button> ';
		$follow_requests .= '<button id="followAcceptBtn" onclick="followReqHandler(\'reject\',\''.$reqID.'\',\''.$user1.'\',\'user_info_'.$reqID.'\')">reject</button>';
		$follow_requests .= '</div>';
		$follow_requests .= '</div>';
	}
}

//group request
$group_requests = "";
$sqlGroupReq = "SELECT * FROM usersgroups WHERE userId='$log_id' AND accepted='0' ORDER BY createDate ASC";
$queryGroupReq = mysqli_query($db_conx, $sqlGroupReq);
$numrows = mysqli_num_rows($queryGroupReq);
if($numrows < 1){
	$group_requests = '<h3>No Groups requests</h3>';
} else {
	while ($row = mysqli_fetch_array($queryGroupReq, MYSQLI_ASSOC)) {
		$ugId = $row["ugId"];
		$userId = $row["userId"];
		$groupId = $row["groupId"];
		$createDate = $row["createDate"];
		$createDate = strftime("%B %d", strtotime($createDate));

		//fetch the group name and description
		$sqlGroupDetail = "SELECT * FROM groups WHERE groupId='$groupId' LIMIT 1";
		$queryGroupDetail = mysqli_query($db_conx, $sqlGroupDetail);
		while($row1 = mysqli_fetch_array($queryGroupDetail, MYSQLI_ASSOC)){
			$groupTitle = $row1["groupTitle"];
			$groupDesc = $row1["groupDesc"];
		}

		$group_requests .= '
						<div id="groupRequests">
							<h3>'.$groupTitle.'</h3>
							<div id="groupReqElem_'.$ugId.'">
								<p>'.$groupDesc.'</p>
								<button id="followAcceptBtn" onclick="groupReqHandler(\'accept\',\''.$ugId.'\',\'groupReqElem_'.$ugId.'\')">accept</button>
								<button id="followAcceptBtn" onclick="groupReqHandler(\'reject\',\''.$ugId.'\',\'groupReqElem_'.$ugId.'\')">Reject</button>
							</div>
						</div>
		';
		/*
		$follow_requests .= '<div id="friendreq_'.$reqID.'" class="friendrequests">';
		$follow_requests .= '<a href="user.php?u='.$user1.'">'.$user1pic.'</a>';
		$follow_requests .= '<div class="user_info" id="user_info_'.$reqID.'"><a href="user.php?u='.$user1.'">'.$user1.'</a> wants to follow you.<br />'.$datemade.'<br />';
		$follow_requests .= '<button id="followAcceptBtn" onclick="followReqHandler(\'accept\',\''.$reqID.'\',\''.$user1.'\',\'user_info_'.$reqID.'\')">accept</button> or ';
		$follow_requests .= '<button id="followAcceptBtn" onclick="followReqHandler(\'reject\',\''.$reqID.'\',\''.$user1.'\',\'user_info_'.$reqID.'\')">reject</button>';
		$follow_requests .= '</div>';
		$follow_requests .= '</div>';
		*/
	}
}
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Notifications and Friend Requests</title>
<link rel="icon" href="favicon.ico" type="image/x-icon">
<link rel="stylesheet" type="text/css" href="Styles.css">
<link rel="stylesheet" type="text/css" href="user-styles.css">
<link rel="stylesheet" type="text/css" href="feedStyle.css">
<style type="text/css">

</style>
<script src="js/main.js"></script>
<script src="js/ajax.js"></script>
<script type="text/javascript">
function friendReqHandler(action,reqid,user1,elem){
	var conf = confirm("Press OK to '"+action+"' this friend request.");
	if(conf != true){
		return false;
	}
	_(elem).innerHTML = "processing ...";
	var ajax = ajaxObj("POST", "php_parsers/friend_system.php");
	ajax.onreadystatechange = function() {
		if(ajaxReturn(ajax) == true) {
			if(ajax.responseText == "accept_ok"){
				_(elem).innerHTML = "<b>Request Accepted!</b><br />Your are now friends";
			} else if(ajax.responseText == "reject_ok"){
				_(elem).innerHTML = "<b>Request Rejected</b><br />You chose to reject friendship with this user";
			} else {
				_(elem).innerHTML = ajax.responseText;
			}
		}
	}
	ajax.send("action="+action+"&reqid="+reqid+"&user1="+user1);
}
function followReqHandler(action,reqid,user1,elem){
	var conf = confirm("Press OK to '"+action+"' this request.");
	if(conf != true){
		return false;
	}
	_(elem).innerHTML = "processing ...";
	var ajax = ajaxObj("POST", "php_parsers/follow_system.php");
	ajax.onreadystatechange = function() {
		if(ajaxReturn(ajax) == true) {
			if(ajax.responseText == "accept_ok"){
				_(elem).innerHTML = "<h5>Request Accepted!</h5><br />";
			} else if(ajax.responseText == "reject_ok"){
				_(elem).innerHTML = "<b>Request Rejected</b><br />";
			} else {
				_(elem).innerHTML = ajax.responseText;
			}
		}
	}
	ajax.send("action="+action+"&reqid="+reqid+"&user1="+user1);
}
</script>
</head>
<body>
<?php include_once("php_includes/template_pageTop.php"); ?>
<div class="notificationWrapper">
<div id="pageMiddle">
  <!-- START Page Content -->
  <div id="notesBox"><h5>Notifications</h5><?php echo $notification_list; ?></div>
  <div id="groupReqBox">
  	<h5>Group Requests</h5>
  	<?php echo $group_requests; ?>
  </div>
  <div id="friendReqBox"><h5>Follow Requests</h5><?php echo $follow_requests; ?></div>
  <div style="clear:left;"></div>
  <!-- END Page Content -->
</div>
</div>
</body>
</html>