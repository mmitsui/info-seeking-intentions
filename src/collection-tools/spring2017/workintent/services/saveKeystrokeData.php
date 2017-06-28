
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



	// $localTime = $_GET['localTime'];
	// $localDate = $_GET['localDate'];
	// $localTimestamp = $_GET['localTimestamp'];







	$projectID = $base->getProjectID();
	$userID = $base->getUserID();
	$time = $base->getTime();
	$date = $base->getDate();
	$timestamp = $base->getTimestamp();
	$stageID = $base->getStageID();
	$questionID = $base->getQuestionID();



	$query = "INSERT INTO keystroke_data (userID, projectID, stageID, questionID, url, keyCode, modifiers, timestamp, date, time, `localTimestamp`, `localDate`, `localTime`) VALUES";

	for ($i = 0; $i < count($_GET['keyCodes']); ++$i) {
		$keyCode = $_GET['keyCodes'][$i];
		$url = $_GET['URLs'][$i];
		$localTimestamp = $_GET['localTimestamps'][$i];
		$localDate = $_GET['localDates'][$i];
		$localTime = $_GET['localTimes'][$i];
		$modifiers = $_GET['modifiers'][$i];


		$query .= "('$userID','$projectID','$stageID', '$questionID','$url','$keyCode','$modifiers','$timestamp','$date','$time','$localTimestamp','$localDate','$localTime')";
		if($i < count($_GET['keyCodes'])-1){
			$query .= ",";
		}

  }

	echo "QUERY $query";




	// $query = "INSERT INTO keystroke_data (userID, projectID, stageID, questionID, url, keyCode, timestamp, date, time, `localTimestamp`, `localDate`, `localTime`)
	//  		                VALUES";

	$connection = Connection::getInstance();
	$results = $connection->commit($query);
	$snippetID = $connection->getLastID();

	$action = new Action('keystrokesave',$snippetID);
	$action->setBase($base);
	$action->setLocalTimestamp($localTimestamp);
	$action->setLocalTime($localTime);
	$action->setLocalDate($localDate);
	$action->save();
}
?>
