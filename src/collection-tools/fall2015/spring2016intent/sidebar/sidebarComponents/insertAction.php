<?php
	require_once("../../core/Connection.class.php");
	require_once("../../core/Base.class.php");
	require_once("../../core/Util.class.php");
	session_start();

	$base = Base::getInstance();


	$time = $base->getTime();
	$date = $base->getDate();
	$timestamp = $base->getTimestamp();
	$projectID = $base->getProjectID();
	$userID = $base->getUserID();
        $ip=$base->getIP();
	$action = $_GET['action'];
 	$value = $_GET['value'];
	Util::getInstance()->saveAction("$action","$value",$base);
	// $query = "INSERT INTO actions (userID, projectID, timestamp, date, time, action, value, ip) VALUES ('$userID', '$projectID', '$timestamp', '$date', '$time', '$action', '$value','$ip')";
	$connection = Connection::getInstance();
	// $results = $connection->commit($query);

    $query = "SELECT * FROM users WHERE userID='$userID'";
	$results = $connection->commit($query);
	$line = mysql_fetch_array($results, MYSQL_ASSOC);
?>
