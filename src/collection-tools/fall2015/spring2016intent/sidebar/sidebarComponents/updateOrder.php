<?php
	require_once('../../core/Connection.class.php');
	require_once('../../core/Base.class.php');
	require_once('../../core/Util.class.php');
	session_start();
	$base = Base::getInstance();

	 if ((isset($_SESSION['CSpace_userID']))) {
	$table = $_GET['table'];
	$orderBy = $_GET['orderBy'];
	$webPage = $_GET['webPage'];
	$_SESSION['orderBy'.$table] = $orderBy;
  $userID = $base->getUserID();
	$projectID = $_SESSION['CSpace_projectID'];
	$timestamp = $base->getTimestamp();
	$date = $base->getDate();
	$time = $base->getTime();
  $ip=$_SERVER['REMOTE_ADDR'];
	Util::getInstance()->saveAction("updateOrder_$table","$orderBy",$base);

	// $aquery = "INSERT INTO actions (userID, projectID, timestamp, date, time, action, value, ip) VALUES ('$userID', '$projectID', '$timestamp', '$date', '$time', 'updateOrder_$table', '$orderBy','$ip')";

	// $connection = Connection::getInstance();
	// $result = $connection->commit($aquery);
	if ($webPage!=""){
		require_once($webPage);
	}

	 }
?>
