<?php
include_once("php_includes/check_login_status.php");
$gid = "";
$groupId = "";
$totalMemCount = "0";
//fetch group from get
if(isset($_GET['gid'])){
$gid = $_GET['gid'];
}else{
	header("location: index.php");
    exit();
}
$sql = "SELECT * FROM groups WHERE groupId='$gid' LIMIT 1";
$query = mysqli_query($db_conx, $sql);
$statusnumrows = mysqli_num_rows($query);
while ($row = mysqli_fetch_array($query, MYSQLI_ASSOC)) {
	$groupId = $row["groupId"];
	$groupTitle = $row["groupTitle"];
	$groupDesc = $row["groupDesc"];
	$groupAvatar = $row["groupAvatar"];
	$privacy = $row["privacy"];
	$creator = $row["creator"];
	$createDate = $row["createDate"];
}

//total members count 
$sqlMemCount = "SELECT COUNT(ugId) FROM usersgroups WHERE groupId='$groupId' AND accepted='1'";
$queryMemCount = mysqli_query($db_conx, $sqlMemCount);
$totalMemRow = mysqli_fetch_row($queryMemCount);
$totalMemCount = $totalMemRow[0];
//also add the creator in the totalMemCount;
$sqlCreatorCount = "SELECT COUNT(creator) FROM groups WHERE groupId='$groupId' LIMIT 1";
$queryCreatorCount = mysqli_query($db_conx, $sqlCreatorCount);
$totalCreatorRow = mysqli_fetch_row($queryCreatorCount);
$totalCreatorCount = $totalCreatorRow[0];

$totalMemCount =  $totalMemRow[0] +  $totalCreatorRow[0];

