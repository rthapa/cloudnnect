<?php
//a php to create table. Run once only

include_once("php_includes/db_conx.php");

//creates table user and populates that table with all of the field that specified in ();
//unique key = no two user can have same username or email
$tbl_users = "CREATE TABLE IF NOT EXISTS users (
				id INT(11) NOT NULL AUTO_INCREMENT,
				username VARCHAR(16) NOT NULL,
				email VARCHAR(255) NOT NULL,
				password VARCHAR (255) NOT NULL,
				gender ENUM('m','f') NOT NULL,
				country VARCHAR(255) NULL,
				userlevel ENUM('a','b','c','d') NOT NULl DEFAULT 'a',
				avatar VARCHAR(255) NULL,
				ip VARCHAR(255) NOT NULL,
				signup DATETIME NOT NULL,
				lastlogin DATETIME NOT NULL,
				notesCheck DATETIME NOT NULL,
				activated ENUM('0','1') NOT NULL DEFAULT '0',
                                totalDownloads INT(11) NOT NULL DEFAULT '0',
                                totalUploads INT(11) NOT NULL DEFAULT '0',
				PRIMARY KEY (id),
				UNIQUE KEY username(username, email)
			  )";
	
//mysqli_query(database connection link, sql syntax)
$query= mysqli_query($db_conx, $tbl_users);

//verifiy 
if($query === TRUE){
	echo "<h3> user table created</h3>";
}else{
	echo "<h3> user table not created</h3>";
}

////////////////////////////////////
$tbl_useroptions = "CREATE TABLE IF NOT EXISTS useroptions ( 
                id INT(11) NOT NULL,
                username VARCHAR(16) NOT NULL,
		background VARCHAR(255) NOT NULL,
		question VARCHAR(255) NULL,
		answer VARCHAR(255) NULL,
                temp_pass VARCHAR(255) NULL,
                PRIMARY KEY (id),
                UNIQUE KEY username (username) 
                )"; 
$query = mysqli_query($db_conx, $tbl_useroptions); 
if ($query === TRUE) {
	echo "<h3>useroptions table created OK :) </h3>"; 
} else {
	echo "<h3>useroptions table NOT created :( </h3>"; 
}
////////////////////////////////////
$tbl_friends = "CREATE TABLE IF NOT EXISTS friends ( 
                id INT(11) NOT NULL AUTO_INCREMENT,
                user1 VARCHAR(16) NOT NULL,
                user2 VARCHAR(16) NOT NULL,
                follower VARCHAR(16) NOT NULL,
                following VARCHAR(16) NOT NULL,
                datemade DATETIME NOT NULL,
                accepted ENUM('0','1') NOT NULL DEFAULT '0',
                PRIMARY KEY (id)
                )"; 
$query = mysqli_query($db_conx, $tbl_friends); 
if ($query === TRUE) {
	echo "<h3>friends table created OK :) </h3>"; 
} else {
	echo "<h3>friends table NOT created :( </h3>"; 
}
////////////////////////////////////
////////////////////////////////////
$tbl_follow = "CREATE TABLE IF NOT EXISTS follow ( 
                id INT(11) NOT NULL AUTO_INCREMENT,
                follower VARCHAR(16) NOT NULL,
                following VARCHAR(16) NOT NULL,
                datemade DATETIME NOT NULL,
                accepted ENUM('0','1') NOT NULL DEFAULT '0',
                PRIMARY KEY (id)
                )"; 
$query = mysqli_query($db_conx, $tbl_follow); 
if ($query === TRUE) {
        echo "<h3>follow table created OK :) </h3>"; 
} else {
        echo "<h3>follow table NOT created :( </h3>"; 
}
////////////////////////////////////
$tbl_blockedusers = "CREATE TABLE IF NOT EXISTS blockedusers ( 
                id INT(11) NOT NULL AUTO_INCREMENT,
                blocker VARCHAR(16) NOT NULL,
                blockee VARCHAR(16) NOT NULL,
                blockdate DATETIME NOT NULL,
                PRIMARY KEY (id) 
                )"; 
