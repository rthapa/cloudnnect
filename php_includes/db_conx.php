<?php
//$db_conx = mysqli_connect("localhost", "rabi", "Js30jackson", "social");
$db_conx = mysqli_connect("localhost", "root","","social");


//Evaluate the connection
if(mysqli_connect_errno()){
	echo mysqli_connect_error();
	exit();
}
?>