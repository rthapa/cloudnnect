<?php
include_once("../php_includes/check_login_status.php");
if($user_ok != true || $log_username == "") {
	exit();
}
?><?php
if (isset($_POST['type']) && isset($_POST['user'])){
	$user = preg_replace('#[^a-z0-9]#i', '', $_POST['user']);
	$sql = "SELECT COUNT(id) FROM users WHERE username='$user' AND activated='1' LIMIT 1";
	$query = mysqli_query($db_conx, $sql);
	$exist_count = mysqli_fetch_row($query);
	if($exist_count[0] < 1){
		mysqli_close($db_conx);
		echo "$user does not exist.";
		exit();
	}
	if($_POST['type'] == "follow"){
		//checking if blocked by either user
		$sql = "SELECT COUNT(id) FROM blockedusers WHERE blocker='$user' AND blockee='$log_username' LIMIT 1";
		$query = mysqli_query($db_conx, $sql);
		$blockcount1 = mysqli_fetch_row($query);
		$sql = "SELECT COUNT(id) FROM blockedusers WHERE blocker='$log_username' AND blockee='$user' LIMIT 1";
		$query = mysqli_query($db_conx, $sql);
		$blockcount2 = mysqli_fetch_row($query);
		// checking if already logged user has followed
		$sql = "SELECT COUNT(id) FROM follow WHERE follower='$log_username' AND following='$user' AND accepted='1' LIMIT 1";
		$query = mysqli_query($db_conx, $sql);
		$row_count1 = mysqli_fetch_row($query);
		// chekcing if user has followed logged user. dont think this is necessary ??
		/*
		$sql = "SELECT COUNT(id) FROM follow WHERE follower='$user' AND following='$log_username' AND accepted='1' LIMIT 1";
		$query = mysqli_query($db_conx, $sql);
		$row_count2 = mysqli_fetch_row($query);
		**/
		//checking if logged user's follow has not been accepted 
		$sql = "SELECT COUNT(id) FROM follow WHERE follower='$log_username' AND following='$user' AND accepted='0' LIMIT 1";
		$query = mysqli_query($db_conx, $sql);
		$row_count3 = mysqli_fetch_row($query);
		//ehcking if user's follow has not accepted by logged user.
		$sql = "SELECT COUNT(id) FROM follow WHERE follower='$user' AND following='$log_username' AND accepted='0' LIMIT 1";
		$query = mysqli_query($db_conx, $sql);
		$row_count4 = mysqli_fetch_row($query);

		// checking the follow count of the user
	    if($blockcount1[0] > 0){ // checking if the user has blocked logged user
            mysqli_close($db_conx);
	        echo "$user has you blocked, we cannot proceed.";
	        exit();
        } else if($blockcount2[0] > 0){ // checkin gif the logged user has blocked the user
            mysqli_close($db_conx);
	        echo "You must first unblock $user in order to friend with them.";
	        exit();
        } else if ($row_count1[0] > 0 ) { // checking if the logged user has followed already
		    mysqli_close($db_conx);
	        echo "You are already following $user.";
	        exit();
	    } else if ($row_count3[0] > 0) { // checking if request is already sent and pending
		    mysqli_close($db_conx);
	        echo "You have a pending friend request already sent to $user.";
	        exit();
	    } else if ($row_count4[0] > 0) { // checking if user has already requested and not accepted
		    mysqli_close($db_conx);
	        echo "$user has requested to follow you first. Check your follow requests.";
	        exit();
	    } else {

	    	//get logged user id
	    	$sqlLoggedId = "SELECT id FROM users WHERE username='$log_username' AND activated='1' LIMIT 1";
	    	$queryLoggedId = mysqli_query($db_conx, $sqlLoggedId);
	    	while($row = mysqli_fetch_array($queryLoggedId)){
			$loggedId = $row['id'];
			}

	    	//get following user id
	    	$sqlFollowingId = "SELECT id FROM users WHERE username='$user' AND activated='1' LIMIT 1";
	    	$queryFollowingId = mysqli_query($db_conx, $sqlFollowingId);
	    	while($row = mysqli_fetch_array($queryFollowingId)){
			$followingId = $row['id'];
			}

	    	//fill the table
	        $sql = "INSERT INTO follow(follower, follower_id, following, following_id, datemade) VALUES('$log_username', '$loggedId','$user', '$followingId',now())";
		    $query = mysqli_query($db_conx, $sql);
			mysqli_close($db_conx);
	        echo "follow_request_sent"; //change here -------->
	        exit();
		}
	} else if($_POST['type'] == "unfollow"){
		// check if following or not by the logged user
		$sql = "SELECT COUNT(id) FROM follow WHERE follower='$log_username' AND following='$user' LIMIT 1";
		$query = mysqli_query($db_conx, $sql);
		$row_count1 = mysqli_fetch_row($query);
	
		//unfollow 
	    if ($row_count1[0] > 0) {
	        $sql = "DELETE FROM follow WHERE follower='$log_username' AND following='$user' LIMIT 1";
			$query = mysqli_query($db_conx, $sql);
			mysqli_close($db_conx);
	        echo "unfollow_ok";
	        exit();
	    }else {
			mysqli_close($db_conx);
	        echo "No followship could be found between your account and $user, therefore we cannot unfollow.";
	        exit();
		}
	}
}
?><?php
//accept or decline system
if (isset($_POST['action']) && isset($_POST['reqid']) && isset($_POST['user1'])){
	$reqid = preg_replace('#[^0-9]#', '', $_POST['reqid']);
	$user = preg_replace('#[^a-z0-9]#i', '', $_POST['user1']);
	$sql = "SELECT COUNT(id) FROM users WHERE username='$user' AND activated='1' LIMIT 1";
	$query = mysqli_query($db_conx, $sql);
	$exist_count = mysqli_fetch_row($query);
	if($exist_count[0] < 1){
		mysqli_close($db_conx);
		echo "$user does not exist.";
		exit();
	}
	if($_POST['action'] == "accept"){
		$sql = "SELECT COUNT(id) FROM follow WHERE follower='$user' AND following='$log_username' AND accepted='1' LIMIT 1";
		$query = mysqli_query($db_conx, $sql);
		$row_count1 = mysqli_fetch_row($query);
		
	    if ($row_count1[0] > 0) {
		    mysqli_close($db_conx);
	        echo "following $user already accepted.";
	        exit();
	    } else {
			$sql = "UPDATE follow SET accepted='1' WHERE id='$reqid' AND follower='$user' AND following='$log_username' LIMIT 1";
			$query = mysqli_query($db_conx, $sql);
			mysqli_close($db_conx);
	        echo "accept_ok";
	        exit();
		}
	} else if($_POST['action'] == "reject"){
		mysqli_query($db_conx, "DELETE FROM follow WHERE id='$reqid' AND follower='$user' AND following='$log_username' AND accepted='0' LIMIT 1");
		mysqli_close($db_conx);
		echo "reject_ok";
		exit();
	}
}
?>
