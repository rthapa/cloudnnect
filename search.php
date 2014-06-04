<?php
include_once("php_includes/check_login_status.php");


if(isset($_GET["search"])){
	$search = mysqli_real_escape_string($db_conx, $_GET["search"]);// preg_replace('#[^a-z0-9]#i', '', $_GET['f']);
}else if($_GET["search"] && $_GET["searchType"]){
	$search = mysqli_real_escape_string($db_conx, $_GET["search"]);
	$searchType = mysqli_real_escape_string($db_conx, $_GET["search"]);

	if($searchType == "file name"){
		$searchColumn = "fileName";
	}else if($searchType == "Person"){
		$searchColumn = "username";
	}else if($searchColum == "file keywords"){
		$searchColumn = "keywords";
	}
	
}else {
    header("location: index.php");
    exit();	
}
$keys = explode(" ",$search);

$searchSql ="SELECT * FROM file WHERE type='a' AND keywords LIKE '%$search%'";

foreach($keys as $k){
    $searchSql .= " OR type='a' AND keywords LIKE '%$k%' OR type='a' AND fileName LIKE '%$k%' OR type='a' AND owner LIKE '%$k%'";
}




//
$uploader ="";
$fileName ="";
$ResultList ="";
$keywordList ="";

$query = mysqli_query($db_conx, $searchSql);
$queryNumRows = mysqli_num_rows($query);
while ($row = mysqli_fetch_array($query, MYSQLI_ASSOC)) {
	$fileid = $row["id"];
	$fileName = $row["fileName"];
	$fileType = $row["file_type"];
	$fileSize = $row["file_size"];
	$url = $row["url"];
	$file_owner = $row["owner"];
	$file_desc = $row["description"];
	$uploaddate = $row["uploaddate"];
	$totalDownloads = $row["totalDownloads"];
	$keywords = $row["keywords"];

	$keywordListArray = explode(" ", $keywords);

	foreach($keywordListArray as $k){
		if($k != ""){
   			$keywordList .= "<a href='search.php?search=$k'><h5>$k</h5></a>";
   		}
	}

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
/*
//check for likes and dsilike in from the user in this particular file
$dislikeButton = '<button title="I dislike this" onclick="likeToggle(\'dislike\','.$fileid.',\'dislikeBtnSpan\')" class="likeButton" style="margin-left: 10px;">Dislike</button>';
$likeButton = '<button title="I like this" onclick="likeToggle(\'like\','.$fileid.',\'likeBtnSpan\')" class="likeButton">Like</button>';

$sqlLikeDislike = "SELECT * FROM likedislike WHERE username='$log_username' AND fileId='$fileid' LIMIT 1";
$queryLikeDislike = mysqli_query($db_conx, $sqlLikeDislike);
$likeDislikeNumrows = mysqli_num_rows($queryLikeDislike);
while ($row = mysqli_fetch_array($queryLikeDislike, MYSQLI_ASSOC)) {
	$type = $row['type'];

	if($type == 'like'){
		$dislikeButton = '<button title="I dislike this" onclick="likeToggle(\'dislike\','.$fileid.',\'dislikeBtnSpan\')" class="likeButton" style="margin-left: 10px;">Dislike</button>';
		$likeButton = '<button title="You liked this" onclick="likeToggle(\'like\','.$fileid.',\'likeBtnSpan\')" class="likeButton">Liked</button>';
	}else if($type == 'dislike'){
		$dislikeButton = '<button title="You disliked this" onclick="likeToggle(\'dislike\','.$fileid.',\'dislikeBtnSpan\')" class="likeButton" style="margin-left: 10px;">Disliked</button>';
		$likeButton = '<button title="I like this" onclick="likeToggle(\'like\','.$fileid.',\'likeBtnSpan\')" class="likeButton">Like</button>';
	}
}
*/
if(isset($_SESSION['username'])){
//check for likes and dsilike in from the user in this particular file
$dislikeButton = '<button title="I dislike this" onclick="likeToggle(\'dislike\','.$fileid.',\'dislikeBtnSpan'.$fileid.'\')" class="likeButton" id="idDislike'.$fileid.'" style="margin-left: 10px;">Dislike</button>';
$likeButton = '<button title="I like this" onclick="likeToggle(\'like\','.$fileid.',\'likeBtnSpan'.$fileid.'\')" class="likeButton" id="idLike'.$fileid.'">Like</button>';

$sqlLikeDislike = "SELECT * FROM likedislike WHERE username='$log_username' AND fileId='$fileid' LIMIT 1";
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
}else{
	$dislikeButton = '<a href="index.php"><button title="I dislike this" class="likeButton" style="margin-left: 10px;">Dislike</button></a>';
	$likeButton = '<a href="index.php"><button title="I like this" class="likeButton">Like</button></a>';
}
//get the users avatar!
$sqlAvatar = "SELECT * FROM users WHERE username='$file_owner' LIMIT 1";
$queryAvatar = mysqli_query($db_conx, $sqlAvatar);
$avatarNumrows = mysqli_num_rows($queryAvatar);
while ($row = mysqli_fetch_array($queryAvatar, MYSQLI_ASSOC)) {
	$profile_id = $row['id'];
	$uploader_avatar = $row['avatar'];
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
				<li onclick="deleteFile(\''.$fileid.'\')">Delete</li>
				<li>Report</li>
					';
}else{
	$optionArrowList ="<a href=''><li>Report</li></a>";
}
//
	$ResultList .= '
				<div id="status_'.$fileid.'" class="upload_boxes">
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
	$keywordList ="";
}
?>
<!DOCTYPE html>
<html>
<head>
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
<div class="searchWrapper">
	<div class="searchContent">
		<div class="searchContentTop">
			<h5><?php echo $queryNumRows; ?> files found</h5> 
		</div>
	<?php
		echo $ResultList;
	?>
	</div>
		<?php include_once("rightBarContent.php"); ?>
</div>
