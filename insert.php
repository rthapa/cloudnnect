<?php
$con=mysqli_connect("localhost", "root","","cloudbox_db");
//check connection
if(mysqli_connect_errno())
{
	echo "Failed to connect to MySQL: " . mysqli_connect_error();	
}

$password = $_POST['password'];
$encrypt_password = md5($password);

$sql="INSERT INTO members (firstname, lastname, username, password, email,
 alternateemail, signup, lastlogin)
VALUES ('$_POST[firstname]', '$_POST[lastname]', '$_POST[username]', '$encrypt_password',
'$_POST[email]', '$_POST[alternateemail]', now(), now())";

if(!mysqli_query($con, $sql))
{
	die('Error: ' . mysqli_error($con));
}
echo"1 record added";

mysqli_close($con);
	
?>