<?php
	session_start();
    require_once('../core/Base.class.php');
    require_once('../core/Connection.class.php');
    $base = Base::getInstance();
//    $type = $_GET['type'];
    $itemID = $_GET['itemID'];
    $userID = $base->getUserID();
    $projectID = $base->getProjectID();
    $value = $_GET['value'];
    $timestamp = $base->getTimestamp();
    $date = $base->getDate();
    $time = $base->getTime();
    $query = "WHERE bookmarkID='$bookmarkID'AND `userID`='$userID' AND `projectID`='$projectID'";
    $connection = Connection::getInstance();
    $result = $connection->commit($query);

    //TODO: Insert actions
    //                $aquery = "INSERT INTO actions (userID, projectID, timestamp, date, time, action, value, ip) VALUES ('$userID', '$projectID', '$timestamp', '$date', '$time', 'updateRating_$type', 'itemID:$itemID:$value','$ip')";
    //                $results2 = mysql_query($aquery) or die(" ". mysql_error());


	if ((isset($_SESSION['CSpace_userID']))) {
		$query1 = "UPDATE rating SET `active`='0' WHERE `idResource`='$itemID' AND `type`='$type' AND `active`='1' AND `userID`='$userID' AND `projectID`='$projectID'";
		$results = mysql_query($query1) or die(" ". mysql_error());

		$query2 = "INSERT INTO rating (`idResource`, `type`, `value`, `userID`, `projectID`, `active`, `time`, `date`, `timestamp`) VALUES ('$itemID', '$type', '$value', '$userID', '$projectID', '1', '$time', '$date', '$timestamp')";
		$results = mysql_query($query2) or die(" ". mysql_error());
		$webPage = $_GET['webPage'];
                $ip=$_SERVER['REMOTE_ADDR'];

                $pQuery = "SELECT points FROM users WHERE userID='$userID'";
                $pResults = mysql_query($pQuery) or die(" ". mysql_error());
                $pLine = mysql_fetch_array($pResults, MYSQL_ASSOC);
                $totalPoints = $pLine['points'];
                $newPoints = $totalPoints+10;
                $pQuery = "UPDATE users SET points=$newPoints WHERE userID='$userID'";
                $pResults = mysql_query($pQuery) or die(" ". mysql_error());

                if ($webPage!="")
                    require_once($webPage);
	}
?>
