<?php
include_once("php_includes/check_login_status.php");

if(isset($_GET["f"])){
	$urlName = $_GET['f'];// preg_replace('#[^a-z0-9]#i', '', $_GET['f']);
} else {
    header("location: index.php");
    exit();	
}

$fileName = "";
$fileSize = "";
$fileType = "";
$uploader = "";
$fileDesc = "";
$uploadDate = "";
$likeButton = "";
$dislikeButton = "";
$totalDownloads = "";
$logged_user = "";
$ahref= "";
$ahrefEnd ="";

//check if user is logged or not
if(isset($_SESSION['username'])){
	$logged_user = $_SESSION['username'];

}else{
	$logged_user = "";
	$ahref= "<a href='index.php'>";
	$ahrefEnd = "</a>";
}

$isViewerAlsoOwner = false;

$sql = "SELECT * FROM file WHERE url='$urlName'";
$query = mysqli_query($db_conx, $sql);
$statusnumrows = mysqli_num_rows($query);
while ($row = mysqli_fetch_array($query, MYSQLI_ASSOC)) {
	$fileid = $row["id"];
	$fileName = $row["fileName"];
	$fileType = $row["file_type"];
	$filePrivacy = $row["type"];
	$fileSize = $row["file_size"];
	$uploader = $row["owner"];
	$keywords = $row["keywords"];
	$fileDesc = $row["description"];
	$uploadDate = $row["uploaddate"];
	$totalDownloads = $row["totalDownloads"];

	$keywordListArray = explode(" ", $keywords);
	$keywordList ="";
	if(!$keywords ==""){
		foreach($keywordListArray as $k){
			if($k != ""){
	    		$keywordList .= "<a href='search.php?search=$k'><h5>$k</h5></a>";
			}
		}
	}else{
		$keywordList = "";
	}


	//now fetch uploader deatail
	$sqlUploader = "SELECT * FROM users WHERE username='$uploader'";
	$queryUploader = mysqli_query($db_conx, $sqlUploader);
	while ($rowUploader = mysqli_fetch_array($queryUploader, MYSQLI_ASSOC)) {
		$profile_id = $rowUploader['id'];
		$uploader_avatar = $rowUploader['avatar'];
		$userRank = $rowUploader["userRank"];
	}
	if($uploader_avatar==null){
		$uploader_pic_link = 'Images/avatardefault.jpg';
	}else{
		$uploader_pic_link = 'user/'.$uploader.'/'.$uploader_avatar;
	}
	//check if the viewer is the owner himself
	if($uploader == $log_username){
		$isViewerAlsoOwner = true;
	}

	//check if the file type is private and if so is the owner viewing it?
	if($filePrivacy == "c"  && $isViewerAlsoOwner == false){
		header("location: message.php?msg='Not authorised to view this file'") ;
		exit();
	}

	//fileSize to MB or GB
	//1kb  = 1024bytes
	//1mb = 1024kb
	//1gb = 1024mb
	if($fileSize > 1024){
		$suitedConversion = '';
		//to kb
		$toKb = $fileSize/1024;
		$fileSize = round($toKb,1);
		$suitedConversion = 'KB';

		//to mb
		if($fileSize > 1024){
		$toMb = $fileSize/1024;
		$fileSize = round($toMb,1); 
		$suitedConversion = 'MB';

			if($fileSize > 1024){
			$toGb = $fileSize/1024;
			$fileSize = round($toGb,1); 	
			$suitedConversion = 'GB';
			}
		}

		$fileSize .= $suitedConversion;
	}

}
$explodeDate = explode(' ',$uploadDate);
$dateOnly = $explodeDate[0];
?>
<?php

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

//do this only if user is logged in
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
	$dislikeButton = $ahref.'<button title="I dislike this" class="likeButton" style="margin-left: 10px;">Dislike</button>'.$ahrefEnd;
	$likeButton = $ahref.'<button title="I like this" class="likeButton">Like</button>'.$ahrefEnd;
}

//give the permission to delete to only owner of the file.
if($uploader == $log_username){
	$optionArrowList ='
				<li onclick="deleteFile(\''.$fileid.'\')">Delete</li>
				<li>Report</li>
					';
}else{
	$optionArrowList ="<a href=''><li>Report</li></a>";
}
?>
<!DOCTYPE html>
<html>
<head>
	<title><?php echo $fileName; ?></title>
	<link rel="shortcut icon" href="Images/favicon.ico">
	<link rel="stylesheet" type="text/css" href="Styles.css">
	<link rel="stylesheet" type="text/css" href="user-styles.css">
	<script src="js/main.js"></script>
	<script src="js/ajax.js"></script>
	<script type="text/javascript">
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

<div class="uploadWrapper">
	<div class="title">
	<h4>Download</h4>
	</div>

	<div class="downloadInfo">
		<div class="uploaderPic">
			<a href="user.php?u=<?php echo $uploader;?>"><img src="<?php echo $uploader_pic_link; ?>" alt="<?php echo $uploader;?>"></a>
		</div>
		<div class="left_top_info">
			<h5>Uploaded by <a href="user.php?u=<?php echo $uploader; ?>"><span class="rankColorClass<?php echo $profile_id;?>"><?php echo $uploader; ?></span></a></h5>
			<script type="text/javascript">
  				 checkr(<?php echo '\''.$userRank.'\''; ?>, <?php echo '\''.$profile_id.'\''; ?> , 0);
			</script>
		</div>
		<div class="right_top_info">
			<h5><?php echo $dateOnly;?></h5>
				<h5 id="optionArrow"><img src="Images/optionArrow.png" width="10px" height="6px">
				<ul>
					<?php echo $optionArrowList; ?>
				</ul>
			</h5>
		</div>
		<div class="mid_info">
			<a href=""><h4><?php echo $fileName; ?></h4></a>
			<h5><?php echo $fileDesc; ?></h5>
			<div class="userKeywordDiv">
				<?php echo $keywordList; ?>
			</div>
		</div>
		<?php echo $fileSize; ?>
		<div class="bot_info">
			<div class="bot_info_left">
				<span id="likeBtnSpan<?php echo $fileid;?>"><?php echo $likeButton; ?></span>
				<h5 class="counter"><?php echo $totalLikes; ?></h5>
				<span id="dislikeBtnSpan<?php echo $fileid;?>"><?php echo $dislikeButton; ?></span>
				<h5 class="counter"><?php echo $totalDislikes; ?></h5>
			</div>
						
			<div class="bot_info_right">
				<h5 class="counterDownloads"><?php echo $totalDownloads; ?></h5>
				<img src="Images/downloadLogo.png" title="Download">
			</div>
		</div>
		
	</div>
	<form class="downloadForm" action="download-system.php" method="post" name="downloadform">
 		 	 <input name="file_name" value="<?php echo $fileName;?>" type="hidden">
		 	 <input class="downloadButton" type="submit" value="Download">
	</form>
</div>
</body>
</html>
