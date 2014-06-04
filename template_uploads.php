<?php
include_once("php_includes/check_login_status.php");
$uploadlist = "";
$groupList = "";
$totalGroupsCount="";
$keywordList ="";

if($isOwner == "yes"){
	
} else if($isFriend == true && $log_username != $u){

}
?>
<?php 

$sql = "SELECT * FROM file WHERE owner='$u' AND type='a' ORDER BY uploaddate DESC LIMIT 20";
$query = mysqli_query($db_conx, $sql);
$statusnumrows = mysqli_num_rows($query);
while ($row = mysqli_fetch_array($query, MYSQLI_ASSOC)) {
	$fileid = $row["id"];
	$fileName = $row["fileName"];
	$url = $row["url"];
	$file_type = $row["file_type"];
	$file_owner = $row["owner"];
	$file_desc = $row["description"];
	$keywords = $row["keywords"];
	$uploaddate = $row["uploaddate"];
	$totalDownloads = $row["totalDownloads"];

	$keywordListArray = explode(" ", $keywords);

	if($keywords !=""){
		foreach($keywordListArray as $k){

			if($k != ""){
	    		$keywordList .= "<a href='search.php?search=$k'><h5>$k</h5></a>";
	   		}
		}
	}else{
		$keywordList = "";
	}
	/*get user profile pic **
	$query2 = "SELECT avatar FROM users WHERE username='$file_owner'";
	$uploader_pic = mysqli_query($db_conx, $query2);
	*/

//total likes 
$sqlLikesCount = "SELECT COUNT(id) FROM likedislike WHERE fileId='$fileid' AND type='like'";
$queryLikesCount = mysqli_query($db_conx, $sqlLikesCount);
$totalLikesRow = mysqli_fetch_row($queryLikesCount);
$totalLikes = $totalLikesRow[0];

//total dislikes
$sqlDislikesCount = "SELECT COUNT(id) FROM likedislike WHERE fileId='$fileid' AND type='dislike'";
$queryDislikesCount = mysqli_query($db_conx, $sqlDislikesCount);
$totalDislikesRow = mysqli_fetch_row($queryDislikesCount);
$totalDislikes = $totalDislikesRow[0];

//check for likes and dsilike in from the user in this particular file
$dislikeButton = '<button title="I dislike this" onclick="likeToggle(\'dislike\','.$fileid.',\'dislikeBtnSpan'.$fileid.'\')" class="likeButton" id="idDislike'.$fileid.'" style="margin-left: 10px;">Dislike</button>';
$likeButton = '<button title="I like this" onclick="likeToggle(\'like\','.$fileid.',\'likeBtnSpan'.$fileid.'\')" class="likeButton" id="idLike'.$fileid.'">Like</button>';

$sqlLikeDislike = "SELECT * FROM likedislike WHERE username='$u' AND fileId='$fileid' LIMIT 1";
$queryLikeDislike = mysqli_query($db_conx, $sqlLikeDislike);
$likeDislikeNumrows = mysqli_num_rows($queryLikeDislike);
while ($row = mysqli_fetch_array($queryLikeDislike, MYSQLI_ASSOC)) {
	$type = $row['type'];

	if($type == 'like'){
		$dislikeButton = '<button title="I dislike this" onclick="likeToggle(\'dislike\','.$fileid.',\'dislikeBtnSpan'.$fileid.'\')" class="likeButton" id="idDislike'.$fileid.'" style="margin-left: 10px;">Dislike</button>';
		$likeButton = '<button title="You liked this" onclick="likeToggle(\'like\','.$fileid.',\'likeBtnSpan'.$fileid.'\')" class="likeButton" id="idLike'.$fileid.'">Liked</button>';
	}else if($type == 'dislike'){
		$dislikeButton = '<button title="You disliked this" onclick="likeToggle(\'dislike\','.$fileid.',\'dislikeBtnSpan'.$fileid.'\')" class="likeButton" id="idDislike'.$fileid.'" style="margin-left: 10px;">Disliked</button>';
		$likeButton = '<button title="I like this" onclick="likeToggle(\'like\','.$fileid.',\'likeBtnSpan'.$fileid.'\')" class="likeButton" id="idLike'.$fileid.'">Like</button>';
	}
}

//get the users avatar!
$sqlAvatar = "SELECT * FROM users WHERE username='$file_owner' LIMIT 1";
$queryAvatar = mysqli_query($db_conx, $sqlAvatar);
$avatarNumrows = mysqli_num_rows($queryAvatar);
while ($row = mysqli_fetch_array($queryAvatar, MYSQLI_ASSOC)) {
	$uploader_avatar = $row['avatar'];
}
if($uploader_avatar==null){
	$uploader_pic_link = 'Images/avatardefault.jpg';
}else{
	$uploader_pic_link = 'user/'.$file_owner.'/'.$uploader_avatar;
}
//give the permission to delete to only owner of the file.
if($file_owner == $log_username){
	$optionArrowList ='
				<li onclick="deleteFile(\''.$fileid.'\')">Delete</li>
				<li>Report</li>
					';
}else{
	$optionArrowList ="<a href=''><li>Report</li></a>";
}

$uploadlist .= '<div id="status_'.$fileid.'" class="upload_boxes" >
					<div class="uploaderPic">
						<a href="user.php?u='.$file_owner.'"><img src="'.$uploader_pic_link.'" alt="'.$file_owner.'"></a>
					</div>
					<div class="left_top_info">
						<h5>Uploaded by <a href="user.php?u='.$file_owner.'"><span class="rankColorClass'.$profile_id.'">'.$file_owner.'</span></a></h5>
						<script type="text/javascript">
  				 			checkr(\''.$userRank.'\', '.$profile_id.', 1);
						</script>
					</div>
					<div class="right_top_info">
						<h5>'.$uploaddate.'</h5>
						<h5 id="optionArrow"><img src="Images/optionArrow.png" width="10px" height="6px">
						<ul>
							'.$optionArrowList.'
						</ul>
						</h5>
					</div>
					<div class="mid_info">
						<a href="download.php?f='.$url.'"><h4>'.$fileName.'</h4></a>
						<h5>'.$file_desc.'</h5>
						<div class="userKeywordDiv">
							'.$keywordList.'
						</div>
					</div>
					<div class="bot_info">
						<div class="bot_info_left">
							<span id="likeBtnSpan'.$fileid.'">'.$likeButton.'</span>
							<h5 class="counter">'.$totalLikes.'</h5>
							<span id="dislikeBtnSpan'.$fileid.'">'.$dislikeButton.'</span>
							<h5 class="counter">'.$totalDislikes.'</h5>
						</div>
						
						<div class="bot_info_right">
							<h5 class="counterDownloads">'.$totalDownloads.'</h5>
							<a href="download.php?f='.$url.'"><img src="Images/downloadLogo.png" title="Download"></a>
						</div>
					</div>
				</div>
				
				';
				$keywordList="";

/***
	if($isFriend == true || $log_username == $u){
	    $uploadlist .= '<textarea id="replytext_'.$statusid.'" class="replytext" onkeyup="statusMax(this,250)" placeholder="write a comment here"></textarea><button id="replyBtn_'.$statusid.'" class="replyBtn" onclick="replyToStatus('.$statusid.',\''.$u.'\',\'replytext_'.$statusid.'\',this)">Reply</button>';	
	}
**/
}

//grouplist
$sql = "SELECT * FROM groups WHERE creator='$u' ORDER BY createDate DESC LIMIT 20";
$query = mysqli_query($db_conx, $sql);
$statusnumrows = mysqli_num_rows($query);
while ($row = mysqli_fetch_array($query, MYSQLI_ASSOC)) {
	$groupId = $row["groupId"];
	$groupTitle = $row["groupTitle"];
	$groupAvatar = $row["groupAvatar"];
	$privacy = $row["privacy"];
	$creator = $row["creator"];
	$createDate = $row["createDate"];

	$groupList .= '<div class="group">
						<a href="group.php?gid='.$groupId.'">
						<img src="Images/cloudConnect.png" height="22px" width="32px">
						<h5>'.$groupTitle.'</h5>
						</a>
				   </div>';
}
//other group that the user is in
//grouplist

//fetch the profile ($u) user id
$sqlUserId = "SELECT * FROM users WHERE username='$u' LIMIT 1";
$queryUserId = mysqli_query($db_conx, $sqlUserId);
$idnumrows = mysqli_num_rows($query);
while($row = mysqli_fetch_array($queryUserId, MYSQLI_ASSOC)){
	$profileUsersId = $row["id"];
}

$sqlMyGroup = "SELECT * FROM usersgroups WHERE userId='$profileUsersId' ORDER BY createDate DESC LIMIT 20";
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
	$groupList .= '<div class="group">
						<a href="group.php?gid='.$groupIdOther.'">
						<img src="Images/cloudConnect.png" height="22px" width="32px">
						<h5>'.$groupTitleOther.'</h5>
						</a>
				   </div>';
}
//total group count 
$sqlGroupsCount = "SELECT COUNT(groupId) FROM groups WHERE creator='$u'";
$queryGroupsCount = mysqli_query($db_conx, $sqlGroupsCount);
$totalGroupsRow = mysqli_fetch_row($queryGroupsCount);
$totalGroupsCount = $totalGroupsRow[0];
?>

<div id="uploadsList">
  <?php echo $uploadlist; ?>
</div>
<div id="newsSection">
	<div id="groups-text">
		<h5><?php echo $totalGroupsCount; ?> Groups</h5>
	</div>
	<div id="view-all-text">
		<a href="createGroup.php"><h5>Create group</h5></a>
	</div>
	<div id="border-bot"></div>
	<div id="groupList">
		<?php echo $groupList; ?>
	</div>
</div>