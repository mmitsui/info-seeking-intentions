<?php
	session_start();
	require_once('../core/Settings.class.php');
	require_once('../core/Base.class.php');
    require_once('../core/Connection.class.php');
		require_once('../core/Util.class.php');

if (Base::getInstance()->isSessionActive())
{
	$base = Base::getInstance();
	$userID = $base->getUserID();
    $projectID = $base->getProjectID();
	$stageID = $base->getStageID();
	$questionID = $base->getQuestionID();
    $userName = $base->getUserName();
		// $localTime = $_GET['localTime'];
		// $localDate = $_GET['localDate'];
		// $localTimestamp = $_GET['localTimestamp'];

    $topicAreaID = $base->getTopicAreaID();

    $port = 0;

		$query = "SELECT I.etherpadPort as port FROM recruits R,instructors I WHERE R.userID='$userID' AND R.instructorID=I.instructorID";

		$connection = Connection::getInstance();
		$results = $connection->commit($query);
		$line = mysql_fetch_array($results,MYSQL_ASSOC);
		$port = $line['port'];

		// Util::getInstance()->saveActionWithLocalTime("Clicked Etherpad Button", 0, $base, $localTime, $localDate, $localTimestamp);



	//Commented out etherpad instance on port 9000 since it cannot be accessed from outside SCI network. Need to be enabled.
	header("Location: http://coagmentopad.rutgers.edu:$port/p/spring2016intent_report-".$projectID."-".$stageID."-".$questionID."?userName=".$userName);
	//header("Location: http://coagmentopad.rutgers.edu/userstudy2014_report-".$userID."-".$stageID."-".$questionID);
}
?>
