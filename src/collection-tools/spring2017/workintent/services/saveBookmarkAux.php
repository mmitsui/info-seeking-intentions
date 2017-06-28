<?php

	session_start();
    require_once('../core/Connection.class.php');
    require_once('../core/Base.class.php');
    require_once('../core/Util.class.php');

		use Pubnub\Pubnub;

		require_once('../sidebar/pubnub-lib/autoloader.php');


    if (Base::getInstance()->isSessionActive())
    {

 



			$pubnub = new Pubnub(array('publish_key'=>'pub-c-0ee3d3d2-e144-4fab-bb9c-82d9be5c13f1','subscribe_key'=>'sub-c-ac9b4e84-b567-11e4-bdc7-02ee2ddab7fe'));
    $ip=$_SERVER['REMOTE_ADDR'];
    $connection = Connection::getInstance();
    $base = Base::getInstance();
    $userID = $base->getUserID();
    $projectID = $base->getProjectID();
    $stageID = $base->getStageID();
    $questionID = $base->getQuestionID();
    $localDate = $_GET['localDate'];
    $localTime = $_GET['localTime'];
    $localTimestamp = $_GET['localTimestamp'];





    $title = addslashes($_GET['title']);
    $originalURL = addslashes($_GET['page']);


    $timestamp = $base->getTimestamp();
    $date = $base->getDate();
    $time = $base->getTime();


    $query = "INSERT INTO bookmarks (userID,projectID,stageID,questionID,url,title,timestamp,date,time,`localDate`,`localTime`,`localTimestamp`,status) VALUES('$userID','$projectID','$stageID','$questionID','$originalURL','$title','$timestamp','$date','$time','$localDate','$localTime','$localTimestamp','1')";
    $results = $connection->commit($query);

    $bookmarkID = $connection->getLastID();
		Util::getInstance()->saveActionWithLocalTime("Save Bookmark",$bookmarkID,$base,$localTime,$localDate,$localTimestamp);



		$query = "SELECT userID as userID from users WHERE projectID='$projectID'";
		$results = $connection->commit($query);

		while($lineBroadcast = mysql_fetch_array($results,MYSQL_ASSOC)){
			$userIDBroadcast = $lineBroadcast['userID'];
			$message = array('message'=>'refresh-bookmarks');
			$res=$pubnub->publish("spr15-".$base->getStageID()."-".$base->getProjectID()."-".$userIDBroadcast,$message);
		}

    }

?>
