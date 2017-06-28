<?php

//	Gets number of bookmarks.  Meant to be used as an AJAX Call.
//  Don't use.
//  AJAX didn't work when tried implementing it.

	session_start();
	require_once('../core/Base.class.php');
	require_once('../core/Connection.class.php');

	$base = Base::getInstance();



	$userID = $base->getUserID();
	$projectID = $base->getProjectID();

	$cxn = Connection::getInstance();
	$query = "SELECT COUNT(*) as ct FROM bookmarks WHERE userID='$userID'";
	$results = $cxn->commit($query);
	$line = mysql_fetch_array($results, MYSQL_ASSOC);
	$nbookmarks = "0";
	$nbookmarks = strval($line['ct']);



	echo $nbookmarks;
?>
