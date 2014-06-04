<?php
include_once("../php_includes/check_login_status.php");
include_once("../php_includes/db_conx.php");


if($user_ok != true || $log_username == "") {
	exit();
}
$fileId = "";
if(isset($_POST['fileId'])){
	$fileId = $_POST['fileId'];
}	
//fetch the file info !
$sqlFile = "SELECT * FROM file WHERE id='$fileId' LIMIT 1";
$queryFile = mysqli_query($db_conx, $sqlFile);
$fileNumrows = mysqli_num_rows($queryFile);
while ($row = mysqli_fetch_array($queryFile, MYSQLI_ASSOC)) {
	$file_type = $row['file_type'];
	$fileName = $row['fileName'];
	$url = $row['url'];
	$owner_id = $row['owner_id'];
}

$filePath = '../files/'.$url;
if(is_readable($filePath)){

	if(unlink($filePath)){
		//check if this file is in filegroups table /
		$sqlIsInGroup = "SELECT COUNT(fgId) FROM filegroups WHERE fId='$fileId' LIMIT 1";
		$queryIsInGroupCount = mysqli_query($db_conx, $sqlIsInGroup);
		$totalIsInGroupRow = mysqli_fetch_row($queryIsInGroupCount);
		$totalIsInGroup = $totalIsInGroupRow[0];

		$sqlDelete = "DELETE FROM file WHERE id='$fileId' LIMIT 1";
		$queryDelete = mysqli_query($db_conx, $sqlDelete);
		if($queryDelete){
				//if($fromGroup == '1'){
				if($totalIsInGroupRow[0] > 0){
					//found and delete
					$sqlDeleteFileGroup = "DELETE FROM filegroups WHERE fId='$fileId' LIMIT 1";
					$queryDeleteFileGroup = mysqli_query($db_conx, $sqlDeleteFileGroup);
					if($queryDeleteFileGroup){
						echo "delete_success";
						exit();
					}else{
						echo "delete_unsuccess";
						exit();
					}
				}
			//}
		echo "delete_success";
		exit();
		}
	}
}else{
	echo "delete_unsuccess";
	exit();
}
echo "delete_unsuccess";
exit();
?>