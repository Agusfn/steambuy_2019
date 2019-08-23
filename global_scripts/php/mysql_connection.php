<?php

if($_SERVER["SERVER_ADDR"] == "::1" || $_SERVER["SERVER_ADDR"] == "127.0.0.1") // Si es localhost
{
	$mysql_server = "localhost";
	$mysql_user = "root";
	$mysql_password = "20596";
	$mysql_database = "steambuy_legacy_db";
} else {
	$mysql_server = "localhost";
	$mysql_user = "steambuy";
	$mysql_password = "3P4h$a{yJ=2YE4Natu;CThg&fQf^B%";
	$mysql_database = "steambuy";
}

$connection_error = 0;
$con = @mysqli_connect($mysql_server, $mysql_user, $mysql_password, $mysql_database);
if($con != false) {
	mysqli_query($con, "SET NAMES 'utf8', time_zone = '-03:00'");
}




?>