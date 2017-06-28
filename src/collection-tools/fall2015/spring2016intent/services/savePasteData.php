
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
	
	
    
	$from_url = $_GET['fromURL'];
    $to_url = $_GET['toURL'];
    
	$from_title = htmlspecialchars($_GET['fromtitle']);
    $from_title = str_replace(" - Mozilla Firefox","",$from_title);
    
    $to_title = htmlspecialchars($_GET['totitle']);
    $to_title = str_replace(" - Mozilla Firefox","",$to_title);
    
    
	$snippet = addslashes($_GET['snippet']);
	$snippet = stripslashes($snippet);
	$snippet = stripslashes($snippet);
	$snippetValue = str_replace("\"","&quote;",$snippet);
	$snippet = str_replace("&quote;", "\"", $snippet);
	$snippet = str_replace("'", "\\'", $snippet);
    
	
	$projectID = $base->getProjectID();
	$userID = $base->getUserID();
	$time = $base->getTime();
	$date = $base->getDate();
	$timestamp = $base->getTimestamp();
	$stageID = $base->getStageID();
	$questionID = $base->getQuestionID();
    
		
	$query = "INSERT INTO paste_data (userID, projectID, stageID, questionID, from_url, from_title, snippet, to_url, to_title, timestamp, date, time, `localTimestamp`, `localDate`, `localTime`, type)
	 		                VALUES('$userID','$projectID','$stageID', '$questionID','$from_url','$from_title','$snippet','$to_url','$to_title','$timestamp','$date','$time','$localTimestamp','$localDate','$localTime','text')";
	
	$connection = Connection::getInstance();			
	$results = $connection->commit($query);
	$snippetID = $connection->getLastID();
		
	$action = new Action('paste',$snippetID);
	$action->setBase($base);
	$action->setLocalTimestamp($localTimestamp);
	$action->setLocalTime($localTime);
	$action->setLocalDate($localDate);
	$action->save();
}
?>