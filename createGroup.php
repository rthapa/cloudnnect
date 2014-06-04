<?php
include_once("php_includes/check_login_status.php");

$profile_id = "";
$username = "";

$sql = "SELECT * FROM users WHERE username='$log_username' AND activated='1' LIMIT 1";
$user_query = mysqli_query($db_conx, $sql);

while ($row = mysqli_fetch_array($user_query, MYSQLI_ASSOC)) {
	$username = $row["username"];
	$profile_id = $row["id"];

}

if(isset($_POST['title'])){
	$groupName = $_POST['title'];
	$groupDesc = $_POST['desc'];
	$groupPrivacy = $_POST['privacy'];

	if (strlen($groupDesc) > 160){
        echo "Description section characters should not be more than 155";
        exit(); 
    }else if($groupName == "" || $groupPrivacy== ""){
    	echo "Either group title or privacy selection is empty";
    	exit();
    }else{
    	//storing new group data to db
    	$sql = "INSERT INTO groups (groupTitle, groupDesc, groupAvatar, privacy, creator, createDate)       
		        VALUES('$groupName','$groupDesc','', '$groupPrivacy', '$username', now())";
		$query = mysqli_query($db_conx, $sql); 

		//getting group id from the recent created group

		//linking user to the database
		/*
		$sql2 = "INSERT INTO usersgroup (userId, groupId, createDate)       
		        VALUES('$profile_id','', now())";
		$query = mysqli_query($db_conx, $sql2);
		**/
			echo "Sucessfully created group: ".$groupName;
 			exit();        
    }
    echo "Something went wrong, please try again later.";
    exit();

}

?>

<!DOCTYPE html>
<html>
<head>
	<title></title>
	<link rel="shortcut icon" href="Images/favicon.ico">
	<link rel="stylesheet" type="text/css" href="Styles.css">
	<link rel="stylesheet" type="text/css" href="user-styles.css">
	<script src="js/main.js"></script>
	<script src="js/ajax.js"></script>

<script>
		function create(){
			var title = _("title").value;
			var desc = _("desc").value;
			var privacy = document.querySelector('input[name = "privacy"]:checked').value;//_("privacy").value;
			var status = _("groupCreateStatus");
			if(title == "" || privacy == ""){
				status.style.display = "block";
				status.innerHTML = "Fill all the box";
			}else {

				_("createBtn").style.display = "none";
				status.innerHTML = 'please wait ...';
				var ajax = ajaxObj("POST", "createGroup.php");
		        ajax.onreadystatechange = function() {
			        if(ajaxReturn(ajax) == true) {
			            if(ajax.responseText != "success"){
			            	status.style.display = "block";
							status.innerHTML = ajax.responseText;//"oops something went wrong, Please try again later";
							_("createBtn").style.display = "block";
						} else {
							window.scrollTo(0,0);
							status.style.display = "block";
							status.innerHTML = "Sucessfully created group "+title;
							_("createBtn").style.display = "block";
						}
			        }
		        }
		        ajax.send("title="+title+"&desc="+desc+"&privacy="+privacy);
			}
		}

		
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
	<h4>Create Group</h4>
	</div>

	<form name="createGroupForm.php" onsubmit="return false;">
	<div class="createGroupBox">
		<div class="createGroupBoxDiv1">
			<label>Group name </label>
			<br />
			<input type="text" class="createGroupInput" id="title" name="groupName">
			<br />
			<div class="groupDesc">
				<label>Group Description </label>
				<br />
				 <textarea name="groupDesc" class="createGroupInput" id="desc" rows="5" cols="32" id="about" maxlength="160"  placeholder=""></textarea>
				 
				</textarea>
			</div>
		</div>
		<div class="createGroupBoxDiv2">
			<br />
			<input type="radio" name="privacy" value="open" id="privacy" > Open
			<p>Anyone can see this group and all the content that members share in it. </p>
			<br />
			<input type="radio" name="privacy" value="closed" id="privacy" checked="checked"> Closed
			<p>Anyone can see this group but wont see any content inside it. </p>
			<br />
			<input type="radio" name="privacy" value="secret" id="privacy"> Secret
			<p>Only members see this group and the content inside it.</p>
		</div>

		<p id="groupCreateStatus"></p>
	
		<div class="createBtnWrapper">
		 <input class="downloadButton" type="submit" value="create" id="createBtn" onclick="create()">
		</div>
	</div>
	</form>
	

</div>
</body>
</html>
