<?php
	session_start();
    require_once('../../core/Base.class.php');
    require_once('../../core/Connection.class.php');
    require_once('../../core/Util.class.php');

    $base = Base::getInstance();
//    $type = $_GET['type'];
    $itemID = $_GET['itemID'];
    $userID = $base->getUserID();
    $projectID = $base->getProjectID();
    $value = $_GET['value'];
    $bookmarkID = $itemID;
    $timestamp = $base->getTimestamp();
    $date = $base->getDate();
    $time = $base->getTime();
    
    $query = "SELECT * from bookmarks WHERE bookmarkID='$bookmarkID'AND `userID`='$userID' AND `projectID`='$projectID'";
    $connection = Connection::getInstance();
    $result = $connection->commit($query);
    $line = mysql_fetch_array($result,MYSQL_ASSOC);

    $oldrating = $line['rating'];
    $newrating = $value;
    
    Util::getInstance()->saveAction("bmrk_rating old:$oldrating new:$newrating",0,$base);
    
    $query = "UPDATE bookmarks SET rating='$value' WHERE bookmarkID='$bookmarkID'AND `userID`='$userID' AND `projectID`='$projectID'";
    $connection = Connection::getInstance();
    $result = $connection->commit($query);

    //TODO: Insert actions
    //                $aquery = "INSERT INTO actions (userID, projectID, timestamp, date, time, action, value, ip) VALUES ('$userID', '$projectID', '$timestamp', '$date', '$time', 'updateRating_$type', 'itemID:$itemID:$value','$ip')";
    //                $results2 = mysql_query($aquery) or die(" ". mysql_error());

    
	if (!is_null($userID)) {
//		$query1 = "UPDATE rating SET `active`='0' WHERE `idResource`='$itemID' AND `type`='$type' AND `active`='1' AND `userID`='$userID' AND `projectID`='$projectID'";
//		$results = mysql_query($query1) or die(" ". mysql_error());
//        
//		$query2 = "INSERT INTO rating (`idResource`, `type`, `value`, `userID`, `projectID`, `active`, `time`, `date`, `timestamp`) VALUES ('$itemID', '$type', '$value', '$userID', '$projectID', '1', '$time', '$date', '$timestamp')";
//		$results = mysql_query($query2) or die(" ". mysql_error());
		$webPage = $_GET['webPage'];

                if ($webPage!="")
                    require_once($webPage);
	}
?>