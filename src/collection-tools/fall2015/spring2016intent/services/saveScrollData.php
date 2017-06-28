
<?php
	session_start();
	require_once('../core/Connection.class.php');
	require_once('../core/Base.class.php');
	require_once('../core/Action.class.php');
	require_once('../core/Util.class.php');
	require_once("utilityFunctions.php");

if (Base::getInstance()->isSessionActive())
{

    $base = new Base();



	$localTime = $_GET['localTime'];
	$localDate = $_GET['localDate'];
	$localTimestamp = $_GET['localTimestamp'];




	$clientX = $_GET['clientX'];
	$clientY = $_GET['clientY'];
	$pageX = $_GET['pageX'];
	$pageY = $_GET['pageY'];
	$screenX = $_GET['screenX'];
	$screenY = $_GET['screenY'];
	$scrollX = $_GET['scrollX'];
	$scrollY = $_GET['scrollY'];
	$url = $_GET['URL'];
	$type= $_GET['type'];





	$projectID = $base->getProjectID();
	$userID = $base->getUserID();
	$time = $base->getTime();
	$date = $base->getDate();
	$timestamp = $base->getTimestamp();
	$stageID = $base->getStageID();
	$questionID = $base->getQuestionID();


	$query = "INSERT INTO scroll_data (userID, projectID, stageID, questionID, url, clientX, clientY, pageX, pageY, screenX, screenY, scrollX, scrollY, timestamp, date, time, `localTimestamp`, `localDate`, `localTime`,`type`)
	 		                VALUES('$userID','$projectID','$stageID', '$questionID','$url','$clientX', '$clientY', '$pageX', '$pageY', '$screenX', '$screenY', '$scrollX', '$scrollY', '$timestamp','$date','$time','$localTimestamp','$localDate','$localTime','$type')";

	$connection = Connection::getInstance();
	$results = $connection->commit($query);
	$snippetID = $connection->getLastID();

	$action = new Action("scroll-$type",$snippetID);
	$action->setBase($base);
	$action->setLocalTimestamp($localTimestamp);
	$action->setLocalTime($localTime);
	$action->setLocalDate($localDate);
	$action->save();
}
?>
