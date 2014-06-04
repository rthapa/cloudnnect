<?php
include_once("../php_includes/check_login_status.php");
// Ajax calls this NAME CHECK code to execute
if(isset($_POST["userNameCheckInGroup"])){ //IF AXAJ requests the 'usernamecheck' via post do this
	$uName="";
	$username = preg_replace('#[^a-z0-9]#i', '', $_POST['userNameCheckInGroup']);
	$sqlCheckUsername = "SELECT id FROM users WHERE username='$username' LIMIT 1";
    $queryCheckUsername = mysqli_query($db_conx, $sqlCheckUsername); 
    $uname_check = mysqli_num_rows($queryCheckUsername);
    /*
    while ($row = mysqli_fetch_array($queryCheckUsername, MYSQLI_ASSOC)) {
    	$uName = $row["username"];
    }
	*/
    if ($uname_check < 1) {
    	echo 'user_not_found|'.$uName;
	    exit();
    } else {
    	echo 'user_ok|'.$uName;
	    exit();
    }

}

if(isset($_POST["addUserToGroup"]) && isset($_POST["gId"])){ //IF AXAJ requests the 'usernamecheck' via post do this
    $gId = preg_replace("/[^0-9]/", "", $_POST['gId']);
	$profile_id="";
	$username = preg_replace('#[^a-z0-9]#i', '', $_POST['addUserToGroup']);
	$sqlAddUser = "SELECT id FROM users WHERE username='$username' AND activated='1' LIMIT 1";
    $queryAddUser = mysqli_query($db_conx, $sqlAddUser); 
    $uname_check = mysqli_num_rows($queryAddUser);
	
    if ($uname_check < 1) {
    	echo 'user_not_found';
	    exit();
    } else {


        //fetch username's profile id
        $sql = "SELECT * FROM users WHERE username='$username' AND activated='1' LIMIT 1";
        $user_query = mysqli_query($db_conx, $sql);
        while ($row = mysqli_fetch_array($user_query, MYSQLI_ASSOC)) {
            $profile_id = $row["id"];
        }

        //check if the user has the privilage to add

        //check if the invitation is already pending
        $sqlCountPending = "SELECT COUNT(ugId) FROM usersgroups WHERE userId='$profile_id' AND groupId='$gId' AND accepted='0' LIMIT 1";
        $queryCountPending = mysqli_query($db_conx, $sqlCountPending);
        $row_count1 = mysqli_fetch_row($queryCountPending);
         if ($row_count1[0] > 0) {
            mysqli_close($db_conx);
            echo "Invitation already pending";
            exit();
        }

        //check if the user is already in group
        $sqlCountAlreadyMem = "SELECT COUNT(ugId) FROM usersgroups WHERE userId='$profile_id' AND groupId='$gId' AND accepted='1' LIMIT 1";
        $queryCountAlreadyMem = mysqli_query($db_conx, $sqlCountAlreadyMem);
        $row_count2 = mysqli_fetch_row($queryCountAlreadyMem);
        //or the user is the creator of the group which makes the user in group
        $sqlCountCreator = "SELECT COUNT(groupId) FROM groups WHERE creator='$username' AND groupId='$gId' LIMIT 1";
        $queryCountCreator = mysqli_query($db_conx, $sqlCountCreator);
        $row_count3 = mysqli_fetch_row($queryCountCreator);

         if ($row_count2[0] > 0 || $row_count3[0] > 0 ) {
            mysqli_close($db_conx);
            echo "User is already in group";
            exit();
        }
        
    	$queryAdd = "INSERT INTO usersgroups (ugId ,userId, groupId, createDate) 
		VALUES('','$profile_id', '$gId', now())";
		mysqli_query($db_conx, $queryAdd);

    	echo 'user_added';
	    exit();
    }

}

if(isset($_POST["groupReqAction"]) && isset($_POST["ugId"])){
    $ugId = $_POST['ugId'];
    if($_POST['groupReqAction'] == "accept"){

        //check if already accepted
        $sqlCount = "SELECT COUNT(ugId) FROM usersgroups WHERE ugId='$ugId' AND accepted='1' LIMIT 1";
        $queryCount = mysqli_query($db_conx, $sqlCount);
        $row_count1 = mysqli_fetch_row($queryCount);
         if ($row_count1[0] > 0) {
            mysqli_close($db_conx);
            echo "Group request already accepted";
            exit();
        }

        $sql = "UPDATE usersgroups SET accepted='1' WHERE ugId='$ugId' LIMIT 1";
        $query = mysqli_query($db_conx, $sql);
        mysqli_close($db_conx);
        echo "accept_ok";
        exit();
    }else if($_POST['groupReqAction'] == "reject"){

        //check if already accepted
        $sqlCountRej = "SELECT COUNT(ugId) FROM usersgroups WHERE ugId='$ugId' LIMIT 1";
        $queryCountRej = mysqli_query($db_conx, $sqlCountRej);
        $row_count1 = mysqli_fetch_row($queryCountRej);
         if ($row_count1[0] == 0) {
            mysqli_close($db_conx);
            echo "Group request already rejected";
            exit();
        }

        $sql = "DELETE FROM usersgroups WHERE ugId='$ugId' LIMIT 1";
        $query = mysqli_query($db_conx, $sql);
        mysqli_close($db_conx);
        echo "reject_ok";
        exit();
    }

    echo 'accept_ok';
}
?>