<?php
include_once("../php_includes/check_login_status.php");
if($user_ok != true || $log_username == "") {
	exit();
}
?>
<?php
if (isset($_POST['type']) && isset($_POST['fileId'])){

	$type = $_POST['type'];
	$fileId = $_POST['fileId'];

	//if dislike delet the like entry of this user if exist
	//if like delet the dislike entry of this user if exist

			//total dislikes
	$sqlDislikesCount = "SELECT COUNT(id) FROM likedislike WHERE fileId='$fileId' AND username='$log_username' AND type='dislike' LIMIT 1";
	$queryDislikesCount = mysqli_query($db_conx, $sqlDislikesCount);
	$totalDislikesRow = mysqli_fetch_row($queryDislikesCount);
	$totalDislikes = $totalDislikesRow[0];
	
			//total likes
	$sqlLikesCount = "SELECT COUNT(id) FROM likedislike WHERE fileId='$fileId' AND username='$log_username' AND type='like' LIMIT 1";
	$queryLikesCount = mysqli_query($db_conx, $sqlLikesCount);
	$totalLikesRow = mysqli_fetch_row($queryLikesCount);
	$totalLikes = $totalLikesRow[0];

	if($type == 'like'){

		if($totalDislikes > 0){
			$queryDislikeDelete = "DELETE FROM likedislike WHERE fileId='$fileId' AND username='$log_username' and type='dislike' LIMIT 1";
			mysqli_query($db_conx, $queryDislikeDelete);
		}else if($totalDislikes == 0 && $totalLikes > 0){
			$queryUndoLike = "DELETE FROM likedislike WHERE fileId='$fileId' AND username='$log_username' AND type='like' LIMIT 1";
			mysqli_query($db_conx, $queryUndoLike);
			echo "undo_success|".$fileId;
			exit();
		}

	}else if($type == 'dislike'){

		if($totalLikes > 0){
			$querylikeDelete = "DELETE FROM likedislike WHERE fileId='$fileId' AND username='$log_username' AND type='like' LIMIT 1";
			mysqli_query($db_conx, $querylikeDelete);
		}else if($totalLikes == 0 && $totalDislikes > 0){
			$queryUndoDislike = "DELETE FROM likedislike WHERE fileId='$fileId' AND username='$log_username' AND type='dislike' LIMIT 1";
			mysqli_query($db_conx, $queryUndoDislike);
			echo "undo_success|".$fileId;
			exit();
		}

	}

	$query = "INSERT INTO likedislike (username, fileId, type, dateAdded) 
	VALUES('$log_username', '$fileId', '$type' , now())";
	mysqli_query($db_conx, $query);

	if($type == "like"){
		echo "like_success|".$fileId;
		exit();	
	}else{
		echo "unlike_success|".$fileId;
		exit();
	}

}
?>