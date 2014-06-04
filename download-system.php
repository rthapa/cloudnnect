<?php
include_once("php_includes/check_login_status.php");
if(isset($_POST['file_name'])){
	$fileName = $_POST['file_name'];
	$fileUrl = "";
	$fileSize = "";
	$fileType = "";
	$uploader = "";
	$fileDesc = "";
	$uploadDate = "";

	$sql = "SELECT * FROM file WHERE fileName='$fileName'";
	$query = mysqli_query($db_conx, $sql);
	$statusnumrows = mysqli_num_rows($query);
	while ($row = mysqli_fetch_array($query, MYSQLI_ASSOC)) {
		$fileUrl = $row["url"];
		$fileType = $row["file_type"];
		$fileSize = $row["file_size"];
		$uploader = $row["owner"];
		$fileDesc = $row["description"];
		$uploadDate = $row["uploaddate"];
		$totalDownloadsThisFile = $row["totalDownloads"];
	}

	header('Content-type: '.$fileType.'');
    header('Content-Disposition: attachment; filename="'.$fileName.'"');
    readfile('files/'.$fileUrl);

    //update total downloads record if user is logged in
    if($log_username != ''){

    	$profile_id = "";
	    $totalDownloads = "";

		$sql = "SELECT * FROM users WHERE username='$log_username' AND activated='1' LIMIT 1";
		$user_query = mysqli_query($db_conx, $sql);

		while ($row = mysqli_fetch_array($user_query, MYSQLI_ASSOC)) {
			$profile_id = $row["id"];
			$totalDownloads = $row["totalDownloads"];
		}
		$totalDownloads = $totalDownloads + 1;
		$query2 = "UPDATE users SET totalDownloads = '$totalDownloads' WHERE id = '$profile_id'";
		mysqli_query($db_conx, $query2);

		//update that file's total downloads
		$totalDownloadsThisFile = $totalDownloadsThisFile + 1;
		$query3 = "UPDATE file SET totalDownloads = '$totalDownloadsThisFile' WHERE fileName='$fileName'";
		mysqli_query($db_conx, $query3);
    }
	exit();

}
?>
