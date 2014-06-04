<?php
include_once("../php_includes/check_login_status.php");
include_once("../php_includes/randStrGen.php");
include_once("../php_includes/db_conx.php");

$username = "";
$profile_id = "";
$groupId = "";
$groupUpload = false;

$totalDownloads = "";
$totalUploads = "";

$uploadType = $_POST["uploadType"];
$fileDesc = $_POST["fileDesc"];
$fileKeywords = $_POST["fileKeywords"];

//if upload to group
if(isset($_POST["groupId"])){
	$groupId = $_POST["groupId"];
	$groupUpload = true;
}

$sql = "SELECT * FROM users WHERE username='$log_username' AND activated='1' LIMIT 1";
$user_query = mysqli_query($db_conx, $sql);

while ($row = mysqli_fetch_array($user_query, MYSQLI_ASSOC)) {
	$username = $row["username"];
	$profile_id = $row["id"];

	$totalDownloads = $row["totalDownloads"];
	$totalUploads = $row["totalUploads"];
}

$fileName = $_FILES["file1"]["name"]; // The file name
$fileTmpLoc = $_FILES["file1"]["tmp_name"]; // File in the PHP tmp folder
$fileType = $_FILES["file1"]["type"]; // The type of file it is
$fileSize = $_FILES["file1"]["size"]; // File size in bytes
$fileErrorMsg = $_FILES["file1"]["error"]; // 0 for false... and 1 for true
//random string generator
$num_files = count(glob('../files/'."*"));
$random_name = randStrGen(7);
//$random_name .= + $num_files;
//$exts = explode('.',$fileName);
//$store_rand_name_ext = $random_name.'.'.$exts[1];
$pi = pathinfo($fileName);
$txt = $pi['filename'];
$ext = $pi['extension'];
$store_rand_name_ext = $random_name.'.'.$ext;


if (!$fileTmpLoc) { // if file not chosen
    echo "ERROR: Please browse for a file before clicking the upload button.";
    exit();
}


//$fileName
if(move_uploaded_file($fileTmpLoc, "../files/$store_rand_name_ext")){
	$query = "INSERT INTO file (fileName, url, file_type, file_size, owner, owner_id, type,description, keywords, uploaddate, totalDownloads) 
	VALUES('$fileName', '$store_rand_name_ext', '$ext', '$fileSize', '$log_username', '$profile_id', '$uploadType', '$fileDesc', '$fileKeywords', now(), '0')";
	mysqli_query($db_conx, $query);
	$totalUploads = $totalUploads + 1;
	$query2 = "UPDATE users SET totalUploads = '$totalUploads' WHERE id = '$profile_id'";
	mysqli_query($db_conx, $query2);

	//update user rank!!
	//total actual uploads
	$sqlCurrUploadsCount = "SELECT COUNT(id) FROM file WHERE owner='$log_username'";
	$queryCurrUploadsCount = mysqli_query($db_conx, $sqlCurrUploadsCount);
	$totalCurrUploadsRow = mysqli_fetch_row($queryCurrUploadsCount);
	$totalCurrUploads = $totalCurrUploadsRow[0];

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

	$query3 = "UPDATE users SET userRank = '$userRank' WHERE id = '$profile_id'";
	mysqli_query($db_conx, $query3);

	//if group upload then check if the user has the priveldge to upload to that group page
	if($groupUpload == true){
		//check if the user is creator
		$sqlIsCreator = "SELECT COUNT(groupId) FROM groups WHERE creator='$log_username'";
		$queryIsCreator = mysqli_query($db_conx, $sqlIsCreator);
		$totalIsCreator = mysqli_fetch_row($queryIsCreator);
		$totalIsCreatorCount = $totalIsCreator[0];

		//check if the user is creator
		$sqlIsMem = "SELECT COUNT(groupId) FROM groups WHERE creator='$log_username'";
		$queryIsMem = mysqli_query($db_conx, $sqlIsMem);
		$totalIsMem = mysqli_fetch_row($queryIsMem);
		$totalIsMemCount = $totalIsMem[0];


	    if ($totalIsCreatorCount[0] > 0 || $totalIsMemCount[0] > 0 ) {
	    	//exits and is allowed
	    	//fetch the fileId
		    $sqlFileId = "SELECT * FROM file WHERE owner='$log_username' AND url='$store_rand_name_ext' LIMIT 1";
			$queryFileId = mysqli_query($db_conx, $sqlFileId);
			while ($row = mysqli_fetch_array($queryFileId, MYSQLI_ASSOC)) {
				$fileId = $row["id"];
			}  
			//finally insert the data
			$queryAddFileGroups = "INSERT INTO filegroups (fId, groupId, createDate) 
			VALUES('$fileId', '$groupId', now())";
			mysqli_query($db_conx, $queryAddFileGroups);
	    }
	}//if group upload end 
    echo '<a href="download.php?f='.$store_rand_name_ext.'">'.$fileName.'</a> upload is complete';
} else {
    echo "move_uploaded_file function failed";
}
?>