//upload list for this group
$totalGroupUpload = "0";
$keywordList = "";
$groupUploadlist = "";
$sqlFileGroups = "SELECT * FROM filegroups WHERE groupId='$gid' LIMIT 20";
$queryFileGroups = mysqli_query($db_conx, $sqlFileGroups);
while ($fileGroupsRow = mysqli_fetch_array($queryFileGroups, MYSQLI_ASSOC)) {
	$fgId = $fileGroupsRow["fgId"];
	$fId = $fileGroupsRow["fId"];
	$createDate = $fileGroupsRow["createDate"];

//total uploads in the group 
$sqlGroupUploadCount = "SELECT COUNT(fgId) FROM filegroups WHERE groupId='$gid'";
$queryGroupUploadCount = mysqli_query($db_conx, $sqlGroupUploadCount);
$totalGroupUploadRow = mysqli_fetch_row($queryGroupUploadCount);
$totalGroupUpload = $totalGroupUploadRow[0];
//total likes 
$sqlLikesCount = "SELECT COUNT(id) FROM likedislike WHERE fileId='$fId' AND type='like'";
$queryLikesCount = mysqli_query($db_conx, $sqlLikesCount);
$totalLikesRow = mysqli_fetch_row($queryLikesCount);
$totalLikes = $totalLikesRow[0];

//total dislikes
$sqlDislikesCount = "SELECT COUNT(id) FROM likedislike WHERE fileId='$fId' AND type='dislike'";
$queryDislikesCount = mysqli_query($db_conx, $sqlDislikesCount);
$totalDislikesRow = mysqli_fetch_row($queryDislikesCount);
$totalDislikes = $totalDislikesRow[0];

//check for likes and dsilike in from the user in this particular file
$dislikeButton = '<button title="I dislike this" onclick="likeToggle(\'dislike\','.$fId.',\'dislikeBtnSpan'.$fId.'\')" class="likeButton" id="idDislike'.$fId.'" style="margin-left: 10px;">Dislike</button>';
$likeButton = '<button title="I like this" onclick="likeToggle(\'like\','.$fId.',\'likeBtnSpan'.$fId.'\')" class="likeButton" id="idLike'.$fId.'">Like</button>';

$sqlLikeDislike = "SELECT * FROM likedislike WHERE username='$log_username' AND fileId='$fId' LIMIT 1";
$queryLikeDislike = mysqli_query($db_conx, $sqlLikeDislike);
$likeDislikeNumrows = mysqli_num_rows($queryLikeDislike);
while ($row = mysqli_fetch_array($queryLikeDislike, MYSQLI_ASSOC)) {
	$type = $row['type'];

	if($type == 'like'){
		$dislikeButton = '<button title="I dislike this" onclick="likeToggle(\'dislike\','.$fId.',\'dislikeBtnSpan'.$fId.'\')" class="likeButton" id="idDislike'.$fId.'" style="margin-left: 10px;">Dislike</button>';
		$likeButton = '<button title="You liked this" onclick="likeToggle(\'like\','.$fId.',\'likeBtnSpan'.$fId.'\')" class="likeButton" id="idLike'.$fId.'">Liked</button>';
	}else if($type == 'dislike'){
		$dislikeButton = '<button title="You disliked this" onclick="likeToggle(\'dislike\','.$fId.',\'dislikeBtnSpan'.$fId.'\')" class="likeButton" id="idDislike'.$fId.'" style="margin-left: 10px;">Disliked</button>';
		$likeButton = '<button title="I like this" onclick="likeToggle(\'like\','.$fId.',\'likeBtnSpan'.$fId.'\')" class="likeButton" id="idLike'.$fId.'">Like</button>';
	}
}

	//get the file info of the fileId
	$file_owner = "";
	$url = "";
	$fileName = "";
	$file_desc = "";
	$totalDownloads = "";
	$uploaddate = "";
	$sqlFileInfo = "SELECT * FROM file WHERE id='$fId' AND type='d' ORDER BY uploaddate DESC LIMIT 20";
	$queryFileInfo = mysqli_query($db_conx, $sqlFileInfo);
	while ($rowFileInfo = mysqli_fetch_array($queryFileInfo, MYSQLI_ASSOC)) {
		$fileName = $rowFileInfo["fileName"];
		$url = $rowFileInfo["url"];
		$file_owner = $rowFileInfo["owner"];
		$file_desc = $rowFileInfo["description"];
		$keywords = $rowFileInfo["keywords"];
		$uploaddate = $rowFileInfo["uploaddate"];
		$totalDownloads = $rowFileInfo["totalDownloads"];

		$keywordListArray = explode(" ", $keywords);

		foreach($keywordListArray as $k){
	    $keywordList .= "<a href='search.php?search=$k'><h5>$k</h5></a>";
		}
	}

	//get the users avatar!
	$uploader_avatar = "";
	$sqlAvatar = "SELECT * FROM users WHERE username='$file_owner' LIMIT 1";
	$queryAvatar = mysqli_query($db_conx, $sqlAvatar);
	$avatarNumrows = mysqli_num_rows($queryAvatar);
	while ($row = mysqli_fetch_array($queryAvatar, MYSQLI_ASSOC)) {
		$uploader_avatar = $row['avatar'];
		$profile_id = $row['id'];
		$userRank = $row['userRank'];
	}
	if($uploader_avatar==null){
		$uploader_pic_link = 'Images/avatardefault.jpg';
	}else{
		$uploader_pic_link = 'user/'.$file_owner.'/'.$uploader_avatar;
	}
	//give the permission to delete to only owner of the file.
	if($file_owner == $log_username){
		$optionArrowList ='
					<li onclick="deleteFile(\''.$fId.'\')">Delete</li>
					<li onclick="toggleOverlayReport()">Report</li>
						';
	}else{
		$optionArrowList ="<a href=''><li>Report</li></a>";
	}

	$groupUploadlist .= '<div id="status_'.$fId.'" class="upload_boxes">
					<div class="uploaderPic">
						<a href="user.php?u='.$file_owner.'"><img src="'.$uploader_pic_link.'" alt="'.$file_owner.'"></a>
					</div>
					<div class="left_top_info">
						<h5>Uploaded by <a href="user.php?u='.$file_owner.'"><span class="rankColorClass'.$profile_id.'">'.$file_owner.'</span></a></h5>
						<script type="text/javascript">
  				 			checkr(\''.$userRank.'\', '.$profile_id.', 0);
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
							<span id="likeBtnSpan'.$fId.'">'.$likeButton.'</span>
							<h5 class="counter">'.$totalLikes.'</h5>
							<span id="dislikeBtnSpan'.$fId.'">'.$dislikeButton.'</span>
							<h5 class="counter">'.$totalDislikes.'</h5>
						</div>
						
						<div class="bot_info_right">
							<h5 class="counterDownloads">'.$totalDownloads.'</h5>
							<a href="download.php?f='.$url.'"><img src="Images/downloadLogo.png" title="Download"></a>
						</div>
					</div>
				</div>';
				$keywordList="";


}
			//check if the user is creator
			$sqlIsCreator = "SELECT COUNT(groupId) FROM groups WHERE creator='$log_username' AND groupId='$gid' LIMIT 1";
			$queryIsCreatorCount = mysqli_query($db_conx, $sqlIsCreator);
			$totalIsCreatorRow = mysqli_fetch_row($queryIsCreatorCount);
			$totalIsCreator = $totalIsCreatorRow[0];

			//check if the user is member
			$sqlIsMember = "SELECT COUNT(ugId) FROM usersgroups WHERE userId='$log_id' AND groupId='$gid' LIMIT 1";
			$queryIsMemberCount = mysqli_query($db_conx, $sqlIsMember);
			$totalIsMemberRow = mysqli_fetch_row($queryIsMemberCount);
			$totalIsMember = $totalIsMemberRow[0];

			$uploadToGroupBtn = "";
			$inviteToGroupBtn = "";
			$editGroupLi = "";

			if($totalIsCreatorRow[0] > 0 || $totalIsMemberRow[0] > 0){
				$uploadToGroupBtn = '<a class="button icon" onclick="toggleOverlayUpload()"><span id="add-btn-span-icon"></span>Upload to group</a>';
				$inviteToGroupBtn = '<a class="button icon" onclick="toggleOverlayAddMem()"><span id="add-btn-span-icon"></span>Invite to group</a>';
				$editGroupLi = '<li><a href="http://localhost/cloudbaxa/settings.php"><img src="Images/editLogoSized.png">Edit Group</a></li>';
			}
?>
<!DOCTYPE html>
<html>
<head>
	<title><?php echo $groupTitle; ?></title>
	<link rel="shortcut icon" href="Images/favicon.ico">
	<link rel="stylesheet" type="text/css" href="Styles.css">
	<link rel="stylesheet" type="text/css" href="user-styles.css">
	<link rel="stylesheet" type="text/css" href="feedStyle.css">
	<script src="js/main.js"></script>
	<script src="js/ajax.js"></script>
	<style type="text/css">

	</style>
<script type="text/javascript">
function toggleOverlayAddMem(){
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
function toggleOverlayUpload(){
	var overlayUpload = document.getElementById('overlayUpload');
	var specialBoxUpload = document.getElementById('specialBoxUpload');
	overlayUpload.style.opacity = .8;
	if(overlayUpload.style.display == "block"){
		overlayUpload.style.display = "none";
		specialBoxUpload.style.display = "none";
	} else {
		overlayUpload.style.display = "block";
		specialBoxUpload.style.display = "block";
	}
}
function toggleOverlayReport(){
	var overlayReport = document.getElementById('overlayReport');
	var specialBoxReport = document.getElementById('specialBoxReport');
	overlayReport.style.opacity = .8;
	if(overlayReport.style.display == "block"){
		overlayReport.style.display = "none";
		specialBoxReport.style.display = "none";
	} else {
		overlayReport.style.display = "block";
		specialBoxReport.style.display = "block";
	}
}
</script>
	</head>
<body>

<!-- report overlay -->
<div id="overlayReport" onmousedown="toggleOverlayReport()">
</div>
<div id="specialBoxReport">
	<textarea id="about" rows="5" cols="40" name="desc" maxlength="255"  placeholder="Description of the report.."></textarea>
	<span id="statusOfReport"><h5></h5></span>
  	<div class="addButtonDiv addButtonDiv-inMember" id="addButtonDiv">
			<a class="button icon" onclick="reportFile('<?php echo $gid; ?>')"><span id="add-btn-span-icon"></span>Submit <span id="span-username"></span></a>
	</div>
</div>

<!-- add member overlay -->
<div id="overlay" onmousedown="toggleOverlayAddMem()">
</div>
<div id="specialBox">
	<input type="text" onblur="checkUsernameInGroup()" maxlength="16" id="username-addMem" placeholder="username..">
	<span id="usernameStatusGroup"><h5>Enter username</h5></span>
  	<div class="addButtonDiv addButtonDiv-inMember" id="addButtonDiv">
			<a class="button icon" onclick="addMemberInGroup('<?php echo $gid; ?>')"><span id="add-btn-span-icon"></span>Invite <span id="span-username"></span></a>
	</div>
</div>

<!-- upload to group overlay -->
<div id="overlayUpload" onmousedown="toggleOverlayUpload()">
</div>
<div id="specialBoxUpload">
	<div class="fileName fileNameInGroup" id="fileName">
		<h5 id="fileNameH5">Choose a file</h5>
	</div>
	<div id="groupUploadFormWrapper">
		<form id="upload_form" enctype="multipart/form-data" method="post" >
			  <input type="file" name="file1" id="file1"><br>
			  <!--<input type="button" value="Upload File" onclick="uploadFile()"> 
				-->
		</form>
	</div>
	<div class="progress" id="progress">
	    <span class="progress-val" id="progress-val">0%</span>
	    <span class="progress-bar"><span class="progress-in" id="progress-in"></span></span>
  	</div>
	<textarea id="about" rows="5" cols="40" name="desc" maxlength="255"  placeholder="File Description.."></textarea>
	<br />
	<input id="usernameAddMem"  type="text" maxlength="25" placeholder="Keywords or tags">
	<div class="uploadStatusDiv uploadStatusDiv-inGroup" id="uploadStatusDiv">
  	  <h3 id="uploadStatus"></h3>
  	  <p id="loaded_n_total"></p>
  	</div>

	<div class="addButtonDiv addButtonDiv-inMember" id="addButtonDiv-inMember">
		<a class="button icon" onclick="uploadFileToGroup('<?php echo $gid; ?>')"><span id="add-btn-span-icon"></span>Upload<span id="span-username"></span></a>
	</div>

</div>

<?php
if($log_username != ''){
 include_once("php_includes/template_pageTop.php"); 
}else{
 include_once("php_includes/template_pageTop_notLogged.php"); 
}
?>

<div class="uploadWrapper">
	<div class="title">
		<h4><?php echo $groupTitle; ?></h4>
	</div>
	<div class="groupMain">
		<div class="groupMain-top">
			<div class="addButtonDiv uploadButtonDiv">
				<?php echo $uploadToGroupBtn; ?>
			</div>
		</div>
		<div class="groupMain-mid">
			<?php
			
			if($privacy == "closed" || $privacy == "secret"){
				if($totalIsCreatorRow[0] > 0 || $totalIsMemberRow[0] > 0){
				 echo $groupUploadlist;
				}else{
					echo '<div class="groupMsgDiv"><h4>You have no access to this group</h4></div>';
				}
			}else{
				echo $groupUploadlist;
			}

			 ?>
		</div>
	</div>
	<div class="groupRight">
		<div class="addButtonDiv">
			<?php echo $inviteToGroupBtn; ?>
		</div>

		<div class="groupAbout">
			<h5 class="accountHeading"><span>About</span></h5>
			<h2 id="groupTypeH2"><?php echo $privacy; ?> group</h2>
			<h4><?php echo $groupTitle; ?></h4>
			<h4><?php echo $groupDesc; ?></h4>
			<h4>Created by <a href="user.php?u=<?php echo $creator?>"><?php echo $creator;?></a></h4>
		</div>
		<div class="groupSettings">
			<h5 class="accountHeading"><span>Settings</span></h5>
			<ul class="loggedUserDetail group-prefix">
					<?php echo $editGroupLi ?>
					<li><a href=""><img src="Images/peopleNoBg.png" width="30px" height="22px">Members (<?php echo $totalMemCount; ?>)</a></li>
					<li><a href=""><img src="Images/uploadLogoSized.png" width="30px" height="22px">Uploads (<?php echo $totalGroupUpload;?>)</a></li>
			</ul>
			<h5 class="accountHeading"><span>Recomended</span></h5>
		</div>
	</div>
	<!--
	<div class="groupBannerWrapper">
		<a href="#"><h4 class="addMemBtn"><img src="Images/peopleNoBg.png" width="20" height="20">add member</h4></a>
	</div>
	<!-
	<div id="groupUploadFormWrapper">
		<form id="upload_form" enctype="multipart/form-data" method="post" >
		  <input type="file" name="file1" id="file1"><br>
		  <!-<input type="button" value="Upload File" onclick="uploadFile()"> 
		</form>
	</div>
	-->
<script>
document.getElementById("file1").onchange = function() {
	_("fileName").style.display = "none";
	var file = _("file1").files[0];
	fileName = file.name;
	_("fileName").style.display = "block";
	_("fileNameH5").innerHTML = fileName;
}
</script>
</body>
</html>