$query = mysqli_query($db_conx, $tbl_blockedusers); 
if ($query === TRUE) {
	echo "<h3>blockedusers table created OK :) </h3>"; 
} else {
	echo "<h3>blockedusers table NOT created :( </h3>"; 
}
////////////////////////////////////
$tbl_status = "CREATE TABLE IF NOT EXISTS status ( 
                id INT(11) NOT NULL AUTO_INCREMENT,
                osid INT(11) NOT NULL,
                account_name VARCHAR(16) NOT NULL,
                author VARCHAR(16) NOT NULL,
                type ENUM('a','b','c') NOT NULL,
                data TEXT NOT NULL,
                postdate DATETIME NOT NULL,
                PRIMARY KEY (id) 
                )"; 
$query = mysqli_query($db_conx, $tbl_status); 
if ($query === TRUE) {
	echo "<h3>status table created OK :) </h3>"; 
} else {
	echo "<h3>status table NOT created :( </h3>"; 
}
////////////////////////////////////
$tbl_photos = "CREATE TABLE IF NOT EXISTS photos ( 
                id INT(11) NOT NULL AUTO_INCREMENT,
                user VARCHAR(16) NOT NULL,
                gallery VARCHAR(16) NOT NULL,
		filename VARCHAR(255) NOT NULL,
                description VARCHAR(255) NULL,
                uploaddate DATETIME NOT NULL,
                PRIMARY KEY (id) 
                )"; 
$query = mysqli_query($db_conx, $tbl_photos); 
if ($query === TRUE) {
	echo "<h3>photos table created OK :) </h3>"; 
} else {
	echo "<h3>photos table NOT created :( </h3>"; 
}
////////////////////////////////////
$tbl_files = "CREATE TABLE IF NOT EXISTS file ( 
                id INT(11) NOT NULL AUTO_INCREMENT,
                fileName VARCHAR(255) NOT NULL,
                url VARCHAR(255) NOT NULL,
                file_type VARCHAR(255) NOT NULL,
                owner VARCHAR(255) NOT NULL,
                private ENUM('0','1') NOT NULL DEFAULT '0',
                description VARCHAR(255) NULL,
                keywords VARCHAR(30) NULL,
                uploaddate DATETIME NOT NULL,
                PRIMARY KEY (id) 
                )"; 
$query = mysqli_query($db_conx, $tbl_photos); 
if ($query === TRUE) {
        echo "<h3>files table created OK :) </h3>"; 
} else {
        echo "<h3>files table NOT created :( </h3>"; 
}
////////////////////////////////////
$tbl_notifications = "CREATE TABLE IF NOT EXISTS notifications ( 
                id INT(11) NOT NULL AUTO_INCREMENT,
                username VARCHAR(16) NOT NULL,
                initiator VARCHAR(16) NOT NULL,
                app VARCHAR(255) NOT NULL,
                note VARCHAR(255) NOT NULL,
                did_read ENUM('0','1') NOT NULL DEFAULT '0',
                date_time DATETIME NOT NULL,
                PRIMARY KEY (id) 
                )"; 
$query = mysqli_query($db_conx, $tbl_notifications); 
if ($query === TRUE) {
	echo "<h3>notifications table created OK :) </h3>"; 
} else {
	echo "<h3>notifications table NOT created :( </h3>"; 
}
/////////////////////////////////////////////////////////
$tbl_group = "CREATE TABLE IF NOT EXISTS group ( 
                groupId INT(11) NOT NULL AUTO_INCREMENT,
                groupTitle VARCHAR(16) NOT NULL,
                groupDisc VARCHAR(16) NOT NULL,
                groupAvatar VARCHAR(255) NOT NULL,
                private ENUM('0','1') NOT NULL DEFAULT '0',
                createDate DATETIME NOT NULL,
                PRIMARY KEY (id) 
                )"; 
$query = mysqli_query($db_conx, $tbl_group); 
if ($query === TRUE) {
        echo "<h3>group table created OK :) </h3>"; 
} else {
        echo "<h3>group table NOT created :( </h3>"; 
}
/////////////////////////////////////////////////////////
$tbl_usersGroups = "CREATE TABLE IF NOT EXISTS usersGroups ( 
                ugId INT(11) NOT NULL AUTO_INCREMENT,
                userId INT(11) NOT NULL,
                groupId INT(11) NOT NULL,
                createDate DATETIME NOT NULL,
                PRIMARY KEY (ugId) 
                )"; 
$query = mysqli_query($db_conx, $tbl_group); 
if ($query === TRUE) {
        echo "<h3>group table created OK :) </h3>"; 
} else {
        echo "<h3>group table NOT created :( </h3>"; 
}
?>