<?php
session_start();
if(!isset($_SESSION['username'])){
header("location:file-management.html");
}
?>

<html>
<body>
Login Successful
</body>
</html>	