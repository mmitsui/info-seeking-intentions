
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
    $userID = $base->getUserID();
	$projectID = $userID;
	$time = $base->getTime();
	$date = $base->getDate();
    $timestamp = $base->getTimestamp();



	$keystroke_buffer = $_POST['keys'];
	$modifier_buffer = $_POST['modifiers'];
	$values_array = array();
	foreach($keystroke_buffer as $lTs=>$keystroke_data){
		$localTimestamp	= $lTs;
		$lts_seconds = $lTs/1000.0;
		$localDate = date("Y-m-d", $lts_seconds);
        $localTime = date("h:i:s",$lts_seconds);

		foreach($keystroke_data as $index=>$key){
			$modifier = $modifier_buffer[$lTs][$index];
			array_push($values_array,"($userID,$projectID,'$key','$modifier',$timestamp,'$date','$time',$localTimestamp,'$localDate','$localTime')");
		}
	}

	$values_str = implode(',',$values_array);

	$cxn = Connection::getInstance();
	$query = "INSERT INTO keystroke_data (userID, projectID, keyCode, modifiers, `timestamp`, `date`, `time`, `localTimestamp`, `localDate`, `localTime`) VALUES $values_str";
    $cxn->commit($query);

    $action = new Action('keystrokesave',$cxn->getLastID());
	$action->setBase($base);
	$action->setLocalTimestamp($localTimestamp);
	$action->save();

//
//
//
//
//
//
//

//	$time = $base->getTime();
//	$date = $base->getDate();
//	$timestamp = $base->getTimestamp();
//	$stageID = $base->getStageID();
//	$questionID = $base->getQuestionID();
//
//
//
//	$query = "INSERT INTO keystroke_data (userID, projectID, stageID, questionID, url, keyCode, modifiers, timestamp, date, time, `localTimestamp`, `localDate`, `localTime`) VALUES";
//
//	for ($i = 0; $i < count($_GET['keyCodes']); ++$i) {
//		$keyCode = $_GET['keyCodes'][$i];
//		$url = $_GET['URLs'][$i];
//		$localTimestamp = $_GET['localTimestamps'][$i];
//		$localDate = $_GET['localDates'][$i];
//		$localTime = $_GET['localTimes'][$i];
//		$modifiers = $_GET['modifiers'][$i];
//
//
//		$query .= "('$userID','$projectID','$stageID', '$questionID','$url','$keyCode','$modifiers','$timestamp','$date','$time','$localTimestamp','$localDate','$localTime')";
//		if($i < count($_GET['keyCodes'])-1){
//			$query .= ",";
//		}
//
//  }
//
//	echo "QUERY $query";
//
//
//
//
//	// $query = "INSERT INTO keystroke_data (userID, projectID, stageID, questionID, url, keyCode, timestamp, date, time, `localTimestamp`, `localDate`, `localTime`)
//	//  		                VALUES";
//
//	$connection = Connection::getInstance();
//	$results = $connection->commit($query);
//	$snippetID = $connection->getLastID();
//
//	$action = new Action('keystrokesave',$snippetID);
//	$action->setBase($base);
//	$action->setLocalTimestamp($localTimestamp);
//	$action->setLocalTime($localTime);
//	$action->setLocalDate($localDate);
//	$action->save();
}
?>
